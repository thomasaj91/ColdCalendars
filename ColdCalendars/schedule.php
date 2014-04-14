<!DOCTYPE html>
<html>
<head>
<link href='../css/fullcalendar.css' rel='stylesheet' />
<link href='../css/fullcalendar.print.css' rel='stylesheet' media='print' />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../css/jquery.timepicker.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src='../js/fullcalendar.min.js'></script>
<script src='../js/jquery.timepicker.js'></script>
<script src='../js/jquery.timepicker.min.js'></script>
<script src='../js/schedule.js'></script>
<script src='../js/main.js'></script>
<style>

	body {
		margin-top: 40px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		}

	#calendar {
		width: 900px;
		margin: 0 auto;
		}

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

	<div id='Shift_Options' title='Shift Options' style='display:none'>
		<table>
			<tr>
				<td><button id='Release_Shift_Button'>Release Shift</button></td>
			</tr>
			<tr>
				<td><button id='Pickup_Shift_Button'>Pickup Shift</button></td>
			</tr>
			<tr>
				<td><button id='Delete_Shift_Button'>Delete Shift</button></td>
			</tr>
		</table>
	</div>
	<div id='Add_Shift' title='Add Shift' style='display:none'>
		<table>
			<tr>
				<td><label>Employee Name</label></td>
				<td><input id='Employee_Name' type='text'></td>
			</tr>
			<tr>
				<td><label>Shift Start Time</label></td>
				<td><p><input id='Shift_Start' type='text' class='time' /></p></td>
			</tr>
			<tr>
				<td><label>Shift End Time</label></td>
				<td><p><input id='Shift_End' type='text' class='time' /></p></td>
			</tr>
		</table>	 
	</div>
	<div id='calendar'></div>
	<input type="checkbox" class = 'filter' name = "filter" id = "Only_Me_Filter" >Only My Shifts </input>
<!--	
	<input type="radio" class = 'filter' name = "filter" id = "no_filter" value = "0" checked> Don't Filter </input>
	<input type="radio" class = 'filter' name = "filter" id = "me_filter" value = "1"> Filter My Shifts </input>

	<input type="radio" class = 'filter' name = "filter" id = "emp_filter" value = "2"> Filter Only Employee Shifts</input>
	<input type="radio" class = 'filter' name = "filter" id = "man_filter" value = "3"> Filter Only Manager Shifts</input>
-->
</body>
</html>
