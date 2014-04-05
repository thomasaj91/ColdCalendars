$(document).ready(function() {
	loadSchedulePage();
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
		    	   				shiftObject.requestType = 'AddToSchedule';
		    	   				shiftObject.userID	  = employeeName.split(',')[1];
		    	   				shiftObject.startTime = dateObjectToDateString(startTime);
		    	   				shiftObject.endTime	  = dateObjectToDateString(endTime);
		    	   				
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
            loadNames();
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
	
	loadAllShifts();
	
}

function loadAllShifts()
{
	var date = new Date(), y = date.getFullYear(), m = date.getMonth();
	var startTime = new Date(y, m, 1);
	var endTime = new Date(y, m+1, 1);
	
	var shiftListObject = new Object();
	shiftListObject.requestType = 'ViewSchedule';
	shiftListObject.startTime = dateObjectToDateString(startTime);
	shiftListObject.endTime	  = dateObjectToDateString(endTime);
	
	var retVal = $.ajax({
		url: "rest.php",
		data: "json="+JSON.stringify(shiftListObject),
		dataType: "json",
		async: false
		});
	var obj = jQuery.parseJSON(retVal.responseText);
}

function parseTime(timeString)
{
  if (timeString == '') return null;
  var d = new Date();
  var time = timeString.match(/(\d+)(:(\d\d))?\s*(p?)/);
  d.setHours( parseInt(time[1])===12 && time[4]!=='p'?0:(parseInt(time[1]) + ( ( parseInt(time[1]) < 12 && time[4] ) ? 12 : 0)) );
  d.setMinutes( parseInt(time[3]) || 0 );
  d.setSeconds(0, 0);
  return d;
} 

function loadNames() {
	 var requestObject = new Object();
	    requestObject.requestType="UserListInfo";
		var retVal = $.ajax({
				url: "rest.php",
				data: "json="+JSON.stringify(requestObject),
				dataType: "json",
				async: false
				});
		var list = jQuery.parseJSON(retVal.responseText);
		
		for(var e in list){
			list[e] = list[e].join();
		}
		
		  $( "#Employee_Name" ).autocomplete({
		      source: list
		    });
}
