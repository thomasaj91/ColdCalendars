function loadContactsPage() {
	  setUserType();
	  hideManagerListOptions();
	  loadUser();
	  
	  //Create Removal Dialogs
	  createPhoneNumberRemovalList();
	  createEmailAddressRemovalList();
	  
	  //Create Priority Dialogs
	  createPhoneNumberPriorityList();
	  createEmailPriorityList();
	  
	  //Hide create/delete buttons if not admin
	  hideAdminButtons();
	  
	  $('input:text').val('');
	  
	  //Create accordion style lists
	  $("#Contact_List").accordion();
	  $("#Current_User_Info").accordion();
	  
	  $("#Create_User").click(function() {
 	  		$( "#Create_User_Dialog" ).dialog("open");
      });
	  
	  $("#Delete_User").click(function() {
		  	createDeleteDropdown();
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
	  
	  $('#Edit_Availability_Button').click(function() {
		  $('#Edit_Availability_Dialog').dialog('open');
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
		
		      									var obj = ajaxGetJSON(userObject);
		      									
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
	   		height: 250,
	   		width: 250,
	   		modal: true,
	   		resizable: false,
	   		draggable: true,
	   		buttons: { "Delete User": function() { $('#Confirm_Delete_Dialog').dialog('open'); }, 
		   		  		"Cancel": function() { $(this).dialog("close"); } }
	   });
	  
	  $('#Confirm_Delete_Dialog').dialog({
	  		autoOpen: false,
	   		height: 250,
	   		width: 400,
	   		modal: true,
	   		resizable: false,
	   		draggable: true,
	   		buttons: { 'Yes' : function() {
	   						var userObject = new Object();

						    userObject.requestType = "DeleteUser";
						    userObject.userID      = $("#DeleteLogin").val();

						    var obj = ajaxGetJSON(userObject);
					
							if(obj === null) {
								alert('unexpected server error');
							}
							else {
								var zero_found = false;
								for(var e in obj){
									if(obj[e]===0) {
										alert('invalid field: '+e);
										zero_found = true;
									}
								}
								if (!zero_found) {
									location.reload();
								}
							}
	   					},
	   				   'No'  : function() {$(this).dialog('close');}
	   			
	   		}
	  });
	  
	  $("#Edit_Availability_Dialog").dialog({
		  	
	  		autoOpen: false,
	   		height: 190,
	   		width: 850,
	   		modal: true,
	   		resizable: false,
	   		draggable: true,
	   		buttons: { "Submit": function() { 
	   						var availObject = new Object();
	   						availObject.requestType = 'AddAvailability';
	   						
	   						//alert($('#Availability_Day').val());
	   						//alert(standardToMilitaryTime($('#Availability_Start').val()));
	   						//alert(standardToMilitaryTime($('#Availability_End').val()));
	   						
	   						availObject.day   = $('#Availability_Day').val();
	   						availObject.start = standardToMilitaryTime($('#Availability_Start').val());
	   						availObject.end   = standardToMilitaryTime($('#Availability_End').val());
	   						
	   						var obj = ajaxGetJSON(availObject);	
		      			}, 
		   		  		"Cancel": function() { $(this).dialog("close"); } }
	   });
	  
	  //TODO AUSTIN: Phone priority dialog stuff
	  //See also: createPhoneNumberPriority list
	  $("#Phone_Priority_Button").click(function() {
		  $('#Phone_Priority_Dialog').dialog('open');
      });
	  
	  $('#Phone_Priority_Dialog').dialog({
		  autoOpen: false,
		  height: 400,
		  width: 407,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
					 var priorityObject = new Object();
					 priorityObject.requestType = 'PhonePriority';
					 
					 var contents = [];
					 $('#Phone_Priority_List li').each(function(i,elem) {
						 contents.push($(elem).text());
					 });
					 
					 for(var i = 0; i < contents.length; i++){
						 priorityObject.phone = contents[i];
						 priorityObject.priority = i;
						 var obj = ajaxGetJSON(priorityObject);
						 if(obj === null) {
							 alert('unexpected server error');
						  }
						  else {
							 if(obj['priority']===0)
								 alert('Error moving phone numbers around.');
						  }
					 }
					 location.reload();
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
		  }		
	  
	  });
	  $('#Phone_Priority_List').sortable();
	  $('#Phone_Priority_List').disableSelection();
	  
	  //TODO AUSTIN: Email priority dialog stuff
	  //See also: createEmailPriority list
	  $("#Email_Priority_Button").click(function() {
		  $('#Email_Priority_Dialog').dialog('open');
      });
	  
	  $('#Email_Priority_Dialog').dialog({
		  autoOpen: false,
		  height: 400,
		  width: 407,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
					 var priorityObject = new Object();
					 priorityObject.requestType = 'EmailPriority';
					 
					 var contents = [];
					 $('#Email_Priority_List li').each(function(i,elem) {
						 contents.push($(elem).text());
					 });
					 
					 for(var i = 0; i < contents.length; i++){
						 priorityObject.email = contents[i];
						 priorityObject.priority = i;
						 var obj = ajaxGetJSON(priorityObject);
						 if(obj === null) {
							 alert('unexpected server error');
						  }
						  else {
							 if(obj['priority']===0)
								 alert('Error moving email addresses around.');
						  }
					 }
					 location.reload();
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
		  }		
	  
	  });
	  $('#Email_Priority_List').sortable();
	  $('#Email_Priority_List').disableSelection();
	  
	  //Admin Edit Dialogs
	  $('.editTitle').click(function() {
		  window.editLogin = $(this).attr('data-login');
		  $('#Edit_Title_Dialog').dialog('open');
      });
	  
	  $('#Edit_Title_Dialog').dialog({
		  autoOpen: false,
		  height: 175,
		  width: 225,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
			  			var titleObject = new Object();
			  			
			  			titleObject.requestType = 'ChangeTitle';
			  			titleObject.userID 		= window.editLogin;
			  			titleObject.title		= $('#User_Title').val();
			  			
			  			var obj = ajaxGetJSON(titleObject);
			  			
						for(var e in obj){
							if(obj[e]===0) {
								alert('invalid field: '+e);
							}
						}
						
						location.reload();
					 
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
		  }		
	  
	  });
	  
	  $('.editStatus').click(function() {
		  window.editLogin = $(this).attr('data-login');
		  $('#Edit_Status_Dialog').dialog('open');
      });
	  
	  $('#Edit_Status_Dialog').dialog({
		  autoOpen: false,
		  height: 175,
		  width: 225,
		  modal: true,
		  resizable: false,
		  draggable: true,
		  buttons: { "Submit": function() {
			  			var statusObject = new Object();
			  			
			  			statusObject.requestType = 'ChangeWorkStatus';
			  			statusObject.userID 	 = window.editLogin;
			  			statusObject.workStatus	 = $('#User_Status').val();
			  			
			  			var obj = ajaxGetJSON(statusObject);
		  			
		  				for(var e in obj){
						if(obj[e]===0) {
							alert('invalid field: '+e);
						}
	  				}
				
		  			location.reload();
					 
				 }, 
				 "Cancel": function() { $(this).dialog("close"); }
		  }		
	  
	  });
	  
}

