<?php

// Define maximum number for selected projects.
define("MAX_PRO", 5);
define("FAILED", -1);
define("SUCCESSFUL",       0);
define("INVALID_CSUID",    1);
define("EMPTY_ENROLLMENT", 2);
define("INVALID_PROJECT_ID",    3);
define("SUCCESSFUL_ENROLLMENT", 4);
define("NONEXISTING_STUDENT",   5);
define("NONEXISTING_STUDENT_IN_VIEW", 6);
define("SUCCESSFUL_SEARCH_IN_VIEW",   7);

$fname = "";
$lname = "";
$csuid = "";
$proid = "";
$pro_array = "";
$errors = array();
$enroll_project = array();


function notice_for_invalid_csuid(){
	echo "<br>";
	echo "Failed!<br>";
	echo "Mismatched first/last name with CSU ID.<br>";
	echo "<br>";
}


function notice_for_empty_enrollment(){
	echo "<br>";
	echo "Successful!<br>";
	echo "No enrollment is recorded in system.<br>";
	echo "<br>";
}


function notice_for_invalid_project_id(){
	echo "<br>";
	echo "Failed!<br>";
	echo "Invalid project ID(s).<br>";
	echo "No enrollment is recorded in system.<br>";
	echo "<br>";
}


function notice_for_successful_enrollmet(){
	echo "<br>";
	echo "Successful enrollment!<br>";
	echo "Please click 'View Enrollment' for details.<br>";
	echo "<br>";
}


function notice_for_nonexisting_student_in_VIEW(){
	echo "<br>";
	echo "You haven't enrolled any project.<br>";
	echo "<br>";
}


function notice_for_enrollment_in_VIEW(){
	global $enroll_project;
		echo "Enrolled project ID: ";
		echo $enroll_project;
		echo "<br><br>";
}


function check_valid_student($db_conn, $fname, $lname, $csuid){
	global $errors;
	$table_name = "student";
	// Check if current student has enrolled.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return FAILED;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) { // Current student doesn't enroll any project.
		return NONEXISTING_STUDENT; 	// Return 0 to mean that 
					// current student doesn't enroll any project.
	}
	else{ // Current student enrolls projects.
		$sql = "SELECT fname, lname FROM {$table_name} WHERE csuid=$csuid";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access TABLE {$table_name}.");
			return FAILED;
		}
		$row = mysqli_fetch_array($results);
		$strcmp_fname = strcmp(strtolower($row['fname']), strtolower($fname));
        $strcmp_lname = strcmp(strtolower($row['lname']), strtolower($lname));
		if ($strcmp_fname!=0 || $strcmp_lname!=0) {
			array_push($errors, "Incorrect first/last name or CSU ID.");
			return INVALID_CSUID;	// Return -1 to mean that 
						// input student information dismatchs the one in database.
		}
	}		
	return SUCCESSFUL;	// Return 1 to mean that current student enrolls project(s).
}


function acquire_enrollment($db_conn, $csuid){
	global $errors, $enroll_project;
	$table_name = "preference";
	// Check if current csuid has corresponding projects in TABLE.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return FAILED;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) {// There is no enrollment information
								// for this csuid in system.
		array_push($errors, "System error! No enrollment information.");
		return NONEXISTING_STUDENT;
	}
	else{ // There is enrollment information for this csuid in system.
		$sql = "SELECT pro_id FROM {$table_name} WHERE csuid=$csuid";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to access TABLE {$table_name}.");
			return FAILED;
		}
		while($row = mysqli_fetch_array($results)){
			array_push($enroll_project, $row["pro_id"]);
		}
		$enroll_project = implode(", ", $enroll_project);
		rtrim($enroll_project,", ");
	}			
	return SUCCESSFUL_SEARCH_IN_VIEW;	
}


function add_student($db_conn, $fname, $lname, $csuid){
	global $errors;
	$table_name = "student";
	$sql = "INSERT INTO {$table_name} (fname, lname, csuid)
			VALUES ('$fname', '$lname', '$csuid')";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "add_student Failed to insert student info to TABLE {$table_name}.");
		return FAILED;
	}	
	return SUCCESSFUL;
}


function del_project($db_conn, $csuid){
	global $errors;
	$table_name = "preference";
	// Check if there are enrolled projects for this csuid in system.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "Failed to access TABLE {$table_name}.");
		return FAILED;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) {// There is no enrollment information
								// for this csuid in system.
		array_push($errors, "System error! No enrollment information.");
		return FAILED;
	}
	else{ // There is enrollment information for this csuid in system.
		$sql = "DELETE FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to delete projects in TABLE {$table_name}.");
			return FAILED;
		}
	}
	return SUCCESSFUL;
}


function update_enrollment($db_conn, $csuid, $proid){
	global $pro_array, $errors;
	$table_name = "preference";
	if (empty($proid)) {	// If current student (csuid)
							// doesn't input any project id.
		del_student($db_conn, $csuid);
		return EMPTY_ENROLLMENT;
	}
	$r_val = check_valid_pro($db_conn, $proid);
	if ($r_val == INVALID_PROJECT_ID) {		
		del_student($db_conn, $csuid);
		return INVALID_PROJECT_ID;
	}
	insert_preference($db_conn, $csuid);
	return SUCCESSFUL_ENROLLMENT;
}


