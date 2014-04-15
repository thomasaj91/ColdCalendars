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
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/main.js"></script> 
  <script src="js/contacts.js"></script> 
  <script src='../js/jquery.timepicker.min.js'></script>
  <style>
		.center { margin : 0 auto; text-align: center; }
		 
		.queueItem { list-style-type: none; }
		 
		#navbar ul { 
		          margin: 0; 
		          padding: 5px; 
		          list-style-type: none; 
		          text-align: center; 
		          background-color: #000; 
		          } 
		 
		#navbar ul li {  
		          display: inline; 
		          } 
		  
		#navbar ul li a { 
		          text-decoration: none; 
		          padding: .2em 1em; 
		          color: #fff; 
		          background-color: #000; 
		          } 
		 
		#navbar ul li a:hover { 
		          color: #000; 
		          background-color: #fff; 
		          } 
		#footer  { 
		          font-size: 10px; 
		          }
		#container {
		          width: 500px;
		          height: 500px;
		          background-color: #FDA;
		          margin: 0 auto;
		          overflow: hidden;
		}
  </style>
</head>
<body>
	<div id="wrapper" class="center">
	    <header>
		  <div id="navbar"> 
		    <ul> 
		        <li><a href="contacts.php">Contacts</a></li> 
		        <li><a href="managerView_queue.php">Requests</a></li> 
		        <li><a href="schedule.php">Schedule</a></li> 
		        <li><a href="#">Reports</a></li> 
        		<li><a id='Logout' href="#" onclick="logUserOut();return false;">Log Out</a></li>
		        </ul> 
		  </div> 
		  <br>
	   </header>
	
	</div>
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

<div  id="Edit_Availability_Dialog" title="Edit Availability" style='display:none'>
		<!-- Trick to take auto focus off first timepicker -->
		<input type='text' size='1' style='position:relative;top:-500px;' />
		<table>
			<tr>
				<td></td>
				<td>Start Time</td>
				<td>End Time</td>
			</tr>
			<tr>
				<td id='SundayCell'>Sunday</td>
				<td><input id='Sunday_Start' type='text' class='time' /></td>
				<td><input id='Sunday_End' type='text' class='time' /></td>
			</tr>
			<tr>
				<td id='MondayCell'>Monday</td>
				<td><input id='Monday_Start' type='text' class='time' /></td>
				<td><input id='Monday_End' type='text' class='time' /></td>
			</tr>
			<tr>
				<td id='TuesdayCell'>Tuesday</td>
				<td><input id='Tuesday_Start' type='text' class='time' /></td>
				<td><input id='Tuesday_End' type='text' class='time' /></td>
			</tr>
			<tr>
				<td id='WednesdayCell'>Wednesday</td>
				<td><input id='Wednesday_Start' type='text' class='time' /></td>
				<td><input id='Wednesday_End' type='text' class='time' /></td>
			</tr>
			<tr>
				<td id='ThursdayCell'>Thursday</td>
				<td><input id='Thursday_Start' type='text' class='time' /></td>
				<td><input id='Thursday_End' type='text' class='time' /></td>
			</tr>
			<tr>
				<td id='FridayCell'>Friday</td>
				<td><input id='Friday_Start' type='text' class='time' /></td>
				<td><input id='Friday_End' type='text' class='time' /></td>
			</tr>
			<tr>
				<td id='SaturdayCell'>Saturday</td>
				<td><input id='Saturday_Start' type='text' class='time' /></td>
				<td><input id='Saturday_End' type='text' class='time' /></td>
			</tr>
		</table>
</div>

<div id="Contact_List">
</div>
 
</body>
</html>
