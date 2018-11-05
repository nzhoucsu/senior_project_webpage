<?php

// Define maximum number for selected projects.
define("MAX_PRO", 5);
define("SYSERROR_NO_CSUID_IN_STUDENT",            -3);
define("SYSERROR_THIS_STUDENT_NOT_IN_PREFERENCE", -2);
define("FAILED", -1);
define("SUCCESS",       0);
define("INVALID_PROID", 1);
define("NO_RADIO_BUTTON_CHOICE", 7);
define("PROID_MORE_THAN_MAX", 9);
define("NONEXISTING_STUDENT", 10);
define("MISMATCHED_STUDENT",  11);
define("CLEAR_ENROLLMENT",    12);

$fname = "";
$lname = "";
$csuid = "";
$proid = "";
$pro_array = "";
$enroll_project = array();


function notice_for_general($r_val){
	if ($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Fail to connect database.<br>";
		echo "<br></font>";
	}	
	if ($r_val == NO_RADIO_BUTTON_CHOICE) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Please select an operation.<br>";
		echo "<br></font>";
	}	
}


function notice_for_view($r_val){
	global $enroll_project;
	if ($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Fail to connect database.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SUCCESS) {
		echo "<font color='blue'>";
		echo "You have enrolled: <br>";
		echo $enroll_project;
		echo "<br><br></font>";
	}
	elseif ($r_val == NONEXISTING_STUDENT) {
		echo "<font color='blue'>";
		echo "You have not enrolled any project.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == MISMATCHED_STUDENT) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Mismatched CSU ID with first/last name.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SYSERROR_THIS_STUDENT_NOT_IN_PREFERENCE) {
		echo "<font color='red'>";
		echo "System error<br>";
		echo "This student should be in TABLE preference, but not.<br>";
		echo "<br></font>";
	}
}


function notice_for_enrl($r_val){
	if ($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Fail to connect database.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SUCCESS) {
		echo "<font color='blue'>";
		echo "Successful enrollment!<br>";
		echo "Please click VIEW to get enrollment result.<br>";
		echo "<br><br></font>";
	}
	elseif ($r_val == MISMATCHED_STUDENT) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Mismatched CSU ID, first/last name.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == INVALID_PROID) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Your input project ID(s) don't exist.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == CLEAR_ENROLLMENT) {
		echo "<font color='blue'>";
		echo "Successful!<br>";
		echo "Your enrollment has been cleared up.<br>";
		echo "<br><br></font>";
	}
	elseif ($r_val == PROID_MORE_THAN_MAX) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "The maximum enrolled projects is five.<br>";
		echo "Please type in project ID.<br>";
		echo "Multiple project IDs, please separated by ','.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SYSERROR_NO_CSUID_IN_STUDENT) {
		echo "<font color='red'>";
		echo "System error<br>";
		echo "This student should be in TABLE student, but not.<br>";
		echo "<br></font>";
	}
}


function view_enrollment($db_conn, $fname, $lname, $csuid){
	$r_val = check_valid_student($db_conn, $fname, $lname, $csuid);
	if ($r_val != SUCCESS){ 
		return $r_val;
	}
	$r_val = acquire_enrollment($db_conn, $csuid);	
	return $r_val;
}


function enrol_project($db_conn, $fname, $lname, $csuid, $proid){
	// Check if input proid is valid.
	$r_val = check_valid_proid($db_conn, $proid);
	if ($r_val != SUCCESS){
		return $r_val;
	}
	// Check if input student info is valid.
	$r_val = check_valid_student($db_conn, $fname, $lname, $csuid);
	if ($r_val == MISMATCHED_STUDENT){	
		return $r_val;
	}
	elseif ($r_val == FAILED){
		return $r_val;
	}
	elseif ($r_val == NONEXISTING_STUDENT) {
		$r_val = add_student($db_conn, $fname, $lname, $csuid);
		if ($r_val == FAILED){
			return $r_val;
		}
	}
	// Enroll projects.
	$r_val = update_enrollment($db_conn, $csuid, $proid);
	return $r_val;
}


function check_valid_student($db_conn, $fname, $lname, $csuid){
	$table_name = "student";
	// Check if current csuid has enrolled.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		return FAILED;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) { // Current csuid doesn't enroll any project.
		return NONEXISTING_STUDENT; 
	}
	else{ // Current csuid enrolls projects.
		$sql = "SELECT fname, lname FROM {$table_name} WHERE csuid=$csuid";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			return FAILED;
		}
		$row = mysqli_fetch_array($results);
		$strcmp_fname = strcmp(strtolower($row['fname']), strtolower($fname));
        $strcmp_lname = strcmp(strtolower($row['lname']), strtolower($lname));
		if ($strcmp_fname!=0 || $strcmp_lname!=0) { 
			// Current input student info mismatches with the info stored in system 
			// corresponding to current csuid.
			return MISMATCHED_STUDENT;	
		}
	}		
	return SUCCESS;	
}


