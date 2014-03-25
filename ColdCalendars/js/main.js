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

