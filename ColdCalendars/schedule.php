<!DOCTYPE html>
<html>
<head>
<link href='../fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='../fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../css/jquery.timepicker.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src='../fullcalendar/fullcalendar.min.js'></script>
<script src='../js/jquery.timepicker.js'></script>
<script src='../js/jquery.timepicker.min.js'></script>
<script src='../js/schedule.js'></script>
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

</style>
</head>
<body>
	<div id='Shift_Options' title='Shift Options' style='display:none'>
		<table>
			<tr>
				<td><button>Release Shift</button></td>
			</tr>
			<tr>
				<td><button>Pickup Shift</button></td>
			</tr>
			<tr>
				<td><button>Delete Shift</button></td>
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
</body>
</html>