function del_student($db_conn, $csuid){
	global $errors;
	$table_name = "student";
	// Check if current csuid is in TABLE student.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid={$csuid}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		array_push($errors, "del_student Failed to access TABLE {$table_name}.");
		return FAILED;
	}
	$row = mysqli_fetch_array($results);
	if ($row['COUNT(*)'] == 0) {// There is no student information
								// for this csuid in system.
		array_push($errors, "del_student System error! No current CSU ID in system.");
		return FAILED;
	}
	else{	// There is student information for this csuid in system.
		$sql = "DELETE FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			array_push($errors, "Failed to delete student in TABLE {$table_name}.");
			return FAILED;
		}
	}
	return SUCCESSFUL;
}


function check_valid_pro($db_conn, $proid){
	global $pro_array, $errors;
	$table_name = "project";	
	$pro_array = explode(",", $proid);
	// Check number of input project id(s) is more than maximum.
	if (count($pro_array)>MAX_PRO){
		array_push($errors, "Maximum number of selected projects is ".MAX_PRO.".");
		return INVALID_PROJECT_ID;
	}	
	// Check if input project id(s) is valid.
	$a = array();
	foreach ($pro_array as $pro_item) {
		if(intval($pro_item) == 0){	// Check if input project id is an integer.
			array_push($errors, "Project ID must be integer(s) separated by comma ','.");
			return INVALID_PROJECT_ID;
		}
		else{	// Check if input project id is in TABLE project.			
			$sql = "SELECT COUNT(*) FROM {$table_name} WHERE pro_id={intval($pro_item)}";
			$results = mysqli_query($db_conn, $sql);
			if (!$results) {
				array_push($errors, "Failed to access TABLE {$table_name}.");
				return FAILED;
			}
			$row = mysqli_fetch_array($results);
			if($row['COUNT(*)'] == 0){ // Current project id is not in system.
				array_push($errors, 
					"Failure! Project ID {$pro_item} is not in project list.");
				return INVALID_PROJECT_ID;		
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
	return SUCCESSFUL_ENROLLMENT;
}


if(isset($_POST['bn_sbmt'])){
	$errors = array();
	// Get user input.
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$csuid = $_POST['csuid'];
	$proid = $_POST['proid'];
	$operation = $_POST['operation'];
	//Connect database.	
	$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
	if (!$db_conn) {
		array_push($errors, "Failed to connect database!");
		goto error_report;
	}

	switch ($operation) {
		case 'view':			
			$r_val = check_valid_student($db_conn, $fname, $lname, $csuid);
			if ($r_val == INVALID_CSUID){ 	// First/last name or cus id don't 
								// match each other.
				mysqli_close($db_conn);
				goto error_report;
			}
			elseif ($r_val == NONEXISTING_STUDENT) {	// No current student information
									// (csu id) in system.
				$r_val = NONEXISTING_STUDENT_IN_VIEW;
				mysqli_close($db_conn);
				goto error_report;
			}
			$r_val = acquire_enrollment($db_conn, $csuid);	
			if ($r_val == FAILED){
				mysqli_close($db_conn);
				goto error_report;
			}
			break;
		case 'enrl':
			$r_val = check_valid_student($db_conn, $fname, $lname, $csuid);
			if ($r_val == INVALID_CSUID){	// First/last name or cus id don't 
								// match each other.
				mysqli_close($db_conn);
				goto error_report;
			}
			elseif ($r_val == NONEXISTING_STUDENT) {	// No current student information
									// (csu id) in system.
				$r_val = add_student($db_conn, $fname, $lname, $csuid);
				if ($r_val == FAILED){
					mysqli_close($db_conn);
					goto error_report;
				}
			}
			elseif ($r_val == SUCCESSFUL) {	// Current student information
									// (csu id) in system.
				$r_val = del_project($db_conn, $csuid);
				if ($r_val == FAILED){
					mysqli_close($db_conn);
					goto error_report;
				}
			}
			$r_val = update_enrollment($db_conn, $csuid, $proid);
			break;
		default:
			break;
	}
	//Disconnect database;
	mysqli_close($db_conn);	
	// Debug output.
	error_report:	
	if(count($errors)>0){
		echo "<br>DEBUG OUTPUT:<br>";
		foreach ($errors as $error) {
			echo $error;
			echo "<br>";
		}
	}
	echo "<br>";
	// Notice for user.
	if ($r_val == INVALID_CSUID) {
		notice_for_invalid_csuid();
	}
	elseif ($r_val == EMPTY_ENROLLMENT) {
		notice_for_empty_enrollment();
	}
	elseif ($r_val == INVALID_PROJECT_ID) {
		notice_for_invalid_project_id();
	}
	elseif ($r_val == SUCCESSFUL_ENROLLMENT) {
		notice_for_successful_enrollmet();
	}
	elseif ($r_val == NONEXISTING_STUDENT_IN_VIEW) {
		notice_for_nonexisting_student_in_VIEW();
	}
	elseif ($r_val == SUCCESSFUL_SEARCH_IN_VIEW) {		
		notice_for_enrollment_in_VIEW();
	}
}
?>

