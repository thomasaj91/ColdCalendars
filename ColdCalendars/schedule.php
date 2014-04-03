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
<script>

	$(document).ready(function() {
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			selectable: true,
			selectHelper: true,
	        dayClick: function (start1) {
		            $('#Add_Shift').dialog(
		            {
		    	   		height: 300,
		    	   		width: 350,
		    	   		modal: true,
		    	   		resizable: false,
		    	   		draggable: true,
		    	   		buttons: { 'Add Shift': function() { 		
			    	   				var employeeName 	= $('#Employee_Name').val();
			    	   				var shiftStart 		= parseTime($('#Shift_Start').val());
			    	   				var shiftEnd   		= parseTime($('#Shift_End').val());	    	   				
			    	   				
				    	   			$('#calendar').fullCalendar('renderEvent',
									{
										title: employeeName,
										start: new Date(start1.getFullYear(), start1.getMonth(), start1.getDate(), shiftStart.getHours(), shiftStart.getMinutes()),
										end:   new Date(start1.getFullYear(), start1.getMonth(), start1.getDate(), shiftEnd.getHours(), shiftEnd.getMinutes()),
										allDay: false
									},true);
									
						    	   	$('#calendar').fullCalendar('unselect');
						    	   	$(this).dialog("close");
						    
						}, 
		    		   	"Cancel": function() { $(this).dialog("close"); } }
	            });
	        },
		    editable: true
		});

		$('#Shift_Start').timepicker({ 'scrollDefaultNow': true });

		$('#Shift_End').timepicker({ 'scrollDefaultNow': true });
		
	});

	function parseTime(timeString)
	{
	  if (timeString == '') return null;
	  var d = new Date();
	  var time = timeString.match(/(\d+)(:(\d\d))?\s*(p?)/);
	  d.setHours( parseInt(time[1]) + ( ( parseInt(time[1]) < 12 && time[4] ) ? 12 : 0) );
	  d.setMinutes( parseInt(time[3]) || 0 );
	  d.setSeconds(0, 0);
	  return d;
	} 

</script>
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
