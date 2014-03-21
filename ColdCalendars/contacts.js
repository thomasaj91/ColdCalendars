  function loadUser()
  {
	    var requestObject = new Object();
	    requestObject.requestType="UserList";
		var retVal = $.ajax({
				url: "rest.php",
				data: "json="+JSON.stringify(requestObject),
				dataType: "json",
				async: false
				});
		var list = jQuery.parseJSON(retVal.responseText); 

		var elem = $('#Contact_List').empty();
		for(var e in list){
			var info   = getInfoByLogin(list[e]);
			var phones = getPhoneNumbersByLogin(list[e]);
			var emails = getEmailAddressesByLogin(list[e]);
			if(info   !== null
		    && phones !== null
		    && emails !== null) {
				appendUserDataTo(elem,info,phones,emails);
			}
				
		}
  }
  
  function appendUserDataTo(elem,info,phones,emails) {
	  $('<h3>').appendTo(elem).text(info.lastName + ', ' + info.firstName);
	  var div = $('<div>').appendTo(elem);
	  $('<p>').appendTo(div).text('Title: ' + info.title)
	  $('<p>').appendTo(div).text('Work Status: '+ (info.workStatus ? 'FT' : 'PT'));
	  var li1 = $('<ul>').appendTo(div);
	  var li2 = $('<ul>').appendTo(div);
	  for(var e in phones)
		  $('<li>').appendTo(li1).text(phones[e]);
	  for(var a in emails)
		  $('<li>').appendTo(li2).text(emails[a]);
	  
  }
  
  function getInfoByLogin(login) {
	  var getInfo = new Object();
	  getInfo.requestType = 'UserInfo';
	  getInfo.userID = login;
	  return ajaxGetJSON(getInfo);	  
  }

  function getPhoneNumbersByLogin(login) {
	  var getNumbers = new Object();
	  getNumbers.requestType = 'UserPhone';
	  getNumbers.userID = login;
	  return ajaxGetJSON(getNumbers);
  }
  
  function getEmailAddressesByLogin(login) {
	  var getEmails = new Object();
	  getEmails.requestType = 'UserEmail';
	  getEmails.userID = login;
	  return ajaxGetJSON(getEmails);
	  
  }
  
  function ajaxGetJSON(obj) {
		var retVal = $.ajax({
			url: "rest.php",
			data: "json="+JSON.stringify(obj),
			dataType: "json",
			async: false
			});
	return jQuery.parseJSON(retVal.responseText); 	  
  }
  
  function callback(param)
  {
	  alert(param);
  }

  $(document).ready(function() {
	  loadUser();
	  $(function() {
		    $("#Contact_List").accordion();
	  });

	  $("#Create_User").click(function() {
 	  		$( "#Create_User_Dialog" ).dialog("open");
      });
	  
	  $("#Delete_User").click(function() {
	  		$( "#Delete_User_Dialog" ).dialog("open");
      });
	  
	  $("#Create_User_Dialog").dialog({

	  		autoOpen: false,
	   		height: 600,
	   		width: 407,
	   		modal: true,
	   		resizable: false,
	   		draggable: false,
	   		buttons: { "Create User": function() { var userObject = new Object();

	   											 userObject.requestType 	  = "CreateUser";
		   										 userObject.userID        	  = $("#Login").val();
		 	  									 userObject.password          = $("#Passwd").val();
		 	  									 userObject.firstName         = $("#First_Name").val();
		      									 userObject.lastName          = $("#Last_Name").val();
		      									 userObject.vacationDays      = $("#Vacation_Days").val();
		      									 userObject.title		      = $("input:radio[name=Title]:checked").val();
		      									 userObject.workStatus	      = $("input:radio[name=WorkStatus]:checked").val();
		      									 userObject.phone			  = $("#Phone").val();
		      									 userObject.email			  = $("#Email").val();
		
			  									// var retVal           		  = $.ajax("rest.php?json=" + JSON.stringify(userObject),{async:false});
		      									var retVal = $.ajax({
		      										url: "rest.php",
		      										data: "json="+JSON.stringify(userObject),
		      										dataType: "json",
		      										async: false
		      										});
		      									var obj = jQuery.parseJSON(retVal.responseText);
		      									if(obj === null) {
		      										alert('unexpected server error');
		      									}
		      									else {
		      										var zero_found = false;
		      										for(var e in obj){
			      									    //alert(e + ' : ' + obj[e]);
		      											if(obj[e]===0) {
		      												alert('invalid field: '+e);
		      												zero_found = true;
		      											}
		      										}
		      										if (!zero_found) {
		      											alert('success');
		      										}
		      									}
		      												  		}, 
		   		  		"Cancel": function() { $(this).dialog("close"); } }
	   });
	  
	  $("#Delete_User_Dialog").dialog({

	  		autoOpen: false,
	   		height: 600,
	   		width: 407,
	   		modal: true,
	   		resizable: false,
	   		draggable: false,
	   		buttons: { "Create User": function() { var userObject = new Object();

	   											 userObject.requestType 	  = "DeleteUser";
		   										 userObject.userID        	  = $("#DeleteLogin").val();
		
			  									// var retVal           		  = $.ajax("rest.php?json=" + JSON.stringify(userObject),{async:false});
		      									var retVal = $.ajax({
		      										url: "rest.php",
		      										data: "json="+JSON.stringify(userObject),
		      										dataType: "json",
		      										async: false
		      										});
		      									var obj = jQuery.parseJSON(retVal.responseText);
		      									if(obj === null) {
		      										alert('unexpected server error');
		      									}
		      									else {
		      										var zero_found = false;
		      										for(var e in obj){
			      									    //alert(e + ' : ' + obj[e]);
		      											if(obj[e]===0) {
		      												alert('invalid field: '+e);
		      												zero_found = true;
		      											}
		      										}
		      										if (!zero_found) {
		      											alert('success');
		      										}
		      									}
		      												  		}, 
		   		  		"Cancel": function() { $(this).dialog("close"); } }
	   });
	  
  });