<?php
?>

<!DOCTYPE html>
<html>
<head>
	<title>Reports</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="../css/coldcalendar.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script src="js/main.js"></script> 
	<script src="js/reports.js"></script>
</head>
<body>

	<?php include 'navbar.php'; ?>

	<table class='center'>
		<tr>
			<td><p>Report Start Date: <input type='text' id='Report_Start_Date'></p></td>
			<td><p>Report End Date: <input type='text' id='Report_End_Date'></p></td>
		</tr>
		<tr>
			<td colspan=2><button id='Generate_Report_Button'>Generate Report</button></td>
		</tr>
	</table>
</body>
</html>
