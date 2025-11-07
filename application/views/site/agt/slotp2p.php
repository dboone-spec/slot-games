<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8" />
        <meta content="user-scalable=0, initial-scale=1,minimum-scale=1, maximum-scale=1, width=device-width, minimal-ui=1, viewport-fit=cover" name="viewport">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <title>
            <?php echo $game->visible_name; ?>
        </title>
        <script>
            window.gametype = 'slots';
            localStorage.KpZevaOVbk = '<?php echo empty(auth::$token)?'demo':auth::$token; ?>';
            localStorage.WKqhbUlTze = Date.now()+24*60*60*1000;
            localStorage.syMtvvgLJj = '<?php echo auth::$user_id; ?>';
            window.gamename = '<?php echo $name; ?>';
            window.ver = '<?php echo th::ver(); ?>';

//            var console = {};
//            console.log = function(e){
//                if(document.getElementById('log')) {
//                    document.getElementById('log').insertAdjacentHTML('beforeend', e+'<br>');
//                }
//                alert(e);
////                document.getElementById('log').innerHTML=document.getElementById('log').innerHTML+e+'<br>';
//            };
//            console.warn = function(e){
//
//                    alert(e);
////                document.getElementById('log').innerHTML=document.getElementById('log').innerHTML+e+'<br>';
//            };
//            console.error = function(e){
//
//                    alert(e);
////                document.getElementById('log').innerHTML=document.getElementById('log').innerHTML+e+'<br>';
//            };

            window.cachesRun=false;

            if('caches' in window) {
                //todo delete cache when change version
                caches.open('rTdgRLMkiz').then(function(cache) {
                    window.cachesReady = cache;
                    window.cachesRun=true;
                });
            }

//            window.onerror = function(msg, url, line) {
//                alert("Message : " + msg );
//                alert("url : " + url );
//                alert("Line number : " + line );
//            }

            var lastTouchEnd = 0;
            document.addEventListener('touchend', function (event) {
                var now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    event.preventDefault();
                }
                lastTouchEnd = now;
            }, false);

            <?php if(in_array(auth::user()->office_id,[1029,1038,1041])): ?>
                window.whitelogo = '1029';
            <?php endif; ?>
        </script>
        <?php if(KOHANA::$environment == KOHANA::DEVELOPMENT): ?>
<!--            <script src="js/lib/phaser.min.js?v=<?php echo th::ver(); ?>">
            </script>-->
            <script src="js/lib/phaser2151.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/phaser-nocache.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/ping.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/peerjs.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/peerloader.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/zepto.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/configs/<?php echo $name; ?>.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/gameConstants.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/gambleButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/lineSelect.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/langSelect.js?v=<?php echo th::ver(); ?>">
            </script>
            <?php if(!in_array($name,['pharaoh','timemachine'])): ?>
            <script src="js/comps/ui/fgBar.js?v=<?php echo th::ver(); ?>">
            </script>
            <?php endif; ?>
            <script src="js/comps/ui/fsBar.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/fsPopup.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/nextButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/autoButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/betButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/collectButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/lineButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/spinButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/colorButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/creditButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/iconButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/logoButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/winline.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/infoButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/openMenuButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/suitButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/slowNetwork.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/mc/controller.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/mc/loader.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/mc/model.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/messages.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/winlines.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/slotsymbol.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/stats.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/playSounds.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/balance.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/infoBar.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/buttonsMobile.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/menu.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/infoBarGamble.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/lastwin.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/messageline.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/reel.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/popupMessage.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/layout/background.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/layout/logo.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/area.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/card.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/chooser.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/coin.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/gamblearea.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/pagearea.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/util/mediaManager.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/states/stateMain.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/states/stateLoad.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/states/stateInit.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/states/stateGamble.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/main.js?v=<?php echo th::ver(); ?>">
            </script>
        <?php else: ?>
