<div class="bill-window-game" style="display: block;">
    <div class="container">
        <div class="bill-data bill-lot-game"></div>
        <div class="bill-data bill-number-game"></div>
        <div class="bill-data bill-amount-game"></div>
        
        <div class="bill-data bill-dateplace-game">24.09.18, 129344, г. Москва, ул. Искры, д. 31, корп. 1, пом. 2, ком. 23</div>
        <div class="bill-data bill-company-game">Общество с ограниченной ответственностью "Командо - С", 129344, г. Москва, ул. Искры, д. 31, корп. 1, пом. 2, ком. 23</div>
        <div class="bill-data bill-wordamount-game"><span class="bill-amount-game_text"></span> рублей</div>
        <div class="bill-data bill-who-game">Обществу с ограниченной ответственностью "ФОРТУНА", 109431, г. Москва, ул. Привольная, д. 70</div>
        <div class="bill-data bill-time-game">По предъявлении, но не ранее трех лет от даты составления</div>
        <div class="bill-data bill-payplace-game">129344, г. Москва, ул. Искры, д. 31, корп. 1, пом. 2, ком. 23</div>
        <div class="bill-data bill-holder-game">Генеральный директор ООО "Командо - С" Дубоносов В.Ю.</div>
        <div class="bill-data bill-sign-game"></div>
        <div class="bill-data bill-stamp-game"></div>

    </div>
    <div class="controls">
        <div class="start">
            <button class="make_bet" data-act="normal">Купить / Продать</button>
        </div>
        <div class="">
            <label>Порог</label>

            <button class="bet" data-act="down">-</button>
            <div class="bet">0</div>
            <button class="bet" data-act="up">+</button>
        </div>
        <div class="">
            <label>Волатильность</label>

            <button class="lines" data-act="down">-</button>
            <div class="lines">0</div>
            <button class="lines" data-act="up">+</button>
        </div>
    </div>
</div>
<div class="bill-window-game-double" style="display:none;">
    <p>Вы можете совершить сделку по текущей цене или по цене следующей торговой сессии</p>
    <p>Текущая цена уступки права</p>
    <p class="double_amount">1000</p>

    <button class="make_bet" data-act="save">Текущая</button>
    <button class="make_bet"  data-act="double">Следующая</button>
</div>

<script src="/js/jquery.js" type="text/javascript"></script>
<script>
    $(window).load(function () {

        var arr_bets = [0.1, 0.2, 0.5, 1, 2, 5, 10, 20, 50, 100];
        var curr_bet = 0.1;
        var bet_button = $("button.bet");

        var arr_lines = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        var curr_lines = 1;
        var lines_button = $("button.lines");

        var button_normal = $('button[data-act=normal]');
        //        var button_double = $('button[data-act=double]');
        var double_cont = $('.bill-window-game-double');//окно удвоения при выигрыше ставки
        //        var res;
        var action;

        var $number = $(".bill-number-game");
        var $lot = $(".bill-lot-game");
        var $amount = $(".bill-amount-game");
        var $amount_text = $(".bill-amount-game_text");


        checkState();

        //---------------------------------//  

        $("div.bet").text(curr_bet);
        $("div.lines").text(curr_lines);

        $(bet_button).on("click", function () {
            var change = $(this).data("act");
            changeValues(bet_button, change);
        });

        $(lines_button).on("click", function () {
            var change = $(this).data("act");
            changeValues(lines_button, change);
        });

        $("button.make_bet").on("click", function () {
            action = $(this).data("act");
            checkButton(action);

        });

        //---------------------------------// 

        function checkState(clear = false) {
            if (clear) {
                var num = $(".table-deals tr").first().data("number");
                if (num) {
                    $(".bill-window").css("left", "600px");
                    $(".bill-data").show();
                    $(".bill-window").show();
                    $(".bill-window").animate({
                        left: 1999
                    }, 300);
                    $(".bill-window").hide(0);
                }
                
                $(".bill-data").hide();
                $($number).text("");
                $($lot).text("");
                $($amount).text("");

            } else {
                $.ajax({
                    dataType: 'json',
                    'url': '/veksel/data/<?php echo (substr(auth::$user_id,-1) ?? "0"); ?>/<?php echo (substr(auth::$user_id,-2,1) ?? "0"); ?>/<?php echo auth::$user_id; ?>.json?rnd=' + Date.now(),
                    success: function (data) {
                        $($number).text(data.history[0]["veksel_id"]);
                        $($lot).text(data.history[0]["veksel_serial"]);
                        var a = atob(data.history[0]["veksel_value"]);
                        var a_text = digiToWords(a);//Номинал векселя словами
                        $($amount).text(a);
                        $($amount_text).text(a_text);
                        $(".bill-data").show();
                    },
                    error: function (data) {}
                });
            }
        }

        function changeValues(obj, direction) {
            if (obj.selector == "button.lines") {//кнопка линий
                arr_elem = arr_lines;
                curr_elem = curr_lines;
                sel = "div.lines";
                curr_lines = change();
            }
            if (obj.selector == "button.bet") {//кнопка ставок
                arr_elem = arr_bets;
                curr_elem = curr_bet;
                sel = "div.bet";
                curr_bet = change();
            }
            function change() {
                i = arr_elem.indexOf(curr_elem);//индекс текущего значения в массиве
                if (direction == "up") {//прибавляем
                    if (arr_elem[i + 1]) {
                        curr_elem = arr_elem[i + 1];
                    }
                } else { //уменьшаем
                    if (arr_elem[i - 1]) {
                        curr_elem = arr_elem[i - 1];
                    }
                }
                $(sel).text(curr_elem);//показываем значение

                return curr_elem;
            }
        }

        function getResponse(data) {

            if (data.error == 1) {
                $("#message_game_veksel").text(data.text);
                return false;
            }

            if(data.type=="normal"){
                checkState(true);//очищаем значения векселя при каждой обычной ставке
            }
            if (data.win != 0) {//есть выигрыш - показываем окно, отключаем кнопку

                setTimeout(function () {//Через секунду показываем окно удвоения
                    $(button_normal).attr("disabled", "disabled");
                    $(double_cont).show();
                    $("p.double_amount").text(data.double_amount);
                }, 1000);

            } else {//если забрал выигрыш или проиграл в удвоении скрываем окно, включаем кнопку

                $(button_normal).removeAttr("disabled");
                $(double_cont).hide();

                setTimeout(function () {
                    checkState();//заполняем значения векселя
                }, 1000);
            }
        }

        function checkButton(action_button) {
            if (action_button == "save") {
                $(double_cont).hide();
                $(button_normal).show();
            }
            $.ajax({
                type: 'POST',
                url: '/veksel/init',
                dataType: 'json',
                data: {
                    action: action,
                    bet: curr_bet,
                    lines: curr_lines
                },
                cache: false
            }).done(function (response) {
                getResponse(response);
            });
        }
    });
</script>