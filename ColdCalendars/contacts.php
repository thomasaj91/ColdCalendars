<?php
require_once(__DIR__.'/auth/authentication.php');
assertValidUserPageAccess();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Employee View</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/main.js"></script> 
  <script src="js/contacts.js"></script> 
</head>
<body>
<button id='Logout'>Logout</button>
<div id='Schedule'></div>
<div id="Current_User_Info">
</div>
<br>
<br>

<button id="Create_User">Create new user</button>   
<button id="Delete_User">Delete user</button> 

<div  id="Create_User_Dialog" title="Create new user" style='display:none'>
  <p class="validateTips">All form fields are required.</p>
    <form>
		<table>
			<tr>
				<td><label for="login">Login</label></td>
				<td><input id="Login"></td>
			</tr>
			<tr>
				<td><label for="passwd">Password</label></td>
				<td><input type="password" id="Passwd"></td>
			</tr>
			<tr>
				<td><label for="firstName">First Name</label></td>
				<td><input id="First_Name"></td>
			</tr>
			<tr>
				<td><label for="lastName">Last Name</label></td>
				<td><input id="Last_Name"></td>
			</tr>
			<tr>
				<td><label for="phone">Phone #</label></td>
				<td><input id="Phone"></td>
			</tr>
			<tr>
				<td><label for="email">Email</label></td>
				<td><input id="Email"></td>
			</tr>
			<tr>
				<td><label for="vacationDays">Vacation Days</label></td>
				<td><input id="Vacation_Days"></td>
			</tr>
		</table>
		
		<div id="Title_Choices">
			<input type="radio" id="Title1" name="Title" value="Admin"><label for="radio1">Admin</label>
			<input type="radio" id="Title2" name="Title" value="Employee"><label for="radio2">Employee</label>
			<input type="radio" id="Title3" name="Title" value="Manager"><label for="radio3">Manager</label>
		</div>
		
		<div id="Work_Status">
			<input type="radio" id="Work_Status" name="WorkStatus" value="False"><label for="radio4">Part Time</label>
			<input type="radio" id="Work_Status" name="WorkStatus" value="True"><label for="radio5">Full Time</label>
		</div>

	</form>
</div>

<div  id="Delete_User_Dialog" title="Delete existing user" style='display:none'>
	<p class="validateTips">All form fields are required.</p>
		<form>
			<table>
				<tr>
					<td><label for="Deletelogin">Login</label></td>
					<td><input id="DeleteLogin"></td>
				</tr>
			</table>
		</form>
</div>

<div  id="Add_Phone_Dialog" title="Add Phone Number" style='display:none'>
		<form>
			<table>
				<tr>
					<td><label>Phone Number</label></td>
					<td><input id="New_Number"></td>
				</tr>
			</table>
		</form>
</div>

<div  id="Remove_Phone_Dialog" title="Remove Phone Number" style='display:none'>
		<form id='Phone_Number_Remove_List'>
		</form>
</div>

<div  id="Add_Email_Dialog" title="Add Email" style='display:none'>
		<form>
			<table>
				<tr>
					<td><label>Email Address</label></td>
					<td><input id="New_Email"></td>
				</tr>
			</table>
		</form>
</div>

<div  id="Remove_Email_Dialog" title="Remove Email" style='display:none'>
		<form id='Email_Address_Remove_List'>
		</form>
</div>

<div id="Contact_List">
</div>
 
</body>
</html>
