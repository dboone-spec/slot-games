<!DOCTYPE html>
<html lang="en" translate="no">
    <head>
        <meta charset="utf-8" />
        <meta content="user-scalable=0, initial-scale=1,minimum-scale=1, maximum-scale=1, width=device-width, minimal-ui=1, viewport-fit=cover" name="viewport">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <title>
            <?php echo isset($game->visible_name) ? $game->visible_name : 'site-domain demo game' ?>
        </title>
        <script>
            document.oncontextmenu = function(){return false;};
            document.onkeydown = function(e) {
                if (e.ctrlKey &&
                    (e.keyCode === 67 ||
                     e.keyCode === 86 ||
                     e.keyCode === 85 ||
                     e.keyCode === 117)) {
                    return false;
                } else {
                    return true;
                }
            };

            window.gametype = 'drops';


            try {
                LS15 = localStorage;
            }
            catch(e) {
                LS15 = {};
            }

            <?php if(DEMO_MODE): ?>
                LS15 = {};
            <?php endif; ?>

            LS15.KpZevaOVbk = '<?php echo empty(auth::$token)?'demo':auth::$token; ?>';
            LS15.WKqhbUlTze = Date.now()+24*60*60*1000;
            <?php if(DEMO_MODE): ?>
                LS15.WKqhbUlTze = Date.now()+20*60*1000;
            <?php endif; ?>
            LS15.syMtvvgLJj = '<?php echo auth::$user_id; ?>';
            window.gamename = '<?php echo $name; ?>';
            window.ver = '<?php echo th::ver(); ?>';

            window.cachesRun=false;

            function startCacheAGT() {

                LS15.zujsOt = window.ver;

                caches.open('rTdgRLMkiz').then(function(cache) {
                    window.cachesReady = cache;
                    window.cachesRun=true;
                });
            }

            if('caches' in window) {

                if(!LS15.zujsOt || LS15.zujsOt!=window.ver) {
                    caches.delete('rTdgRLMkiz').then(function() {
                        startCacheAGT();
                    });
                }
                else {
                    startCacheAGT();
                }
            }

            var lastTouchEnd = 0;
            document.addEventListener('touchend', function (event) {
                var now = (new Date()).getTime();
                if (now - lastTouchEnd <= 300) {
                    event.preventDefault();
                }
                lastTouchEnd = now;
            }, false);

                jackpotPopupName='popup';
                <?php if(in_array(auth::user()->office->currency->code,['ZAR','JMD'])): ?>
                    jackpotPopupName='bonusprize';
                <?php endif; ?>

                <?php if(in_array(auth::user()->office_id,[1029,1038,1041,1043,1045,1046,1047,1050,1117,1120,1201,1219])): ?>
                    window.whitelogo = '1029';
                <?php endif; ?>

            window.office_id=<?php echo auth::user()->office_id; ?>;
            window.jptime=<?php echo Kohana::$config->load('static.jptime'); ?>;
            window.jp_wss_url = '<?php echo Kohana::$config->load('static.jp_wss_url'); ?>';

           demo_mode = <?php echo DEMO_MODE?'true':'false';?>;

           window.forceMobile=false;
           window.noCloseGame=false;
           <?php if(arr::get($_GET,'force_mobile',0)): ?>
               window.forceMobile=true;
           <?php endif; ?>
           <?php if(arr::get($_GET,'no_close',0)): ?>
               window.noCloseGame=true;
           <?php endif; ?>

           <?php if(auth::user()->office->apitype==4 && $cashierURL=arr::get($_GET,'cashierurl')): ?>
               window.showPayPopup=true;
               window.URLPayPopup='<?php echo $cashierURL; ?>';
           <?php endif; ?>

            window.fsback_enable=false;
            <?php if(auth::user()->office->enable_bia>0): ?>
               window.fsback_enable=true;
            <?php endif; ?>

            window.promopanel_enable=false;
            <?php if(auth::user()->promopanel_enable()): ?>
                window.promopanel_enable=true;
            <?php endif; ?>

            window.showfakeversion=false;
            <?php if(auth::user()->office->showfakeversion): ?>
                window.showfakeversion='11.540. <?php echo isset($game->rtp) ? 'RTP: '.$game->rtp : '' ?>';
            <?php endif; ?>

        </script>
        <?php if(auth::user()->api==6): ?>
            <script src="js/everymatrix.js?v=<?php echo th::ver(); ?>">
            </script>
        <?php endif; ?>
        <?php if(KOHANA::$environment == KOHANA::DEVELOPMENT): ?>
<!--            <script src="js/lib/phaser.min.js?v=<?php echo th::ver(); ?>">
            </script>-->
<!--            <script src="js/lib/phaser2151.min.js?v=<?php echo th::ver(); ?>">
            </script>-->
            <script src="js/lib/phaser220.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/phaser-debug.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/phaser-nocache.min.js?v=<?php echo th::ver(); ?>">
            </script>
<!--            <script src="js/lib/ping.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/peerjs.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/peerloader.js?v=<?php echo th::ver(); ?>">
            </script>-->
            <script src="js/lib/zepto.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/delaunay.js?v=<?php echo th::ver(); ?>">
            </script>
