<?php


define("FAILED", -1);
define('SUCCESS', 0);

// Constant variables used in function modify_project.
define("INVALID_PROID",   1);
define("VALID_PROID",     2);
define("VALID_SPONSOR",   3);
define("INVALID_SPONSOR", 4);
define("VALID_TITLE",     5);
define("INVALID_TITLE",   6);
define("SUCCESS_MODF",    0);


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


function notice_for_modf($r_val){
	if ($r_val == INVALID_PROID) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Input project id is invailid.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == INVALID_SPONSOR) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "You are not the author of this project.<br>";
		echo "<br></font>";
	}elseif ($r_val == INVALID_TITLE) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Input project title is empty.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Errors display in DEBUG OUTPUT.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SUCCESS) {
		echo "<font color='blue'>";
		echo "Modification Successful!<br>";
		echo "<br></font>";
	}
}


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


function modify_project($db_conn, 
						$pro_id, $pro_title, $pro_requr, 
						$fname, $lname){
	global $errors;
	$table_name = "project";
	// Check if input project id is valid.
	$r_val = check_valid_proid($db_conn, $pro_id);
	if ($r_val != VALID_PROID) {
		return $r_val;
	}
	// Check if input user is the author who registered this project.
	$r_val = check_valid_sponsor($db_conn, $pro_id, $fname, $lname);
	if ($r_val != VALID_SPONSOR) {
		return $r_val;
	}
	// Check if project title is empty.
	$r_val = check_valid_modfinfo($pro_title);
	if ($r_val != VALID_TITLE) {
		return $r_val;
	}
	// Update project modification.
	$r_val = update_project($db_conn, $pro_id, $pro_title, $pro_requr);
	return $r_val;
}


function check_valid_proid($db_conn, $pro_id){
	global $errors;
	$table_name = "project";
	// Check if input project id is an integer.
	if ((string)intval($pro_id) != $pro_id) {
		return INVALID_PROID;
	}
	// Check if input project id is in TABLE project.
	$sql = "SELECT COUNT(*)FROM {$table_name} WHERE pro_id=$pro_id";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    array_push($errors, "Failed to valide proid in TABLE $table_name.");
	    return FAILED;
	}
	$row = mysqli_fetch_array($results);
	if ($row['COUNT(*)'] == 0) {
		return INVALID_PROID;
	}
	else{
		return VALID_PROID;
	}
}


function check_valid_sponsor($db_conn, $pro_id, $fname, $lname){
	global $errors;
	$table_name = "project";
	// Check if current user is the author registered this project.
	$sql = "SELECT *FROM {$table_name} WHERE pro_id=$pro_id";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    array_push($errors, "Failed to validate sponsor in TABLE {$table_name}.");
	    return FAILED;
	}
	$row = mysqli_fetch_array($results);
	$cmp_fname = strcmp(strtolower($row['fname']), strtolower($fname));
	$cmp_lname = strcmp(strtolower($row['lname']), strtolower($lname));
	if ($cmp_fname==0 && $cmp_lname==0) {
		return VALID_SPONSOR;
	}
	else{
		return INVALID_SPONSOR;
	}
}


function check_valid_modfinfo($pro_title){
	if (empty($pro_title)) {
		return INVALID_TITLE;
	}
	else{
		return VALID_TITLE;
	}
}


function update_project($db_conn, $pro_id, $pro_title, $pro_requr){
	global $errors;
	$table_name = "project";
	// Check if current user is the author registered this project.
	$sql = "UPDATE {$table_name}
			SET title='$pro_title', requirement='$pro_requr'
			WHERE pro_id=$pro_id";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    array_push($errors, "Failed to update proinfo in TABLE $table_name.");
	    return FAILED;
	}
	return SUCCESS_MODF;
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
			$r_val = modify_project($db_conn, 
									$pro_id, $pro_title, $pro_requr, 
									$fname, $lname);	
			if ($r_val == FAILED){
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
		echo "<br> DEBUG OUPUT<br>";
		foreach ($errors as $error) {
			echo $error;
			echo "<br>";
		}
	}
	// Display user notice information.
	switch ($operation) {
		case 'add_pro':
			# code...
			break;
		case 'mod_pro':
			notice_for_modf($r_val);
			break;
		case 'del_pro':
			# code...
			break;
		default:
			# code...
			break;
	}
}
?>