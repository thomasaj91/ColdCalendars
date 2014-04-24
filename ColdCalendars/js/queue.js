function getQueue() {

    var queueObject = new Object();
    
    queueObject.requestType = "ViewQueue";
    queueObject.startTime = dateObjectToDateString(new Date());
    queueObject.endTime = dateObjectToDateString(new Date("01/01/2099"));
     var list = ajaxGetJSON(queueObject);
     
     /*
     for(var e in list) {
       var li  = $('<li>').appendTo('#Display_Queue').addClass('queueItem');
       var div = $('<div>').appendTo(li)
                 .text(list[e]['pickuper'] + " wants to swap with " + list[e]['owner'] + " for " + list[e]['startTime'] + " to " + list[e]['endTime'])
                 .attr('data-owner',list[e]['owner'])
                 .attr('data-startTime',list[e]['startTime'])
                 .attr('data-endTime',list[e]['endTime']);
       $('<button>').addClass('acceptQueueItem').appendTo(div).text('+');
       $('<button>').addClass('rejectQueueItem').appendTo(div).text('-');
     }
	*/
     for(var e in list) {
         var li  = $('<li>').appendTo('#Display_Queue').addClass('queueItem');
         var div = $('<div>').appendTo(li);
         switch(list[e]['type']) {
         case 'Swap':
        	 $(div).text(list[e]['swapper']['first'] + " " + list[e]['swapper']['last'] + " wants to swap with " + list[e]['owner']['first'] + " " + list[e]['owner']['last'] + " for " + list[e]['startTime'] + " to " + list[e]['endTime'])
        	 .attr('data-swapper',list[e]['swapper']['login'])
        	 .attr('data-login',list[e]['owner']['login']);
        	 break;
         case 'Vacation':
        	 $(div).text(list[e]['first'] + " " + list[e]['last'] + " is requesting vacation time from " + list[e]['startTime'] + " to " + list[e]['endTime'])
        	 .attr('data-login',list[e]['login']);
        	 break;
         case 'TimeOff':
	         $(div).text(list[e]['first'] + " " + list[e]['last'] + " is requesting unpaid time off from " + list[e]['startTime'] + " to " + list[e]['endTime'])
	         .attr('data-login',list[e]['login']);
	         break;
         }
         $(div)
         .attr('data-startTime',list[e]['startTime'])
         .attr('data-endTime',list[e]['endTime'])
         .attr('data-type',list[e]['type']);
         $('<button>').addClass('acceptQueueItem').appendTo(div).text('+');
		 $('<button>').addClass('rejectQueueItem').appendTo(div).text('-');
       }
}

$(document).ready(function(){
	setUserType();
	hideManagerListOptions();
	getQueue();
	
    $('.acceptQueueItem').click(function(){
    	var acceptObj = new Object();

        acceptObj.userID    = $(this).parent().attr('data-login');   
        acceptObj.startTime = $(this).parent().attr('data-startTime');   
        acceptObj.endTime   = $(this).parent().attr('data-endTime');   
        acceptObj.approved  = true;
        
        switch($(this).parent().attr('data-type')) {
        case 'Swap':acceptObj.requestType = 'DecideSwap';
        			acceptObj.swapper = $(this).parent().attr('data-swapper');
        			break;
        case 'Vacation':acceptObj.requestType = 'DecideVacation'; break;
        case 'TimeOff':acceptObj.requestType = 'DecideTimeOff'; break;
        }
        
        var obj = ajaxGetJSON(acceptObj);
        
        $(this).parent().parent().remove();
    });	

    $('.rejectQueueItem').click(function(){
    	var acceptObj = new Object();
    	
        acceptObj.userID    = $(this).parent().attr('data-login');   
        acceptObj.startTime = $(this).parent().attr('data-startTime');   
        acceptObj.endTime   = $(this).parent().attr('data-endTime');   
        acceptObj.approved  = false;
        
        switch($(this).parent().attr('data-type')) {
        case 'Swap':acceptObj.requestType = 'DecideSwap';
        			acceptObj.swapper = $(this).parent().attr('data-swapper');
        			break;
        case 'Vacation':acceptObj.requestType = 'DecideVacation'; break;
        case 'TimeOff':acceptObj.requestType = 'DecideTimeOff'; break;
        }
        
        var obj = ajaxGetJSON(acceptObj);
        
        $(this).parent().parent().remove();
    });	

});
