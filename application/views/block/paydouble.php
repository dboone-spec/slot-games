<style>
    #suggest_to_double, #suggest_to_double_nogo, #suggest_to_double_go {
        /*display:none;*/
    }
    #suggest_to_double.active {
        display:block;
    }
    #suggest_to_double.active+div {
        display:none;
    }
    #suggest_to_double_go {
        font-size: 36px;
    }
    #suggest_to_double_go {
        width: 100%;
        height: 40px;
        background: none;
        border: #fff 2px solid;
        border-radius: 40px;
    }
    .suggest_to_double_wrapper button:hover {
        /*background-color: orangered;*/
    }
</style>
<div class="suggest_to_double_wrapper">
    <div id="suggest_to_double">
        <div class="section_choose" id="choose_double_two">
            <style>
                .portrait #gamble-game {
                    font-size: .5em
                }
                #gamble-game {
                    border: none;
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-bg.jpg) center no-repeat;
                    -webkit-background-size: 100% 100%!important;
                    -khtml-background-size: 100% 100%!important;
                    -moz-background-size: 100% 100%!important;
                    -o-background-size: 100% 100%!important;
                    background-size: 100% 100%!important;
                }

                #gamble-game #botton-gamble-text {
                    display: block;
                    position: absolute;
                    top: 5%;
                    text-shadow: #000 1px 1px 0, #000 -1px -1px 0, #000 -1px 1px 0, #000 1px -1px 0;
                    color: gold;
                    /*color: #ffffff;*/
                    font: 700 0.8em Tahoma;
                    /* white-space: nowrap; */
                    left: 0;
                    width: 100%;
                    text-align: center;
                }

                #gamble-game,.suggest_to_double_wrapper {
                    width: 100%;
                    height: 337px;
                }

                #gamble-game.invisible {
                    visibility: hidden!important
                }

                #gamble-game.visible {
                    visibility: visible!important
                }

                #play-card {
                    position: relative;
                    width: 15.1%;
                    height: 52.4%;
                    margin: 0 auto;
                    top: 40.1%
                }

                #play-card div {
                    width: 100%;
                    height: 100%
                }

                .red-card-0 {
                    background: url(/games/novomatic/dolphinsd/images/common/red-card-0.png) no-repeat;
                    background-size: 100% 100%
                }

                .red-card-1 {
                    background: url(/games/novomatic/dolphinsd/images/common/red-card-1.png) no-repeat;
                    background-size: 100% 100%
                }

                .black-card-0 {
                    background: url(/games/novomatic/dolphinsd/images/common/black-card-0.png) no-repeat;
                    background-size: 100% 100%
                }

                .black-card-1 {
                    background: url(/games/novomatic/dolphinsd/images/common/black-card-1.png) no-repeat;
                    background-size: 100% 100%
                }

                #history-cards {
                    padding: 1%;
                    position: absolute;
                    top: 22%;
                    left: 52%
                }

                #history-cards .little-card {
                    height: 80%;
                    width: 14%;
                    border-radius: 7px;
                    border: 8px solid #fff;
                    margin: 0 0 0 2.5%;
                    float: left
                }

                #history-cards .little-card:before {
                    content: '';
                    display: inline-block;
                    width: 120%;
                    height: 120%;
                    margin: -10%;
                    border-radius: 9px
                }

                #amount {
                    text-shadow: #000 1px 1px 0, #000 -1px -1px 0, #000 -1px 1px 0, #000 1px -1px 0;
                    text-align: left;
                    width: 50%;
                    position: absolute;
                    top: 14.2%;
                    left: 5%;
                    font: 700 0.8em Tahoma;
                }

                #amount p {
                    margin: 0
                }

                #amount .text {
                    color: #fff;
                    font-size: 1.6em;
                    line-height: 1.2em
                }

                #amount .value {
                    color: #EFF383;
                    margin-top: 0;
                    font-size: 1.6em;
                    line-height: 1.2em
                }

                #to-win {
                    width: 40%;
                    position: absolute;
                    top: 14.2%;
                    right: 5%;
                    text-shadow: #000 1px 1px 0, #000 -1px -1px 0, #000 -1px 1px 0, #000 1px -1px 0;
                    text-align: right;
                    font: 700 0.8em Tahoma;
                }

                #to-win p {
                    margin: 0
                }

                #to-win .text {
                    color: #fff;
                    font-size: 1.6em;
                    line-height: 1.2em
                }

                #to-win .value {
                    color: #EFF383;
                    margin-top: 0;
                    font-size: 1.6em;
                    line-height: 1.2em
                }

                #gamble-black-btn,
                #gamble-red-btn {
                    cursor: pointer;
                    width: 26.3%;
                    height: 28%;
                    border-radius: 50%;
                    position: absolute;
                    top: 39%;
                }

                #gamble-black-btn p,
                #gamble-red-btn p {
                    text-shadow: #fff 1px 1px 0, #fff -1px -1px 0, #fff -1px 1px 0, #fff 1px -1px 0;
                    line-height: 1.3em;
                    text-align: center;
                    font: 900 1.7em Ebrima, sans-serif
                }

                #gamble-red-btn p {
                    margin: 110% 0 0;
                }

                #gamble-black-btn p {
                    margin: 110% 0 0;
                }

                #gamble-red-btn {
                    left: 10.3%
                }

                #gamble-red-btn.disable {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 56.69% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-red-btn.disable p {
                    color: #666
                }

                #gamble-red-btn.off {
                    pointer-events: none
                }

                #gamble-red-btn.enable {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 71.2% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-red-btn.enable:hover {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 100.3% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-red-btn.enable:hover p {
                    text-shadow: #fff 1px 1px 5px, #fff -1px -1px 5px, #fff -1px 1px 5px, #fff 1px -1px 5px
                }

                #gamble-red-btn.enable:active {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 85.76% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-red-btn p {
                    color: red
                }

                #gamble-black-btn {
                    left: 63.6%
                }

                #gamble-black-btn.disable {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 .5% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-black-btn.disable p {
                    color: #666
                }

                #gamble-black-btn.off {
                    pointer-events: none
                }

                #gamble-black-btn.enable {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 15% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-black-btn.enable:hover {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 44.1% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-black-btn.enable:hover p {
                    text-shadow: #fff 1px 1px 5px, #fff -1px -1px 5px, #fff -1px 1px 5px, #fff 1px -1px 5px
                }

                #gamble-black-btn.enable:active {
                    background: url(/games/novomatic/dolphinsd/images/common/gamble-btn-sprite.png) 0 29.54% no-repeat;
                    background-size: 100% 900%
                }

                #gamble-black-btn p {
                    color: #000
                }

                #play-card {
                    -webkit-transform: translateZ(0);
                    -moz-transform: translateZ(0);
                    -ms-transform: translateZ(0);
                    -o-transform: translateZ(0);
                    transform: translateZ(0);
                    will-change: -webkit-transform;
                    will-change: -moz-transform;
                    will-change: -ms-transform;
                    will-change: -o-transform;
                    will-change: transform
                }

                #play-card div.blink-0 {
                    background: url(/games/novomatic/dolphinsd/images/common/card-001.png) no-repeat;
                    background-size: 100% 100%!important
                }

                #play-card div.blink-1 {
                    background: url(/games/novomatic/dolphinsd/images/common/card-002.png) no-repeat;
                    background-size: 100% 100%!important
                }

                #play-card.show-process {
                    -webkit-animation: webkit-close-card .2s;
                    -moz-animation: close-card .2s;
                    -ms-animation: close-card .2s;
                    -o-animation: close-card .2s;
                    animation: close-card .2s
                }

                #play-card.open-process {
                    width: .2%;
                    -webkit-animation: webkit-open-card .2s;
                    -moz-animation: open-card .2s;
                    -ms-animation: open-card .2s;
                    -o-animation: open-card .2s;
                    animation: open-card .2s
                }



                .landscape .layer .gamble {
                    z-index: 12000;
                    top: -120.1%;
                    margin: 0;
                    width: 100%;
                    height: 100%
                }

                @-webkit-keyframes webkit-close-card {
                    to {
                        width: .2%
                    }
                }

                @keyframes close-card {
                    to {
                        width: .2%
                    }
                }

                @-webkit-keyframes webkit-open-card {
                    to {
                        width: 15.1%
                    }
                }

                @keyframes open-card {
                    to {
                        width: 15.1%
                    }
                }
            </style>
        </div>
        <div class="layer gamble move-bottom process">
            <div id="gamble-game">
               <div id="play-card">
                  <div class="blink-0"></div>
               </div>
               <div id="to-win">
                   <p class="text"><?php echo __('После удвоения') ?></p>
                  <p class="value">10</p>
               </div>
               <div id="amount">
                   <p class="text"><?php echo __('Текущий выигрыш') ?></p>
                  <p class="value">5</p>
               </div>
               <div id="botton-gamble-text">
                  <p><?php echo __('Выберите красное или черное, чтобы рискнуть, или заберите выигрыш'); ?>!</p>
               </div>
               <div id="gamble-red-btn" class="enable on">
                  <p><?php echo __('Красное'); ?></p>
               </div>
               <div id="gamble-black-btn" class="enable on">
                  <p><?php echo __('Черное'); ?></p>
               </div>
            </div>
         </div>
    </div>
    <button id="suggest_to_double_nogo" class="btn-blue">
        <?php echo __('Вывести выигрыш'); ?>
    </button>
