<script>
    window.have_freespins = true;
</script>
<style>
    #change_freespins { font-weight: 400; text-transform: uppercase; text-align: center; color: #ffffff; margin: 0 auto; margin-top: 20%; height: 50%; width: 856px; }
    #change_freespins:before {
        content: " ";
        background: url(/assets/img/freespin/bg_up.png) 100% no-repeat;
        display: block;
        position: absolute;
        width: 856px;
        height: 432px;
        z-index: -100;
        top: -50%;
    }
    #change_freespins:after {
        content: " ";
        background: url(/assets/img/freespin/bg_down.png) 100% no-repeat;
        display: block;
        position: absolute;
        width: 856px;
        height: 432px;
        z-index: -100;
        top: 60%;
    }
    #change_freespins h1, #change_freespins h2 { margin: 0; }
    #change_freespins h2.gold { color: gold; }
    #change_freespins .left_games_block {
        width: 360px;
        float: left;
        background: url(/assets/img/freespin/rectangle_2.png) 100% no-repeat;
        height: 224px;
    }
    #change_freespins .left_games_block .title{ text-align: center; margin-top: 25px; }
    #change_freespins hr:after {
        content: " ";
        background: url(/assets/img/freespin/line.png) 100% no-repeat;
        width: 99%;
        z-index: 1000;
        display: block;
        height: 15px;
    }
    #change_freespins hr { line-height: 0; border: none; margin: 0; }
    #change_freespins .left_games_block .left_freespins{ font-weight: bold; font-size: 100px; line-height: 115px; }
    #change_freespins .right_games_block {
        width: 360px;
        float: left;
        background: url(/assets/img/freespin/rectangle_2.png) 100% no-repeat;
        height: 224px;
    }
    #change_freespins .rules_line { line-height: 50px; }
    #change_freespins .rules_line:first-child { margin-top: 15px; }
    .freespins_play_now {
        cursor: pointer;
        background: url(/assets/img/freespin/big_green_button_atlas.png) 100%;
        width: 357px;
        height: 85px;
        border: none;
        font-size: 32px;
        color: #fff;
        cursor: pointer;
        background-position-x: -1110px;
    }
    .freespins_play_now:hover {
        background-position-x: 1096px;
    }
    .freespins_play_future {
        cursor: pointer;
        background: url(/assets/img/freespin/big_orange_button_atlas.png) 100%;
        width: 360px;
        height: 85px;
        border: none;
        font-size: 32px;
        color: #fff;
        cursor: pointer;
        background-position-x: -1108px;
    }
    .freespins_play_future:hover {
        background-position-x: 1096px;
    }
    #freespins_dont_play {
        cursor: pointer;
        background: none;
        border: none;
        color: #fff;
        text-decoration: underline;
        font-weight: 800;
        line-height: 144px;
        line-height: 20px;
    }
    #change_freespins button {
        float: left;
        text-transform: uppercase;
        float: right;
    }
    #change_freespins .buttons {
        display: block;
        width: 720px;
        margin: 0 auto;
    }
    .how-much-offers {
        position: relative;
        height: 88px;
        width: 240px;
        display: inline-block;
        background: url(/assets/img/sys/sprites/sprite1664.png);
    }
    .how-much-offers button ,
    .how-much-offers span {
        position: absolute;
    }
    .how-much-offers button:hover {
        background-position: 117px -158px;
        cursor: pointer;
        outline: none;
    }
    .how-much-offers .button-left {
        z-index: 1;
        left: 10px;
        top: 14px;
        height: 66px;
        width: 49px !important;
        background: url(/assets/img/sys/sprites/sprite1664.png);
        background-position: -12px -158px;
        border: none;
    }
    .how-much-offers .button-right {
            z-index: 1;
            right: 10px;
            top: 10px;
            height: 66px;
            width: 49px !important;
            background: url(/assets/img/sys/sprites/sprite1664.png);
            background-position: -12px -158px;
            border: none;
            transform: rotateZ(180deg);
    }
    .how-much-offers .button-right:active,
    .how-much-offers .button-left:active {
        background-position: 61px -158px;
        outline: none;
    }
    .how-much-offers .content {
        background: transparent;
        width: 120px;
        height: 59px;
        z-index: 1;
        top: 15px;
        left: 60px;
        text-transform: uppercase;
        font-size: 15px;
        padding-top: 8px;
    }
    .buttons button {
        outline: none;
    }
    .wraper-for-game-blockes {
        margin: 0 auto;
        display: block;
        width: 720px;
    }

    /* мои стили */

    .change_freespins-body {
        margin-top: 0 !important;
        width: 100% !important;
    }
    .change_freespins-body:after,
    .change_freespins-body:before {
        width: 100% !important;
            background-size: contain !important;
    }
    .change_freespins-body .left_games_block,
    .change_freespins-body .right_games_block {
        width: 49% !important;
        background-size: 100% 100% !important;
        display: inline-block !important;
        height: 100% !important;
        float: none !important;
        position: relative !important;
        min-height: 101px !important;
        padding-top: 4% !important;
    }
    .change_freespins-body .buttons {
        width: 100% !important;
        margin: 0 !important;
        flex-wrap: wrap !important;
        display: flex !important;
        justify-content: space-around !important;
    }
    .change_freespins-body button {
        width: 49% !important;
        float: none !important;
    }
    .change_freespins-body .how-much-offers button {

    }
    .change_freespins-body hr:after {
            width: 96% !important;
            background-size: cover!important;
    }
    .change_freespins-body .left_games_block .title {
            font-size: 2em !important;
        margin-top: 0 !important;
    }

    /*  */
    .change_freespins-body .left_games_block .left_freespins {
            font-size: 5em !important;
            line-height: normal !important;
    }

    .change_freespins-body .rules_line {
        font-size: 2em  !important;
        line-height: normal  !important;
        margin-top: 0 !important;
    }

        /*  */

    .change_freespins-body .wraper-for-game-blockes {
        width: 100% !important;
        display: flex !important;
            margin: 0 !important;
        margin-left: 0.7% !important;
    }
    .change_freespins-body .freespins_play_future,
    .change_freespins-body #freespins_play_now {
        background-position-x: 0 !important;
        font-size: 2.8em !important;
        width: 49% !important;
        height: 4rem !important;
    }
    .change_freespins-body .freespins_play_future {
        background: url(/assets/img/freespin/orange4.png) !important;
        background-size: 100% 100%!important;
        order: 2 !important;
        margin-right: 0.5% !important;
    }
    .change_freespins-body #freespins_play_now {
        background: url(/assets/img/freespin/green4.png) !important;
        background-size: 100% 100%!important;
        order: 1 !important;
    }
    .change_freespins-body .freespins_play_future:hover {
        background: url(/assets/img/freespin/orange3.png) !important;
    }
    .change_freespins-body #freespins_play_now:hover {
        background: url(/assets/img/freespin/green3.png) !important;
    }
    .change_freespins-body .freespins_play_future:hover,
    .change_freespins-body #freespins_play_now:hover {
        background-position-x: 0 !important;
        background-size: 100% 100%!important;
    }
    .change_freespins-body .how-much-offers-wraper {
        order: 3 !important;
    }
    #change_freespins:before {
                top: -27% !important;
    }
    #change_freespins:after {
        top: 23% !important;
    }

    #error-box { /*fix bord*/
        top: 0;
    }

    <?php if(th::isMobile()): ?>
    #game-box {
        width: 100% !important;
    }

    @media only screen and (min-width: 1000px) {
        .change_freespins-body .left_games_block, .change_freespins-body .right_games_block {
            padding-bottom: 3% !important;
        }
        .change_freespins-body .freespins_play_future, .change_freespins-body #freespins_play_now {
                height: 5rem !important;
        }
        .change_freespins-body:after, .change_freespins-body:before {
                background-size: cover!important;
        }
        #change_freespins:before {
            top: -4% !important;
        }
    }
    <?php else: ?>
        @media only screen and (min-width: 1500px) {
            #game-box {
                overflow: visible;
            }
        }
        @media only screen and (min-width: 1400px) {
            #game-box {
                transform: scale(0.9);
            }
        }
        @media only screen and (min-width: 1330px) {
            #game-box {
                transform: scale(0.8);
            }
        }
    <?php endif; ?>

