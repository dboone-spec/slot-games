<style>
            #dialog_black_back {
                position: fixed;
                text-align: center;
                width: 100%;
                height: 100%;
                z-index: 1000;
                top: 0;
                left: 0;
                background: rgba(0,0,0,0.96);
                display: none;
            }

            .no-flash-message {
                padding: 0 25px;
            }

            .no-flash-message > div > p {
                display: block;
                text-align: center;
                margin: 0;
                margin-bottom: 15px;
                line-height: 1.35;
            }

            .no-flash-message.rotate > div {
                -webkit-transform: rotate(-90deg);
                -ms-transform: rotate(-90deg);
                -o-transform: rotate(-90deg);
                transform: rotate(-90deg);
            }

            #invisible_helper {
                display: -moz-inline-box;
                display: inline-block;
                vertical-align: middle;
                height: 100%;
                width: 0;
                zoom: 1;
            }

            #error_message {
                font-family: 'UniNeueHeavy', sans-serif;
                display: inline-block;
                color: coral;
                text-align: center;
            }

            #error_message a.flashbutton{
                text-decoration: none;
                color: #fff;
                display: inline-block;
                border: 1px dashed #fff;
                padding: 10px;
            }

            #error_message a.flashbutton:hover{
                border: 1px solid #fff;
            }
        </style>
        <script>
            function checkIfFlashEnabled() {
                var isFlashEnabled = false;
                if (typeof(navigator.plugins) != "undefined" && typeof(navigator.plugins["Shockwave Flash"]) == "object") {
                    isFlashEnabled = true;
                } else if (typeof(window.ActiveXObject) != "undefined") {
                    try {
                        if (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) {
                            isFlashEnabled = true;
                        }
                    } catch (e) {}
                }
                return isFlashEnabled;
            }
            window.onload = function() {
                if (!checkIfFlashEnabled()) {
                    document.getElementById('dialog_black_back').style.display='block';
                }
            }
        </script>
        <div id="dialog_black_back" class="no-flash-message" style="display: none;">
            <div id="error_message">
                <p>На данном устройстве не обнаружена поддержка технологии Flash, которая необходима для работы игры.</p>
                <p>Пожалуйста, нажмите на кнопку ниже, для того, чтобы активировать либо установить Flash плеер.</p>
                <a class="flashbutton" href="https://get.adobe.com/flashplayer/">Flash Player</a>
            </div>
            <div id="invisible_helper"></div>
        </div>