$(document).ready(function(){
	$('#Request_Start_Time').timepicker();
	$('#Request_End_Time').timepicker();
	$('#Request_Start_Date').datepicker();
	$('#Request_End_Date').datepicker();
	
	$('#Submit_Request_Button').click(function(){
		var type = $('#Request_Type').val();
		var start = dateObjectToDateString(new Date($('#Request_Start_Date').val() + ' ' + standardToMilitaryTime($('#Request_Start_Time').val())));
		var end = dateObjectToDateString(new Date($('#Request_End_Date').val() + ' ' + standardToMilitaryTime($('#Request_End_Time').val())));
		
		var requestObject = new Object();
		requestObject.requestType = type;
		requestObject.startDate = start;
		requestObject.endDate = end;
		
		var obj = ajaxGetJSON(requestObject);
	});
});
