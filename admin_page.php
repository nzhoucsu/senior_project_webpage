<!DOCTYPE html>
<html>
<title>Project List</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css.css">
<link rel="stylesheet" href="test.css">

<head>    

</head>

<body>
	<div class="split_left">
		<div class="relative">
    		<div style="font-size: 22px"><b>Administration</b></div>
    		<p>   </p>
    	</div>
		<div>
			<form class="relative" method="post" action="admin_page.php" autocomplete="off">
				<?php include('admin_error_check.php');?>
				<!-- Input user information -->
				First Name<br>
				<input type="text" name="fname" value="<?php echo $fname; ?>" required><br>
				Last Name<br>
				<input type="text" name="lname" value="<?php echo $lname; ?>" required><br>
				<!-- Input project operation -->
				<h4>Administration Operation</h4>
				<input id="radio_deadln" type="radio" name="operation" value="deadln" 
				       onclick="deadline()">Set due date (mm/dd/yyyy)<br>
				<div id="deadln" style="display: none">
					<input id="deadln_input" type="text" name="deadln"
					value="<?php echo $deadln; ?>"><br>
					<!-- Display deadline date -->
					<?php
						$db_conn = mysqli_connect('localhost', 'root', '0302', 'senior_project_db');
						if (!$db_conn) {
							echo "Failed to connect database.<br>";
						}else{
							$table_name = "deadline";
							$sql = "SELECT COUNT(*)FROM {$table_name}";
							$results = mysqli_query($db_conn, $sql);
							if (!$results) {
							    echo "Failed to connect database.<br>";
							}
							else{
								$row = mysqli_fetch_array($results);
								if ($row['COUNT(*)'] != 0) {
									$sql = "SELECT *FROM {$table_name}";
									$results = mysqli_query($db_conn, $sql);
									if (!$results) {
									    echo "Failed to connect database.<br>";
									}
									else{
										$row = mysqli_fetch_array($results);
										echo "<font size='2'>";
										echo "Current deadline: ";
										echo $row['deadlinedate'];
										echo "<br></font>";
									}
								}
							}
						}
						mysqli_close($db_conn);						
					?>
				</div>
				<input id="radio_down" type="radio" name="operation" value="down" 
				       onclick="down()">Download enrollment<br>				
				<div style="position: relative; top: 10px">
					<input type="submit" name="bn_sbmt" value="Submit">
				</div>
				<script>
					// This paragraph script for web page display when page refresh.
					var choice = sessionStorage.getItem("oprt_session_store");
					if(choice == 'radio_deadln'){
						document.getElementById('radio_deadln').checked = true;
						document.getElementById('deadln').style.display = 'block';
					}
					else if (choice == 'radio_down') {
						document.getElementById('radio_down').checked = true;
						document.getElementById('deadln_input').value   = '';	
						document.getElementById('deadln').style.display = 'none';
					}
				</script> 					
			</form>	
		</div>
		<div class="relative" style="top: 20px">
        	<p><a href="http://localhost/student_page.php">Student Entrance</a></p>
        	<p><a href="http://localhost/instructor_page.php">Instructor Enrutrance</a></p>
        </div>			
	</div> 


	<div class="split_right">
		<div style="width: 98%; height: 600px; overflow: auto;">
			<?php include('import_project_list.php');?>
		</div>
	</div>


	<script>
		function deadline(){
			sessionStorage.setItem("oprt_session_store", "radio_deadln");
			document.getElementById('deadln').style.display = 'block';
		}
		function down(){
			sessionStorage.setItem("oprt_session_store", "radio_down");
			document.getElementById('deadln_input').value   = '';	
			document.getElementById('deadln').style.display = 'none';
		}	
	</script>


</body>
</html>
