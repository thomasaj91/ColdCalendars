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
         
         var div = $('<div>').appendTo(li)
                   .text(list[e]['login'] + " is requesting off from " + list[e]['startTime'] + " to " + list[e]['endTime'])
                   .attr('data-login',list[e]['login'])
                   .attr('data-startTime',list[e]['startTime'])
                   .attr('data-endTime',list[e]['endTime']);
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
    	acceptObj.requestType = 'DecideSwap';
        acceptObj.userID    = $(this).parent().attr('data-owner');   
        acceptObj.startTime = $(this).parent().attr('data-startTime');   
        acceptObj.endTime   = $(this).parent().attr('data-endTime');   
        acceptObj.approved  = true;
        var obj = ajaxGetJSON(acceptObj);
        
        $(this).parent().parent().remove();
    });	

    $('.rejectQueueItem').click(function(){
    	var acceptObj = new Object();
    	acceptObj.requestType = 'DecideSwap';
        acceptObj.userID    = $(this).parent().attr('data-owner');   
        acceptObj.startTime = $(this).parent().attr('data-startTime');   
        acceptObj.endTime   = $(this).parent().attr('data-endTime');   
        acceptObj.approved  = false;
        var obj = ajaxGetJSON(acceptObj);
        
        $(this).parent().parent().remove();
    });	

});
