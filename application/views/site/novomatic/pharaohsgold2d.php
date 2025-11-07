<!DOCTYPE HTML>
<html lang="ru">
    <head>
        <title>Pharaon's Gold II Deluxe</title>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon.png" />
        <link rel="stylesheet" href="../common/css/style.css?ver=<?php echo th::ver(); ?>">
        <style>

            @font-face {
                font-family:'A1010Helvetika-Bold';
                src: url('../common/fonts/A101HLVB.ttf');
            }
            @font-face {
                font-family:'Semibold';
                src: url('../common/fonts/ACaslonPro-Semibold.otf');
            }
            @font-face {
                font-family:'Arial';
                src: url('../common/fonts/A101HLVB.ttf');
            }


            div#credit {
                width: 3.5%;
                height: 4%;
                top: 86%;
                left: 69%;
            }
            div#redbtn, div#blackbtn {
                top: 51.8%;
            }
            .block-wrap {
                background: url('images/splashscreen.jpg') no-repeat fixed center center / contain #000;
            }
        </style>
        <script>
                window.gameid = 'pharaohsgold2d';
                static_domain = '<?php echo kohana::$config->load('static.static_domain'); ?>';
	</script>
	<script src="pharaohsgold2d.min.js?ver=<?=th::ver()?>" type="text/javascript"></script>
    </head>
    <body>
        <div id="font-preload">
            <span style="font-family:A1010Helvetika-Bold">abc</span>
            <span style="font-family:Open Sans Extrabold">abc</span>
            <span style="font-family:Semibold">abc</span>
        </div>
        <div class="wrapper">
            <div class="canvas-container">
                <div id="sound" class="btn">
                    <div><span>Sound</span></div>
                </div>
                <div id="start" class="btn">
                    <span><span>Start</span><hr><span>Skip</span><hr><span>Take&nbsp;Win</span></span>
                </div>
                <div id="info" class="btn">
                    <span>Info</span>
                </div>
                <div id="select" class="btn">
                    <span>Exit</span>
                </div>
                <div id="autostart" class="btn">
                    <span>Auto</span>
                </div>
                <div id="fullscreen" class="btn">
                    <div><span>Full&nbsp;Screen</span></div>
                </div>
                <div id="bet-max" class="btn">
                    <span>Bet&nbsp;max</span>
                </div>
                <div id="gamble" class="btn disabled" disabled="disabled">
                    <span>Gamble<hr></span>
                </div>
                <div id="lines" class="btn">
                    <span id="linesName">Lines</span>
                    <div id="linesValue"></div>
                    <div id="linesP" class="P"></div>
                    <div id="linesM" class="M"></div>
                </div>
                <div id="bet-line" class="btn">
                    <span id="bet-lineName" style="width: 33%; top: 44%; line-height: 1; left: 10%;">Bet<wbr>Line</span>
                    <div id="bet-lineValue">1</div>
                    <div id="bet-lineP" class="P"></div>
                    <div id="bet-lineM" class="M"></div>
                </div>
                <div id="credit"></div>
                <div id="redbtn"></div>
                <div id="blackbtn"></div>
                <canvas id="canvas"></canvas>
            </div>
        </div>
        <div class="block-wrap">
            <div id="loading"></div>
            <div id="progress-panel">
                <span id="progress-info"><?php echo __('Загрузка&nbsp;файлов') ?>...</span>
                <div id="progress-bar">
                    <span id="progress" style="width: 0%"></span>
                </div>
            </div>
            <div class="btn startGame"><span><?php echo __('Начать&nbsp;игру') ?></span></div>
        </div>
        <!--<script src="/webSocketConsole/webSocketConsole.js"></script>-->
<?php echo block::rfid_listen(); ?>
    </body>
</html>