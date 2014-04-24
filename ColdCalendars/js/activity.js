$(document).ready(function(){
	setUserType();
	hideManagerListOptions();
	getActivityLog();
});

function getActivityLog() {
var queueObject = new Object();
    
    queueObject.requestType = "GetUserActivityLog";
    queueObject.startTime = dateObjectToDateString(new Date());
    queueObject.endTime = dateObjectToDateString(new Date("01/01/2099"));
     var list = ajaxGetJSON(queueObject);
     
     for(var e in list) {
         var li  = $('<li>').appendTo('#Display_Queue').addClass('queueItem');
         
         var div = $('<div>').appendTo(li)
                   .text(list[e]['first'] + " " + list[e]['last'] + " is requesting off from " + list[e]['startTime'] + " to " + list[e]['endTime'])
                   .attr('data-login',list[e]['login'])
                   .attr('data-startTime',list[e]['startTime'])
                   .attr('data-endTime',list[e]['endTime']);
         $('<button>').addClass('acceptQueueItem').appendTo(div).text('+');
         $('<button>').addClass('rejectQueueItem').appendTo(div).text('-');
       }
}
