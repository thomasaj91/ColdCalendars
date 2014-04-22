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

function dateObjectToDateString(date){
	var temp = $.datepicker.formatDate('yy-mm-dd', date); 
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	return $.datepicker.formatDate('yy-mm-dd', date) + " "
	     + (hours <10 ? "0"+hours : hours) + ":"
	     + (minutes <10 ? "0"+minutes : minutes) + ":"
	     + (seconds <10 ? "0"+ seconds : seconds);
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

function stringToDateObject(str){
	var obj = new Date();
	obj.setYear( parseInt(str.substr(0,4)));
	obj.setMonth(parseInt(str.substr(5,2))-1,
			     parseInt(str.substr(8,2)));
	obj.setHours(parseInt(str.substr(11,2)),
			     parseInt(str.substr(14,2)),
			     parseInt(str.substr(17,2)),
			     0);
	return obj;
}

function setUserType() {
	var requestObject = new Object();
	requestObject.requestType = "UserInfo";
	requestObject.userID = parseCookie().login;

	var retval = ajaxGetJSON(requestObject);

	window.userType = retval['title'];
}
/*
$("#Logout").click(function () {
	  logUserOut();
});
*/
function logUserOut() {
    var obj = new Object();
	    obj.requestType = "LogoutUser";
	    $retJson = ajaxGetJSON(obj);
	    window.location.href = "home.php";
}

function parseTime(timeString) {
	  if (timeString == '') return null;
	  var d = new Date();
	  var time = timeString.match(/(\d+)(:(\d\d))?\s*(p?)/);
	  d.setHours( parseInt(time[1])===12 && time[4]!=='p'?0:(parseInt(time[1]) + ( ( parseInt(time[1]) < 12 && time[4] ) ? 12 : 0)) );
	  d.setMinutes( parseInt(time[3]) || 0 );
	  d.setSeconds(0, 0);
	  return d;
} 

function standardToMilitaryTime(timeString)
{
	if (timeString == '') return null;
	
    var time = timeString.match(/(\d+)(:(\d\d))?\s*(p?)/);
    var militaryTimeString;
    var hours = parseInt(time[1])===12 && time[4]!=='p'?0:(parseInt(time[1]) + ( ( parseInt(time[1]) < 12 && time[4] ) ? 12 : 0));
    var minutes = parseInt(time[3]) || 0 ;
    
    if(hours<10) { hours = '0' + hours;}
    if(minutes===0){ minutes = '0' + minutes;}

    militaryTimeString = hours + ':' + minutes;

    
    return militaryTimeString;
}

function hideManagerListOptions()
{
	if(window.userType != 'Manager')
	{
		$('#Navbar_Options li').eq(3).hide();
		$('#Navbar_Options li').eq(4).hide();
	}
}
