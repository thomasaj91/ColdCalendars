<?php ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="css/jquery.timepicker.css" />
	<link rel="stylesheet" href="../css/coldcalendar.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script src="js/main.js"></script>
	<script src="js/jquery.timepicker.min.js"></script>
	<script src="js/requests.js"></script>
</head>
<body>

	<?php include 'navbar.php'; ?>
	
	<table>
		<tr>
			<td>Type: 
				<select id='Request_Type'>
					<option value='RequestVacation'>Vacation</option>
					<option value='RequestTimeOff'>Unpaid</option>
				</select>
			</td>
			<td><p>Request Start Date: <input type='text' id='Request_Start_Date'></p></td>
			<td><input type='text' class='time' id='Request_Start_Time'></td>
			<td><p>Request End Date: <input type='text' id='Request_End_Date'></p></td>
			<td><input type='text' class='time' id='Request_End_Time'></td>
		</tr>
		<tr>
			<td colspan=4><button id='Submit_Request_Button'>Submit Request</button></td>
		</tr>
	</table>
</body>
</html>
