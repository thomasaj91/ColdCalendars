function getQueue() {

    var queueObject = new Object();
    
    queueObject.requestType = "ViewQueue";
    queueObject.startTime = dateObjectToDateString(new Date());
    queueObject.endTime = dateObjectToDateString(new Date("01/01/2099"));
     var list = ajaxGetJSON(queueObject);
     
     for(var e in list) {
    	 alert(list[e].login);
    	 /*
          switch(e.type){
          case 'Swap':
                  var li = $('li').appendTo('#Display_Queue');
                  $('div').appendTo(li).text(list[e].login + " wants to swap with " + list[e].pickeruper)
                  .attr('data-login',thing.login)
                  .attr('data-type',thing.type)
                  .attr;
                  $('button').class('acceptQueueItem').appendTo(li);
                  $('button').class('rejectQueueItem').appendTo(li);
          }*/
     }
  /*
  $('.acceptQueueItem').click(function(){
          $(this).parent().objecthatwearelookingfor   
          var login = $(thing).attr('data-login');
          var type  = $(thing).attr('data-type');
          switch(){
          var login = $(thing).attr('data-starttime');
          var login = $(thing).attr('data-startend');
          }
     $(this).parent.remove(0);
  }
*/
}

$(document).ready(function(){
	getQueue();
});
