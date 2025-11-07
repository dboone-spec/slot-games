<?php $guid=implode('',explode('.',microtime(true))).mt_rand(1000,9999); ?>
<style>
   @font-face {
      font-family: "BebasNeue-Regular";
      font-display: swap;
      src: url("/jpwidget/fonts/BebasNeue-Regular.eot");
      src: local("/jpwidget/fonts/BebasNeue-Regular"),
          local("/jpwidget/fonts/BebasNeue-Regular"), url("/jpwidget/fonts/BebasNeue-Regular.eot?#iefix") format("embedded-opentype"),
          url("/jpwidget/fonts/BebasNeue-Regular.woff") format("woff"), url("/jpwidget/fonts/BebasNeue-Regular.ttf") format("truetype");
      font-weight: 400;
      font-style: normal;
   }

   button:focus {
      outline: none;
   }

   button {
      cursor: pointer;
      background-color: transparent;
      border: none;
   }

   body {
      margin: 0;
   }

   .vidget-jackpots {
      width: 100vw;
      height: 100vh;
      font-family: "BebasNeue-Regular", sans-serif;
   }

   .vidget-jackpots__container {
      width: 100vw;
      height: 100vh;
      padding: 10vw;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      background-image: url("/jpwidget/img/bg.jpg");
      <?php if($bgimg): ?>
          background-image: url("<?php echo $bgimg; ?>");
      <?php endif; ?>
   }

   .vidget-jackpots__header {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
      -webkit-box-pack: justify;
      -ms-flex-pack: justify;
      justify-content: space-evenly;
      height: 16vh;
      margin-top: -6vw;
   }

   .vidget-jackpots__logo {
      height: 10vmax;
      /*min-height: 30px;*/
      width: auto;
      /*min-width: 70px;*/
   }

   .vidget-jackpots__logo .img {
      width: 100%;
      height: 100%;
      -o-object-fit: contain;
      object-fit: contain;
   }

   .vidget-jackpots__info {
      color: gold;
      font-size: 5em;
      line-height: 100%;
      font-weight: bold;
      margin: 0 0 0 10px;
      margin: 0 0 -2vmax -4vmin;
   }

   .vidget-jackpots__wrap {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
      height: 80vh;
   }

   .vidget-jackpots__content {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      grid-column-gap: 80px;
      grid-row-gap: 4vw;
      grid-row-gap: 1vmin;
      width: 100%;
   }

   .vidget-jackpots__item {
      position: relative;
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
      -webkit-box-pack: end;
      -ms-flex-pack: end;
      justify-content: flex-end;
      border: 1px solid grey;
      border-radius: 5px;
      font-size: 3.5em;
      padding: 1vmax;
      background: rgba(0, 0, 0, 0.5);
      color: gold;
   }

   .vidget-jackpots__icon {
      position: absolute;
      top: 0;
      left: -40px;
      width: 85px;
      height: 100%;
      -o-object-fit: contain;
      object-fit: contain;
   }

   .vidget-jackpots__win {
      margin: 5px 0 0 0;
      line-height: 30px;
   }

   iframe {
      border-width: 0;
   }

   @media (max-width: 915px) {
      .example-top {
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         margin: 0;
      }

      .example-top__item_title {
         -webkit-box-ordinal-group: 2;
         -ms-flex-order: 1;
         order: 1;
         text-align: center;
         width: 100%;
         margin: 20px 0 0 0;
      }

      .example-top__item_title span {
         display: none;
      }

      .example-top__item_title br {
         display: none;
      }

      .example-top__column {
         margin: 0 25px 0 0;
      }
   }

   @media (max-width: 870px) {
      .example-top__item:last-child {
         width: 51%;
      }
   }

   @media screen and (max-width: 700px) {
      .vidget-jackpots__content {
         grid-column-gap: 60px;
      }
   }

   @media (max-width: 690px) {
      .example__content {
         grid-template-columns: repeat(3, 1fr);
         margin: 10px 0;
      }

      .example__item:last-child {
         display: none;
      }

      .example__wrap {
         bottom: 15px;
      }

      .example__wrap .example-btn {
         background-color: rgba(30, 162, 249, 0.8);
      }

      .example-top {
         margin: 0 0 10px 0;
      }

      .example-top__item_title {
         -webkit-box-ordinal-group: 1;
         -ms-flex-order: 0;
         order: 0;
         margin: 0 0 10px 0;
         line-height: 15px;
      }

      .example-top__item:first-child {
         width: 100%;
         -webkit-box-pack: center;
         -ms-flex-pack: center;
         justify-content: center;
         margin: 0 0 10px 0;
      }

      .example-top__item:last-child {
         width: 100%;
      }
   }

   @media screen and (max-width: 650px) {
/*      .vidget-jackpots__icon {
         top: 0px !important;
         left: -30px !important;
         width: 65px !important;
         height: 65px !important;
      }*/
   }

   @media screen and (max-width: 550px) {
      .vidget-jackpots__content {
         grid-column-gap: 40px;
      }
   }

   @media screen and (max-width: 510px) {
      .vidget-jackpots__container {
         /*padding: 10px 20px 10px 30px;*/
      }
   }

   @media (max-width: 480px) {
      .example {
         padding: 11px 7px;
      }

      .example__content {
         grid-column-gap: 3px;
      }

      .example__item {
         margin: 0 0 3px 0;
      }

      .example__wrap {
         bottom: 5px;
      }

      .example__icon {
         top: 5px;
         right: 5px;
      }

      .example__icon .img {
         width: 13px;
         height: 13px;
      }

      .example-top {
         -webkit-box-pack: center;
         -ms-flex-pack: center;
         justify-content: center;
      }

      .example-top__avatar {
         margin: 0 15px 0 0;
      }
   }

   @media screen and (max-width: 470px) {
      .vidget-jackpots__item {
         font-size: 3em;
      }

/*      .vidget-jackpots__icon {
         top: 7px !important;
         left: -30px !important;
         width: 55px !important;
         height: 55px !important;
      }*/
   }

   @media screen and (max-width: 405px) {
      .vidget-jackpots__item {
            font-size: 20vmin;
            height: 16vmin;
            padding: 2vmin;
      }

/*      .vidget-jackpots__icon {
         left: -30px !important;
         width: 50px !important;
         height: 50px !important;
      }*/
   }

   @media screen and (max-width: 400px) {
/*      .vidget-jackpots__logo {
         height: 40px !important;
         width: 100px !important;
      }*/

      .vidget-jackpots__info {
         font-size: 2.5em !important;
      }
   }

   @media screen and (max-width: 385px) {
/*      .vidget-jackpots__logo {
         height: 30px !important;
         width: 70px !important;
      }*/

      .vidget-jackpots__info {
         font-size: 2.2em !important;
      }

      .vidget-jackpots__wrap {
         height: 80vh;
      }

      .vidget-jackpots__content {
         grid-template-columns: repeat(1, 1fr);
         width: 60%;
         margin: 0 auto;
      }

      .vidget-jackpots__item {
/*         font-size: 2.1em !important;
         padding: 0px 5px !important;*/
      }

/*      .vidget-jackpots__icon {
         top: -5px !important;
         left: -20px !important;
         width: 40px !important;
         height: 40px !important;
      }*/
   }

   @media (max-width: 330px) {
      .example-top__column {
         margin: 0;
      }
   }

   @media screen and (max-width: 280px) {
      .vidget-jackpots__header {
         /*margin: 0 -10px 0 -20px;*/
      }

/*      .vidget-jackpots__logo {
         margin: 0 0 5px 0 !important;
      }*/
   }

   @media screen and (max-width: 249px) {
      .vidget-jackpots__content {
         width: 75%;
      }
   }

   @media screen and (max-width: 235px) {
      .vidget-jackpots__info {
         font-size: 13vmax !important;
      }
   }

   @media screen and (max-height: 260px) {
/*      .vidget-jackpots__logo {
         height: 60px;
         width: 150px;
      }*/

      .vidget-jackpots__info {
         font-size: 3.5em;
      }
   }

   @media screen and (min-height: 399px) {
      .vidget-jackpots__content {
         grid-template-columns: repeat(1, 1fr);
         width: 70%;
         margin: 0 auto;
      }
   }

   @media screen and (max-height: 230px) {
      .vidget-jackpots__item {
/*         padding: 4.5vh 1vw;
         font-size: 2.5em;*/
      }

/*      .vidget-jackpots__icon {
         width: 60px;
         height: 60px;
      }*/
   }


   @media (min-aspect-ratio: 1/1) {
       .vidget-jackpots__item {
           font-size: 19vmin;
           height: 14vmin;
           padding: 2vmin;
       }
   }

   @media (max-aspect-ratio: 1/2) {
       .vidget-jackpots__item {
           font-size: 20vmin;
           height: 16vmin;
           padding: 2vmin;
       }
   }


   @media (max-aspect-ratio: 3/2) {
       .vidget-jackpots__item {
           font-size: 14vmin;
           height: 11vmin;
           padding: 2vmin;
       }
   }

   @media (max-aspect-ratio: 1/3) {
       .vidget-jackpots__header {
           flex-wrap: wrap;
       }
       .vidget-jackpots__logo,.vidget-jackpots__info {
           width: 100%;
       }
       .vidget-jackpots__info {
            font-size: 24vmin !important;
            text-align: center;
            line-height: 40vmin;
            margin: 0;
       }
   }

   .webp .vidget-jackpots__container {
      background-image: url(<?php echo URL::site('/',true); ?>img/bg.webp);
   }

   .winstate-2 .vidget-jackpots__item {
        color: grey;
    }

   .winstate-1 .vidget-jackpots__item {
       animation-duration: 1200ms;
        animation-name: blink;
        animation-iteration-count: infinite;
        animation-direction: alternate;
        -webkit-animation:blink 1200ms infinite; /* Safari and Chrome */
   }

    @-webkit-keyframes blink {
        to {
            color:red;
        }
    }


