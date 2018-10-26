<!DOCTYPE html>
<html>
<title>Project List</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<head>
    <style>
    div.relative {
        position: relative;
        top: 30px;
    }
    </style>
</head>

<body>

<div class="w3-third w3-container">
	<form class="w3-container" method="post" action="20181025.php">
        <?php include('error_check.php');?>
		<p>
        <label>First Name</label>
		<input class="w3-input" type="text" name="fname" value="<?php echo $fname; ?>">
		</p>
		<p>
        <label>Last Name</label>
		<input class="w3-input" type="text" name="lname" value="<?php echo $lname; ?>">
		</p>
		<p>
        <label>CSU ID</label>
		<input class="w3-input" type="text" name="csuid" value="<?php echo $csuid; ?>">
		</p>
        <p>
        <label>Project #</label>
        <input class="w3-input" type="text" name="proid" value="<?php echo $proid; ?>">
        </p>
        <p><button class="w3-button w3-light-gray" type="submit" name="bn_sbmt">Submit</button></p>
        <p><button class="w3-button w3-light-gray" type="submit" name="bn_update">Update</button></p>
	</form>
</div>

<div class="w3-twothird w3-container">
	<div class="w3-panel w3-gray">
    <h2>PROJECT LIST</h2>
    </div>

    <table class="w3-table-all w3-hoverable">
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    	<tr>
    		<td>
    			<input type="checkbox" value="123">123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123 123
    		</td>
    	</tr>
    </table>

	
</div>


</body>
</html>
