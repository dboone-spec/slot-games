<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>Crazy Monkey</title>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon.png" />
    <link rel="stylesheet" href="../common/css/stylenovomatic.css?ver=<?php echo th::ver(); ?>">
	<style>
		/*Используються ***/
		@font-face {
			font-family:'Comic Sans MS Bold';
			src: url('../common/fonts/comicbd.ttf');
		}

		@font-face {
			font-family:'Hatten';
			src: url('../common/fonts/hatten.ttf');
		}

		@font-face {
			font-family:'Iskoola Pota Regular';
			src: url('../common/fonts/IskoolaPotaRegular.ttf');
		}

		@font-face {
			font-family:"Din PRO Bold";
			src: url("../common/fonts/DinPROBold.ttf") ;
		}
		@font-face {
			font-family:"Cent";
			src: url("../common/fonts/Cent.ttf") ;
		}
		/* используются ***/

        .block-wrap {
			background: url('images/splashscreen.jpg') no-repeat fixed center center / contain #000;
		}
	/* Управляем блоком 1 CREDIT = ... */
		div#credit {
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            -webkit-tap-highlight-color: transparent;
			top: -5%;
			left: -5%;
			width:18%;
			height:5%;
			z-index: 0;

		}
		div#card0, div#card1, div#card2, div#card3{
            width: 11.1%;
            height: 31.7%;
            left: 31.9%;
            top: 25.3%;
			border-radius: 5px;
			position: absolute;
			cursor: pointer;
			display: none;
			z-index: 10;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            -webkit-tap-highlight-color: transparent;
		}
		div#card1{
			left: 45%;
		}
		div#card2{
			left: 57.8%;
		}
		div#card3{
			left: 70.6%;

		}

		div#rope0, div#rope1, div#rope2, div#rope3, div#rope4{
			width: 3.1%;
			height: 65.7%;
			left: 30.8%;
			top: 10.3%;
			border-radius: 5px;
			 z-index: 100;
			/*	background-color:rgba(255, 255, 255, 0.5);	*/

            -webkit-tap-highlight-color: rgba(0,0,0,0);
            -webkit-tap-highlight-color: transparent;
			position: absolute;
			cursor: pointer;
			display:none;

		}
		div#block_op {/* екран блокировки в играх на удвоение и бонусной*/
			width:100%;
			height:100%;
			position:absolute;
			display:none;
			z-index: 99999;
			/*background-color:rgba(255, 255, 255, 0.5);*/
		}

		div#container1, div#container0 {
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            -webkit-tap-highlight-color: transparent;
			left:23%;
			top:64%;
			width:15%;
			height:23%;
			position:absolute;
			display:none;
			cursor:pointer;
			 z-index: 100;
			/*background-color:rgba(255, 255, 255, 0.5);*/
		}

		div#container1 {
			left:62%;

		}

		div#rope1{
			left: 40.1%;
		}
		div#rope2{
			left: 49.3%;
		}
		div#rope3{
			left: 58.5%;
		}
		div#rope4{
			left:67.5%;
		}

		div.bt {
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            -webkit-tap-highlight-color: transparent;
            left: 17%;
            top: 81%;
            width: 15.7%;
            height: 12%;
			position:absolute;
			display:none;
			cursor:pointer;
			z-index: 10;

		}
		div.btExit {
			left: 42%;
            height: 10%;
		}
		div.btNext {
			left:67%;
		}
        .mobile #lines, .mobile #bet-line, .mobile #bet-total {
            opacity: 0;
            display: none;
        }
        .mobile #lines.scaled, .mobile #bet-line.scaled, .mobile #bet-total.scaled {
            left: 22.8%;
            width: 11.6%;
            -webkit-box-shadow: 0px 0px 4px #000 inset;
            -moz-box-shadow: 0px 0px 4px #000 inset;
            box-shadow: 0px 0px 4px #000 inset;
            font-size: 140%;
            transform: scale(1.9);
            top: 82.6%;
            opacity: 1;
            display: block;
        }
        .mobile #lines #linesValue, .mobile #bet-line #bet-lineValue, .mobile #bet-total > #bet-totalValue {
            height: 100%;
        }
        .mobile #bet-line.scaled {
            left: 45.9%;
        }
        .mobile #bet-total.scaled {
            left: 68%;
            width: 8.9%;
        }
        .mobile .btn {
            width: 12.8%;
        }
        .mobile #info {
            left: 30.5%;
        }
        .mobile #gamble{
            right: 30.7%;
        }
        #betshow {
            display: none;
        }
        .mobile #betshow {
            display: block;
            left: 43.6%;
            text-align: center;
            font-size: 200%;
            z-index: 2;
            background: #FF7400;
            background: -moz-linear-gradient(to top, #f2f24b 0, #eddb1f 23%, #ff8f00 53%, #ff8d00 81%, #ff8d00 91%, #ff9b00 100%);
            background: -webkit-linear-gradient(to top, #f2f24b 0, #eddb1f 23%, #ff8f00 53%, #ff8d00 81%, #ff8d00 91%, #ff9b00 100%);
            background: linear-gradient(to bottom, #f2f24b 0, #eddb1f 23%, #ff8f00 53%, #ff8d00 81%, #ff8d00 91%, #ff9b00 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f7d200', endColorstr='#f2f24b', GradientType=0);
        }
        .mobile #lines > div.M, .mobile #bet-line > div.M, .mobile #lines > div.P, .mobile #bet-line > div.P {
            top: 5%;
        }
        .mobile #lines > div.P, .mobile #bet-line > div.P {
            width: 32%;
        }
        /* все закомментированное - под слотмашину
        #bet-lineV {
            width: 350px;
        }
        .P1,.P3,.P5,.P7,.P9 {
            background: none;
            top: 79.1%;
            left: 25.3%;
            border-radius: 60%;
            height: 7.3%;
            width: 5.3%;
            -webkit-transform: skewX(-18deg);
            border: none;
        }
        .P1.disabled,.P3.disabled,.P5.disabled,.P7.disabled,.P9.disabled {
            background-color: green !important;
            opacity: 0.3;
        }
        .P3 {
            left: 35.9%;
            -webkit-transform: skewX(-9deg);
        }
        .P5 {
            left: 46.5%;
            -webkit-transform: skewX(0);
        }
        .P7 {
            left: 57%;
            -webkit-transform: skewX(9deg);
        }
        .P9 {
            left: 67.6%;
            -webkit-transform: skewX(15deg);
        }
        #bet-lineP,#gamble {
            top: 90.7%;
            left: 52.8%;
            width: 5.3%;
            height: 7.7%;
            -webkit-transform: skewX(5deg);
            background: none;
            z-index: 4;
        }
        #bet-lineP.disabled {
           background-color: yellow !important;
           opacity: 0.3;
           z-index: 3;
        }
        #credit {
            display: none !important;
        }
        #info {
            top: 90.8%;
            width: 5.4%;
            left: 22.9%;
            height: 7.7%;
            background: none !important;
            -webkit-transform: skewX(-14deg);
        }
        #info span, #sound span, #bet-max span, #autostart span, #start span,#gamble span,#fullscreen span{
            display: none;
        }
        #bet-max {
            top: 90.5%;
            left: 40.1%;
            background: none !important;
            width: 5%;
            height: 8%;
            -webkit-transform: skewX(-3deg);
        }
        #autostart {
            top: 93.5%;
            right: 15.2%;
            width: 4%;
            height: 6%;
            -webkit-transform: skewX(25deg);
            background: none !important;
        }
        #start {
            top: 90.7%;
            right: 24.6%;
            height: 8.2%;
            width: 5.3%;
            background: none !important;
            -webkit-transform: skewX(18deg);
        }
        #autostart.disabled {
            background-color: purple !important;
        }
        #bet-max.disabled {
            background-color: red !important;
        }
        #start.disabled {
            background-color: red !important;
        }
        #lines,#bet-line {
            display: none;
        }
        */
        #fullscreen, #sound,#select {
            right: 5.6%;
            width: 7.6%;
            height: 5.8%;
        }
        #sound {
            top: 13.1%
        }
        #sound div{
            margin-left: 33.75%;
            width: 24%;
        }
        #fullscreen div {
            background-repeat: no-repeat;
            width: 100%;
            height: 100%;
            background-size: contain;
            margin: 0;
        }
        #select {
            font-size: 150%;
            top: 21.3%;
        }
        #select:before {
            content: 'X'
        }

	</style>
	<script>
            window.gameid = 'crazymonkey';
            static_domain = '<?php echo kohana::$config->load('static.static_domain'); ?>';
        </script>
    <?php if(Kohana::$environment==Kohana::PRODUCTION): ?>
	<script src="crazymonkey.min.js?ver=<?=th::ver()?>" type="text/javascript"></script>
	<?php else: ?>
	<script>
		var partPath = '../common/js';

			;[
				partPath+"/fabric.min.js",
				partPath+"/common.class.js",
				partPath+"/messenger.class.js",
				partPath+"/animate.class.js",
				partPath+"/buttons.class.js",
				partPath+"/sprite.class.js",
				partPath+"/custom.js",
				"js/init.class.js"

			].forEach(function(src) {
				var script = document.createElement('script');
				script.src = src + '?ver=<?php echo th::ver(); ?>';
				script.async = false;
				document.head.appendChild(script);
			});
		partPath  = null;
	</script>
	<?php endif; ?>
