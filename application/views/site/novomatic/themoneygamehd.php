<!DOCTYPE HTML>
<html lang="ru">
<head>
	<title>The Money Game HD</title>

	<meta charset="utf-8"/>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/> <!-- height=device-height, -->

	<!-- цвет статусбара для apple -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

	<!-- фулскрин для apple -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="mobile-web-app-capable" content="yes" />
	<!-- preloader  -->
	<link rel="stylesheet" href="style/preloader/index.css"/>
	<link rel="stylesheet" href="style/fonts/index.css"/>
	<!-- предзагрузка сплешскрина для device  -->
	<link rel="prefetch" href="images/device/splashscreen.jpg" />

	<!-- title icon -->
	<link rel="apple-touch-icon" sizes="57x57" href="apple-touch-icon.png" />

	<!-- Loading all script files .. -->
	<script>

		var partPath = '../../../common/js';

        ;[
            partPath+"/vendor/howler.min.js",
            partPath+"/vendor/apng-canvas.min.js",
            partPath+"/libs/Constants.js",

            partPath+"/modules/Game.js",
            partPath+"/modules/Expansions.js",
            partPath+"/modules/Utils.js",

            partPath+"/modules/Screen.js",
            partPath+"/modules/Audios.js",
            partPath+"/modules/Images.js",

            // IMPORTANT
            partPath+"/polyfills/closest.js",
            partPath+"/polyfills/Promise.js",
            partPath+"/polyfills/assign.js",

            partPath+"/libs/lochalization.js",
            partPath+"/libs/Template.js",
            partPath+"/libs/game-list.js",

//				partPath+"/libs/pixi.js",
            partPath+"/canvas/WebGL.js",

            partPath+"/instance/GameEvent.js",
            partPath+"/instance/Model.js",
            partPath+"/instance/View.js",
            partPath+"/instance/Collection.js",
            partPath+"/instance/Button.js",
            partPath+"/instance/Banner.js",
            partPath+"/instance/Response.js",
            partPath+"/instance/WinLines.js",
            partPath+"/instance/Executer.js",
            partPath+"/instance/Slot.js",
            partPath+"/instance/States.js",
            partPath+"/instance/History.js",

            partPath+"/modules/Slot.js",
            partPath+"/modules/Enroll.js",
            partPath+"/modules/Preloader.js",
            partPath+"/modules/Error.js",
            partPath+"/modules/Logic.js",
            partPath+"/modules/Ajax.js",
            partPath+"/modules/CheckWin.js",
            partPath+"/modules/Interface.js",
            partPath+"/modules/InfoPage.js",
            partPath+"/modules/Gamble.js",
            partPath+"/modules/Auto.js",
            partPath+"/modules/Freespin.js",
            partPath+"/modules/Crutch.js",
            partPath+"/modules/CommonController.js",
            partPath+"/modules/CommonBonus.js",
            // Отвечает за генерацию и работу менюхи игры
            partPath+"/modules/Menu.js",
            // Скрипт для отигрывания анимации спрайта
            partPath+"/modules/SpriteJS.js",
            // Скрипт для навешивания события свайпа на елементы для мобыльних устройств
            partPath+"/modules/Swipe.js",
            // Скрипт для получения информации о флагах модулей
            partPath+"/modules/FlagList.js",

            partPath+"/modules/Custom.js",

            partPath+"/modules/Terminal.js",

            "js/Bonus.js",
            "js/Controller.js",
            "js/Variables.js"

        ].forEach(function(src) {

            var script = document.createElement('script');
            script.src = src + '?ver=<?php echo th::ver(); ?>';
            script.async = false;
            document.head.appendChild(script);

        });

        partPath  = null;

	</script>

</head>
<body>
<div id="preload"><!-- предзагрузка -->
	<span style="font-family:Icons">abc</span>
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
		<div id="background" class="hidePsevdo"><!--Не удаляйте этот класс, пока изображение полностью не подгрузится класс будет скрывать её изображение-->
			<div id="game-box">
				<div id="modal-window" class="load1">
					<div id="box-rotate">
						<div id="place-holder" hidden="hidden">
							<div class="place-holder-text" style="display: none">
								Touch Screen to Start
							</div>
						</div><!--аттрибут hidden не брогать! он от постепенной прогрузки, только когда подгрузится весь фон он отобразится-->
					</div>
					<div class="preloader-container">
						<div class="preloader-content">
							<div id="preloader-percent">0</div>
							<div id="preloader-bar">
								<div id="bar-loader">
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
</body>
</html>