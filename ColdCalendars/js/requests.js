$(document).ready(function(){
	setUserType();
	hideManagerListOptions();
	insertVacationDays();

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
		if(obj === null){
			alert('Not enough vacation days remaining for that request.');
			return;
		}
		//Error checking/handling
		var zero_found = false;

		for(var e in obj){		
			if(obj[e]===0) {
				zero_found = true;
			}
		}

		if(zero_found)
		{
			alert('Invalid request. Please try again.');
		}
		else
		{
			alert('Request awaiting manager action. Follow activity log for approval/denial.');
			//location.reload();
		}
	});
});

function insertVacationDays(){
	var vacationInquiry = new Object();
	vacationInquiry.requestType = 'UserInfo';
	vacationInquiry.userID = parseCookie()['login'];
	
	var obj = ajaxGetJSON(vacationInquiry);
	
	
	
	var str = $('#Vacation_Days').text();
	str += obj['remainingVacationDays'] + '/' + obj['vacationDays'] + ' Used';
	$('#Vacation_Days').text(str);
}
