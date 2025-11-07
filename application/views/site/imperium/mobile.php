<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title><?php echo $name ?></title>

    <meta charset="utf-8"/>

<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
     height=device-height, 

     цвет статусбара для apple 
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>

     фулскрин для apple 
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="mobile-web-app-capable" content="yes"/>
    
    <link rel="stylesheet" href="style/preloader/index.css"/>
    <link rel="stylesheet" href="style/fonts/index.css"/>
     предзагрузка сплешскрина для device  
    <link rel="prefetch" href="images/device/splashscreen.jpg"/>

     title icon 
    <link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon.png"/>-->

    <style>
        html, body, iframe {
            margin: 0;
            padding: 0;
            height : 100%;
        }
        iframe, div {
            display: block;
            width: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <script>
        function closeGame() { 
            location = '/';
        }

        window.close=closeGame;
    	window.gclose=closeGame;
    	window.onmessage=function(event) {
            if (event.data=='closeGame' || event.data=='close') {
                closeGame();
            }
        }
    </script>
    <?php echo $content ?>
</body>
</html>