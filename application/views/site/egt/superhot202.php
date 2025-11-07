<!DOCTYPE HTML>
<html lang="ru">
<head>
    <title>Superhot 202</title>

    <meta charset="utf-8"/>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/> <!-- height=device-height, -->

    <!-- цвет статусбара для apple -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

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
    <link rel="stylesheet" href="style/fonts/index.css"/>
    <!-- предзагрузка сплешскрина для device  -->
    <link rel="prefetch" href="images/device/splashscreen.jpg" />

    <!-- title icon -->
    <link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon.png" />

</head>
<body>
<div class="preload"></div>
<div id="preload"><!-- предзагрузка -->
    <span style="font-family:credit">abc</span>
    <span style="font-family:vinque">abc</span>
    <span style="font-family:Schwabacher">abc</span>
    <span style="font-family:HeliosCond-Bold">abc</span>
    <span style="font-family:A1010Helvetika-Bold">abc</span>
    <span style="font-family:Open Sans Extrabold">abc</span>
    <span style="font-family:Icons">abc</span>
    <span style="font-family:Icons2">abc</span>
    <span style="font-family:ButtonsFont">abc</span>
    <span style="font-family:JpCongrFont">abc</span>
    <span style="font-family:JpValFont">abc</span>
    <span style="font-family:InfoFieldsFont">abc</span>
    <span style="font-family:Ebrima">abc</span>
    <span style="font-family:RectsFont">abc</span>
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
                        </div>
                        <?php if(count($fs=auth::user()->game_freespins('superhot202'))): ?>
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
        var src = "superhot202.min.js?ver=<?=th::ver()?>";
        var script = document.createElement('script');
        script.src = src + '?ver=<?php echo th::ver(); ?>';
        script.async = false;
        document.head.appendChild(script);
</script>
<?php else: ?>
<script>
		var partPath = '../../../common/js';
		 [
			 partPath + "/modules/Game.js",
			 partPath + "/modules/Expansions.js",
			 partPath + "/libs/Constants.js",
			 partPath + "/modules/Utils.js",

			 partPath + "/vendor/howler.min.js",
			 partPath + "/vendor/apng-canvas.min.js",

			 partPath + "/modules/Screen.js",
			 partPath + "/modules/Audios.js",
			 partPath + "/modules/Images.js",

			 // IMPORTANT
			 partPath + "/polyfills/closest.js",
			 partPath + "/polyfills/Promise.js",
			 partPath + "/polyfills/assign.js",

			 partPath + "/libs/lochalization.js",
			 partPath + "/libs/Template.js",
			 partPath + "/libs/game-list.js",

//				partPath+"/libs/pixi.js",
			 partPath + "/canvas/WebGL.js",

			 partPath + "/instance/GameEvent.js",
			 partPath + "/instance/Model.js",
			 partPath + "/instance/View.js",
			 partPath + "/instance/Collection.js",
			 partPath + "/instance/Button.js",
			 partPath + "/instance/Banner.js",
			 partPath + "/instance/Response.js",
			 partPath + "/instance/WinLines.js",
			 partPath + "/instance/Executer.js",
			 partPath + "/instance/Slot.js",
			 partPath + "/instance/States.js",
			 partPath + "/instance/History.js",

			 partPath + "/modules/Slot.js",
			 partPath + "/modules/Enroll.js",
			 partPath + "/modules/Preloader.js",
			 partPath + "/modules/Error.js",
			 partPath + "/modules/Logic.js",
			 partPath + "/modules/Ajax.js",
			 partPath + "/modules/CheckWin.js",
			 partPath + "/modules/Interface.js",
			 partPath + "/modules/InfoPage.js",
			 partPath + "/modules/Gamble.js",
			 partPath + "/modules/Auto.js",
			 partPath + "/modules/Freespin.js",
			 partPath + "/modules/Crutch.js",
			 partPath + "/modules/CommonController.js",
			 partPath + "/modules/CommonBonus.js",
			 // Отвечает за генерацию и работу менюхи игры
			 partPath + "/modules/Menu.js",
			 // Скрипт для отигрывания анимации спрайта
			 partPath + "/modules/SpriteJS.js",
			 // Скрипт для навешивания события свайпа на елементы для мобыльних устройств
			 partPath + "/modules/Swipe.js",
			 // Скрипт для получения информации о флагах модулей
			 partPath + "/modules/FlagList.js",
			 partPath+"/modules/Terminal.js",

			 partPath + "/modules/Custom.js",

			 "js/Bonus.js",
			 "js/Controller.js",
			 "js/Variables.js",
			 "js/ladySlotsCustoms.js"

		 ].forEach(function (src) {

			 var script = document.createElement('script');
			 script.src = src + '?ver=<?php echo th::ver(); ?>';
			 script.async = false;
			 document.head.appendChild(script);

		 });

		 partPath = null;
    </script>
<?php endif; ?>
</body>
</html>