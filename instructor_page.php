<!DOCTYPE html>
<html>
<title>Project List</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css.css">

<head>    
<style>
table {
    border-collapse: collapse;
    width: 100%;
}
th, td {
    text-align: left;
    padding: 8px;
}
tr:nth-child(even){background-color: #f2f2f2}
</style>  
</head>

<body>
	<div class="split_left">
		<div class="relative">
    		<div style="font-size: 22px"><b>Project Registration</b></div>
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
			<form class="relative" method="post" action="instructor_page.php" autocomplete="off">
				<?php include('instructor_error_check.php');?>
				<!-- Input user information -->
				First Name<br>
				<input type="text" name="fname" value="<?php echo $fname; ?>" required><br>
				Last Name<br>
				<input type="text" name="lname" value="<?php echo $lname; ?>" required><br>
				<!-- Input project operation -->
				<p>   </p>
                <div style="font-size: 18px"><b>Registration Operation</b></div>
				<input id="radio_add" type="radio" name="operation" value="add_pro" 
				       onclick="add()"> Add Project<br>
				<input id="radio_mod" type="radio" name="operation" value="mod_pro" 
				       onclick="modf()"> Modify Project<br>
				<input id="radio_del" type="radio" name="operation" value="del_pro" 
				       onclick="del()"> Delet Project<br><br>
				<div id="project_id" style="display: none">
					Project ID<br>  
					<input id="pro_id_input" type="text" name="pro_id"
					value="<?php echo $pro_id; ?>"><br>
				</div>
				<div id="project_title"  style="display: none">
					Project Title<br>
					<input id="pro_title_input" type="text" name="pro_title"
					 value="<?php echo $pro_title; ?>"><br>
				</div>
				<div id="project_requirement"  style="display: none">
					Comment<br>
					<input id="pro_requr_input" type="text" name="pro_requr"
					 value="<?php echo $pro_requr; ?>"><br>
				</div>
				<script>
					// This paragraph script for web page display when page refresh.
					var choice = sessionStorage.getItem("oprt_session_store");
					if(choice == 'radio_add_pro'){
						document.getElementById('radio_add').checked = true;
						document.getElementById('project_id').style.display = 'none';
						document.getElementById('project_title').style.display = 'block';
						document.getElementById('project_requirement').style.display = 'block';
					}
					else if (choice == 'radio_mod_pro') {
						document.getElementById('radio_mod').checked = true;
						document.getElementById('project_id').style.display = 'block';
						document.getElementById('project_title').style.display = 'block';
						document.getElementById('project_requirement').style.display = 'block';
					}
					else if (choice == 'radio_del_pro') {
						document.getElementById('radio_del').checked = true;
						document.getElementById('project_id').style.display = 'block';
						document.getElementById('project_title').style.display = 'none';
						document.getElementById('project_requirement').style.display = 'none';
					}
				</script>
				<div style="position: relative; top: 10px">
					<input type="submit" name="bn_sbmt" value="Submit">
				</div>					
			</form>	
		</div>
		<div class="relative" style="top: 20px">
        	<p><a href="http://localhost/student_page.php">Student Entrance</a></p>   
        	<p><a href="http://localhost/admin_page.php">Admin Entrance</a></p>
        </div>			
	</div> 


	<div class="split_right">
		<div style="width: 98%; height: 600px; overflow: auto;">
			<?php include('import_project_list.php');?>
		</div>
	</div>


	<script>
		function add(){
			// Store radio button choice.
			sessionStorage.setItem("oprt_session_store", "radio_add_pro");
			// Clear up project id, project title, project requirement input.
			document.getElementById('pro_id_input').value    = '';	
			document.getElementById('pro_title_input').value = '';			
			document.getElementById('pro_requr_input').value = '';
			// Set display attributes for project id, project title, project requirement.
			document.getElementById('project_id').style.display    = 'none';
			document.getElementById('project_title').style.display = 'block';
			document.getElementById('project_requirement').style.display = 'block';
		}
		function modf(){
			// Store radio button choice.
			sessionStorage.setItem("oprt_session_store", "radio_mod_pro");
			// Clear up project id, project title, project requirement input.
			document.getElementById('pro_id_input').value    = '';	
			document.getElementById('pro_title_input').value = '';			
			document.getElementById('pro_requr_input').value = '';
			// Set display attributes for project id, project title, project requirement.
			document.getElementById('project_id').style.display    = 'block';
			document.getElementById('project_title').style.display = 'block';
			document.getElementById('project_requirement').style.display = 'block';
		}
		function del(){
			// Store radio button choice.
			sessionStorage.setItem("oprt_session_store", "radio_del_pro");
			// Clear up project id, project title, project requirement input.
			document.getElementById('pro_id_input').value    = '';	
			document.getElementById('pro_title_input').value = '';			
			document.getElementById('pro_requr_input').value = '';
			// Set display attributes for project id, project title, project requirement.
			document.getElementById('project_id').style.display    = 'block';
			document.getElementById('project_title').style.display = 'none';
			document.getElementById('project_requirement').style.display = 'none';
		}		
	</script>


</body>
</html>
