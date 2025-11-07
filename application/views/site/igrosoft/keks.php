<!DOCTYPE HTML>
<html lang="ru">
<head>
	<title>Keks</title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" sizes="96x96" href="apple-touch-icon.png" />
	<link rel="stylesheet" href="../common/css/stylenovomatic.css?ver=<?php echo th::ver(); ?>">
        <script>
            window.gameid = 'keks';
            static_domain = '<?php echo kohana::$config->load('static.static_domain'); ?>';
        </script>
    <?php if(Kohana::$environment==Kohana::PRODUCTION): ?>
	<script src="keks.min.js?ver=<?=th::ver()?>" type="text/javascript"></script>
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

		div#credit {
			width: 8%;
			height: 8%;
			top: 89%;
			left: 17%;
			z-index: 1;
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

		div#bonusGameOvens {
			display: none;
		}
		div#bonusGameOven0, div#bonusGameOven1, div#bonusGameOven2, div#bonusGameOven3, div#bonusGameOven4 {
			position: absolute;
			width: 10%;
			height: 17%;
			top: 40%;
			cursor: pointer;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}
		div#bonusGameOven0 {
			left: 19%;
		}
		div#bonusGameOven1 {
			left: 32%;
			top: 43%;
		}
		div#bonusGameOven2 {
			left: 44.5%;
		}
		div#bonusGameOven3 {
			left: 57.5%;
			top: 43%;
		}
		div#bonusGameOven4 {
			left: 70.5%;
		}

		div#superBonusGameBushes {
			display: none;
		}
		div#superBonusGameBush0, div#superBonusGameBush1 {
			position: absolute;
			top: 60%;
			width: 16%;
			height: 25%;
			cursor: pointer;
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}
		div#superBonusGameBush0 {
			left: 30%;
		}
		div#superBonusGameBush1 {
			left: 63%;
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
			<div id="bonusGameOvens">
				<div id="bonusGameOven0"></div>
				<div id="bonusGameOven1"></div>
				<div id="bonusGameOven2"></div>
				<div id="bonusGameOven3"></div>
				<div id="bonusGameOven4"></div>
			</div>
			<div id="superBonusGameBushes">
				<div id="superBonusGameBush0"></div>
				<div id="superBonusGameBush1"></div>
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