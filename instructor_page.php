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
			<form class="relative">
				<!-- Input user information -->
				First Name<br>
				<textarea name="fname" rows="1" cols="30" 
					      style="resize: none; font-size: 14px;"
					      required></textarea><br>
				Last Name<br>
				<textarea name="lname" rows="1" cols="30" 
					      style="resize: none; font-size: 14px;"
					      required></textarea><br>
				CSU ID<br>
				<textarea name="csuid" rows="1" cols="30" 
					      style="resize: none; font-size: 14px;"
					      required></textarea><br>
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
					<textarea name="pro_id" rows="1" cols="30" 
					          style="resize: none; font-size: 14px;"></textarea><br>
				</div>
				<div id="project_title">
					Title<br>
					<textarea name="pro_title" rows="3" cols="30" 
					          style="resize: none; font-size: 14px"></textarea><br>
				</div>
				<div id="project_requirement">
					Requirement<br>
					<textarea name="pro_require" rows="5" cols="30" 
				          style="resize: none; font-size: 14px"></textarea><br>
				</div>			
				<input type="submit" name="bn_sbmt" value="Submit">
			</form>	
		</div>			
	</div>


	<div class="split_right">
		<div style="width: 98%; height: 700px; overflow: auto;">
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