<!--            <script src="js/lib/phaser.min.js?v=<?php echo th::ver(); ?>">
            </script>-->
            <script src="js/lib/phaser2151.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/phaser-nocache.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/zepto.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="jsmin/<?php echo $name; ?>.js?v=<?php echo th::ver(); ?>">
            </script>
        <?php endif; ?>
        <style>
            #bottomBg,#topButtons,#spin-btn,#spin-btn-label,#gamble-btn {display: none;}
            <?php if(th::isMobile()): ?>
            @media (orientation: portrait) {
                #bottomBg {
                    bottom: 0;
                    position: fixed;
                    z-index: 1;
                    display: block;
                    background-image: url(/games/agt/images/games/<?php echo $name; ?>/ui/back_vert_bottom.png);
                    background-repeat: no-repeat;
                    background-size: cover;
                    width: 100%;
                    height: 70vh;
                }
                canvas {
                    z-index: 7002;
                    position: absolute;
                    top: 5vh;
                    margin-top: 0 !important;
                }
                #topButtons {
                    background: #240d27;
                    height: 6%;
                    position: absolute;
                    z-index: 2;
                    top: 0;
                    width: 100%;
                    display: block;
                    color: #fff;
                }
                #topButtons .left {
                    display: flex;
                    float: left;
                    width: 90%;
                    height: 100%;
                }
                #topButtons .right {
                    display: block;
                    float: right;
                    height: 100%;
                    width: 10%;
                }
                #topButtons span:not(.agtlogo) {
                    background-image: url("/games/agt/images/themes/<?php echo $theme; ?>/buttons/top/mobile.png");
                    background-size: 100vw 6vh;
                    float: left;
                    display: block;
                    height: 100%;
                }
                #topButtons .openMenu {
                    width: 17vw;
                    background-position-x: 0;
                }
                #topButtons .infoPage {
                    width: 17vw;
                    float: left;
                    background-position-x: 19%;
                }
                #topButtons .toggleSound {
                    width: 17vw;
                    background-position-x: 60%;
                }
                #topButtons .autoPlay {
                    width: 17vw;
                    background-position-x: 80%;
                }
                #topButtons .closeGame {
                    width: 17vw;
                    background-position-x: 100%;
                    float: right;
                    display: block;
                }
                #topButtons > * {
                    display: none !important;
                }
                #topButtons .agtlogo {
                    position: absolute;
                    display: none !important;
                    right: 4%;
                    height: 100%;
                }
                #spin-btn,#spin-btn-label {
                    position: absolute;
                    z-index: 3;
                    width: 23vh;
                    bottom: 4vh;
                    left: 0;
                    right: 0;
                    margin: 0 auto;
                }
                #spin-btn-label {
                     font-family:Roboto;
                     text-transform: uppercase;
                     color: #fff;
                     font-size: medium;
                     text-align: center;
                }
                #spin-btn-label.hold {
                     width: 25vw;
                     bottom: 3vh;
                }
                #gamble-btn {
                    position: absolute;
                    z-index: 3;
                    width: 23vh;
                    bottom: 34vh;
                    left: 0;
                    right: 0;
                    margin: 0 auto;
                }
            }
            <?php endif; ?>
            #iOSFullscreenAnimation,#iOSFullscreen {
                display: none;
            }
        </style>
    </head>
    <body>
        <div style="font-family:Roboto; visibility: hidden; height: 0;">Font загружен</div>
        <div id="topButtons">
            <div class="left">
                <span class="openMenu" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.OPEN_MENU);">&nbsp;</span>
                <span class="infoPage" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.INFO_PAGE);">&nbsp;</span>
                <span class="toggleSound" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.TOGGLE_SOUND);">&nbsp;</span>
                <span class="autoPlay" onclick="javascript:autoSpin()">&nbsp;</span>
            </div>
            <div class="right">
                <span class="closeGame" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.CLOSE_GAME);">&nbsp;</span>
            </div>
            <img ontouchend="window.open('https://site-domain.com','_blank');" src="/games/agt/images/common/ui/logo.png" class="agtlogo" id="go-top-agt" />
        </div>
        <div id="wrongWayLandscape">
        </div>
        <div id="wrongWayPortrait">
        </div>
        <?php if(th::isMobile()): ?>
        <style>
            #play-sounds-vert {
                display: block;
                display: none;
                position: fixed;
                background: #230D27;
                width: 22em;
                height: 10em;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                z-index: 7004;
                border: 2px solid #fff;
                border-radius: 0.5em;
            }
            #play-sounds-vert div {
                color: #fff;
                font-size: 2.5em;
                text-align: center;
            }
            #play-sounds-vert-yes {
                display: block;
                border: 2px solid #fff;
                width: 40%;
                margin-left: 5%;
                margin-top: 10%;
                height: 30%;
                line-height: 130%;
                border-radius: 0.2em;
                float: left;
            }
            #play-sounds-vert-no {
                display: block;
                border: 2px solid #fff;
                width: 40%;
                margin-right: 5%;
                margin-top: 10%;
                height: 30%;
                line-height: 130%;
                border-radius: 0.2em;
                float: right;
            }

            @media (orientation: landscape) {

                canvas {
                    z-index: 6999;
                    -webkit-transform: translate3d(0, 0, 0);
                    width: 100vw;
                    position: fixed;
                    left: 0; top: 0;
                    /*pointer-events: none;*/
                }

                #iOSFullscreenAnimation {
                    width: 100vw;
                    display: block;
                    height: 100vh;
                    z-index: 7001;
                    background: #fff;
                    position: fixed;
                }
                #iOSFullscreen {
                    display: block;
                    width: 100vw;
                    height: 255vh;
                    position: absolute;
                    z-index: 7000;
                }

                @keyframes fullscreen-swipe {
                    from {
                        top: 80%;
                    }

                    to {
                        top: 40%;
                    }
                }

                .full-screen-hand {
                    width: 114px;
                    height: 112px;
                    left: 50vw;
                    position: fixed;
                    transform: translate(-50%, -50%);
                    animation-name: fullscreen-swipe;
                    animation-duration: 1.8s;
                    animation-iteration-count: infinite;
                    -webkit-animation-name: fullscreen-swipe;
                    -webkit-animation-duration: 1.8s;
                    -webkit-animation-iteration-count: infinite;
                }
            }
        </style>
        <div id="play-sounds-vert">
            <div id="play-sounds-vert-label"></div>
            <div id="play-sounds-vert-yes" onmouseup="javascript:yesSound()"></div>
            <div id="play-sounds-vert-no" onmouseup="javascript:noSound()"></div>
        </div>
        <div id="bottomBg">
            <!--<img width="100%" src="/games/agt/images/megahot20/ui/back_vert_bottom.png" />-->
        </div>
        <img id="gamble-btn" onmouseup="javascript:startGamble()" src="/games/agt/images/themes/<?php echo $theme; ?>/buttons/mobile/x2_vert.png" />
        <img id="spin-btn" ontouchstart="startGO(event);" ontouchend="spinGO(event);" src="/games/agt/images/themes/<?php echo $theme; ?>/buttons/mobile/spin_vert.png" />
        <div id="spin-btn-label"></div>
        <?php endif; ?>
        <script>

            window.canUseWebp = function() {
                var elem = document.createElement('canvas');
                if (!!(elem.getContext && elem.getContext('2d'))) {
                    return elem.toDataURL('image/webp').indexOf('data:image/webp') == 0;
                }
                return false;
            }();
            <?php if(th::isMobile()): ?>
            if(window.canUseWebp) {
                document.getElementById('bottomBg').style.backgroundImage = 'url(/games/agt/images/games/<?php echo $name; ?>/ui/back_vert_bottom.webp)';
                document.getElementById('gamble-btn').src = document.getElementById('gamble-btn').src.replace('.png','.webp');
                document.getElementById('spin-btn').src = document.getElementById('spin-btn').src.replace('.png','.webp');
                document.getElementById('go-top-agt').src = document.getElementById('go-top-agt').src.replace('.png','.webp');
            }
            <?php endif; ?>
        </script>
        <div id="iOSFullscreenAnimation">
            <img width="10vw" src="/games/agt/css/swipe.png" class="full-screen-hand" />
        </div>
        <div id="iOSFullscreen"></div>
        <!--<div id="lgg" style="color: white; background: black; position: fixed; z-index: 9000;"></div>-->
        <script>
                <?php if($go = arr::get($_GET,'closeurl')): ?>
                window.onmessage=function(event) {
                    if (event.data=='closeGame' || event.data=='close') {
                        window.location = '<?php echo $go; ?>';
                    }
                }
                <?php endif; ?>

                var start_press=0;
                var canRelease=true;

                var yesSound = function () {
                    StateLoad.playSounds.onReleased({key:'yes'});
                    document.getElementById('play-sounds-vert').remove();
                }

                var noSound = function () {
                    StateLoad.playSounds.onReleased({key:'no'});
                    document.getElementById('play-sounds-vert').remove();
                }

                var autoSpin = function () {
                    model.infobar.buttonLayer.children[model.infobar.buttonLayer.children.length-1].onReleased();
                };
                var startGamble = function() {
                    model.infobar.buttonLayer.children[3].onReleased();
                };
                var startGO = function(e) {
                    start_press=game.time.now;
                    e.preventDefault()
                };
                var spinGO = function(e) {
                    canRelease && model.infobar.buttonLayer.children[model.infobar.buttonLayer.children.length-2].onReleased();
                    canRelease=true;
                    start_press=0;
                    e.preventDefault()
                };
                var checkGO = function() {
                    if(typeof model!='undefined') {
                        if(!model.auto_spin && model.flags.can_start_auto_spin && start_press>0 && ((game.time.now-start_press)>500)) {
                            eventDispatcher.dispatch(G.START_AUTO_SPIN,model.bets[model.bet_index]*model.k_list[model.k]);
                            canRelease=false;
                        }
                    }
                    setTimeout(checkGO,100);


                    $('#lgg').html($('canvas').attr('style'));
                }
                checkGO();

                $('img').bind('contextmenu', function(e){
                    return false;
                });

                <?php if(th::isMobile()): ?>
                $(window).on('resize',function() {

                    if(window.self!=window.top) {
                        $('#iOSFullscreenAnimation').remove();
                        $('#iOSFullscreen').remove();
                        $('canvas').css('z-index','7003');
                        if($('#play-sounds-vert').length) {
                            $('#play-sounds-vert').css('z-index','7004 !important');
                        }
                        return;
                    }

                    $('#iOSFullscreenAnimation').css('visibility','hidden');
                    $('#iOSFullscreenAnimation').css('display','none');
                    $('canvas').css('z-index','7003');
                    //20 - adress bar chrome ios

                    if($('#play-sounds-vert').length) {
                        $('#play-sounds-vert').css('z-index','7004');
                    }


                    if(window.outerHeight>window.innerHeight+(window.outerHeight*0.125)) {
                        if($('#play-sounds-vert').length) {
                            $('#play-sounds-vert').css('z-index','6998');
                        }
                        $('#iOSFullscreenAnimation').css('visibility','visible');
                        $('#iOSFullscreenAnimation').css('display','block');
                        $('canvas').css('z-index','6999');

                        if(model.auto_spin) {
                            eventDispatcher.dispatch(G.STOP_AUTO_SPIN);
                        }
                    }
                });
                if(window.self!=window.top) {
                    $('#iOSFullscreenAnimation').remove();
                    $('#iOSFullscreen').remove();
                    $('canvas').css('z-index','7003');
                    if($('#play-sounds-vert').length) {
                        $('#play-sounds-vert').css('z-index','7004 !important');
                    }
                }
                <?php endif; ?>
        </script>
    </body>
</html>