<!--            <script src="js/lib/neonRect.js?v=<?php echo th::ver(); ?>">
            </script>-->
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
            <script src="js/comps/jp/area.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/jp/card.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/jp/top.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/jp/popup.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/jp/popupstatic.js?v=<?php echo th::ver(); ?>">
            </script>
            <?php if(!in_array($name,['pharaoh','timemachine'])): ?>
            <script src="js/comps/ui/fgBar.js?v=<?php echo th::ver(); ?>">
            </script>
            <?php endif; ?>
            <script src="js/comps/ui/fsBar.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/fsPopup.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/payPopup.js?v=<?php echo th::ver(); ?>">
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
            <script src="js/comps/ui/promoPanel.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/iButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/switchButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/topMenu.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/logoButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/winline.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/infoButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/openMenuButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/betLinesButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/ui/buttons/suitButton.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/slowNetwork.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/megawin.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/maxbet.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/mc/controller.js?v=<?php echo th::ver(); ?>">
            </script>
<!--            <script src="js/mc/loader.js?v=<?php echo th::ver(); ?>">
            </script>-->
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
<!--            <script src="js/comps/info/buttonsMobile.js?v=<?php echo th::ver(); ?>">
            </script>-->
            <script src="js/comps/slot/menu.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/betlines.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/infoBarGamble.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/lastwin.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/totalwin.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/clock.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/lastbet.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/messageline.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/dropreel.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/info/popupMessage.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/layout/background.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/layout/logo.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/comps/slot/dropsarea.js?v=<?php echo th::ver(); ?>">
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
            <script src="js/util/math.js?v=<?php echo th::ver(); ?>">
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
            <script src="js/lib/delaunay.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="jsmin/<?php echo $name; ?>.js?v=<?php echo th::ver(); ?>">
            </script>
        <?php endif; ?>
        <style>
            #bottomBg,#topButtons,#spin-btn,#spin-btn-label,#gamble-btn {display: none;}
            <?php if(th::isMobile() || arr::get($_GET,'force_mobile',0)): ?>
            @media (max-aspect-ratio: 86/100) {
                canvas {
                    z-index: 7002;
                    position: absolute;
                    top: 0;
                    /*image-rendering: smooth;*/
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
            }
            <?php endif; ?>
            #iOSFullscreenAnimation,#iOSFullscreen {
                display: none;
            }
        </style>
    </head>
    <body style="overflow:hidden;">
        <div style="font-family:Roboto; visibility: hidden; height: 0;">Font загружен</div>
        <div id="topButtons">
<!--            <div class="left">
                <span class="openMenu" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.OPEN_MENU);">&nbsp;</span>
                <span class="infoPage" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.INFO_PAGE);">&nbsp;</span>
                <span class="toggleSound" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.TOGGLE_SOUND);">&nbsp;</span>
                <span class="autoPlay" onclick="javascript:autoSpin()">&nbsp;</span>
            </div>
            <div class="right">
                <span class="closeGame" onclick="eventDispatcher.dispatch && eventDispatcher.dispatch(G.CLOSE_GAME);">&nbsp;</span>
            </div>
            <img ontouchend="window.open('https://site-domain.com','_blank');" src="/games/agt/images/common/ui/logo.png" class="agtlogo" id="go-top-agt" />-->
        </div>
        <div id="wrongWayLandscape">
        </div>
        <div id="wrongWayPortrait">
        </div>
        <?php if(th::isMobile() || arr::get($_GET,'force_mobile',0)): ?>
        <style>


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
                        top: 70%;
                    }

                    to {
                        top: 10%;
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

                #fs-popup-yes,#fs-popup-no{
                    font-size: 1.8em !important;
                    background: #000000;
                }

                @media (orientation: landscape) {
                    canvas {
                        left: 0; top: 0;
                    }
                }


        </style>
<!--        <div id="play-sounds-vert">
            <div id="play-sounds-vert-label"></div>
            <div id="play-sounds-vert-yes" onmouseup="javascript:yesSound()"></div>
            <div id="play-sounds-vert-no" onmouseup="javascript:noSound()"></div>
        </div>-->
        <div id="fs-popup">
            <div class="bg"></div>
            <div id="fs-popup-label"></div>
            <div id="fs-popup-yes" onmouseup="javascript:yesFS()"></div>
            <div id="fs-popup-no" onmouseup="javascript:noFS()"></div>
            <div id="fs-popup-close" onmouseup="javascript:noFS()">
                <span></span>
            </div>
        </div>
        <?php endif; ?>
        <script>

            window.canUseWebp = function() {
                var elem = document.createElement('canvas');
                if (!!(elem.getContext && elem.getContext('2d'))) {
                    return elem.toDataURL('image/webp').indexOf('data:image/webp') == 0;
                }
                return false;
            }();
        </script>
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

                var yesFS = function () {
                    StateLoad.fsPopup.onChildInputDown.dispatch({'act':'accept'});
                    StateLoad.playSounds.onReleased({key:'yes'});
                    document.getElementById('fs-popup').remove();
                }

                var noFS = function () {
                    StateLoad.fsPopup.onChildInputDown.dispatch({'act':'decline'});
                    StateLoad.playSounds.onReleased({key:'yes'});
                    document.getElementById('fs-popup').remove();
                }

                var autoSpin = function () {
                    model.infobar.btns[model.infobar.btns.length-1].onReleased();
                };
                var startGamble = function() {
                    model.infobar.btns[3].onReleased();
                };
                var startGO = function(e) {
                    start_press=game.time.now;
                    e.preventDefault()
                };
                var spinGO = function(e) {
                    canRelease && model.infobar.btns[model.infobar.btns.length-2].onReleased();
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
//                checkGO();

                $('img').bind('contextmenu', function(e){
                    return false;
                });

        </script>
    </body>
</html>