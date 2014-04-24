$(document).ready(function(){
	setUserType();
	hideManagerListOptions();
	
	//Set up datepickers
	$('#Report_Start_Date').datepicker();
	$('#Report_End_Date').datepicker();
	
	$('#Generate_Report_Button').click(function() {
		var temp         = new Date($('#Report_End_Date').val());
		temp.setTime( temp.getTime() + (1) * 86400000 );
		var startTime    = dateObjectToDateString(new Date($('#Report_Start_Date').val()));
		var endTime      = dateObjectToDateString( temp );
		var reportObject = new Object();
		reportObject.requestType = 'ReportExport';
		reportObject.start = startTime;
		reportObject.end = endTime;

		var obj    = ajaxGetJSON(reportObject);
		var a      = document.createElement('a');
		a.href     = 'data:attachment/csv,' + encodeURIComponent(obj);
		a.target   = '_blank';
		a.download = 'myFile.csv';
		document.body.appendChild(a);
		a.click();
    }); 
});
