<?php

// Variables are to store user id info.
$fname = "";
$lname = "";
// A variable is to store add_project, modify_project or delete_project.
$pro_id = "";
$pro_title = "";
$pro_requr = "";
$pro_id_array = "";
$operation = "";
// A variable is to store error information.
$errors = array();


function insert_new_project($db_conn, $pro_title, $pro_requr, $fname, $lname){
	global $errors;
	$table_name = "project";
	// Check new project title.
	if(empty($pro_title)){
		array_push($errors, "Project title is empty.");
		return -1;
	}
	// Insert data.		
	$sql = "INSERT INTO {$table_name} (title, requirement, fname, lname) 
	        VALUES('$pro_title', '$pro_requr', '$fname', '$lname')";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    array_push($errors, "Failed to add project into database.");
	    return -1;
	}
	// Return success.
	return 0;
}


function modify_project($modf_pro_id, $modf_pro_title, $modf_pro_sponsor, $modf_pro_requirement){
	global $errors;
	$table_name = "project";
	// Check modified project id.
	if(empty($modf_pro_id)){
		array_push($errors, "Project ID is empty.");
		return -1;
	}
	// Check modified project title.
	if(empty($modf_pro_title)){
		array_push($errors, "Project title is empty.");
		return -1;
	}
	// Search in database
	$local_pro_id = intval($modf_pro_id);
	$sql = "SELECT * FROM {$table_name} WHERE pro_id={$local_pro_id}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    array_push($errors, "Failed to access TABLE {$table_name} with project # {$modf_pro_id}.");
		return -1;
	}
	if($row = mysqli_fetch_array($results)){
		$sql = "UPDATE {$table_name} 
		        SET title={$modf_pro_title},
		            sponsor={$modf_pro_sponsor},
		            requirement={$modf_pro_requirement}
		        WHERE pro_id={$local_pro_id}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
		    array_push($errors, "Failed to update TABLE {$table_name} with project # {$modf_pro_id}.");
			return -1;
		}
	}
	else{
		array_push($errors, "Failure! Project # {$modf_pro_id}is not in TABLE {$table_name}.");
		return -1;
	}
	// Return success.
	return 0;
}


function delete_project($db_conn, $fname, $lname, $del_pro_id){
	global $errors, $pro_array;
	$table_name = "project";
	$table_name_2 = "preference";
	$error_flag = 0;
	// Check deleted project id.
	if(empty($del_pro_id)){
		array_push($errors, "Project ID is empty.");
		return -1;
	}
	// Analyze deleted items.
	$pro_array = explode(",", $del_pro_id);	
	$a = array();
	foreach ($pro_array as $pro_item) {
		if(intval($pro_item) == 0){
			array_push($errors, "Project ID must be integer(s) separated by comma ','.");
			return -1;
		}
		else{
			array_push($a, intval($pro_item));
		}
	}
	// Acquire unique deleted items.
	$a = array_unique($a);
	foreach ($a as $item) {
		// Check if project $item is in system.
		$sql = "SELECT COUNT(*) AS count FROM {$table_name} WHERE pro_id=$item";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access deleted project {$item}.");
			$error_flag = -1;
			continue;
		}
		$row=mysqli_fetch_assoc($results);
		if ($row["count"] == 0) { 
			array_push($errors, "Failure! Project {$item} is not in this system.");
			$error_flag = -1;
			continue;
		}
		// Check if this project has been registered by students.
		$sql = "SELECT COUNT(*) AS count FROM {$table_name_2} WHERE pro_id=$item";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access deleted project {$item}.");
			$error_flag = -1;
			continue;
		}
		$row=mysqli_fetch_assoc($results);
		if ($row["count"] != 0) { 
			array_push($errors, "Failure! Project {$item} has been enrolled by students.");
			$error_flag = -1;
			continue;
		}
		// Check if current user is the author created this project.
		// Only project's author can delete the project.
		$sql = "SELECT fname, lname FROM {$table_name} WHERE pro_id={$item}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access author of deleted project {$item}.");
			$error_flag = -1;
			continue;
		}
		$row=mysqli_fetch_assoc($results);
		$strcmp_1 = strcmp(strtolower($row["fname"]), strtolower($fname));
		$strcmp_2 = strcmp(strtolower($row["lname"]), strtolower($lname));
		if ($strcmp_1!=0 || $strcmp_2!=0){
			array_push($errors, "Failure! You are not author of project {$item}.");
			$error_flag = -1;
			continue;
		}
		// Delete project $item.
		$sql = "DELETE FROM {$table_name} WHERE pro_id={$item}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failure! Meet problems when to delete project {$item}.");
			$error_flag = -1;
			continue;
		}
	}
	return $error_flag;	
}


if(isset($_POST['bn_sbmt'])){
	// Connect database.
	$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
	if (!$db_conn) {
		array_push($errors, "Failed to connect database!");
		goto error_report;
	}
	
	// Acquire user id info input.
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	
	// Implement user's operation.
	$operation = $_POST['operation'];
	switch ($operation) {
		case 'add_pro':
			$pro_title = $_POST['pro_title'];
			$pro_requr = $_POST['pro_requr'];
			// Insert new project information into database.
			$r_val = insert_new_project($db_conn, $pro_title, $pro_requr, $fname, $lname);
			if ($r_val == -1){
				mysqli_close($db_conn);
				goto error_report;
			}
			break;
		case 'mod_pro':
			$pro_id = $_POST['pro_id'];
			$pro_title = $_POST['pro_title'];
			$pro_requr = $_POST['pro_requr'];
			// Modify an existing project.
			$r_val = modify_project($pro_id, $pro_title, $pro_requr);	
			if ($r_val == -1){
				mysqli_close($db_conn);
				goto error_report;
			}
			break;
		case 'del_pro':
			$pro_id = $_POST['pro_id'];
			// Delete existing project(s).
			$r_val = delete_project($db_conn, $fname, $lname, $pro_id);	
			if ($r_val == -1){
				mysqli_close($db_conn);
				goto error_report;
			}
				break;
		default:
			break;
	}

	mysqli_close($db_conn);

	error_report:
	if(count($errors)>0){
		foreach ($errors as $error) {
			echo $error;
			echo "<br>";
		}
	}
	else{
		echo "Successful operation!";		
		echo "<br>";
	}
	echo "<br>";
}
?>