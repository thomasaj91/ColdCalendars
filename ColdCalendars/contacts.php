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
	   		buttons: { "Submit To": function() { var login			  = "";
	   											 var authtoken		  = "";
		   										 var userLogin        = $("#Login").val();
		 	  									 var password         = $("#Passwd").val();
		 	  									 var firstName        = $("#First_Name").val();
		      									 var lastName         = $("#Last_Name").val();
		      									 var vacationDays     = $("#Vacation_Days").val();
		      									 var title		      = $("#radio").val();
		      									 var workStatus	      = $("#radio1").val();
		      									 var phone			  = $("#Phone").val();
		      									 var email			  = $("#Email").val();
		      									 var requestType 	  = "CreateUser";
/*
		      									var myObject = new Object();
		      									myObject.name = "John";
		      									myObject.age = 12;
		      									myObject.pets = ["cat", "dog"];
												alert(JSON.stringify(myObject));
	*/	      									 
												
			  									 var retVal           = $.ajax('jsonEcho.php?json=' + JSON.stringify(login, authtoken, requestType, userLogin, passwd, firstName, lastName, workStatus, title, vacationDays, phone, email)); },
												 //var retVal           = $.ajax('jsonEcho.php?json=' + JSON.stringify(myObject)); }, 
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