function loadContactsPage() {
	  hideOnLoad();
	  loadUser();
	  
	  $('input:text').val('');
	  
	  $("#Contact_List").accordion();
	  $("#Current_User_Info").accordion();
	  $("#Current_User_Info > div > ul").sortable();
	  $("#Create_User").click(function() {
 	  		$( "#Create_User_Dialog" ).dialog("open");
      });
	  
	  $("#Logout").click(function () {
		  window.location.href = "home.php";
	  });
	  
	  $("#Delete_User").click(function() {
	  		$( "#Delete_User_Dialog" ).dialog("open");
      });
	  
	  $("#Add_Phone_Button").one('click', function() {
		  var newPhone = $('<input>').insertAfter('#Current_User_Info > div > table > tbody > tr > td > .phoneList').attr('id','New_Number');
		  $('<button>').insertAfter(newPhone).text('Submit').attr('id','Submit_Phone');
      });
	  
	  $(document).on('click', '#Submit_Phone', function(){
		  var phoneObject = new Object();
		  
		  phoneObject.requestType = "AddPhone";
		  phoneObject.phone		  = $('#New_Number').val();
		  
		  var retVal = $.ajax({
				 url: "rest.php",
				 data: "json="+JSON.stringify(phoneObject),
				 dataType: "json",
				 async: false
		  });
		  
		 var obj = jQuery.parseJSON(retVal.responseText);
		 if(obj === null) {
			alert('unexpected server error');
		 }
		 else {
			 if(obj['phone']===0)
				 alert('Invalid phone number. Please try again.');
			 else
				 location.reload();
		 }
      });
	  
	  $("#Add_Email_Button").one('click', function() {
		  var newEmail = $('<input>').insertAfter('#Current_User_Info > div > table > tbody > tr > td > .emailList').attr('id','New_Email');
		  $('<button>').insertAfter(newEmail).text('Submit').attr('id','Submit_Email');
      });
	  
	  $(document).on('click', '#Submit_Email', function(){
		  var emailObject = new Object();
		  
		  emailObject.requestType = "AddEmail";
		  emailObject.email		  = $('#New_Email').val();
		  
		  var retVal = $.ajax({
				 url: "rest.php",
				 data: "json="+JSON.stringify(emailObject),
				 dataType: "json",
				 async: false
		  });
		  
		 var obj = jQuery.parseJSON(retVal.responseText);
		 if(obj === null) {
			alert('unexpected server error');
		 }
		 else {
			 if(obj['email']===0)
				 alert('Invalid email address. Please try again.');
			 else
				 location.reload();
		 }
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
		
		appendAddRemovePhoneButton();
		appendAddRemoveEmailButton();
		
		$("#Contact_List").accordion();
  }

  function appendAddRemovePhoneButton() {	  
	  //'+' button
	  var addButtonData = $('<td>').appendTo('#Phone_Header');	  
	  var addPhoneButton = $('<button>').text('+').attr('id','Add_Phone_Button').appendTo(addButtonData);
	  
	  //'-' button
	  var removeButtonData = $('<td>').appendTo('#Phone_Header');
	  var removePhoneButton = $('<button>').text('-').attr('id','Remove_Phone_Button').appendTo(removeButtonData);
  }
  
  function appendAddRemoveEmailButton() {
	  //'+' button
	  var addButtonData = $('<td>').appendTo('#Email_Header');	  
	  var addEmailButton = $('<button>').text('+').attr('id','Add_Email_Button').appendTo(addButtonData);
	  
	  //'-' button
	  var removeButtonData = $('<td>').appendTo('#Email_Header');
	  var removeEmailButton = $('<button>').text('-').attr('id','Remove_Email_Button').appendTo(removeButtonData);
  }

  function appendUserDataTo(elem,info,phones,emails,login) {
	  $('<h3>').appendTo(elem).text(info.lastName + ', ' + info.firstName).attr('id',login+'_h3');
	  var div = $('<div>').appendTo(elem).attr('id',login+'_div');
	  
	  var table =$('<table>').appendTo(div).attr('id',login+'_table');
	  var userRow = $('<tr>');
	  
	  //Format user title/work status
	  var employeeData = $('<td>').appendTo(userRow).attr('valign','top');
	  $('<p>').appendTo(employeeData).text('Title: ' + info.title)
	  $('<p>').appendTo(employeeData).text('Work Status: '+ (info.workStatus ? 'FT' : 'PT'));
	  
	  //Format user phone list
	  var phoneData = $('<td>').appendTo(userRow).attr('valign','top');
	  var phoneHeaderTable = $('<table>').appendTo(phoneData);
	  var phoneHeaderTableRow = $('<tr>').appendTo(phoneHeaderTable).attr('id','Phone_Header');
	  var phoneHeaderTableData = $('<td>').appendTo(phoneHeaderTableRow);
	  var phoneHeader = $('<p>').appendTo(phoneHeaderTableData).text('Phone');
	  var li1 = $('<ul>').insertAfter(phoneHeaderTable).attr('class','phoneList');
	  for(var e in phones)
		  $('<li>').appendTo(li1).text(phones[e]);
	  
	  //Format user email list
	  var emailData = $('<td>').attr('valign','top');
	  var emailHeaderTable = $('<table>').appendTo(emailData);
	  var emailHeaderTableRow = $('<tr>').appendTo(emailHeaderTable).attr('id','Email_Header');
	  var emailHeaderTableData = $('<td>').appendTo(emailHeaderTableRow);
	  var emailHeader = $('<p>').appendTo(emailHeaderTableData).text('Email');
	  var li2 = $('<ul>').insertAfter(emailHeaderTable).attr('class','emailList');
	  for(var a in emails)
		  $('<li>').appendTo(li2).text(emails[a]);
	  userRow.append(emailData);
	  
	  table.append(userRow);  
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
  
  function callback(param) {
	  alert(param);
  }
  
  function logUserOut() {
	    var obj = new Object();
	    obj.requestType = "LogoutUser";
	    $retJson = ajaxGetJSON(obj);
	    window.location.href = "home.php";
	  }
  
  $(document).ready(function() {
	  setTimeout(loadContactsPage,1);
  });
