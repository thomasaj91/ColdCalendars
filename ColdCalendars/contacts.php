<?php
require_once(__DIR__.'/auth/authentication.php');
assertValidUserPageAccess();
?>

<!DOCTYPE html>
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

<p>YOUR SCHEDULE</p>
<div id="Current_User_Info">
</div>
<br>
<br>

<button id="Create_User">Create new user</button>   
<button id="Delete_User">Delete user</button> 

<div  id="Create_User_Dialog" title="Create new user">
  <p class="validateTips">All form fields are required.</p>
    <form>
<fieldset>
	<label for="login">Login</label>
	<input id="Login">
	<label for="passwd">Password</label>
	<input type="password" id="Passwd">
	<label for="firstName">First Name</label>
	<input id="First_Name">
	<label for="lastName">Last Name</label>
	<input id="Last_Name">
	<label for="phone">Phone #</label>
	<input id="Phone">
	<label for="email">Email</label>
	<input id="Email">
	<label for="vacationDays">Vacation Days</label>
	<input id="Vacation_Days">
</fieldset>

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

<div  id="Delete_User_Dialog" title="Delete existing user">
<p class="validateTips">All form fields are required.</p>
<form>
<fieldset>
	<label for="Deletelogin">Login</label>
	<input id="DeleteLogin">
</fieldset>

</form>
</div>

<div id="Contact_List">
</div>
 
</body>
