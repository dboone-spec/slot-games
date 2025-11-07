<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>BG Live</title>

    <meta charset="utf-8"/>
    <script src="/js/jquery.js" type="text/javascript"></script>
</head>
<body>
    <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter45912849 = new Ya.Metrika({ id:45912849, clickmap:true, trackLinks:true, accurateTrackBounce:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/45912849" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
    <div id="close_game" style="position: absolute; right: 10px; padding: 10px; background-color: rgba(238,97,42,1); color: #fff;">Выход</div>

    <?php echo $content ?>
    <script>
        $('#close_game').click(function() {
            window.location = '/';
        });
    </script>
    <script type="text/javascript">
        var cbuser = {name: '<?php auth::parent_acc()->name; ?>', email: '<?php auth::parent_acc()->email; ?>', message: ''};
        var cburl = '//web.redlinehelp.com/',
            access_token = '<?php echo Arr::get(Kohana::$config->load('static.chat_tokens'),$_SERVER['HTTP_HOST'],'SjqgP1IECAQ5s6n79byJ'); ?>';
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = cburl + 'assets/cmodule-chat/js/chatbull-init.js?rand='+Math.random();
        var x = document.getElementsByTagName('head')[0]; x.appendChild(s);
    </script>

    <script>
        window.onload = function () {
                if(typeof yaCounter45912849!='undefined') {
                        yaCounter45912849.reachGoal('game_open');
                }
        }
    </script>
</body>
</html>