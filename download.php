<?php
// Active output.
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="test.csv"');
$output = fopen('php://output','w') or die("Can't open php://output");
// Input output header.
$field = array('Project_ID', 'Title', 'Sponsor',
			   'Requirement', 'Student', 'CSU_ID',
			   'Major_1', 'Major_2', 'Enrl_Date');
fputcsv($output, $field);
// Input output content.
$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
$sql = "SELECT DISTINCT pro_id FROM preference";
$proid_set = mysqli_query($db_conn, $sql);
while ($proid_row = mysqli_fetch_array($proid_set)){
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
	if (mysqli_num_rows($stdt_set) > 0) {
		while ($stdt_row = mysqli_fetch_array($stdt_set)) {
			$Project_ID  = $stdt_row['Project_ID'];
			$Title      = $stdt_row['Title'];
			$Sponsor    = $stdt_row['spnsfname']." ".$stdt_row['spnslname'];
			$Requirement = $stdt_row['Requirement'];
			$Student    = $stdt_row['stdtfname']." ".$stdt_row['stdtlname'];
			$CSU_ID     = $stdt_row['CSU_ID'];
			$Major1     = $stdt_row['mj1'];
			$Major2     = $stdt_row['mj2'];
			$Enrollment_Date = $stdt_row['Enrl_Date'];
			$lineData = array($Project_ID, $Title, $Sponsor,
								$Requirement, $Student, $CSU_ID, 
								$Major1, $Major2, $Enrollment_Date);
			fputcsv($output, $lineData);
		}			
	}
}
fclose($output) or die("Can't close php://output");
?>