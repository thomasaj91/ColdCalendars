function getActivityLog() {
    var logObject = new Object();
    
    logObject.requestType = window.userType==='Manager' ? 'GetFullActivityLog' : 'GetUserActivityLog';
    logObject.startTime   = dateObjectToDateString(new Date());
    logObject.endTime     = dateObjectToDateString(new Date('01/01/2099'));
    var list = ajaxGetJSON(logObject);
     
    for(var e in list) {
      var li  = $('<li>').appendTo('#Display_Queue').addClass('queueItem');
      var div = $('<div>').appendTo(li);
      switch(list[e]['type']) {
         case 'Swap':
        	 $(div).text("Swap with "+list[e]['swapper']['first'] + " " + list[e]['swapper']['last'] + " " + " for " + list[e]['startTime'] + " to " + list[e]['endTime'] + " was " + (list[e]['approved']?"Approved":"Rejected"))
        	 .attr('data-swapper',list[e]['swapper']['login'])
        	 .attr('data-login',list[e]['owner']['login']);
        	 break;
         case 'Vacation':
        	 $(div).text("Vacation time from " + list[e]['startTime'] + " to " + list[e]['endTime'] + " was " + (list[e]['approved']?"Approved":"Rejected"))
        	 .attr('data-login',list[e]['login']);
        	 break;
         case 'TimeOff':
	         $(div).text("Unpaid time off from " + list[e]['startTime'] + " to " + list[e]['endTime'] + " was " + (list[e]['approved']?"Approved":"Rejected"))
	         .attr('data-login',list[e]['login']);
	         break;
       }
       $(div)
         .attr('data-startTime',list[e]['startTime'])
         .attr('data-endTime',list[e]['endTime'])
         .attr('data-type',list[e]['type']);
//         $('<button>').addClass('acceptQueueItem').appendTo(div).text('+');
//		 $('<button>').addClass('rejectQueueItem').appendTo(div).text('-');
     }
}

$(document).ready(function(){
	setUserType();
	hideManagerListOptions();
	getActivityLog();
});