</style>
<script>

    var guid='<?php echo $guid; ?>';

    var ws, ws_connected;
    var connectTryTimeout = 2000;
    var agt_jp_win_state=0;


    function connectWS() {

        if(typeof wsAGT!='undefined') {
            return;
        }

        try {
            wsAGT = new WebSocket('<?php echo Kohana::$config->load('static.jp_wss_url'); ?>'+<?php echo $office_id; ?>);
        } catch (e) {
            throw e;
        }

        wsAGT.onerror = function () {
            ws_connected = false;
        };
        wsAGT.onopen = function () {
            connectTryTimeout = 2000;
            ws_connected = true;
        }.bind(this);
        wsAGT.onclose = function () {
            ws_connected = false;
            wsAGT = null;

            setTimeout(function () {
                connectTryTimeout += 500;
                connectWS();
            });
        };

        wsAGT.onmessage = function (event) {
            drawJPData(event.data);
        };
    }

    var currency_code = '<?php echo $office->currency->icon; ?>';

    <?php if($disable_currency): ?>
        currency_code='';
    <?php endif; ?>

    function drawJPData(vals) {

        vals = JSON.parse(vals);

        var ids = document.getElementById('agtjpdata<?php echo $guid; ?>');

        vals.data.reverse().forEach(function(jp,ii) {

            jp = parseFloat(jp);

            var jppostfix = '';
            var fulljp = (jp.toFixed(2) * 100).toFixed(0);
            var length = (fulljp + "").length;

            var tofix = 2;

            if(jp.length>6) {
                jp = jp.slice(0,7);
            }

            if (length == 6) {
                jp = (fulljp / 100);
                tofix = 1;
            } else if (length == 7) {
                jp = (fulljp / 100000);
                jppostfix = 'K';
                tofix = 2;
            } else if (length == 8) {
                jp = (fulljp / 100000);
                jppostfix = 'K';
                tofix = 1;
            } else if (length == 9) {
                jp = (fulljp / 100000000);
                jppostfix = 'M';
                tofix = 3;
            }

            var div = ids.getElementsByClassName("vidget-jackpots__win")[ii];

            var curr_code = String.fromCharCode(parseInt(currency_code, 16));

            if (currency_code.split(',').length > 1) {
                curr_code = '';
                currency_code.split(',').forEach(function (c) {
                    curr_code += String.fromCharCode(parseInt(c, 16));
                });
            }

            div.innerHTML = curr_code + jp.toFixed(tofix) + jppostfix;
        });

        if(agt_jp_win_state!=vals.winstate) {
            ids.classList.remove("winstate-"+agt_jp_win_state);

            agt_jp_win_state=vals.winstate;
            ids.classList.add("winstate-"+agt_jp_win_state);
        }

    }

    connectWS();
