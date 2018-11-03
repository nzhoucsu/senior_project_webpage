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
define("NO_RADIO_BUTTON_CHOICE", 7);


// Variables are to store user id info.
$fname = "";
$lname = "";
// A variable is to store add_project, modify_project or delete_project.
$pro_id = "";
$pro_title = "";
$pro_requr = "";
$pro_id_array = "";
$operation = "";


// Display general error notice for user.
function notice_for_general($r_val){
	if($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Fail to connect database.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == NO_RADIO_BUTTON_CHOICE) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Please select an operation.<br>";
		echo "<br></font>";
	}
}


// Display project addition notice for user.
function notice_for_add($r_val){
	if ($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Fail to connect database.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == INVALID_TITLE) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Added project title is empty.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SUCCESS) {
		echo "<font color='blue'>";
		echo "Successfully add a project!<br>";
		echo "<br></font>";
	}
}


// Display project modification notice to user.
function notice_for_modf($r_val){
	if ($r_val == INVALID_PROID) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Modified project id is invailid.<br>";
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
		echo "Modified project title is empty.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Fail to connect database.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SUCCESS) {
		echo "<font color='blue'>";
		echo "Successfully modify a project!<br>";
		echo "<br></font>";
	}
}


function add_project($db_conn, $pro_title, $pro_requr, $fname, $lname){
	global $errors;
	$table_name = "project";
	// Check new project title.
	if(empty($pro_title)){
		return INVALID_TITLE;
	}
	// Insert data.		
	$sql = "INSERT INTO {$table_name} (title, requirement, fname, lname) 
	        VALUES('$pro_title', '$pro_requr', '$fname', '$lname')";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    return FAILED;
	}
	// Return success.
	return SUCCESS;
}


function modify_project($db_conn, $pro_id, $pro_title, $pro_requr, $fname, $lname){
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
	    return FAILED;
	}
	return SUCCESS;
}


if(isset($_POST['bn_sbmt'])){
	// Connect database.
	$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
	if (!$db_conn) {
		$r_val = FAILED;
		goto notice_for_user;
	}
	
	// Acquire user id info input.
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	
	// Implement user's operation.
	$operation = $_POST['operation'];
	if (empty($operation)) {
		$r_val = NO_RADIO_BUTTON_CHOICE;
		goto notice_for_user;
	}
	switch ($operation) {
		case 'add_pro':
			$pro_title = $_POST['pro_title'];
			$pro_requr = $_POST['pro_requr'];
			// Insert new project information into database.
			$r_val = add_project($db_conn, $pro_title, $pro_requr, $fname, $lname);
			break;
		case 'mod_pro':
			$pro_id = $_POST['pro_id'];
			$pro_title = $_POST['pro_title'];
			$pro_requr = $_POST['pro_requr'];
			// Modify an existing project.
			$r_val = modify_project($db_conn, $pro_id, $pro_title, $pro_requr, $fname, $lname);	
			break;
		// case 'del_pro':
		// 	$pro_id = $_POST['pro_id'];
		// 	// Delete existing project(s).
		// 	$r_val = delete_project($db_conn, $fname, $lname, $pro_id);	
		// 	if ($r_val == -1){
		// 		mysqli_close($db_conn);
		// 		goto error_report;
		// 	}
		// 		break;
		default:
			break;
	}

	mysqli_close($db_conn);

	// Display user notice information.
	notice_for_user:
	switch ($operation) {
		case '':
			notice_for_general($r_val);
			break;
		case 'add_pro':
			notice_for_add($r_val);
			break;
		case 'mod_pro':
			notice_for_modf($r_val);
			break;
		// case 'del_pro':
		// 	# code...
		// 	break;
		default:
			# code...
			break;
	}
}
?>