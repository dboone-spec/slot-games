<!DOCTYPE HTML>
<html lang="ru">
	<head>
		<title>Pharaoh's Gold II</title>
		<meta charset="utf-8">
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="mobile-web-app-capable" content="yes">
		<link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon.png" />
                <link rel="stylesheet" href="../common/css/style.css?ver=<?php echo th::ver(); ?>">
		<style>
			@font-face {
				font-family: 'digital-7mono';
				src: url('../common/fonts/SFDigitalReadout.ttf');
			}
			@font-face {
				font-family:'A1010Helvetika-Bold';
				src: url('../common/fonts/calibrib.ttf');
			}
			@font-face {
				font-family:'OpenSansExtrabold';
				src: url('../common/fonts/Tahoma-Bold.ttf');
			}
			@font-face {
				font-family:'NautilusPompilius';
				src: url('../common/fonts/Tahoma-Bold.ttf');
			}
			div#den {
				color: #ffff00;
			}
			.block-wrap {
				background: url('images/splashscreen.jpg') no-repeat fixed center center / contain #000;
			}
		</style>

        <script>
                window.gameid = 'pharaohsgold2';
                static_domain = '<?php echo kohana::$config->load('static.static_domain'); ?>';
	</script>
	<?php if(Kohana::$environment==Kohana::PRODUCTION): ?>
	<script src="pharaohsgold2.min.js?ver=<?=th::ver()?>" type="text/javascript"></script>
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
	<body>
		<div id="font-preload">
			<span style="font-family:digital-7mono">abc</span>
			<span style="font-family:A1010Helvetika-Bold">abc</span>
			<span style="font-family:OpenSansExtrabold">abc</span>
			<span style="font-family:NautilusPompilius">abc</span>
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
<?php if(count($jackpots)): ?>
<?php echo block::gamejp(true); ?>
<?php endif; ?>
<?php echo block::rfid_listen(); ?>
	</body>
</html>