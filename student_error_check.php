<?php

// Define maximum number for selected projects.
define("MAX_PRO", 5);

$fname = "";
$lname = "";
$csuid = "";
$proid = "";
$pro_array = "";
$errors = array();

function check_input($fname, $lname, $csuid, $proid){
	global $pro_array, $errors;
	// Check First Name.
	if(empty($fname)){
		array_push($errors, "First Nanme is empty.");
		return -1;
	}
	// Check Last Name.
	if(empty($lname)){
		array_push($errors, "Last Nanme is empty.");
		return -1;
	}
	// Check CSU ID.
	if(strlen($csuid) != 7){
		array_push($errors, "CSU ID should be a 7-digit number.");
		return -1;
	}
	// Check Project #.
	if(empty($proid)){
		array_push($errors, "Project # is empty.");
		return -1;
	}
	else{
		$pro_array = explode(",", $proid);
		if (count($pro_array)>MAX_PRO){
			array_push($errors, "Maximum number of selected projects is ".MAX_PRO.".");
			return -1;
		}
		else{
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
			$pro_array = array();
			foreach ($a as $item) {
				array_push($pro_array, (string)$item);
			}
		}			
	}
	// Return success.
	return 0;
}


function check_student_in_dbtable($db_conn, $fname, $lname, $csuid){
	global $errors;
	$table_name = "student";
	// Check if input csuid is valid.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid={$csuid}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return -1;
	}
	$row = mysqli_fetch_array($results);
	if($row['COUNT(*)'] == 0){
		array_push($errors, "CSU ID {$csuid} is not in this system!");
		return -1;
	}
	// Check if input name is valid.
	$sql = "SELECT * FROM student WHERE csuid={$csuid}";
	$results = mysqli_query($db_conn, $sql);
	$row = mysqli_fetch_array($results);
	if(strcasecmp($row['fname'], $fname) != 0 OR strcasecmp($row['lname'], $lname) != 0){
		array_push($errors, "Input name is not for CSU ID {$csuid} in system!");
		return -1;
	}
	// Return success.
	return 0;
}


function check_student_project($db_conn, $csuid){
	global $errors;
	$table_name = "preference";
	// Check if input csuid is in table.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid={$csuid}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return -1;
	}
	$row = mysqli_fetch_array($results);
	if($row['COUNT(*)'] == 0){
		return 0;		
	}
	else{
		$sql = "SELECT * FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access TABLE {$table_name}.");
			return -1;
		}
		$s = array();
		while($row = mysqli_fetch_array($results)){
			array_push($s, $row["pro_id"]);
		}
		$s = implode(" ", $s);
		array_push($errors, "Project {$s} have been selected. Please click UPDATE button to submit.");
		return -1;
	}
}


function check_student_project_and_delete($db_conn, $csuid){
	global $errors;
	$table_name = "preference";
	// Check if input csuid is in table.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid={$csuid}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "333 Failed to access TABLE {$table_name}.");
		return -1;
	}
	$row = mysqli_fetch_array($results);
	if($row['COUNT(*)'] == 0){
		return 0;		
	}
	else{
		// Delete csuid records in table.
		$sql = "DELETE FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access TABLE {$table_name}.");
			return -1;
		}
		else{
			return 0;
		}		
	}
}

 
function get_project_max_num($db_conn){
	global $errors;
	$table_name = "project";
	// Search in database.
	$sql = "SELECT COUNT(*) FROM {$table_name}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    array_push($errors, "Failed to access TABLE {$table_name}.");
		return -1;
	}
	// Get maximum project number.
	$row = mysqli_fetch_array($results);
	// Return success.
	return $row['COUNT(*)'];
}


function insert_data($db_conn, $pro_array, $max_pro_num, $csuid){
	global $errors;
	$table_name = "preference";
	// Insert data.
	foreach ($pro_array as $pro_item) {		
		$sql = "INSERT INTO preference (pro_id, csuid) VALUES($pro_item, $csuid)";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
		    array_push($errors, "Failed to insert project {$pro_item}");
		}
	}
	// Return success.
	return 0;
}


if(isset($_POST['bn_sbmt'])){
	$errors = array();
	// Get user input.
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$csuid = $_POST['csuid'];
	$proid = $_POST['proid'];
	// Check user input.
	$r_val = check_input($fname, $lname, $csuid, $proid);	
	if ($r_val == -1){
		goto error_report;
	}
	if(count($errors)==0){	
		// Connect database.	
		$db_conn = mysqli_connect('localhost', 'root', '', 'senior_project_db');
		if (!$db_conn) {
			array_push($errors, "Failed to connect database!");
			goto error_report;
		}
		// Check if input student is in system or not.
		$r_val = check_student_in_dbtable($db_conn, $fname, $lname, $csuid);
		if ($r_val == -1){
			goto error_report;
		}
		// Check is input student has selected projects.
		$r_val = check_student_project($db_conn, $csuid);
		if ($r_val == -1){
			goto error_report;
		}
		// Get maximum project number in database.
		$r_val = get_project_max_num($db_conn);
		if ($r_val == -1){
			goto error_report;
		}
		elseif ($r_val == 0){
			array_push($errors, "There is no project in system! Please contact your advisor.");
			goto error_report;
		}
		// Insert data into database.
		$r_val = insert_data($db_conn, $pro_array, $r_val, $csuid);
		if ($r_val == -1){
			goto error_report;
		}
		mysqli_close($db_conn);
	}
	error_report:
	if(count($errors)>0){
		foreach ($errors as $error) {
			echo $error;
			echo "<br>";
		}
	}
	else{
		echo "Successful enrolment!";
	}
}

if(isset($_POST['bn_update'])){
	$errors = array();
	// Get user input.
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$csuid = $_POST['csuid'];
	$proid = $_POST['proid'];
	// Check user input.
	$r_val = check_input($fname, $lname, $csuid, $proid);
	if ($r_val == -1){
		goto update_error_report;
	}
	if(count($errors)==0){	
		// Connect database.	
		$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
		if (!$db_conn) {
			array_push($errors, "Failed to connect database!");
			goto update_error_report;
		}
		// Check if input student is in system or not.
		$r_val = check_student_in_dbtable($db_conn, $fname, $lname, $csuid);
		if ($r_val == -1){
			goto update_error_report;
		}
		// Check is input student has selected projects.
		$r_val = check_student_project_and_delete($db_conn, $csuid);
		if ($r_val == -1){
			goto update_error_report;
		}
		// Get maximum project number in database.
		$r_val = get_project_max_num($db_conn);
		if ($r_val == -1){
			goto error_report;
		}
		elseif ($r_val == 0){
			array_push($errors, "There is no project in system! Please contact your advisor.");
			goto error_report;
		}
		// Insert data into database.
		$r_val = insert_data($db_conn, $pro_array, $r_val, $csuid);
		if ($r_val == -1){
			goto error_report;
		}
		mysqli_close($db_conn);
	}
	update_error_report:
	if(count($errors)>0){
		foreach ($errors as $error) {
			echo $error;
			echo "<br>";
		}
	}
	else{
		echo "Successful update!";
	}
}
?>