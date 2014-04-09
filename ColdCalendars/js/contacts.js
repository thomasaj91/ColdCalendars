function loadContactsPage() {
	  loadUser();
	  createPhoneNumberRemovalList();
	  createEmailAddressRemovalList();
	  setUserType();
	  hideAdminButtons();
	  
	  $('input:text').val('');
	  
	  $("#Contact_List").accordion();
	  $("#Current_User_Info").accordion();
	  $("#Create_User").click(function() {
 	  		$( "#Create_User_Dialog" ).dialog("open");
      });
	  
	  $("#Logout").click(function () {
		  logUserOut();
	  });
	  
	  $("#Delete_User").click(function() {
	  		$( "#Delete_User_Dialog" ).dialog("open");
      });
	  
	  //Add/delete phone numbers
	  $('#Add_Phone_Button').click(function() {
		  $('#Add_Phone_Dialog').dialog('open');
      });
	  
	  $('#Add_Phone_Dialog').dialog({
		  autoOpen: false,
		  height: 175,
		  width: 407,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
						  var phoneObject = new Object();
						  
						  phoneObject.requestType = "AddPhone";
						  phoneObject.phone		  = $('#New_Number').val();
						  
						  var obj = ajaxGetJSON(phoneObject);
						  
						  if(obj === null) {
							 alert('unexpected server error');
						  }
						  else {
							 if(obj['phone']===0)
								 alert('Invalid phone number. Please try again.');
							 else
								 location.reload();
						  }
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
		  }		
	  });
	  
	  $('#Remove_Phone_Button').click(function() {
		  $('#Remove_Phone_Dialog').dialog('open');
      });
	  
	  $('#Remove_Phone_Dialog').dialog({
		  autoOpen: false,
		  height: 400,
		  width: 407,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
					  
			  		  var phoneObject = new Object();
			  		  phoneObject.requestType = 'RemovePhone';
			  		  
					  var checkBoxes = document.forms['Phone_Number_Remove_List'].elements['numbers'];
					  
					  //Loop thru list, find checked checkboxes/send phone # to server
					  for(var i=0;i<checkBoxes.length;i++){
					    if(checkBoxes[i].checked){
					      phoneObject.phone = checkBoxes[i].value;
					      
					      var obj = ajaxGetJSON(phoneObject);
					      
						  if(obj === null) {
								 alert('unexpected server error');
						  }
						  else if(obj['phone']===0)
						  {
							 alert('Invalid phone number. Unable to remove.');
					      }
					    } 
					  }
					  location.reload();				
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
		  }		
	  });
	  
	  //Add/delete email addresses
	  $("#Add_Email_Button").click(function() {
		  $('#Add_Email_Dialog').dialog('open');
      });
	  
	  $('#Add_Email_Dialog').dialog({
		  autoOpen: false,
		  height: 175,
		  width: 407,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
						  var emailObject = new Object();
						  
						  emailObject.requestType = "AddEmail";
						  emailObject.email		  = $('#New_Email').val();
						  
						  var obj = ajaxGetJSON(emailObject);
						  
						  if(obj === null) {
							 alert('unexpected server error');
						  }
						  else {
							 if(obj['email']===0)
								 alert('Invalid email address. Please try again.');
							 else
								 location.reload();
						  }
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
		  }		
	  });
	  
	  $('#Remove_Email_Button').click(function() {
		  $('#Remove_Email_Dialog').dialog('open');
      });
	  
	  $('#Remove_Email_Dialog').dialog({
		  autoOpen: false,
		  height: 400,
		  width: 407,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
					  //var values = [];
					  var emailObject = new Object();
			  		  emailObject.requestType = 'RemoveEmail';
			  		  
					  var checkBoxes = document.forms['Email_Address_Remove_List'].elements['emails'];
					  for(var i=0;i<checkBoxes.length;i++){
					    if(checkBoxes[i].checked){
					      emailObject.email = checkBoxes[i].value;
					      
					      var obj = ajaxGetJSON(emailObject);
					      
						  if(obj === null) {
								 alert('unexpected server error');
						  }
						  else if(obj['phone']===0)
						  {
							 alert('Invalid email address. Unable to remove.');
					      }
					    } 
					  }
					  location.reload();
			  					
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
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
	  var userRow = $('<tr>').appendTo(table);
	  
	  //Format user title/work status
	  var employeeData = $('<td>').appendTo(userRow).attr('valign','top');
	  $('<p>').appendTo(employeeData).text('Title: ' + info.title)
	  $('<p>').appendTo(employeeData).text('Work Status: '+ (info.workStatus ? 'FT' : 'PT'));
	  
	  //Format user phone list
	  var phoneData = $('<td>').appendTo(userRow).attr('valign','top');
	  var phoneTable = $('<table>').appendTo(phoneData).attr('id','Phone_List').attr('border','1');
	  var phoneHeaderTableRow = $('<tr>').appendTo(phoneTable).attr('id','Phone_Header').attr('align','center');
	  var phoneHeaderTableData = $('<td>').appendTo(phoneHeaderTableRow).text('Phone');
	  var phoneRow;
	  for(var e in phones)
	  {
		  phoneRow = $('<tr>').appendTo(phoneTable);
		  $('<td>').appendTo(phoneRow).text(phones[e]).attr('colspan','3');
	  }
/*	  var phoneData = $('<td>').appendTo(userRow).attr('valign','top');
	  var phoneHeaderTable = $('<table>').appendTo(phoneData);
	  var phoneHeaderTableRow = $('<tr>').appendTo(phoneHeaderTable).attr('id','Phone_Header');
	  var phoneHeaderTableData = $('<td>').appendTo(phoneHeaderTableRow);
	  var phoneHeader = $('<p>').appendTo(phoneHeaderTableData).text('Phone');
	  var li1 = $('<ul>').insertAfter(phoneHeaderTable).attr('class','phoneList');
	  for(var e in phones)
		  $('<li>').appendTo(li1).text(phones[e]);*/
	  
	  //Format user email list
	  var emailData = $('<td>').appendTo(userRow).attr('valign','top');
	  var emailTable = $('<table>').appendTo(emailData).attr('id','Email_List').attr('border','1');
	  var emailHeaderTableRow = $('<tr>').appendTo(emailTable).attr('id','Email_Header').attr('align','center');
	  var emailHeaderTableData = $('<td>').appendTo(emailHeaderTableRow).text('Email');
	  var emailRow;
	  for(var a in emails)
	  {
		  emailRow = $('<tr>').appendTo(emailTable);
		  $('<td>').appendTo(emailRow).text(emails[a]).attr('colspan','3');
	  }
/*	  var emailData = $('<td>').attr('valign','top');
	  var emailHeaderTable = $('<table>').appendTo(emailData);
	  var emailHeaderTableRow = $('<tr>').appendTo(emailHeaderTable).attr('id','Email_Header');
	  var emailHeaderTableData = $('<td>').appendTo(emailHeaderTableRow);
	  var emailHeader = $('<p>').appendTo(emailHeaderTableData).text('Email');
	  var li2 = $('<ul>').insertAfter(emailHeaderTable).attr('class','emailList');
	  for(var a in emails)
		  $('<li>').appendTo(li2).text(emails[a]);
	  userRow.append(emailData);
	  
	  table.append(userRow);  */
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
  
  function callback(param) {
	  alert(param);
  }
  
  function createPhoneNumberRemovalList()
  {
	  var table = document.getElementById('Phone_List');
	  var input;
	  var label;
	  var number;
	  
	  for(var i=1; i<table.rows.length;i++)
	  {
		  input = $('<input>');
		  label = $('<label>');
		  number = table.rows[i].cells[0].innerHTML;
		  input.appendTo('#Phone_Number_Remove_List').attr('type','checkbox').attr('value',number).attr('name','numbers');
		  label.insertAfter(input).text(number);
		  $('<br>').insertAfter(label);
	  }
  }
  
  function createEmailAddressRemovalList()
  {
	  var table = document.getElementById('Email_List');
	  var input;
	  var label;
	  var email;
	  
	  for(var i=1; i<table.rows.length;i++)
	  {
		  input = $('<input>');
		  label = $('<label>');
		  email = table.rows[i].cells[0].innerHTML;
		  input.appendTo('#Email_Address_Remove_List').attr('type','checkbox').attr('value',email).attr('name','emails');
		  label.insertAfter(input).text(email);
		  $('<br>').insertAfter(label);
	  }
  }
  
  function logUserOut() {
        var obj = new Object();
 	    obj.requestType = "LogoutUser";
  	    $retJson = ajaxGetJSON(obj);
  	    window.location.href = "home.php";
  }
  
  function hideAdminButtons()
  {
	    //If not an Admin, hide Create/Delete user buttons
	  	if(window.userType != 'Admin')
	  	{
	  		$('#Create_User').hide();
	  		$('#Delete_User').hide();
	  	}
  }

  
  $(document).ready(function() {
	  setTimeout(loadContactsPage,1);
  });
