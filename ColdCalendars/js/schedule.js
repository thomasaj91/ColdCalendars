$(document).ready(function() {
	setUserType();
	hideManagerListOptions();

	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		selectable: true,
		selectHelper: true,
		defaultView: 'agendaWeek',
		disableDragging: true,
		disableResizing: true,
        dayClick: function (start1) {
	        		if(window.userType == 'Manager')
	        		{
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
		
										var obj = ajaxGetJSON(shiftObject);
										
										//Render shift
										var color = window.standardShift;
				
										if(parseCookie().login === shiftObject.userID)
											color = window.myShift;
		
					    	   			$('#calendar').fullCalendar('renderEvent',
										{
											title: employeeName,
											start: startTime,
											end:   endTime,
											allDay: false,
											color: color,
										},true);
		
							    	   	$('#calendar').fullCalendar('unselect');
							    	   	$(this).dialog("close");
							    	   	$('#calendar').fullCalendar( 'rerenderEvents' );
							}, 
			    		   	"Cancel": function() { $(this).dialog("close"); } }
		            });
		            loadNames();
        		}
        },
        eventClick: function(event){
        	window.targetEvent = event;
        	
        	//Hide the release shift button for shifts that are released/other users' shifts
        	if(event.color == window.releasedShift || parseCookie().login != event.title) {
        		$('#Release_Shift_Button').hide();
        	}
        	else {
        		$('#Release_Shift_Button').show();
        	}
        	
        	//Show the pickup button for released shifts only
        	if(event.color != window.releasedShift) {
        		$('#Pickup_Shift_Button').hide();
        	}
        	else {
        		$('#Pickup_Shift_Button').show();
        	}
        	
        	//Hide the delete button for everyone but managers
        	if(window.userType != 'Manager'){
        		$('#Delete_Shift_Button').hide();
        	}
        	else {
        		$('#Delete_Shift_Button').show();
        	}
        	
            $('#Shift_Options').dialog({
		    	   		height: 175,
		    	   		width: 175,
		    	   		modal: true,
		    	   		resizable: false,
		    	   		draggable: true
	            });
        },
	    editable: true
	});

	loadSchedulePage();

	
	$('#Release_Shift_Button').click(function(){
		if(window.targetEvent.color != window.releasedShift) {
			var shiftObject = new Object();
			shiftObject.requestType = 'ReleaseShift';
			shiftObject.startTime = dateObjectToDateString(new Date(window.targetEvent.start));
			shiftObject.endTime	  = dateObjectToDateString(new Date(window.targetEvent.end));
			
			var obj = ajaxGetJSON(shiftObject);
			
			window.targetEvent.color = window.releasedShift;
		}
		
		$('#Shift_Options').dialog("close");
		$('#calendar').fullCalendar( 'rerenderEvents' );
	});
	
	$('#Pickup_Shift_Button').click(function(){
		if(window.targetEvent.color == window.releasedShift) {
			var shiftObject = new Object();
			shiftObject.requestType = 'PickUpShift';
			shiftObject.userID	  = window.targetEvent.title;
			shiftObject.startTime = dateObjectToDateString(new Date(window.targetEvent.start));
			shiftObject.endTime	  = dateObjectToDateString(new Date(window.targetEvent.end));
			
			var obj = ajaxGetJSON(shiftObject);
		}
		
		$('#Shift_Options').dialog("close");
		$('#calendar').fullCalendar( 'rerenderEvents' );
	});
	
	$('#Delete_Shift_Button').click(function(){
		var shiftObject = new Object();
		shiftObject.requestType = 'RemoveFromSchedule';
		shiftObject.userID	  = window.targetEvent.title;
		shiftObject.startTime = dateObjectToDateString(new Date(window.targetEvent.start));
		shiftObject.endTime	  = dateObjectToDateString(new Date(window.targetEvent.end));
		
		var obj = ajaxGetJSON(shiftObject);
		
		$('#calendar').fullCalendar('removeEvents',window.targetEvent._id);
		$('#Shift_Options').dialog("close");
		$('#calendar').fullCalendar( 'rerenderEvents' );
	});
	
	$('.fc-event').focus(function() {
		$('.last-clicked-event').each(function(){
			$(this).removeClass('last-clicked-event');
		});
		$(this).addClass('last-clicked-event');
	});
	
	$('#Shift_Start').timepicker({ 'scrollDefaultNow': true });

	$('#Shift_End').timepicker({ 'scrollDefaultNow': true });

    $("#Only_Me_Filter").change(function () {
    	var fliter  = $(this).is(':checked');
    	$('.fc-event').each(function() {
          if(fliter && $(this).find('.fc-event-title').html() !== parseCookie().login)
    	    $(this).hide();
    	  else
   		    $(this).show();
    	});
    });

});

function loadSchedulePage()
{
	setShiftColors();
	setUserType();
	loadAllShifts();
}

function loadAllShifts()
{
	var date = new Date(), y = date.getFullYear(), m = date.getMonth();
	var startTime = new Date(y, m, 1);
	var endTime = new Date(y, m+1, 1);

	var shiftListObject = new Object();
	shiftListObject.requestType = "ViewSchedule";
	shiftListObject.startTime = dateObjectToDateString(startTime);
	shiftListObject.endTime	  = dateObjectToDateString(endTime);

	var obj = ajaxGetJSON(shiftListObject);

	if(obj != null)
	{
		if(obj.hasOwnProperty("startTime") && obj.hasOwnProperty("endTime")) {
			for(var e in obj){
				if(obj[e]===0)
					alert('invalid field: '+e);
			}	
		}
		else {
			for(var e in obj) {
				var startTime = stringToDateObject(obj[e]["startTime"]);
				var endTime   = stringToDateObject(obj[e]["endTime"  ]);
				var title = obj[e]["owner"];
				var color = window.standardShift;
		
				if(parseCookie().login === title)
					color = window.myShift;
				if(obj[e]["released"] === true)
					color = window.releasedShift;
		
				$('#calendar').fullCalendar('renderEvent',
						{
							title: title,
							start: startTime,
							end:   endTime,
							allDay: false,
							color: color
						},true);
				$('#calendar').fullCalendar('unselect');
			}
		
		}
	}
}

function loadNames() {
	 var requestObject = new Object();
	    requestObject.requestType="UserListInfo";

		var list = ajaxGetJSON(requestObject);

		for(var e in list){
			list[e] = list[e].join();
		}

		  $( "#Employee_Name" ).autocomplete({
		      source: list
		    });
}

function setShiftColors() {
	window.standardShift = '#0000ff';
	window.releasedShift = '#ff0000';
	window.myShift = '#228b22';
}