</head>
<body <?php if(th::isMobile()): ?> class="mobile"<?php endif; ?>>
		<div class="preload"></div>
	<div id="font-preload">
        <span style="font-family:Icons">abc</span>
		<span style="font-family:Comic Sans MS Bold">abc</span>
		<span style="font-family:Hatten">abc</span>
		<span style="font-family:Iskoola Pota Regular">abc</span>
		<span style="font-family:Din PRO Bold">abc</span>
		<span style="font-family:Cent">abc</span>
	</div>
    <div class="wrapper">
        <div class="canvas-container">
			<div id="sound" class="btn">
                    <div><span><?php echo __('Звук') ?></span></div>
			</div>
            <div id="start" class="btn">
                    <span>
                        <span><?php echo __('Старт') ?></span>
                    </span>
                </div>
            <div id="info" class="btn">
                    <span><?php echo __('Инфо') ?></span>
                </div>
            <div id="betshow" class="btn">
                    <span><?php echo __('Ставка') ?></span>
                </div>
            <div id="select" class="btn">
                    <span><?php echo __('Выход ') ?></span>
                </div>
            <div id="autostart" class="btn">
                    <span><?php echo __('Авто') ?></span>
                </div>
            <div id="fullscreen" class="btn">
                    <div><span>&nbsp;</span></div>
            </div>
			<div id="bet-max" class="btn">
                    <span><?php echo __('МАКС') ?></span>
                </div>
            <div id="gamble" class="btn disabled" disabled="disabled">
                    <span><?php echo __('Риск') ?></span>
                </div>
                <div id="lines" class="btn disabled" disabled="disabled">
                    <span id="linesName"><?php echo __('Линии') ?></span>
				<div id="linesValue"></div>
                    <div id="linesP" class="P" disabled="disabled">+</div>
                    <div id="linesM" class="M" disabled="disabled">-</div>
            </div>
            <div id="bet-line" class="btn">
                    <span id="bet-lineName"><?php echo __('Ставка&nbsp;Линии') ?></span>
				<div id="bet-lineValue">1</div>
                    <div id="bet-lineP" class="P">+</div>
                    <div id="bet-lineM" class="M">-</div>
            </div>
                <div id="bet-total" class="btn">
                    <span id="bet-totalName"><?php echo __('Ставка') ?></span>
                    <div id="bet-totalValue">1</div>
                </div>
            <div id="credit"></div>

			<div id="card0"></div>
			<div id="card1"></div>
			<div id="card2"></div>
			<div id="card3"></div>


<!--		 Bonus game element-->
			<div id="rope0"></div>
			<div id="rope1"></div>
			<div id="rope2"></div>
			<div id="rope3"></div>
			<div id="rope4"></div>
		 <!--Locken screen-->
			<div id="block_op"></div>
		 <!--Super Bonus game element-->
			<div id="container0"></div>
			<div id="container1"></div>
		 <!--Info button-->
			<div id="previous" class="bt btPrev"></div>
			<div id="exit" class="bt btExit"></div>
			<div id="next" class="bt btNext"></div>

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
<?php if(count($jackpots)): ?>
<?php echo block::gamejp(true); ?>
<?php endif; ?>
<?php echo block::rfid_listen(); ?>
</body>
</html>