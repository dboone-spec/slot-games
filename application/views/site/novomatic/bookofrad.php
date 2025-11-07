<!DOCTYPE HTML>
<html lang="ru">
<head>
	<title>Book of Ra Deluxe</title>
	<meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <!-- height=device-height, -->
	<!-- цвет статусбара для apple -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<!-- фулскрин для apple -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes" />
	<!-- preloader  -->
	<?php if(th::isMobile()): ?>
	<style>
		.portrait .preload{
			background-color:rgb(0, 0, 0);
			background-image:url(/assets/img/rotate.gif);
			background-position-x:50%;
			background-position-y:50%;
			display:block;
			opacity: 1;
			font-size:10px;
			height:100%;
			left:0px;
			position:fixed;
			top:0;
			width:100%;
			z-index:5;
			background-repeat: no-repeat;
		}
		#clock {
			display: none !important;
		}
		#bet_all {
			display: none !important;
		}
		#bet_line2 {
			display: none !important;
		}
		#line2 {
			display: none !important;
		}
	</style>
	<?php endif; ?>
	<link rel="stylesheet" href="style/preloader/index.css"/>
    <!--<link rel="stylesheet" href="style/fonts/index.css"/>-->
    <!-- предзагрузка сплешскрина для device
        <link rel="prefetch" href="images/device/splashscreen.jpg"/>
    -->
	<!-- title icon -->
    <link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon.png"/>
</head>
<body>
	<div class="preload"></div>
	<div id="preload"><!-- предзагрузка -->
		<span style="font-family:credit">abc</span>
    <span style="font-family:vinque">abc</span>
    <span style="font-family:Schwabacher">abc</span>
    <span style="font-family:HeliosCond-Bold">abc</span>
    <span style="font-family:Icons">abc</span>
    <span style="font-family:Icons2">abc</span>
    <span style="font-family:A1010Helvetika-Bold">abc</span>
    <span style="font-family:Open Sans Extrabold">abc</span>
    <span style="font-family:Icons">abc</span>
    <span style="font-family:ButtonsFont">abc</span>
    <span style="font-family:JpCongrFont">abc</span>
    <span style="font-family:JpValFont">abc</span>
    <span style="font-family:InfoFieldsFont">abc</span>
    <span style="font-family:Ebrima">abc</span>
    <span style="font-family:RectsFont">abc</span>
    <span style="font-family:Globus">abc</span>
    <span style="font-family:Academy">abc</span>
    <span style="font-family:Conv_OPTIMA">abc</span>
	</div>

	<div id="ioser"></div>

	<div id="box-layout"><!-- точка входа -->
		<div id="wrapper">
        <div id="background" class="hidePsevdo">
				<div id="game-box">
					<div id="modal-window" class="load1">
						<div id="box-rotate">
							<div id="place-holder" hidden="hidden">
                            <div class="place-holder-text" style="">
								</div>
							</div><!--аттрибут hidden не брогать! он от постепенной прогрузки, только когда подгрузится весь фон он отобразится-->
                            <?php if(count($fs=auth::user()->game_freespins('bookofrad'))): ?>
                                <?php echo block::fs_view($fs); ?>
                            <?php endif; ?>
						</div>
						<div class="preloader-container">
							<div class="preloader-content">
								<div id="preloader-percent">0</div>
								<div id="preloader-bar">
									<div id="bar-loader">
                                            <div id="blick"></div>
										<div id="bar-right"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><!-- background -->
		</div><!-- wrapper -->
	</div><!-- box-layout -->
<?php if(Kohana::$environment==Kohana::PRODUCTION): ?>
<script>
        var src = "bookofrad.min.js";
        var script = document.createElement('script');
        script.src = src + '?ver=<?php echo th::ver(); ?>';
        script.async = false;
        document.head.appendChild(script);
</script>
<?php else: ?>
<script src="../../../common2/js/Include.js"></script>
<script>
    window.includeCore({
        scripts: [
            '../../../../common2/js/dictionary/game_64.js',
            'Bonus.js',
            'Controller.js',
            'Variables.js',
            'audio.js',
            'infoPage.js',
            'preloadImages.js',
            'sprites.js',
            'localization.js'
        ],
         cache: false
    });
</script>
<?php endif; ?>
<?php if(count($jackpots)): ?>
<?php echo block::gamejp(true); ?>
<?php endif; ?>
<?php echo block::rfid_listen(); ?>
</body>
</html>