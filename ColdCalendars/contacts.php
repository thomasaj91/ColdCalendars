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
  <link rel="stylesheet" href="../css/jquery.timepicker.css" />
  <link rel="stylesheet" href="../css/coldcalendar.css" />
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script src="js/main.js"></script> 
  <script src="js/contacts.js"></script> 
  <script src='../js/jquery.timepicker.min.js'></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<!--  <button id='Logout'>Logout</button>-->
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
		<form>
			<table>
				<tr>
					<td><label for="Deletelogin">Login: </label></td>
					<td><select id="DeleteLogin"></select></td>
				</tr>
			</table>
		</form>
</div>

<div id ='Confirm_Delete_Dialog' title='Delete User?' style='display:none'>
	<p>Are you sure you want to delete this user? This action cannot be reversed.</p>
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

<div id='Phone_Priority_Dialog' title='Update Phone Priority' style='display:none'></div>
<div id='Email_Priority_Dialog' title='Update Email Priority' style='display:none'></div>

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

<div  id="Edit_Availability_Dialog" title="Add/Edit Availability" style='display:none'>
		<table>
			<tr>
				<td>
					<select id='Availability_Day'>
					  <option value='Sun'>Sunday</option>
    				  <option value='Mon'>Monday</option>
  					  <option value='Tue'>Tuesday</option>
 					  <option value='Wed'>Wednesday</option>
 					  <option value='Thu'>Thursday</option>
 					  <option value='Fri'>Friday</option>
 					  <option value='Sat'>Saturday</option>
 					</select>
 				</td>
 				<td>
 					Start Time: <input id='Availability_Start' type='text' class='time' />
 				</td>
 				<td>
 					End Time: <input id='Availability_End' type='text' class='time' />
 				</td>
 			</tr>
		</table>
</div>

<div  id="Edit_Title_Dialog" title="Edit Title" style='display:none'>
		<table>
			<tr>
				<td>
					<select id='User_Title'>
    				  <option value='Employee'>Employee</option>
    				  <option value='Manager'>Manager</option>
 					</select>
 				</td>
 			</tr>
		</table>
</div>

<div  id="Edit_Status_Dialog" title="Edit Status" style='display:none'>
		<table>
			<tr>
				<td>
					<select id='User_Status'>
    				  <option value=0>Part Time</option>
    				  <option value=1>Full Time</option>
 					</select>
 				</td>
 			</tr>
		</table>
</div>

<div id="Contact_List">
</div>
 
</body>
</html>
