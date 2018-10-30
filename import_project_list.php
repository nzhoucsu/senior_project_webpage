<?php

$errors = array();

// Connect database.	
$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
if (!$db_conn) {
	array_push($errors, "Failed to connect database!");
	goto error_report;
}
// Acquire projects in database.
$sql = "SELECT pro_id, title, requirement, fname, lname FROM project";
$r_val = mysqli_query($db_conn, $sql);
if(!$r_val){
	mysqli_close($db_conn);
	array_push($errors, "Failed to acquire project list!");
	goto error_report;
}
// Create a table in webpage and display project list.			
echo "<table>
			<tr>
			<th>Project ID</th>
			<th>Project Title</th>
			<th>Supervisor</th>
			<th>Comment</th>
			</tr>";
if(count($r_val) == 0){
	echo "</table>";
	echo "0 result";
}
else{
	while($row = mysqli_fetch_array($r_val)){
		$sponsor = $row["fname"]." ".$row["lname"];
		echo "<tr><td>".$row["pro_id"]."</td><td>".$row["title"]."</td><td>"
			 .$sponsor."</td><td>".$row["requirement"]."</td>";
	}
	echo "</table>";
}
// Close database.
mysqli_close($db_conn);
// Print output.
error_report:
if(count($errors)>0){
	foreach ($errors as $error) {
		echo $error;
		echo "<br>";
	}
}
?>