<script>


    function jsonp_request(url, callback, err_callback) {
        var callbackName = 'jsonp_callback_' + Math.round(100000 * Math.random());
        window[callbackName] = function(data) {
            delete window[callbackName];
            document.body.removeChild(script);
            callback(data);
        };

        var script = document.createElement('script');
        script.src = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'callback=' + callbackName;
        script.onerror = err_callback;
        document.body.appendChild(script);
    }

    function json_request(url, callback, err_callback) {
        var xmlhttp; // наш объект ajax
        if (window.XMLHttpRequest){// для IE7+, Firefox, Chrome, Opera, Safari (новые версии)
            xmlhttp = new XMLHttpRequest(); // создаем его (аякс)
        }else{// для IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); //древний способ
        }

        xmlhttp.open("GET", url+(url.indexOf('?') >= 0 ? '&' : '?')+'r='+Math.round(100000 * Math.random()), true); // открываем через запрос
        

        xmlhttp.onreadystatechange = function () {
            // функция при успешном получении данных
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status==200) {
                    callback(xmlhttp.responseText);
                }
                else {
                    err_callback();
                }
            }
        };

        xmlhttp.send();
    }

    function err_mess_player() {

        if(!document.getElementById('loginajax_form')) {
            return;
        }

        var m = document.getElementById('err_mess_player');
        if(!m) {
            m = document.createElement('div');
            m.id='err_mess_player';
            m.style.color='red';
            document.getElementById('loginajax_form').parentNode.insertBefore(m,document.getElementById('loginajax_form').nextSibling);
        }
        m.innerHTML='Player not found';
    }

    function rfid_listen() {

        var $fname = navigator.userAgent.indexOf('Windows')?jsonp_request:json_request;
        <?php if(KIOSK): ?>
            $fname=json_request;
        <?php endif; ?>

        $fname("http://localhost:8686/",function(response) {
            if($fname==json_request) {
                response = JSON.parse(response);
            }
            <?php if(!auth::$user_id): ?>
                response.code!="0" && json_request('/login/rfid/'+response.code,
                    function(d) {
                        if(d=='ok') {
                            window.location='/';
                        }
                        else {
                            err_mess_player();
                        }
                    },
                    err_mess_player
                );
            <?php elseif(auth::user()->rfid): ?>
                if((response.code!="<?php echo auth::user()->rfid; ?>") || (response.code && response.code=="0")) {
                    window.location='/login/logout';
                }
            <?php endif; ?>
        },function() {
            <?php if(auth::$user_id && auth::user()->rfid): ?>
                window.location='/login/logout';
            <?php endif; ?>
        });

//        $.ajax({
//            url: "http://localhost:8686",
//            jsonp: "callback",
//            dataType: navigator.userAgent.indexOf('Windows')?"jsonp":'json',
//            cache: "false",
//            success: function( response ) {
//                <?php if(!auth::$user_id): ?>
//                    response.code!="0" && $.ajax({
//                        url:'/login/rfid/'+response.code,
//                        success: function(d) {
//                            if(d=='ok') {
//                                <?php if($is_person): ?>
//                                    window.location='/enter';
//                                <?php else: ?>
//                                    window.location='/';
//                                <?php endif; ?>
//                            }
//                            else {
//                                alert('Игрок не найден');
//                            }
//                        }
//                    });
//                <?php else: ?>
//                    if((response.code!="<?php echo auth::user()->rfid; ?>") || (response.code && response.code=="0")) {
//                        window.location='/login/logout';
//                    }
//                <?php endif; ?>
//            },
//            error: function (jqXHR, exception, err) {
//                <?php if(auth::$user_id): ?>
//                    window.location='/login/logout';
//                <?php endif; ?>
//            }
//        });

        setTimeout(rfid_listen,4000);
    }

    document.addEventListener('DOMContentLoaded', function(){
        rfid_listen();
    });
</script>