</style>
<div <?php if(th::isMobile()): ?>class="change_freespins-body" <?php endif; ?>id="change_freespins">
    <h1>
        <?php echo __('Поздравляем!') ?>
    </h1>
    <h2>
        <?php echo __('У вас есть бесплатные игры') ?>
    </h2>
    <h2 class="gold"></h2>
    <style>
        #change_freespins table, #change_freespins th, #change_freespins td {
            padding: 5px;
            border: 1px solid #ffffff;
            border-collapse: collapse;
        }
        .change_freespins {
            margin-top: 0px;
        }
    </style>
    <?php $iter=1; ?>
    <?php foreach ($fs as $f): ?>
        <div class="fs_block" fs-choose-id="<?php echo $iter; ?>" style="<?php echo $iter==1?'':'display:none'; ?>">
            <div class="wraper-for-game-blockes">
                <div class="left_games_block">
                    <div class="title"><?php echo __('Бесплатные игры') ?></div>
                    <hr />
                    <div class="left_freespins"><?php echo $f['freespins_break'] - $f['freespins_current'] ?></div>
                </div>
                <div class="right_games_block">
                    <div class="rules_line">
                        <?php echo __('Условия') ?>
                    </div>
                    <hr />
                    <div class="rules_line">
                        <?php echo __('Ставка на линию') ?> <?php echo $f['bet'] ?>
                    </div>
                    <hr />
                    <div class="rules_line">
                        <?php echo __('Количество линий') ?> <?php echo $f['lines'] ?>
                    </div>
                </div>
            </div>
            <div class="buttons">
                <?php if($f['freespins_current'] == 0): ?>
                <button class="freespins_play_future" onclick="set_freespins('off', <?php echo $f['id'] ?>)"><?php echo __('Отказаться') ?></button>
                <?php endif; ?>
                <!--<button class="freespins_play_future" onclick="set_freespins('future', <?php echo $f['id'] ?>)"><?php echo __('Сыграть позднее') ?></button>-->
                <button class="freespins_play_now" onclick="set_freespins('now', <?php echo $f['id'] ?>)"><?php echo __('Сыграть сейчас') ?></button>
                <?php if($f['freespins_break'] == 0 AND $f['payed'] == 0): ?>
                    <button id="freespins_dont_play" onclick="set_freespins('off', <?php echo $f['id'] ?>)"><?php echo __('отказаться') ?></button>
                <?php endif; ?>
                <div class="how-much-offers-wraper">
                    <div style="" class="how-much-offers">
                        <button class="button-left" onclick="show_block(<?php echo $iter-1>0?$iter-1:count($fs) ?>)">

                        </button>
                        <span class="content">
                            <?php echo $iter . '/' . count($fs) ?>
                            <br>
                            предложений
                        </span>
                        <button class="button-right" onclick="show_block(<?php echo $iter<count($fs)?$iter+1:1 ?>)">

                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php $iter++ ?>
    <?php endforeach; ?>
    <script>

        window.onload = function() {
            document.getElementsByClassName('preloader-container')[0].style.display = 'none';
            document.getElementsByTagName('h2')[1].innerText = document.title;
        }


        function show_block(elem_iter) {
            var myTabs = document.querySelectorAll(".fs_block");

            for (var i = 0; i < myTabs.length; i++) {
                if(myTabs[i].getAttribute('fs-choose-id')==elem_iter) {
                    myTabs[i].style.display = 'block';
                } else {
                    myTabs[i].style.display = 'none';
                }
            }
        }

        function set_freespins(data, fs_id) {
            var xmlhttp; // наш объект ajax
            if (window.XMLHttpRequest){// для IE7+, Firefox, Chrome, Opera, Safari (новые версии)
                xmlhttp = new XMLHttpRequest(); // создаем его (аякс)
            }else{// для IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); //древний способ
            }

            xmlhttp.open("GET", "/user/setspins?freespins_id="+fs_id+"&type="+encodeURIComponent(data), false); // открываем через запрос
            xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");

            xmlhttp.onreadystatechange = function () {
                // функция при успешном получении данных
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status==200) {
                        var ans = '{}';

                        try {
                            ans = JSON.parse(xmlhttp.responseText || '{}' );
                        } catch (err) {
                            // console.warn('send =>', err);
                        }

                        if(ans.enabled) {
                            var freespins_view = document.getElementById('change_freespins');
                            freespins_view.remove();
                            window.game.controller.initialized();
                            document.getElementsByClassName('preloader-container')[0].style.display = 'block';
                        }
                    }
                }
            };
            xmlhttp.send();
        };

    </script>
</div>