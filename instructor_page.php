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
		<div>
			<form class="relative" method="post" action="instructor_page.php">
				<?php include('instructor_error_check.php');?>
				<!-- Input user information -->
				First Name<br>
				<input type="text" name="fname" value="<?php echo $fname; ?>" required><br>
				Last Name<br>
				<input type="text" name="lname" value="<?php echo $lname; ?>" required><br>
				CSU ID<br>
				<input type="text" name="csuid" value="<?php echo $csuid; ?>" required><br>
				<!-- Input project operation -->
				<h3>Project Operation</h3>
				<input type="radio" name="operation" value="add_pro" checked
				       onclick="add()"> Add Project<br>
				<input type="radio" name="operation" value="mod_pro"
				       onclick="modf()"> Modify Project<br>
				<input type="radio" name="operation" value="del_pro"
				       onclick="del()"> Delet Project<br><br>
				<div id="project_id" style="display: none;">
					Project #<br>  
					<input type="text" name="pro_id" value="<?php echo $pro_id; ?>"><br>
				</div>
				<div id="project_title">
					Title<br>
					<input type="text" name="pro_title" value="<?php echo $pro_title; ?>"><br>
				</div>
				<div id="project_requirement">
					Requirement<br>
					<input type="text" name="pro_requr" value="<?php echo $pro_requr; ?>"><br>
				</div>
				<div style="position: relative; top: 10px">
					<input type="submit" name="bn_sbmt" value="Submit">
				</div>					
			</form>	
		</div>			
	</div> 



	


	<div class="split_right">
		<div style="width: 98%; height: 600px; overflow: auto;">
			<?php include('import_project_list.php');?>
		</div>
	</div>


	<script>
		function add(){
			document.getElementById('project_id').style.display = 'none';
			document.getElementById('project_title').style.display = 'block';
			document.getElementById('project_requirement').style.display = 'block';
		}
		function modf(){
			document.getElementById('project_id').style.display = 'block';
			document.getElementById('project_title').style.display = 'block';
			document.getElementById('project_requirement').style.display = 'block';
		}
		function del(){
			document.getElementById('project_id').style.display = 'block';
			document.getElementById('project_title').style.display = 'none';
			document.getElementById('project_requirement').style.display = 'none';
		}
		
	</script>


</body>
</html>
