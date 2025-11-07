<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8" />
        <meta content="user-scalable=0, initial-scale=1,minimum-scale=1, maximum-scale=1, width=device-width, minimal-ui=1" name="viewport">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <title>
            <?php echo $game->visible_name; ?>
        </title>

        <script>
            window.gamename = '<?php echo $name; ?>';
            window.ver = '<?php echo th::ver(); ?>';
        </script>
        <?php if(KOHANA::$environment == KOHANA::DEVELOPMENT): ?>
            <script src="js/lib/phaser.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/phaser-nocache.min.js?v=<?php echo th::ver(); ?>">
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
            <?php if(!in_array($name,['pharaoh','timemachine'])): ?>
            <script src="js/comps/ui/fgBar.js?v=<?php echo th::ver(); ?>">
            </script>
            <?php endif; ?>
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
            <script src="js/lib/phaser.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="js/lib/zepto.min.js?v=<?php echo th::ver(); ?>">
            </script>
            <script src="jsmin/<?php echo $name; ?>.js?v=<?php echo th::ver(); ?>">
            </script>
        <?php endif; ?>
        <style>
            #bottomBg,#topButtons,#spin-btn,#gamble-btn {display: none;}
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
                    z-index: 2;
                    position: absolute;
                    top: 6vh;
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
                    display: block !important;
                    right: 4%;
                    height: 100%;
                }
                #spin-btn {
                    position: absolute;
                    z-index: 3;
                    width: 23vh;
                    bottom: 5vh;
                    left: 0;
                    right: 0;
                    margin: 0 auto;
                }
                #gamble-btn {
                    position: absolute;
                    z-index: 3;
                    width: 23vh;
                    bottom: 37vh;
                    left: 0;
                    right: 0;
                    margin: 0 auto;
                }
            }
            <?php endif; ?>
        </style>
    </head>
    <body>
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
        <div id="bottomBg">
            <!--<img width="100%" src="/games/agt/images/megahot20/ui/back_vert_bottom.png" />-->
        </div>
        <img id="gamble-btn" onmouseup="javascript:startGamble()" src="/games/agt/images/themes/<?php echo $theme; ?>/buttons/mobile/x2_vert.png" />
        <img id="spin-btn" ontouchstart="startGO();" ontouchend="spinGO();" src="/games/agt/images/themes/<?php echo $theme; ?>/buttons/mobile/spin_vert.png" />
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

                var autoSpin = function () {
                    model.infobar.buttonLayer.children[model.infobar.buttonLayer.children.length-1].onReleased();
                };
                var startGamble = function() {
                    model.infobar.buttonLayer.children[3].onReleased();
                };
                var startGO = function() {
                    start_press=game.time.now;
                };
                var spinGO = function() {
                    canRelease && model.infobar.buttonLayer.children[model.infobar.buttonLayer.children.length-2].onReleased();
                    canRelease=true;
                    start_press=0;
                };
                var checkGO = function() {
                    if(model) {
                        if(!model.auto_spin && model.flags.can_start_auto_spin && start_press>0 && ((game.time.now-start_press)>500)) {
                            eventDispatcher.dispatch(G.START_AUTO_SPIN,model.bets[model.bet_index]*model.k_list[model.k]);
                            canRelease=false;
                        }
                    }
                    setTimeout(checkGO,100);
                }
                checkGO();

                $('img').bind('contextmenu', function(e){
                    return false;
                });
        </script>
    </body>
</html>