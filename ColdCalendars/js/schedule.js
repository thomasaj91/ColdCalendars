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
	    	   					//Info. from dialog form
		    	   				var employeeName 	= $('#Employee_Name').val();
		    	   				var shiftStart 		= parseTime($('#Shift_Start').val());
		    	   				var shiftEnd   		= parseTime($('#Shift_End').val());	   
		    	   				
		    	   				//Formatted Shift Times
		    	   				var startTime = new Date(start1.getFullYear(), start1.getMonth(), start1.getDate(), shiftStart.getHours(), shiftStart.getMinutes());
		    	   				var endTime   = new Date(start1.getFullYear(), start1.getMonth(), start1.getDate(), shiftEnd.getHours(), shiftEnd.getMinutes());
		    	   				
		    	   				var shiftObject = new Object();
		    	   				shiftObject.userID	  = employeeName;
		    	   				shiftObject.startTime = startTime;
		    	   				shiftObject.endTime	  = endTime;
		    	   				
		    	   				var retVal = $.ajax({
										url: "rest.php",
										data: "json="+JSON.stringify(shiftObject),
										dataType: "json",
										async: false
										});
								var obj = jQuery.parseJSON(retVal.responseText);
		    	   				
			    	   			$('#calendar').fullCalendar('renderEvent',
								{
									title: employeeName,
									start: startTime,
									end:   endTime,
									allDay: false
								},true);
								
					    	   	$('#calendar').fullCalendar('unselect');
					    	   	$(this).dialog("close");
					    
					}, 
	    		   	"Cancel": function() { $(this).dialog("close"); } }
            });
        },
        eventClick: function(){
            $('#Shift_Options').dialog(
		            {
		    	   		height: 175,
		    	   		width: 175,
		    	   		modal: true,
		    	   		resizable: false,
		    	   		draggable: true
	            });
        },
	    editable: true
	});

	$('#Shift_Start').timepicker({ 'scrollDefaultNow': true });

	$('#Shift_End').timepicker({ 'scrollDefaultNow': true });
	
});

function loadSchedulePage()
{
}

function loadAllShifts()
{
}

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
