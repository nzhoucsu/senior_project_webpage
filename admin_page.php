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
    		<h3>Administration</h3>
    	</div>
		<div>
			<form class="relative" method="post" action="instructor_page.php" autocomplete="off">
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
				</div>
				<input id="radio_down" type="radio" name="operation" value="down" 
				       onclick="down()">Download enrollment<br>				
				<div style="position: relative; top: 10px">
					<input type="submit" name="bn_sbmt" value="Submit">
				</div>					
			</form>	
		</div>
		<div class="relative" style="top: 20px">
        	<p><a href="http://localhost/student_page.php">Student Entrance</a></p>
        </div>			
	</div> 


	<div class="split_right">
		<div style="width: 98%; height: 600px; overflow: auto;">
			<?php include('import_project_list.php');?>
		</div>
	</div>


	<script>
		function deadline(){
			document.getElementById('deadln_input').value   = '';	
			document.getElementById('deadln').style.display = 'block';
		}
		function down(){
			document.getElementById('deadln_input').value   = '';	
			document.getElementById('deadln').style.display = 'none';
		}	
	</script>


</body>
</html>
