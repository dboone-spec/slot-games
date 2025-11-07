<!DOCTYPE html>
<html lang="en">

    <head>
        <title>NOVOMATIC</title>
        <meta charset="utf-8">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport"
              content="width=device-width,height=device-height,initial-scale=1.01,maximum-scale=1.01,minimum-scale=1.01,user-scalable=no" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="mobile-web-app-capable" content="yes">

        <link rel="apple-touch-icon" href="img/favicon/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="img/favicon/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="img/favicon/apple-touch-icon-114x114.png">

        <link rel="stylesheet" href="css/main.min.css">

        <style>
            #winAnimation {
                width: 100%;
                height: 100%;
            }
        </style>
        <!-- Fonts-->
        <link
            href='https://fonts.googleapis.com/css?family=Roboto:400,300,500,700&amp;subset=latin,latin-ext,cyrillic-ext,cyrillic,greek-ext,greek'
            rel='stylesheet' type='text/css'>

        <script type="text/javascript" src="content/js/jquery-2.1.3.min.js"></script>

        <meta name="theme-color" content="#ffffff">

        <script>

                    (function () {
                        // An array of all contexts to resume on the page
                        const audioContextList = [];

                        // An array of various user interaction events we should listen for
                        const userInputEventNames = [
                            'click', 'contextmenu', 'auxclick', 'dblclick', 'mousedown',
                            'mouseup', 'pointerup', 'touchend', 'keydown', 'keyup'
                        ];

                        const audioContextName = "undefined" !== typeof AudioContext ? "AudioContext" : "webkitAudioContext";

                        // A proxy object to intercept AudioContexts and
                        // add them to the array for tracking and resuming later
                        self[audioContextName] = new Proxy(self[audioContextName], {
                            construct(target, args) {
                                const result = new target(...args);
                                audioContextList.push(result);
                                return result;
                            }
                        });

                        // To resume all AudioContexts being tracked
                        function resumeAllContexts(event) {
                            let count = 0;

                            audioContextList.forEach(context => {
                                if (context.state !== 'running') {
                                    context.resume()
                                } else {
                                    count++;
                                }
                            });

                            // If all the AudioContexts have now resumed then we
                            // unbind all the event listeners from the page to prevent
                            // unnecessary resume attempts
                            if (count == audioContextList.length) {
                                userInputEventNames.forEach(eventName => {
                                    document.removeEventListener(eventName, resumeAllContexts);
                                });
                            }
                        }

                        // We bind the resume function for each user interaction
                        // event on the page
                        userInputEventNames.forEach(eventName => {
                            document.addEventListener(eventName, resumeAllContexts);
                        });
                    })();
        </script>

        <script type="text/javascript">
            'use strict';


            function urlParam(name, defVal) {
                var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
                if (results == null) {
                    return defVal;
                } else {
                    return results[1] || defVal;
                }
            }

            function toUriString(data, encodeValues) {
                var encodeValues = encodeValues || true;
                var result = '';
                var keys = Object.keys(data);
                for (var i = 0; i < keys.length; i++) {
                    var value = encodeValues ? encodeURIComponent(data[keys[i]]) : data[keys[i]];
                    result += keys[i] + '=' + value;
                    if (i < (keys.length - 1)) {
                        result += '&';
                    }
                }
                return result;
            }

            function getClientInfo() {
                var langs = JSON.stringify(window.navigator.languages);
                var gameRes = window.innerWidth + "x" + window.innerHeight;
                return {
                    'UA': window.navigator.userAgent + " GAME (lg" + langs + " GR[" + gameRes + "])",
                    'L': window.navigator.language,
                    'GR': gameRes,
                    'R': screen.width + "x" + screen.height,
                    'TZ': new Date().toString().match(/([A-Z]+[\+-][0-9]+)/)[1],
                    'LS': langs,
                    'OS': navigator.oscpu ? navigator.oscpu : navigator.platform,
                }
            }

            function getLaunchUrl() {
                return decodeURIComponent(urlParam('launch'));
            }

            var denomination, denIndex, densetUrl;


            var closing = false;

            function doPostMessage(message) {

                if (closing)
                    return;

                closing = true;

                $.get(decodeURIComponent(urlParam('exit')))
                        .always(function () {
                            if (window.parent) {
                                window.parent.postMessage(message, "*");
                            }

                            if (window.opener) {
                                window.opener.postMessage(message, "*");
                            }
                            window.location.replace(decodeURIComponent(urlParam('exit')));
                        });
            }

            window.addEventListener('beforeunload', function () {
                console.log('POST MESSAGE beforeunload');
                doPostMessage("closeFrame");
            });

            window.addEventListener('onbeforeunload', function () {
                console.log('POST MESSAGE onbeforeunload');
                doPostMessage("closeFrame");
            });

            window.addEventListener('onunload', function () {
                console.log('POST MESSAGE onunload');
                doPostMessage("closeFrame");
            });


            var jackpotScript = false;
            var buffer = [];
            var jackpotConnection = true;
            var connection;
            var launchInfo = {
                game: '<?php echo $game->visible_name; ?>',
//                denomination: {
//                    selected:0.01,
//                    values:[0.01,0.02,0.05,0.1],
//                }
            };

            function request_data(p) {

                var request = new XMLHttpRequest();
                request.open('POST', window.location.pathname+'init.php', true);

                request.onload = function (d) {
                    if (this.status >= 200 && this.status < 400) {
                        // Success!
                        if(this.responseText.length>0) {
                            var j = JSON.parse(this.responseText);
                            j && j.forEach(function(w) {
                                CONFIG.CLIENTSLOTAPPLET.connection.connection.connection.onmessage({data:w});
                            });
                        }
                    }
                };

                request.onerror = function () {

                };

                request.send(p);
            }

            WebSocket.prototype.send = function(d) {
                return request_data(d);
            };

            if(window.location.search.length==0) {
                var tech = window.location.href+'tech';
                window.location.replace(window.location.href+'?tech='+tech);
            }

            function createConnection() {
                console.log("Create jackpots connection");
                var conn = new WebSocket(launchInfo.jackpotEndpoint);

                conn.onmessage = function (event) {
                    var data = JSON.parse(event.data);
                    var jackpot = {
                        "id": data.id,
                        "jackName": data.name,
                        "action": data.action,
                        "currency": data.currency,
                        "amount": parseFloat(data.amount),
                        "eventText": data.action == 'HIT' ? "Some player won jackpot \"" + data.name + "\" (" + parseFloat(data.amount) + " " + data.currency + ")" : ""
                    };

                    console.log(jackpot);

                    if (window.panel != undefined && window.panel != null) {
                        window.panel.update(JSON.stringify([jackpot]));
                        adjustGameHeight();
                    } else {
                        console.log("buffering jackpot: " + jackpot);
                        buffer.push(JSON.stringify([jackpot]))
                    }
                };

                conn.onclose = function (event) {
                    conn.onmessage = function () {
                    };
                    conn.onclose = function () {
                    };
                    conn.onerror = function () {
                    };
                    jackpotConnection = false;
                };

                conn.onerror = function (event) {
                    conn.onmessage = function () {
                    };
                    conn.onclose = function () {
                    };
                    conn.onerror = function () {
                    };
                    jackpotConnection = false;
                };
                return conn;
            }


            document.addEventListener('launch:connectionClosed', function () {
                setTimeout(function () {
                    connection = createConnection();
                }, 5000);
            });

            function addScript(src) {
                var script = document.createElement("script");
                script.type = "text/javascript";
                script.src = src;
                document.getElementsByTagName('head')[0].appendChild(script);
            }

            function addStylesheet(src) {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.type = 'text/css';
                link.href = src;
                link.media = 'all';
                document.getElementsByTagName('head')[0].appendChild(link);
            }

            function addGameWidget(container) {
                var elem = document.createElement('div');
                elem.id = 'gameDiv';
                elem.style.position = 'relative';
                elem.style.height = '100%';
                elem.className = 'nrgs-startGame';

                elem.setAttribute('data-nrgs-params', JSON.stringify({
                    closeurl: decodeURIComponent(urlParam('exit')),
                    "button.fullscreen.show": urlParam("fullScreenEnabled", "1"),
                    "button.widescreen.show": urlParam("wideScreenEnabled", "1"),
                }));

                container.appendChild(elem);
            }

            function adjustGameHeight() {
                var game = document.getElementById('game');
                game.style.height = window.innerHeight + 'px';
            }

            function startGame() {

                var game = document.getElementById('game');
                game.style.display = null;

                var denWidget = document.getElementById('den-widget');
                document.body.removeChild(denWidget);


                addGameWidget(game);
                addScript("nrgs/en/assets/nrgs.bundle.js");

                window.addEventListener('resize', function () {
                    adjustGameHeight();
                });

                adjustGameHeight();
            }

            function countChange(delta) {
                var newIndex = denIndex + delta;
                if (newIndex < 0 || newIndex > (denomination.length - 1)) {
                    return;
                }
                denIndex = newIndex;
                denSet();
            }

            function denSet() {
                document.getElementById('den-count-view').textContent = denomination[denIndex].toString();
            }

            document.addEventListener('DOMContentLoaded', function () {

                window.frames.controlframe = window;
                document.title = launchInfo.game;

                // den set
                var denoms = launchInfo.denomination;
                if (denoms && denoms.values && denoms.values.length > 1) {

                    denomination = denoms.values;
                    denIndex = denomination.indexOf(denoms.selected);

                    var widget = document.getElementById('den-widget');
                    widget.style.display = null;

                    document.getElementById('den-minus-btn').addEventListener('click', function (e) {
                        console.log('den-minus-btn');
                        countChange(-1);
                    });

                    document.getElementById('den-plus-btn').addEventListener('click', function (e) {
                        console.log('den-plus-btn');
                        countChange(1);
                    });


                    document.getElementById('den-start-btn').addEventListener('click', function (e) {
                        console.log('den-start-btn');
                        e.preventDefault();
                        startGame();

                        var data = {
                            'session': urlParam('session'),
                            'sign': urlParam('sign'),
                            'denom': denomination[denIndex]
                        };
                    });
                    denSet();
                } else {
                    startGame();
                }

                if (launchInfo.jackpotEndpoint) {
                    console.log('add jackpots');
                    addStylesheet('/jackpot-panel/css/style.css');
                    addScript('/jackpot-panel/js/counter.js');
                    // jackpot panel
                    window.addEventListener("gameReady", function (e) {
                        console.log("on gameReady");
                        if (!jackpotScript) {
                            addScript("/jackpot-panel/js/novo.js")
                        }
                        if (!jackpotConnection) {
                            connection = createConnection();
                        }
                    }, false);

                    window.addEventListener("gameModeReady", function (e) {
                        if (!jackpotConnection) {
                            connection = createConnection();
                        }
                    }, false);

                    document.addEventListener('launch:panelCreated', function () {
                        if (window.panel != undefined && window.panel != null) {
                            while (buffer.length > 0) {
                                console.log('add jackpot from bufer...');
                                window.panel.update(buffer.shift());
                            }
                        }
                    });


                    connection = createConnection();
                }
            });

        </script>


    </head>

    <body>
        <div id="den-widget" class="fullscreen" style="display: none">
            <div class="cube-a">
                <div class="cube-b">
                    <h1 class="h1">Denomination</h1>
                    <div class="circle-box">
                        <div class="l-ar">
                            <div class="l-item-wrapper">
                                <div class="l-ar-item minus" id="den-minus-btn"></div>
                            </div>
                        </div>
                        <div class="circle" id="den-count-view">

                        </div>
                        <div class="r-ar">
                            <div class="r-item-wrapper">
                                <div class="r-ar-item plus" id="den-plus-btn"></div>
                            </div>
                        </div>
                    </div>
                    <div class="start-wrapper">
                        <a href="#" class="start" id="den-start-btn"></a>
                    </div>
                </div>
            </div>
        </div>

        <div id="game" style="width: 100%; height: 100%; display: none; overflow: hidden">
        </div>
<?php echo block::rfid_listen(); ?>
    </body>

</html>