</div>
<script>
    $('#suggest_to_double_nogo').click(function() {
        $('#iframe .layout').show('slow');
        $('[name=nodouble]').val('1');
        $('#payment_form').submit();
        $('.suggest_to_double_wrapper').remove();
    });
    var curr_b=1;
    var stop_blink=false;

    function blink() {


        if(stop_blink) {
            return;
        }

        $('#play-card div').removeClass('blink-0');
        $('#play-card div').removeClass('blink-1');
        if(curr_b==0) {
            curr_b=1;
        }
        else {
            curr_b=0;
        }
        $('#play-card div').addClass('blink-'+curr_b);
        setTimeout(blink,100);
    }

//    blink();

    function update_amounts() {
        $('#suggest_to_double_nogo').attr('disabled','disabled');
        $('#amount .value').text($('[name=amount]').val());
        $('#to-win .value').text(parseInt($('[name=amount]').val())*2);

        if(parseInt($('[name=amount]').val())<=0) {
            return setTimeout(function() {
                window.location.reload();
            },2000);
        }
        stop_blink=false;
        $('#suggest_to_double_nogo').removeAttr('disabled');
    }

    update_amounts();

    function getCard(color) {
        return color+'-card-'+(Math.round(Math.random()));
    }

    function selectcard(card) {

        stop_blink=true;

        var amount = $('[name=amount]').val();

        $('#play-card').addClass('show-process');
        $('#play-card').addClass('open-process');
        $.ajax({
            url: '/payment/double',
            data: {
                'amount': amount,
                'card': card
            },
            'dataType': 'json',
            'type': 'POST',
            success: function(data) {
                $('#play-card').removeClass('show-process');
                $('#play-card').removeClass('open-process');

                var cls='';

                if(parseInt(data.win)>0) {
                    cls=getCard(card);
                }
                else {
                    if(card == 'red') {
                        cls=getCard('black');
                    }
                    else {
                        cls=getCard('red');
                    }
                }
                $('#play-card div').removeClass('blink-0');
                $('#play-card div').removeClass('blink-1');
                $('#play-card').addClass(cls);

                $('[name=amount]').val(data.win);
                update_amounts();
            }

        });
    }

    $('#gamble-red-btn').click(function() {
        selectcard('red');
    });

    $('#gamble-black-btn').click(function() {
        selectcard('black');
    });
</script>