$(document).ready(function(){
	//Set up datepickers
	$('#Report_Start_Date').datepicker();
	$('#Report_End_Date').datepicker();
	
	$('#Generate_Report_Button').click(function() {
			alert($('#Report_Start_Date').val() + ' ' + $('#Report_End_Date').val());
			var A = [['n','sqrt(n)']];  // initialize array of rows with header row as 1st item
			for(var j=1;j<10;++j){ A.push([j, Math.sqrt(j)]) }
			var csvRows = [];
			for(var i=0,l=A.length; i<l; ++i){
			    csvRows.push(A[i].join(','));   // unquoted CSV row
			}
			var csvString = csvRows.join("\n");

			var a = document.createElement('a');
			a.href     = 'data:attachment/csv,' + csvString;
			a.target   = '_blank';
			a.download = 'myFile.csv';
			document.body.appendChild(a);
			a.click();
    }); 
});