function loadUser()
  {
	    var requestObject = new Object();
	    requestObject.requestType="UserList";

		var list = ajaxGetJSON(requestObject); 

		window.allUserList = list;
		
		var elem = $('#Contact_List').empty();
		for(var e in list){
			var info   = getInfoByLogin(list[e]);
			var phones = getPhoneNumbersByLogin(list[e]);
			var emails = getEmailAddressesByLogin(list[e]);
			var availability = getAvailabilityByLogin(list[e]);
			//alert(availability);
			if(info   !== null
		    && phones !== null
		    && emails !== null) {
				appendUserDataTo(elem,info,phones,emails,list[e]);
			}
		}
		
		$('#'+parseCookie().login+'_h3').remove().appendTo($('#Current_User_Info'));
		$('#'+parseCookie().login+'_div').remove().appendTo($('#Current_User_Info'));
		
		if(window.userType == 'Admin')
		{
			$('#Current_User_Info > h3 > .editTitle').hide();
			$('#Current_User_Info > h3 > .editStatus').hide();
		}
		
		appendAddRemoveEditPhoneButton();
		appendAddRemoveEditEmailButton();
		appendEditButton();
		
		$("#Contact_List").accordion();
  }

  function appendAddRemoveEditPhoneButton() {	  
	  //'+' button
	  var addButtonData = $('<td>').appendTo('#Phone_Header');	  
	  var addPhoneButton = $('<button>').text('+').attr('id','Add_Phone_Button').appendTo(addButtonData);
	  
	  //'-' button
	  var removeButtonData = $('<td>').appendTo('#Phone_Header');
	  var removePhoneButton = $('<button>').text('-').attr('id','Remove_Phone_Button').appendTo(removeButtonData);
	  
	  //'Edit Priority' button
	  var editButtonData = $('<td>').appendTo('#Phone_Header');
	  var editPhoneButton = $('<button>').text('Edit').attr('id','Phone_Priority_Button').appendTo(editButtonData);
  }
  
  function appendAddRemoveEditEmailButton() {
	  //'+' button
	  var addButtonData = $('<td>').appendTo('#Email_Header');	  
	  var addEmailButton = $('<button>').text('+').attr('id','Add_Email_Button').appendTo(addButtonData);
	  
	  //'-' button
	  var removeButtonData = $('<td>').appendTo('#Email_Header');
	  var removeEmailButton = $('<button>').text('-').attr('id','Remove_Email_Button').appendTo(removeButtonData);
	  
	  //'Edit Priority' button
	  var editButtonData = $('<td>').appendTo('#Email_Header');
	  var editEmailButton = $('<button>').text('Edit').attr('id','Email_Priority_Button').appendTo(editButtonData);
  }
  
  function appendEditButton(){
	  var tableDataButton = $('<td>').appendTo('#Availability_Header').attr('rowspan',2);
	  $('<button>').appendTo(tableDataButton).text('Edit').attr('id','Edit_Availability_Button');
  }

  function appendUserDataTo(elem,info,phones,emails,login) {
	  var header = $('<h3>').appendTo(elem).text(info.lastName + ', ' + info.firstName).attr('id',login+'_h3');
	  
	  //If the user is an admin, add edit button
	  if(window.userType == 'Admin')
	  {
		  $('<button>').appendTo(header).text('Edit Title').attr('class','editTitle').attr('data-login',login);
		  $('<button>').appendTo(header).text('Edit Status').attr('class','editStatus').attr('data-login',login);
	  }
	  
	  var div = $('<div>').appendTo(elem).attr('id',login+'_div');
	  
	  var table =$('<table>').appendTo(div).attr('id',login+'_table').addClass('contactInfoTable');
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
		  $('<td>').appendTo(phoneRow).text(phones[e]).attr('colspan',4);
	  }
	  
	  //Format user email list
	  var emailData = $('<td>').appendTo(userRow).attr('valign','top');
	  var emailTable = $('<table>').appendTo(emailData).attr('id','Email_List').attr('border','1');
	  var emailHeaderTableRow = $('<tr>').appendTo(emailTable).attr('id','Email_Header').attr('align','center');
	  var emailHeaderTableData = $('<td>').appendTo(emailHeaderTableRow).text('Email');
	  var emailRow;
	  for(var a in emails)
	  {
		  emailRow = $('<tr>').appendTo(emailTable);
		  $('<td>').appendTo(emailRow).text(emails[a]).attr('colspan',4);
	  }
	  
	  //TODO Availability Information
	  if(info.title != 'Admin')
	  {
		  var testArray = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
		  
		  var availabilityData = $('<td>').appendTo(userRow).attr('valign','top');
		  var availabilityTable = $('<table>').appendTo(availabilityData).attr('id','Availability_List').attr('border','1');
		  var availabilityTableHeader = $('<tr>').appendTo(availabilityTable).attr('id','Availability_Header');
		  
		  for(var b in testArray)
		  {
			  $('<td>').appendTo(availabilityTableHeader).text(testArray[b]).attr('colspan',2);
		  }
		  
		  var availabilityRow = $('<tr>').appendTo(availabilityTable);
		  
		  for(var i=0; i<14; i++)
		  {
			  $('<td>').appendTo(availabilityRow).text(i);
		  }
	  }
  }
  
  function createDeleteDropdown(){
	  $('#DeleteLogin').empty();
	  for(var e in window.allUserList)
	  {
		  $('<option>').appendTo('#DeleteLogin').attr('value',window.allUserList[e]).text(window.allUserList[e]);
	  }
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
  
  function getAvailabilityByLogin(login) {
	  var getAvailability = new Object();
	  getAvailability.requestType = 'GetUserAvailability';
	  getAvailability.login = login;
	  return ajaxGetJSON(getAvailability);
	  
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
  
  function createPhoneNumberPriorityList()
  {
	  var table = document.getElementById('Phone_List');
	  var list = $('<ul>').appendTo('#Phone_Priority_Dialog').attr('id','Phone_Priority_List').attr('class','connectedSortable');
	  
	  for(var i=1; i<table.rows.length;i++)
	  {
		  $('<li>').appendTo(list).text(table.rows[i].cells[0].innerHTML).attr('class','ui-state-default').attr('id','p'+i);
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
  
  function createEmailPriorityList()
  {
	  var table = document.getElementById('Email_List');
	  var list = $('<ul>').appendTo('#Email_Priority_Dialog').attr('id','Email_Priority_List');
	  
	  for(var i=1; i<table.rows.length;i++)
	  {
		  $('<li>').appendTo(list).text(table.rows[i].cells[0].innerHTML);
	  }
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
	  
	  //Timepickers for availability dialog
	  $('#Availability_Start').timepicker();
	  
	  $('#Availability_End').timepicker();
  });
