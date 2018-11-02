<?php

// Define maximum number for selected projects.
define("MAX_PRO", 5);

$fname = "";
$lname = "";
$csuid = "";
$proid = "";
$pro_array = "";
$errors = array();


function check_valid_student($db_conn, $fname, $lname, $csuid){
	global $errors;
	$table_name = "student";
	// Check if current student has enrolled.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return -1;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) { // Current student doesn't enroll any project.
		return 0; 	// Return 0 to mean that 
					// current student doesn't enroll any project.
	}
	else{ // Current student enrolls projects.
		$sql = "SELECT fname, lname FROM {$table_name} WHERE csuid=$csuid";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access TABLE {$table_name}.");
			return -1;
		}
		$row = mysqli_fetch_array($results);
		$strcmp_fname = strcmp(strtolower($row['fname']), strtolower($fname));
        $strcmp_lname = strcmp(strtolower($row['lname']), strtolower($lname));
		if ($strcmp_fname!=0 || $strcmp_lname!=0) {
			array_push($errors, "Incorrect first/last name or CSU ID.");
			return -1;	// Return -1 to mean that 
						// input student information dismatchs the one in database.
		}
	}		
	return 1;	// Return 1 to mean that current student enrolls project(s).
}


function acquire_enrollment($db_conn, $csuid){
	global $errors;
	$table_name = "preference";
	$s = array();
	// Check if current csuid has corresponding projects in TABLE.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return -1;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) {// There is no enrollment information
								// for this csuid in system.
		array_push($errors, "System error! No enrollment information.");
		return -1;
	}
	else{ // There is enrollment information for this csuid in system.
		$sql = "SELECT pro_id FROM {$table_name} WHERE csuid=$csuid";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access TABLE {$table_name}.");
			return -1;
		}
		while($row = mysqli_fetch_array($results)){
			array_push($s, $row["pro_id"]);
		}
		$s = implode(", ", $s);
		rtrim($s,", ");
		echo "Enrolled project ID: ";
		echo $s;
		echo "<br>";
	}			
	return 0;	
}


function add_student($db_conn, $fname, $lname, $csuid){
	global $errors;
	$table_name = "student";
	$sql = "INSERT INTO {$table_name} (fname, lname, csuid)
			VALUES ('$fname', '$lname', '$csuid')";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "add_student Failed to insert student info to TABLE {$table_name}.");
		return -1;
	}	
	return 0;
}


function del_project($db_conn, $csuid){
	global $errors;
	$table_name = "preference";
	// Check if there are enrolled projects for this csuid in system.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return -1;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) {// There is no enrollment information
								// for this csuid in system.
		array_push($errors, "System error! No enrollment information.");
		return -1;
	}
	else{ // There is enrollment information for this csuid in system.
		$sql = "DELETE FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to delete projects in TABLE {$table_name}.");
			return -1;
		}
	}
	return 0;
}


function update_enrollment($db_conn, $csuid, $proid){
	global $pro_array, $errors;
	$table_name = "preference";
	if (empty($proid)) {	// If current student (csuid)
							// doesn't input any project id.
		del_student($db_conn, $csuid);
		echo "Clear up enrollment.<br>";
		return 0;
	}
	$r_val = check_valid_pro($db_conn, $proid);
	if ($r_val == -1) {		
		del_student($db_conn, $csuid);
		return -1;
	}
	insert_preference($db_conn, $csuid);
	return 0;
}


function del_student($db_conn, $csuid){
	global $errors;
	$table_name = "student";
	// Check if current csuid is in TABLE student.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid={$csuid}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "del_student Failed to access TABLE {$table_name}.");
		return -1;
	}
	$row = mysqli_fetch_array($results);
	if ($row['COUNT(*)'] == 0) {// There is no student information
								// for this csuid in system.
		array_push($errors, "del_student System error! No current CSU ID in system.");
		return -1;
	}
	else{	// There is student information for this csuid in system.
		$sql = "DELETE FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to delete student in TABLE {$table_name}.");
			return -1;
		}
	}
	return 0;
}


function check_valid_pro($db_conn, $proid){
	global $pro_array, $errors;
	$table_name = "project";	
	$pro_array = explode(",", $proid);
	// Check number of input project id(s) is more than maximum.
	if (count($pro_array)>MAX_PRO){
		array_push($errors, "Maximum number of selected projects is ".MAX_PRO.".");
		return -1;
	}	
	// Check if input project id(s) is valid.
	$a = array();
	foreach ($pro_array as $pro_item) {
		if(intval($pro_item) == 0){	// Check if input project id is an integer.
			array_push($errors, "Project ID must be integer(s) separated by comma ','.");
			return -1;
		}
		else{	// Check if input project id is in TABLE project.			
			$sql = "SELECT COUNT(*) FROM {$table_name} WHERE pro_id={intval($pro_item)}";
			$results = mysqli_query($db_conn, $sql);
			if (!$results) {
				array_push($errors, "Failed to access TABLE {$table_name}.");
				return -1;
			}
			$row = mysqli_fetch_array($results);
			if($row['COUNT(*)'] == 0){ // Current project id is not in system.
				array_push($errors, 
					"Failure! Project ID {$pro_item} is not in project list.");
				return -1;		
			}
			else{	// Current project id is in system.
				array_push($a, intval($pro_item));
			}			
		}
	}
	$a = array_unique($a);
	$pro_array = $a;
	// Return success.
	return 0;
}


function insert_preference($db_conn, $csuid){
	global $pro_array, $errors;
	$table_name = "preference";
	// Insert preference data.
	foreach ($pro_array as $pro_item) {	
		$sql = "INSERT INTO {$table_name} (pro_id, csuid) 
		VALUES('$pro_item', '$csuid')";
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
	$operation = $_POST['operation'];
	// Connect database.	
	$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
	if (!$db_conn) {
		array_push($errors, "Failed to connect database!");
		goto error_report;
	}

	switch ($operation) {
		case 'view':			
			$r_val = check_valid_student($db_conn, $fname, $lname, $csuid);
			if ($r_val == -1){ 	// First/last name or cus id don't 
								// match each other.
				mysqli_close($db_conn);
				goto error_report;
			}
			elseif ($r_val == 0) {	// No current student information
									// (csu id) in system.
				echo "You haven't enrolled any project.<br>";
				break;
			}
			$r_val = acquire_enrollment($db_conn, $csuid);	
			if ($r_val == -1){
				mysqli_close($db_conn);
				goto error_report;
			}
			break;
		case 'enrl':
			$r_val = check_valid_student($db_conn, $fname, $lname, $csuid);
			if ($r_val == -1){	// First/last name or cus id don't 
								// match each other.
				mysqli_close($db_conn);
				goto error_report;
			}
			elseif ($r_val == 0) {	// No current student information
									// (csu id) in system.
				$r_val = add_student($db_conn, $fname, $lname, $csuid);
				if ($r_val == -1){
					mysqli_close($db_conn);
					goto error_report;
				}
			}
			elseif ($r_val == 1) {	// Current student information
									// (csu id) in system.
				$r_val = del_project($db_conn, $csuid);
				if ($r_val == -1){
					mysqli_close($db_conn);
					goto error_report;
				}
			}
			$r_val = update_enrollment($db_conn, $csuid, $proid);
			if ($r_val == -1){
				mysqli_close($db_conn);
				goto error_report;
			}
			break;
		default:
			break;
	}
	// Disconnect database;
	mysqli_close($db_conn);
	error_report:
	if(count($errors)>0){
		foreach ($errors as $error) {
			echo $error;
			echo "<br>";
		}
	}
	echo "<br>";
}
?>

