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
		
		$( "#tags" ).autocomplete({
		      source: list
		    });
}

$(document).ready(function() {
	  loadNames();
});
