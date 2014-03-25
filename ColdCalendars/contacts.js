function loadContactsPage() {
	  hideOnLoad();
	  loadUser();
	  
	  $('input:text').val('');
	  
	  $("#Contact_List").accordion();
	  $("#Current_User_Info").accordion();
	  $("#Create_User").click(function() {
 	  		$( "#Create_User_Dialog" ).dialog("open");
      });
	  
	  $("#Delete_User").click(function() {
	  		$( "#Delete_User_Dialog" ).dialog("open");
      });
	  
	  $("#Create_User_Dialog").dialog({

	  		autoOpen: false,
	   		height: 500,
	   		width: 407,
	   		modal: true,
	   		resizable: false,
	   		draggable: true,
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
		      											$(this).dialog("close");
		      											location.reload();
		      										}
		      									}
		      												  		}, 
		   		  		"Cancel": function() { $(this).dialog("close"); } }
	   });
	  
	  $("#Delete_User_Dialog").dialog({

	  		autoOpen: false,
	   		height: 300,
	   		width: 400,
	   		modal: true,
	   		resizable: false,
	   		draggable: true,
	   		buttons: { "Delete User": function() { var userObject = new Object();

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
		      											$(this).dialog("close");
		      											location.reload();
		      										}
		      									}
		      												  		}, 
		   		  		"Cancel": function() { $(this).dialog("close"); } }
	   });
	  	
	
}

function hideOnLoad() {
	$('#Create_User_Dialog').hide();
    $('#Delete_User_Dialog').hide();	
}  

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
				appendUserDataTo(elem,info,phones,emails,list[e]);
			}
		}
		
		$('#'+parseCookie().login+'_h3').remove().appendTo($('#Current_User_Info'));
		$('#'+parseCookie().login+'_div').remove().appendTo($('#Current_User_Info'));
		
		$("#Contact_List").accordion();
  }
  
  function appendUserDataTo(elem,info,phones,emails,login) {
	  $('<h3>').appendTo(elem).text(info.lastName + ', ' + info.firstName).attr('id',login+'_h3');
	  var div = $('<div>').appendTo(elem).attr('id',login+'_div');
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
  
  function parseCookie(){
		var cookie = document.cookie;

		// split it into key-value pairs
		var cookie_pieces = cookie.split(';');

		// for each of those pairs, split into key and value
		var obj = new Object();
		for(var i=0; i<cookie_pieces.length; i++){

			// get the cookie piece and trim it
			var piece = trim(cookie_pieces[i]);

			// find the location of the '=' and split the string
			var a = piece.indexOf('=');
			if (a == -1){
				// there was no '=' - so we have a key and no value
				var key = piece;
				var value = '';
			}else{
				// we found an '=' - split the string in two
				var key = piece.substr(0,a);
				var value = piece.substr(a+1);
			}

			// now display our cookies
			if(key==='login')
				obj.login=value;
			if(key==='authToken')
				obj.authToken=value;
			//alert('Key: ' + key + "  Value : "+ value);
		}
		return obj;
	}
  
	function trim(str){
		
		// trim off leading spaces
		while (str.charAt(0) == ' '){
			str = str.substring(1);
		}
		
		//trim off trailing spaces
		while (str.charAt(str.length-1) == ' '){
			str = str.substring(0,str.length-1);
		}
	
		return str;
	}

  $(document).ready(function() {
	  setTimeout(loadContactsPage,1);
  });