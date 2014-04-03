function getQueue() {

    var queueObject = new Object
    
    queueObject.requestType = "View_Queue";
    
    var retVal = $.ajax({
        url: "rest.php",
        data: "json=" + JSON.stringify(queueObject),
        dataType: "json",
        async: false
    });
    
     var list = jQuery.parseJSON(retVal.responseText);
     
     for(var e in list) {
          switch(e.type){
          case 'Swap':
                  var li = $('li').appendTo('#masterQueue');
                  $('div').appendTo(li).text(e.login + " wants to swap with " + e.pickeruper)
                  .attr('data-login',thing.login)
                  .attr('data-type',thing.type)
                  .attr;
                  $('button').class('acceptQueueItem').appendTo(li);
                  $('button').class('rejectQueueItem').appendTo(li);
          }
  }
  
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

}


