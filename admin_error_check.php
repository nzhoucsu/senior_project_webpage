<?php


define("FAILED", -1);
define('SUCCESS', 0);
define("NO_RADIO_BUTTON_CHOICE", 7);
define("INVALID_DEADLINE_DATE",  13);
define("INVALID_ADMIN",          14);


$fname = "";
$lname = "";
$deadln = "";
$operation = "";


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


// Deadline notice for user.
function notice_for_deadline($r_val){
	if ($r_val == FAILED) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Fail to connect database.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == SUCCESS) {
		echo "<font color='blue'>";
		echo "Successfully set a deadline date!<br>";
		echo "<br></font>";
	}
	elseif ($r_val == INVALID_ADMIN) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Wrong admin account.<br>";
		echo "<br></font>";
	}
	elseif ($r_val == INVALID_DEADLINE_DATE) {
		echo "<font color='red'>";
		echo "Failed!<br>";
		echo "Invalid deadline date.<br>";
		echo "<br></font>";
	}
}


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
	$sql = "SELECT COUNT(*)FROM {$table_name} WHERE fname='$fname' AND lname='$lname'";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    return FAILED;
	}
	$row = mysqli_fetch_array($results);
	if ($row['COUNT(*)'] == 0) {
		return INVALID_ADMIN;
	}
	return SUCCESS;
}


function update_deadline($db_conn, $deadln){
	$table_name = 'deadline';
	$sql = "DELETE FROM {$table_name}";
	$results = mysqli_query($db_conn, $sql);
	if (!$results) {
	    return FAILED;
	}
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
	$r_val = check_valid_admin($db_conn, $fname, $lname);
	if ($r_val != SUCCESS) {
		return $r_val;
	}
	$r_val = update_deadline($db_conn, $deadln);
	if ($r_val != SUCCESS) {
		return $r_val;
	}
	return SUCCESS;
}


function download_enrollment($db_conn){
	$sql = "SELECT DISTINCT pro_id FROM preference";
	$proid_set = mysqli_query($db_conn, $sql);
	if (!$proid_set) {
	    return FAILED;
	}
	while ($proid_row = mysqli_fetch_array($proid_set)) {
		$cur_proid = $proid_row['pro_id'];
		$sql = "SELECT project.pro_id AS Project_ID, 
					   project.title AS Title, 
					   project.fname AS spnsfname,
					   project.lname AS spnslname, 
					   project.requirement AS Requirement,
					   student.fname AS stdtfname, 
					   student.lname AS stdtlname,
					   student.csuid AS CSU_ID, 
					   student.major1 AS mj1, 
					   student.major2 AS mj2,
					   preference.enrl_date AS Enrl_Date
				FROM preference, project, student
				WHERE preference.pro_id=$cur_proid AND
					  project.pro_id=preference.pro_id AND
					  student.csuid=preference.csuid";
		$stdt_set = mysqli_query($db_conn, $sql);
		if (!$stdt_set) {
		    return FAILED;
		}
		while ($stdt_row = mysqli_fetch_array($stdt_set)) {
			$Project_ID  = $stdt_row['Project_ID'];
			$Title      = $stdt_row['Title'];
			$Sponsor    = $stdt_row['spnsfname']." ".$stdt_row['spnslname'];
			$Requirement = $stdt_row['Requirement'];
			$Student    = $stdt_row['stdtfname']." ".$stdt_row['stdtlname'];
			$CSU_ID     = $stdt_row['CSU_ID'];
			$Major      = $stdt_row['mj1']."    ".$stdt_row['mj2'];
			$Enrollment_Date = $stdt_row['Enrl_Date'];
		}		
	}
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
			$r_val = download_enrollment($db_conn);
			break;
		default:
			# code...
			break;
	}


	mysqli_close($db_conn);

	// // Display user notice information.
	notice_for_user:
	switch ($operation) {
		case '':
			notice_for_general($r_val);
			break;
		case 'deadln':
			notice_for_deadline($r_val);
			break;
	// 	default:
	// 		break;
	}
}
?>