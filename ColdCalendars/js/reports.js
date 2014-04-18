$(document).ready(function(){
	//Set up datepickers
	$('#Report_Start_Date').datepicker();
	$('#Report_End_Date').datepicker();
	
	$('#Generate_Report_Button').click(function() {
		var startTime = dateObjectToDateString(new Date($('#Report_Start_Date').val()));
		var endTime = dateObjectToDateString(new Date($('#Report_End_Date').val()));
		
		var reportObject = new Object();
		reportObject.requestType = 'ReportExport';
		reportObject.start = startTime;
		reportObject.end = endTime;
		var obj = ajaxGetJSON(reportObject);
		
		//alert(obj);
		
		var a = document.createElement('a');
		a.href     = 'data:attachment/csv,' + encodeURIComponent(obj);
		a.target   = '_blank';
		a.download = 'myFile.csv';
		document.body.appendChild(a);
		a.click();
    }); 
});