function acquire_enrollment($db_conn, $csuid){
	global $enroll_project;
	$table_name = "preference";
	// Check if current csuid has corresponding projects in TABLE.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		return FAILED;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] == 0) {
		// There is no enrollment information for this csuid in system.
		return SYSERROR_THIS_STUDENT_NOT_IN_PREFERENCE;
	}
	else{ // There is enrollment information for this csuid in system.
		$sql = "SELECT pro_id FROM {$table_name} WHERE csuid=$csuid";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			return FAILED;
		}
		while($row = mysqli_fetch_array($results)){
			array_push($enroll_project, $row['pro_id']);
		}
		$enroll_project = implode(", ", $enroll_project);
		rtrim($enroll_project,", ");
	}			
	return SUCCESS;	
}


function add_student($db_conn, $fname, $lname, $csuid){
	$table_name = "student";
	$sql = "INSERT INTO {$table_name} (fname, lname, csuid)
			VALUES ('$fname', '$lname', '$csuid')";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		return FAILED;
	}	
	return SUCCESS;
}


function del_project($db_conn, $csuid){
	$table_name = "preference";
	// Check if there are enrolled projects for this csuid in system.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid=$csuid";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		return FAILED;
	}
	$row = mysqli_fetch_array($results);	
	if ($row['COUNT(*)'] != 0) {
		$sql = "DELETE FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			return FAILED;
		}
	}
	return SUCCESS;
}


function update_enrollment($db_conn, $csuid, $proid){
	global $pro_array;
	$table_name = "preference";
	// Remove enrollment in TABLE preference.
	$r_val = del_project($db_conn, $csuid);
	if ($r_val == FAILED){
		return $r_val;
	}
	// Check if clear up history.
	if (empty($proid)) {		
		// Clear up student in TABLE student.
		$r_val = del_student($db_conn, $csuid);
		if ($r_val != SUCCESS) {
			return $r_val;
		}
		return CLEAR_ENROLLMENT;
	}
	foreach ($pro_array as $pro_item) {	
		$sql = "INSERT INTO {$table_name} (pro_id, csuid) 
		VALUES('$pro_item', '$csuid')";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
		    return FAILED;
		}
	}
	return SUCCESS;
}


function del_student($db_conn, $csuid){
	$table_name = "student";
	// Check if current csuid is in TABLE student.
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE csuid={$csuid}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
		return FAILED;
	}
	$row = mysqli_fetch_array($results);
	if ($row['COUNT(*)'] == 0) {
		// There is no this csuid in system.
		return SYSERROR_NO_CSUID_IN_STUDENT;
	}
	else{	// There is student information for this csuid in system.
		$sql = "DELETE FROM {$table_name} WHERE csuid={$csuid}";
		$results = mysqli_query($db_conn, $sql);
		if (!$results) {
			return FAILED;
		}
	}
	return SUCCESS;
}


function check_valid_proid($db_conn, $proid){
	global $pro_array;
	$table_name = "project";	
	$pro_array = explode(",", $proid);
	// Check if proid is empty.
	if (empty($proid)) {
		return SUCCESS;
	}
	// Check number of input project id(s) is more than maximum.
	if (count($pro_array)>MAX_PRO){
		return PROID_MORE_THAN_MAX;
	}	
	// Check if input project id(s) is valid.
	$a = array();
	foreach ($pro_array as $pro_item) {
		// Check if input proid are integer(s).
		if ((string)intval($pro_item) != $pro_item) {
			return INVALID_PROID;
		}
		else{	
			// Check if input project id is in TABLE project.			
			$sql = "SELECT COUNT(*) FROM {$table_name} WHERE pro_id={intval($pro_item)}";
			$results = mysqli_query($db_conn, $sql);
			if (!$results) {
				return FAILED;
			}
			$row = mysqli_fetch_array($results);
			if($row['COUNT(*)'] == 0){ // Current project id is not in system.
				return INVALID_PROID;		
			}
			else{	// Current project id is in system.
				array_push($a, intval($pro_item));
			}			
		}
	}
	$a = array_unique($a);
	$pro_array = $a;
	// Return success.
	return SUCCESS;
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
		$r_val = FAILED;
		goto notice_for_user;
	}
	// Implement user's operation.
	if (empty($operation)) {
		$r_val = NO_RADIO_BUTTON_CHOICE;
		goto notice_for_user;
	}

	switch ($operation) {
		case 'view':	
			$r_val = view_enrollment($db_conn, $fname, $lname, $csuid);
			break;		
		case 'enrl':
			$r_val = enrol_project($db_conn, $fname, $lname, $csuid, $proid);
			break;
		default:
			break;
	}
	//Disconnect database;
	mysqli_close($db_conn);	
	// Debug output.
	notice_for_user:
	// Notice for user.
	switch ($operation) {
		case '':
			notice_for_general($r_val);
			break;
		case 'view':
			notice_for_view($r_val);
			break;
		case 'enrl':
			notice_for_enrl($r_val);
			break;
		default:
			# code...
			break;
	}
}
?>

