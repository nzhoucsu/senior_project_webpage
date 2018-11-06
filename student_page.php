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
    		<div style="font-size: 22px"><b>Student Enrollment</b></div>
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
								echo "Deadline: ";
								echo $row['deadlinedate'];
								echo "<br><br>";
							}
						}
					}
				}
				mysqli_close($db_conn);						
			?>
    	</div>
        <div>
            <form class="relative" method="post" action="student_page.php" autocomplete="off">
                <?php include('student_error_check.php');?>
                <!-- Input user information -->
                First Name<br>
                <input type="text" name="fname" value="<?php echo $fname; ?>" required><br>
                Last Name<br>
                <input type="text" name="lname" value="<?php echo $lname; ?>" required><br>
                CSU ID<br>
                <input type="text" name="csuid" value="<?php echo $csuid; ?>" required><br>
                <!-- <h4>Enrollment Operation</h4> -->
                <p>   </p>
                <div style="font-size: 18px"><b>Enrollment Operation</b></div>
                <input id="radio_view" type="radio" name="operation" value="view"
				       onclick="view()">View Enrollment<br>
				<input id="radio_enrl" type="radio" name="operation" value="enrl" 
				       onclick="enrl()">Enroll Project<br><br>
				<div id="project_id" style="display: none">
					Project ID<br>  
					<input id="pro_id_input" type="text" name="proid"
					value="<?php echo $proid; ?>"><br>
					<div style="font-size: 12px; position: relative; top: 5px">
						Each student can only select five projects at most.<br>
						Multiple project IDs should be separated by ",".<br>
						Keep project ID blank to remove enrollment.<br>
					</div>
					
				</div>
				<div id="sbmt_bn" style="position: relative; top: 15px; display: none">
                    <input type="submit" name="bn_sbmt" value="Submit">
                </div> 
				<script>
					// This paragraph script for web page display when page refresh.
					var choice = sessionStorage.getItem("oprt_session_store");
					if(choice == 'radio_view'){
						document.getElementById('radio_view').checked = true;
						document.getElementById('project_id').style.display = 'none';
						document.getElementById('sbmt_bn').style.display = 'block';
					}
					else if (choice == 'radio_enrl') {
						document.getElementById('radio_enrl').checked = true;
						document.getElementById('project_id').style.display = 'block';		
						document.getElementById('sbmt_bn').style.display = 'block';
					}
				</script>                 
            </form> 
        </div>
        <div class="relative" style="top: 20px">
        	<p><a href="http://localhost/instructor_page.php">Instructor Entrance</a></p>
        	<p><a href="http://localhost/admin_page.php">Admin Entrance</a></p>
        </div>             
    </div> 


    <div class="split_right">
        <div style="width: 98%; height: 600px; overflow: auto;">
            <?php include('import_project_list.php');?>
        </div>
    </div>

	<script>
		function view(){
			// Store radio button choice.
			sessionStorage.setItem("oprt_session_store", "radio_view");
			// Clear up project id.
			document.getElementById('pro_id_input').value = '';
			// Set display attributes for project id.
			document.getElementById('project_id').style.display = 'none';
			document.getElementById('sbmt_bn').style.display = 'block';
		}
		function enrl(){
			// Store radio button choice.
			sessionStorage.setItem("oprt_session_store", "radio_enrl");
			// Clear up project id.
			document.getElementById('pro_id_input').value = '';	
			// Set display attributes for project id.
			document.getElementById('project_id').style.display = 'block';
			document.getElementById('sbmt_bn').style.display = 'block';
		}	
	</script>

</body>
</html>
