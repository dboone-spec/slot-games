<!DOCTYPE html>
<html>

<head>
    <meta charset=utf-8>
    <title>| StarGames Casino</title>
    <meta name=viewport content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <body class="c-page c-page--empty is-authenticated">
        <style></style>
        <div class=gs-body>
            <script>
                window.nrgsConfig = {
                    apiRootUrl: 'nrgs/en/'
                };
            </script>
            <script src=/nrgs/en/assets/public/widgets/nrgs.widgets.js></script>
            <script src=//service.maxymiser.net/cdn/stargames/js/mmcore.js></script>
            <link rel=preload href=Content/styles/build/source-sans.woff2.50047ae.css as=style>
            <link rel=stylesheet href=Content/styles/build/source-sans.woff2.50047ae.css>
            <link href=Content/styles/build/style.7e69cab.css rel=stylesheet>
            <link href=Content/styles/build/ingame.e2bb103.css rel=stylesheet>
            <link rel=preload href=Content/scripts/build/ingameOverlay.7eb2950.js as=script>
            <style>
                html {
                    overflow-y: hidden !important
                }

                html,
                body,
                .gs-body,
                .nrgs-startGame {
                    height: 100%;
                    max-height: 100vh
                }

                .gs-body {
                    color: white;
                    text-align: center
                }

                .nrgs-startGame {
                    display: flex;
                    align-items: center;
                    justify-content: center
                }

                iframe {
                    width: 1px;
                    min-width: 100%
                }

                .nrgs-widget {
                    overflow: hidden
                }

                .fullscreengame iframe {
                    left: 0
                }

                .c-fullscreenClose {
                    position: absolute;
                    top: 0;
                    right: 0
                }

                @media screen and (max-width:1024px) {
                    .c-fullscreenClose {
                        display: none
                    }
                    .has-fallback-fullscreen .c-fullscreenClose {
                        display: block
                    }
                }

                #realitycheck {
                    width: 90%;
                    background-color: #212121 !important;
                    border-color: #ffe349 rgba(255, 255, 255, 0.3) rgba(255, 255, 255, 0.3) !important;
                    border-style: solid !important;
                    border-width: 5px 1px 1px !important;
                    padding: 20px !important;
                    font-size: 0.8em !important;
                    font-family: "Roboto", sans-serif !important
                }

                @media screen and (min-width:768px) {
                    #realitycheck {
                        width: 75%;
                        font-size: 1em !important
                    }
                }

                @media screen and (min-width:1024px) {
                    #realitycheck {
                        width: 40%
                    }
                }

                #realitycheck button {
                    display: block;
                    width: 100%;
                    margin-top: 20px;
                    padding-right: 10px;
                    padding-left: 10px;
                    background-color: #ffe349;
                    color: #000000;
                    max-width: 100%;
                    min-height: 30px;
                    line-height: 26px;
                    padding: 0 10px;
                    font-weight: 600;
                    text-transform: uppercase;
                    text-align: center;
                    border-radius: 5px;
                    border: 2px solid transparent;
                    cursor: pointer;
                    user-select: none;
                    white-space: nowrap
                }

                html.has-overlay #game1 {
                    visibility: hidden
                }

                #game1 {
                    width: 100%;
                    height: 100%
                }
            </style>
            <script src=Content/scripts/build/runtime.64faced.js></script>
            <script src=Content/scripts/build/vue/vendor.e544367.js></script>
            <script>
                window.geoIPDetectionFailed = false;
                window.getCookie = function(cname) {
                    var regexp = new RegExp(cname + '=([^;]+)');
                    var result = regexp.exec(document.cookie);
                    return result === null ? '' : result[1];
                };
                window.userIsLoggedIn = true;
                window.lastUserNickname = window.userIsLoggedIn ? window.getCookie('userToken') : '';
                window.userLanguage = "en";
                window.languages = {
                    "de": {
                        "cultureCode": "de-AT",
                        "name": "Deutsch",
                        "shortCode": "de",
                        "icon": "langde",
                        "url": "de"
                    },
                    "en": {
                        "cultureCode": "en-GB",
                        "name": "English",
                        "shortCode": "en",
                        "icon": "langen",
                        "url": "en"
                    }
                };
                window.isProduction = true;
                window.leaderboardUnlockLevel = 5 ? 5 : 0;
                window.LastInvalidatedCache = "20190912095612", window.nrgsConfig = {
                    apiRootUrl: 'nrgs/en/'
                };
                window.headerSettings = {
                    simpleheader: false,
                    showonlyregister: false
                };
                window.EnglishUrl = "";
                window.EnPageTitle = "";
            </script>
            <script src="en/GTJavascriptVarInit.js?region=Default&amp;v=20190912095612"></script>
            <script>
                window["adrum-app-key"] = "AD-AAB-AAB-JXP";
                window['adrum-start-time'] = new Date().getTime();
            </script>
            <script src=https://cdn.appdynamics.com/adrum/adrum-latest.js></script>
            <script src=Content/scripts/build/main.f9d3d25.js></script>
            <script src=Content/scripts/build/vue/app.e972ba9.js defer></script>
            <script>
                window.currentGameId = 149;

                function onReady() {
                    if (false) {
                        window.top.history.replaceState({
                            closeGame: true
                        }, window.top.document.title, window.top.location.pathname);
                        window.top.history.pushState({}, window.top.document.title, window.top.location.pathname);
                    }
                }
                if (document.readyState !== 'loading') {
                    onReady();
                } else {
                    document.addEventListener('DOMContentLoaded', onReady);
                }

                function GameInitCallback(widget) {
                    widget.onSuccess(GameStartSuccess);
                    widget.onFailure(GameStartFail);
                    widget.onGameEnd(GameEnd);
                    var handlerAdded, fullScreenGame1 = document.querySelector('#fullscreengame1'),
                        inGameAppContainer = document.createElement('div');
                    inGameAppContainer.id = 'ingame-app';
                    fullScreenGame1.appendChild(inGameAppContainer);
                    if (window.matchMedia && window.matchMedia('(min-width:1025px), (pointer:course)').matches) {
                        window.gt.ingameAppInit();
                    } else {
                        var currentGame = window.top.GTGamesJsonBrief[(149)];
                        if (currentGame != null) {
                            for (var i = 0; i < currentGame.Tags.length; i++) {
                                if (currentGame.Tags[i].Type === 'mobileInGameHeaderHidden') {
                                    return;
                                }
                            }
                        }
                        window.gt.ingameAppInit();
                    }
                }

                function GameStartSuccess(data) {
                    if (data && data.params && data.params.freeSpinsMode === "1") {
                        window.postMessage("CS-GAME_MODE:FREESPINS", "*");
                    } else {
                        window.postMessage("CS-GAME_MODE:NORMAL", "*");
                    }
                    triggerTracking(window.top.gt.analytics.events.gameStarted, data);
                }

                function GameEnd(event) {
                    if (event != null && event.restartGame != null && event.restartGame) {
                        window.location.reload();
                    }
                }

                function GameStartFail(data) {
                    triggerTracking(window.top.gt.analytics.events.gameNotStarted, data);
                }

                function triggerTracking(event, data) {
                    var gmnName = "";
                    var gmnRes = window.top.GTGamesJsonBrief[(149)];
                    var gmnTag = null;
                    if (gmnRes != null) {
                        gmnName = gmnRes.GameName;
                        for (var i = 0; i < gmnRes.Tags.length; i++) {
                            if (['slots', 'casino', 'poker', 'skill', 'bingo', 'roulette', 'blackjack', 'match3'].indexOf(gmnRes.Tags[i].Type) > -1) {
                                gmnTag = gmnRes.Tags[i].Type;
                            }
                        }
                    }
                    if (typeof(window.top.gt) !== "undefined" && window.top.gt.analytics) {
                        var obj = {
                            gameName: gmnName,
                            gameStartedFrom: 'slots',
                            gameStartedFilter: 'all',
                            gameTag: gmnTag,
                            splitscreenLevel: 1,
                            recommendedGame: window.top.gt.analytics.triggers.trackRecommended ? true : false,
                            leaderboard: gmnRes.NRGSID,
                            challenges: gmnRes.NRGSID,
                            challengeId: window.top.gt.$GlobalStore.getters['challenges/getCurrentChallengeData'] ? window.top.gt.$GlobalStore.getters['challenges/getCurrentChallengeData'].Id : undefined,
                            freeSpins: data && data.params && data.params.freeSpinsMode === "1" ? true : false
                        };
                        window.top.gt.analytics.trackEvent(event, obj);
                        window.top.gt.analytics.triggers.trackRecommended = false;
                    }
                }

                function GameDestroyCallback(data) {
                    console.log("GameDestroyCallback");
                }
            </script>
            <div class=nrgs-startGame data-nrgs-panel=first-panel data-nrgs-gameid=149 data-nrgs-mode=fun data-nrgs-params="{ &#34;fixedContainer&#34;: &#34;1&#34;, &#34;preventopenlink&#34;: &#34;1&#34;, &#34;helpurl&#34;: &#34;en/slots/lord-of-the-ocean/&#34;, &#34;payinurl&#34;: &#34;disabled&#34;,&#34;closeurl&#34;:&#34;&#34;, &#34;wmode&#34;: &#34;opaque&#34;, &#34;autofullscreen&#34; : &#34;0&#34; , &#34;ismusicactive&#34;:&#34;1&#34;, &#34;postmessagetarget&#34; : &#34;self&#34;}" data-nrgs-oninit=GameInitCallback data-nrgs-ondestroy=GameDestroyCallback data-nrgs-freespinsserviceurl=https://at-freespins-v1.greentube.com/api/player data-hj-suppress></div>
        </div>