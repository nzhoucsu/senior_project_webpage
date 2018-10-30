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
				<input id="radio_add" type="radio" name="operation" value="add_pro" 
				       onclick="add()"> Add Project<br>
				<input id="radio_mod" type="radio" name="operation" value="mod_pro" 
				       onclick="modf()"> Modify Project<br>
				<input id="radio_del" type="radio" name="operation" value="del_pro" 
				       onclick="del()"> Delet Project<br><br>
				<div id="project_id" style="display: none">
					Project #<br>  
					<input id="pro_id_input" type="text" name="pro_id"
					value="<?php echo $pro_id; ?>"><br>
				</div>
				<div id="project_title"  style="display: none">
					Title<br>
					<input id="pro_title_input" type="text" name="pro_title"
					 value="<?php echo $pro_title; ?>"><br>
				</div>
				<div id="project_requirement"  style="display: none">
					Requirement<br>
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
	</div> 


	<div class="split_right">
		<div style="width: 98%; height: 600px; overflow: auto;">
			<?php include('import_project_list.php');?>
		</div>
	</div>


	<script>
		function add(){
			sessionStorage.setItem("oprt_session_store", "radio_add_pro");
			document.getElementById('pro_id_input').value    = '';	
			document.getElementById('pro_title_input').value = '';			
			document.getElementById('pro_requr_input').value = '';
			document.getElementById('project_id').style.display    = 'none';
			document.getElementById('project_title').style.display = 'block';
			document.getElementById('project_requirement').style.display = 'block';
		}
		function modf(){
			sessionStorage.setItem("oprt_session_store", "radio_mod_pro");
			document.getElementById('pro_id_input').value    = '';	
			document.getElementById('pro_title_input').value = '';			
			document.getElementById('pro_requr_input').value = '';
			document.getElementById('project_id').style.display    = 'block';
			document.getElementById('project_title').style.display = 'block';
			document.getElementById('project_requirement').style.display = 'block';
		}
		function del(){
			sessionStorage.setItem("oprt_session_store", "radio_del_pro");
			document.getElementById('pro_id_input').value    = '';	
			document.getElementById('pro_title_input').value = '';			
			document.getElementById('pro_requr_input').value = '';
			document.getElementById('project_id').style.display    = 'block';
			document.getElementById('project_title').style.display = 'none';
			document.getElementById('project_requirement').style.display = 'none';
		}		
	</script>


</body>
</html>
