<?php


define("FAILED", -1);
define('SUCCESS', 0);
define("INVALID_PROID",   1);
define("VALID_PROID",     2);
define("VALID_SPONSOR",   3);
define("INVALID_SPONSOR", 4);
define("VALID_TITLE",     5);
define("INVALID_TITLE",   6);
define("NO_RADIO_BUTTON_CHOICE", 7);
define("STUDT_ENROL_PRO", 8);


$fname = "";
$lname = "";
$deadln = "";
$operation = "";


function check_valid_deadline_date($deadln){
	if (date_create($deadln) == FALSE){
		return INVALID_DEADLINE_DATE;
	}
	$local_deadln = strtotime($deadln);
	$local_now = strtotime('now');
	if ($local_deadln <= $local_now) {
		return INVALID_DEADLINE_DATE;
	}
	return SUCCESS;
}


function check_valid_admin($db_conn, $fname, $lname){
	$table_name = 'admin';
	$sql = "SELECT COUNT(*)FROM {$table_name} WHERE fname=$fname AND lname=$lname";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    return FAILED;
	}
	$row = mysqli_fetch_array($results);
	if ($row['COUNT(*)' == 0]) {
		return INVALID_ADMIN;
	}
	return SUCCESS;
}


function update_deadline($db_conn, $deadln){
	$table_name = 'deadline';
	$sql = "DELETE FROM {$table_name}";
	$sql = "INSERT INTO {$table_name} (deadlinedate) VALUES('$deadln')";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    return FAILED;
	}
	return SUCCESS;
}


function set_deadline_date($db_conn, $deadln, $fname, $lname){
	$r_val = check_valid_deadline_date($deadln);
	if ($r_val != SUCCESS) {
		return $r_val;
	}
	$r_Val = check_valid_admin($db_conn, $fname, $lname)
	if ($r_val != SUCCESS) {
		return $r_val;
	}
	$r_Val = update_deadline($db_conn, $deadln);
	if ($r_val != SUCCESS) {
		return $r_val;
	}
	return SUCCESS;
}

if(isset($_POST['bn_sbmt'])){
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$operation = $_POST['operation'];
	$deadln = $_POST['deadln'];
	// Connect database.
	$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
	if (!$db_conn) {
		$r_val = FAILED;
		goto notice_for_user;
	}
	// Implement user's operation.
	$operation = $_POST['operation'];
	if (empty($operation)) {
		$r_val = NO_RADIO_BUTTON_CHOICE;
		goto notice_for_user;
	}
	switch ($operation) {
		case 'deadln':
			$r_val = set_deadline_date($db_conn, $deadln, $fname, $lname);
			break;
		case 'down':
			# code...
			break;
		default:
			# code...
			break;
	}



	// Check if deadline date is valid.
	$r_val = check_valid_deadline_date($deadln);
	if ($r_val != SUCCESS) {
		goto notice_for_user;
	}
	
	
	// Check if input user information is valid.
	
	$r_val = check_valid_admin($db_conn, $fname, $lname);
	if (!$db_conn) {
		$r_val = FAILED;
		goto notice_for_user;
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
		case 'del_pro':
			notice_for_del($r_val);
			break;
		default:
			break;
	}
}
?>