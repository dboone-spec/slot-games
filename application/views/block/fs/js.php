<script>
    document.getElementsByClassName('preloader-container')[0].style.display = 'none';
    function set_freespins(data) {

        var xmlhttp; // наш объект ajax
        if (window.XMLHttpRequest){// для IE7+, Firefox, Chrome, Opera, Safari (новые версии)
            xmlhttp = new XMLHttpRequest(); // создаем его (аякс)
        }else{// для IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); //древний способ
        }

        xmlhttp.open("GET", "/user/setspins?game=<?php echo $game; ?>&type="+encodeURIComponent(data), false); // открываем через запрос
        xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xmlhttp.onreadystatechange = function () {
            // функция при успешном получении данных
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status==200) {
                    var ans = '{}';

                    try {
                        ans = JSON.parse(xmlhttp.responseText || '{}' );
                    } catch (err) {
                        // console.warn('send =>', err);
                    }

                    if(ans.enabled) {
                        window.game.controller.initialized();
                        document.getElementsByClassName('preloader-container')[0].style.display = 'block';
                    }
                }
            }
        };
        xmlhttp.send();
    };

    function set_freespins_future() {
        set_freespins('future');
        //удаляем выбор фриспинов
        var freespins_view = document.getElementById('change_freespins');
        freespins_view.remove();
    };

    function set_freespins_now() {
        set_freespins('now');
        //удаляем выбор фриспинов
        var freespins_view = document.getElementById('change_freespins');
        freespins_view.remove();
    };

    function set_freespins_off() {
        set_freespins('off');
        //удаляем выбор фриспинов
        var freespins_view = document.getElementById('change_freespins');
        freespins_view.remove();
    };

    var selectors = {
        freespins_play_future: "future",
        freespins_play_now: "now",
        freespins_dont_play: "off",
    };

    var el = document.getElementById("freespins_play_future");
    el.addEventListener("click", set_freespins_future, false);

    var el = document.getElementById("freespins_play_now");
    el.addEventListener("click", set_freespins_now, false);

    <?php if(auth::user()->freespins_info($game)['freespins_break'] == 0): ?>
        var el = document.getElementById("freespins_dont_play");
        el.addEventListener("click", set_freespins_off, false);
    <?php endif; ?>

    document.getElementsByTagName('h2')[1].innerText = document.title;
</script>