<!DOCTYPE html>
<head>
  <meta charset="utf-8" />
  <title>Employee View</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-

ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script>

  $(document).ready(function() {
	  
	  $(function() {
		    $( "#Contact_List" ).accordion();
		  });

	  $("#Create_User").click(function() {
 	  		$( "#dialog-form" ).dialog( "open" );
      });

 	  $( "#dialog-form" ).dialog({
	  		autoOpen: false,
	   		height: 600,
	   		width: 407,
	   		modal: true,
	   		resizable: false,
	   		draggable: false,
	   		buttons: { "Submit To": function() { var userObject = new Object();

	   											 userObject.requestType 	  = "CreateUser";
		   										 userObject.userID        	  = $("#Login").val();
		 	  									 userObject.password          = $("#Passwd").val();
		 	  									 userObject.firstName         = $("#First_Name").val();
		      									 userObject.lastName          = $("#Last_Name").val();
		      									 userObject.vacationDays      = $("#Vacation_Days").val();
		      									 userObject.title		      = $("#radio").val();
		      									 userObject.workStatus	      = $("#radio1").val();
		      									 userObject.phone			  = $("#Phone").val();
		      									 userObject.email			  = $("#Email").val();
		
			  									 var retVal           = $.ajax("jsonEchoFile.php?json=" + JSON.stringify(userObject)); 
			  									 }, 
		   		  		"Cancel": function() { $(this).dialog("close"); } }
	   });
  });

  </script>
</head>
<body>

<center><b>YOUR SCHEDULE</b></center>
<br>
<br>
<br>
<br>
<br>

<button id="Create_User">Create new user</button>    

<div id="dialog-form" title="Create new user">
<p class="validateTips">All form fields are required.</p>
<form>
<fieldset>
	<label for="login">Login</label>
	<input id="Login">
	<label for="passwd">Password</label>
	<input id="Passwd">
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

<div id="radio">
	<input type="radio" id="radio1" name="radio" value="Admin"><label for="radio1">Admin</label>
	<input type="radio" id="radio2" name="radio" checked="checked" value="Employee"><label for="radio2">Employee</label>
	<input type="radio" id="radio3" name="radio"><label for="radio3" value="Manager">Manager</label>
</div>

<div id="radio1">
	<input type="radio" id="radio4" name="radio1" value="False"><label for="radio4">Part Time</label>
	<input type="radio" id="radio5" name="radio1" value="True"><label for="radio5">Full Time</label>
</div>

</form>
</div>
 
<div id="Contact_List">
  <h3>First Section</h3>
  <div>
    <p>
  Content for section first
    </p>
  </div>
  <h3>Second Section</h3>
  <div>
    <p>
	Content For Second Section
    </p>
  </div>
  <h3>Third Section</h3>
  <div>
    <p>
	Content for third section
    </p>
  </div>
</div>
 
</body>