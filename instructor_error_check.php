<?php

// Variables are to store user id info.
$fname = "";
$lname = "";
$csuid = "";
// A variable is to store add_project, modify_project or delete_project.
$pro_id = "";
$pro_title = "";
$pro_requr = "";
$pro_id_array = "";
$operation = "";
// A variable is to store error information.
$errors = array();


function insert_new_project($db_conn, 
							$pro_title, $pro_requr,
							$fname, $lname, $csuid){
	global $errors;
	$table_name = "project";
	// Insert data.		
	$sql = "INSERT INTO {$table_name} (title, requirement, fname, lname, csuid) 
	        VALUES('$pro_title', '$pro_requr', '$fname', '$lname', '$csuid')";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    array_push($errors, "Failed to add project into database.");
	    return -1;
	}
	// Return success.
	return 0;
}


function modify_project($modf_pro_id, $modf_pro_title, 
			            $modf_pro_sponsor, $modf_pro_requirement){
	global $errors;
	$table_name = "project";
	// Check new project title.
	if(empty($modf_pro_id)){
		array_push($errors, "Project # is empty.");
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


function delete_project($del_pro_id){
	global $errors, $pro_array;
	$table_name = "project";
	$error_flag = 0;
	// Delete project(s).
	$pro_array = explode(",", $del_pro_id);	
	$a = array();
	foreach ($pro_array as $pro_item) {
		if(intval($pro_item) == 0){
			array_push($errors, "Project # must be integer(s) separated by comma ','.");
			return -1;
		}
		else{
			array_push($a, intval($pro_item));
		}
	}
	$a = array_unique($a);
	rsort($a);
	foreach ($a as $item) {
		$sql = "DELETE FROM {$table_name} WHERE pro_id={$item}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to delete project {$item}.");
			$error_flag = -1;
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
	$csuid = $_POST['csuid'];
	
	// Implement user's operation.
	$operation = $_POST['operation'];
	switch ($operation) {
		case 'add_pro':
			$pro_title = $_POST['pro_title'];
			$pro_requr = $_POST['pro_requr'];
			// Insert new project information into database.
			$r_val = insert_new_project($db_conn, $pro_title, $pro_requr, $fname, $lname, $csuid);
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
			$del_pro_id = $_POST['$pro_id'];
			// Delete existing project(s).
			$r_val = delete_project($pro_id);	
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
	}
}
?>