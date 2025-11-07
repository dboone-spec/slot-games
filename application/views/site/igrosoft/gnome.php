<!DOCTYPE HTML>
<html lang="ru">
<head>
	<title>Gnome</title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" sizes="96x96" href="apple-touch-icon.png" />
	<link rel="stylesheet" href="../common/css/stylenovomatic.css?ver=<?php echo th::ver(); ?>">
    <script>
        window.gameid = 'gnome';
        static_domain = '<?php echo kohana::$config->load('static.static_domain'); ?>';
    </script>
    <?php if(Kohana::$environment==Kohana::PRODUCTION): ?>
	<script src="gnome.min.js?ver=<?=th::ver()?>" type="text/javascript"></script>
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
	<style>
		@font-face {
			font-family:'HeliosCond-Bold';
			src: url('../common/fonts/HeliosCond-Bold.ttf');
		}
		@font-face {
			font-family:'A1010Helvetika-Bold';
			src: url('../common/fonts/A101HLVB.ttf');
		}
		@font-face {
			font-family:"Open Sans Extrabold";
			src: url("../common/fonts/OpenSans-ExtraBold.ttf");
		}

        /*gnome only! ++*/

        #autostart
        {
            left: 11.6%;
        }
        #gamble
        {
            right: 33.4%;
        }
        #bet-line
        {
            left: 40.9%;
        }
        #bet-total
        {
            left: 51%;
        }
        #lines
        {
            left: 30.8%;
        }
        #betshow
        {
            left: 43.6%;
        }
        #info
        {
            left: 21.2%;
        }
        #start
        {
            right: 8.7%;
        }
        #lines #linesValue, #bet-line #bet-lineValue{
            left: 31%;
        }
        /*gnome only! --*/

		div#credit {
			top: -5%;
			left: -5%;
			width:18%;
			height:5%;
			z-index: 0;
			background: url("images/denLabel.png") no-repeat;
			background-size: 100% auto;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}

		div#infoPageButtons {
			display: none;
		}
		div#infoPageButtonPrevious,
		div#infoPageButtonExit,
		div#infoPageButtonNext {
			position: absolute;
			width: 20%;
			height: 11%;
			top: 87%;
			cursor: pointer;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}
		div#infoPageButtonPrevious {
			left: 19%;
		}
		div#infoPageButtonExit {
			left: 40%;
		}
		div#infoPageButtonNext {
			left: 61%;
		}

		div#doublingCards {
			display: none;
		}
		div#card0, div#card1, div#card2, div#card3 {
			position: absolute;
			width: 12%;
			height: 32%;
			top: 22%;
			cursor: pointer;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}
		div#card0 {
			left: 31.5%;
		}
		div#card1 {
			left: 44.5%;
		}
		div#card2 {
			left: 57.5%;
		}
		div#card3 {
			left: 70.5%;
		}

		div#bonusGameMineCarts {
			display: none;
		}
		div#bonusGameMineCart0, div#bonusGameMineCart1, div#bonusGameMineCart2, div#bonusGameMineCart3, div#bonusGameMineCart4 {
			position: absolute;
			width: 11%;
			height: 15%;
			top: 14%;
			cursor: pointer;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}
		div#bonusGameMineCart0 {
			left: 15%;
		}
		div#bonusGameMineCart1 {
			left: 29.5%;
		}
		div#bonusGameMineCart2 {
			left: 44.5%;
		}
		div#bonusGameMineCart3 {
			left: 59%;
		}
		div#bonusGameMineCart4 {
			left: 74%;
		}

		div#superBonusGameChests {
			display: none;
		}
		div#superBonusGameChest0, div#superBonusGameChest1 {
			position: absolute;
			top: 48%;
			width: 12%;
			height: 16%;
			cursor: pointer;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}
		div#superBonusGameChest0 {
			left: 34.5%;
		}
		div#superBonusGameChest1 {
			left: 64%;
		}

		.block-wrap {
			background: url('images/splashscreen.jpg') no-repeat fixed center center / contain #000;
		}

		#lines-canvas {
			position: absolute;
			display: block;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			width: 100% !important;
			height: 100% !important;
			margin: 0 auto;
			background-size: 100% auto;
			background-repeat: no-repeat;
		}

		.game-canvas {
			position: absolute;
			display: block;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			width: 100% !important;
			height: 100% !important;
			margin: 0 auto;
			background-size: 100% auto;
			background-repeat: no-repeat;
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
            left: 24.5%;
        }
        .mobile #gamble{
            right: 36.7%;
        }
        #betshow {
            display: none;
        }
        .mobile #betshow {
            display: block;
            left: 37.6%;
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
        #betshow {
            display: none;
        }
	</style>
    <div class="preload"></div>
	<div id="bootResources">
		<img src="../common/images/icon.png">
		<img src="../common/images/body.png">
		<img src="images/splashscreen.jpg">
	</div>
	<div id="font-preload">
        <span style="font-family:Icons">abc</span>
		<span style="font-family:HeliosCond-Bold">abc</span>
		<span style="font-family:A1010Helvetika-Bold">abc</span>
		<span style="font-family:Open Sans Extrabold">abc</span>
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
            <!-- Locken screen -->
			<div id="block_op"></div>
			<div id="credit"></div>
			<canvas id="canvas"></canvas>
			<div id="infoPageButtons">
				<div id="infoPageButtonPrevious"></div>
				<div id="infoPageButtonExit"></div>
				<div id="infoPageButtonNext"></div>
			</div>
			<div id="doublingCards">
				<div id="card0"></div>
				<div id="card1"></div>
				<div id="card2"></div>
				<div id="card3"></div>
			</div>
			<div id="bonusGameMineCarts">
				<div id="bonusGameMineCart0"></div>
				<div id="bonusGameMineCart1"></div>
				<div id="bonusGameMineCart2"></div>
				<div id="bonusGameMineCart3"></div>
				<div id="bonusGameMineCart4"></div>
			</div>
			<div id="superBonusGameChests">
				<div id="superBonusGameChest0"></div>
				<div id="superBonusGameChest1"></div>
			</div>
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