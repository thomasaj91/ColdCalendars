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

$(document).ready(function() {
	  setTimeOut(loadNames,1);
});
