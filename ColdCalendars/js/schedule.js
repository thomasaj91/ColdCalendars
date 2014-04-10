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
        eventClick: function(event){
        	window.targetEvent = event;
        	if(event.color == '#ff0000' || parseCookie().login != event.title) {
        		$('#Release_Shift_Button').hide();
        	}
        	else {
        		$('#Release_Shift_Button').show();
        	}
        	if(event.color != '#ff0000') {
        		$('#Pickup_Shift_Button').hide();
        	}
        	else {
        		$('#Pickup_Shift_Button').show();
        	}
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
		if(window.targetEvent.color != '#ff0000') {
			var shiftObject = new Object();
			shiftObject.requestType = 'ReleaseShift';
			shiftObject.startTime = dateObjectToDateString(new Date(window.targetEvent.start));
			shiftObject.endTime	  = dateObjectToDateString(new Date(window.targetEvent.end));
			
			var obj = ajaxGetJSON(shiftObject);
			
			window.targetEvent.color = '#ff0000';
		}
		
		$('#Shift_Options').dialog("close");
	});
	
	$('#Pickup_Shift_Button').click(function(){
		if(window.targetEvent.color == '#ff0000') {
			var shiftObject = new Object();
			shiftObject.requestType = 'PickUpShift';
			shiftObject.userID	  = window.targetEvent.title;
			shiftObject.startTime = dateObjectToDateString(new Date(window.targetEvent.start));
			shiftObject.endTime	  = dateObjectToDateString(new Date(window.targetEvent.end));
			
			var obj = ajaxGetJSON(shiftObject);
		}
		
		$('#Shift_Options').dialog("close");
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
			var color = '#0000ff';

			if(parseCookie().login === title)
				color = '#00ff00';
			if(obj[e]["released"] === true)
				color = '#ff0000';

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

function parseTime(timeString) {
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