</script>
<div class="vidget-jackpots" id="agtjpdata<?php echo $guid; ?>">
    <div class="vidget-jackpots__container">
        <div class="vidget-jackpots__header">
            <div class="vidget-jackpots__logo">
                <picture>
                    <source srcset="/jpwidget/img/logo.webp" type="image/webp"><img src="/jpwidget/img/logo.png" alt="" class="img">
                </picture>
            </div>
            <div class="vidget-jackpots__info"> JACKPOTS </div>
        </div>
        <div class="vidget-jackpots__wrap">
            <div class="vidget-jackpots__content">
                <div class="vidget-jackpots__item">
                    <picture>
                        <source srcset="/jpwidget/img/icon/img2.webp" type="image/webp"><img src="/jpwidget/img/icon/img2.png" alt="" class="vidget-jackpots__icon">
                    </picture>
                    <div class="vidget-jackpots__win">-</div>
                </div>
                <div class="vidget-jackpots__item">
                    <picture>
                        <source srcset="/jpwidget/img/icon/img4.webp" type="image/webp"><img src="/jpwidget/img/icon/img4.png" alt="" class="vidget-jackpots__icon">
                    </picture>
                    <div class="vidget-jackpots__win">-</div>
                </div>
                <div class="vidget-jackpots__item">
                    <picture>
                        <source srcset="/jpwidget/img/icon/img3.webp" type="image/webp"><img src="/jpwidget/img/icon/img3.png" alt="" class="vidget-jackpots__icon">
                    </picture>
                    <div class="vidget-jackpots__win">-</div>
                </div>
                <div class="vidget-jackpots__item">
                    <picture>
                        <source srcset="/jpwidget/img/icon/img1.webp" type="image/webp"><img src="/jpwidget/img/icon/img1.png" alt="" class="vidget-jackpots__icon">
                    </picture>
                    <div class="vidget-jackpots__win">-</div>
                </div>
            </div>
        </div>
    </div>
</div>