<!DOCTYPE html>
<html>
    <head>
        <title>Payonline</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <!--[if IE]><link type="image/x-icon" rel="shortcut icon" href="/payonlinesolutions_favicon.ico?v76"><![endif]-->
        <link rel="apple-touch-icon-precomposed" href="/payonlinesolutions_favicon.png?v76">
        <link rel="icon" href="/payonlinesolutions_favicon.png?v76">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="/css/pay/styles.css?ver=<?php echo th::ver(); ?>"/>
        <link rel="stylesheet" type="text/css" href="/css/pay/colors.css?ver=<?php echo th::ver(); ?>"/>
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/jquery.form.js"></script>
        <script type="text/javascript" src="/js/jquery-slider.min.js"></script>

        <style>

            .mobilever{
                    position: relative;
/*                    overflow: scroll;*/
                    height: 100%;
                    max-width: 100%;
                    outline: 0;
                    direction: ltr;
            }

            .tabs { display: none !important; }

            #error_message {
                line-height: 100%;
                display: block;
                margin: 0 auto;
                width: 100%;
                text-align: center;
                background: #886a10;
            }

            .tabs-item {
                font-family: "proxima_nova_ltsemibold";
                margin-left: 5px;
                margin-right: 5px;
                cursor: pointer;
                -webkit-transition: 0.2s;
                -o-transition: 0.2s;
                transition: 0.2s;
                display: block;
                opacity: 0;
                height: 0;
                padding: 0;
                text-align: left;
                padding-left: 15px;
                border-radius: 0;
            }

            .tabs-item.active {
                display: block;
                text-align: left;
                opacity: 1;
                height: auto;
                padding: 7px 10px;
                border-radius: 0;
            }

            .tabs-item.return {
                will-change: padding, opacity, height;
            }

            .mobilever .tabs-wrap-mob .tabs:after {
                content: '';
                position: absolute;
                background: none;
                width: 16px;
                height: 16px;
                border-left: 3px solid #ffffff;
                border-top: 3px solid #ffffff;
                top: 7px;
                right: 12px;
                border-radius: 2px;
                -moz-transform: rotate(225deg);
                -webkit-transform: rotate(225deg);
                -o-transform: rotate(225deg);
                -ms-transform: rotate(225deg);
                transform: rotate(225deg);
            }

            .tabs {
                display: table;
                width: 100%;
                border-radius: 5px 5px 0 0;
            }
            #iframe:not(.mobile) .tabs {
                background-color: #0f1418;
            }
            #iframe .tabs {
                border-radius: 0;
            }
            .tabs-item {
                color: #fff;
                text-shadow: 0 0 0 #0f1418;
            }
            .tabs-item {
                display: table-cell;
                font-family: "proxima_nova_ltsemibold";
                margin-left: 5px;
                margin-right: 5px;
                text-align: center;
                padding: 8px 0;
                cursor: pointer;
                -webkit-transition: 0.2s;
                -o-transition: 0.2s;
                transition: 0.2s;
                border-radius: 4px 4px 0 0;
            }
            .tumbs-item.active {display:block}

            ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
                color: #666666;
            }
            ::-moz-placeholder { /* Firefox 19+ */
                color: #666666;
            }
            :-ms-input-placeholder { /* IE 10+ */
                color: #666666;
            }
            :-moz-placeholder { /* Firefox 18- */
                color: #666666;
            }
            .step2 {
                padding-top: 20px;
            }
        </style>

    </head>

    <body id="iframe" data-lang="ru" class="<?php echo th::isMobile()?'mobilever':''; ?>">
        <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter45912849 = new Ya.Metrika({ id:45912849, clickmap:true, trackLinks:true, accurateTrackBounce:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/45912849" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->

        <div class="layout">
            <div class="container  ps-choice ">
                <div id="pay-systems" class="animated step active">
                    <div class="tumbs-box">
                        <div class="tabs-wrap-mob">
                            <div class="tabs">
                                <span id="tabs_all_methods" class="tabs-item active" data-tabs="0"><?php echo __('Все методы') ?></span>
                                <?php foreach($groups as $group=>$group_name) :?>
                                <span id="tabs_<?php echo $group; ?>" class="tabs-item" data-tabs="<?php echo $group; ?>"><?php echo $group_name; ?></span>
                                <?php endforeach; ?>
<!--                                <span id="tabs_bank_cards" class="tabs-item" data-tabs="bank_cards">Банковские карты</span>
                                <span id="tabs_online_banking" class="tabs-item" data-tabs="online_banking">Онлайн-банкинг</span>
                                <span id="tabs_e_commerce" class="tabs-item" data-tabs="e_commerce">Другое</span>-->
                            </div>
                        </div>
                        <div id="payment_methods" class="tumbs">
                            <?php foreach ($systems as $sys): ?>
                                <div class="tumbs-item anim_medium" data-tabs="<?php echo $sys->group; ?>">
                                    <div class="tumbs-inner" data-paymentid="<?php echo $sys->id ?>" id="pay_sys_<?php echo $sys->id ?>">
                                        <span data-ps="<?php echo $sys->id ?>" data-image="<?php echo $sys->image ?>">
                                            <img src="/games/payment/<?php echo $sys->image ?>">
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div id="pay-form-container" class="step animated" style="display: none">
                    <form id="payment_form" action="<?php echo $dir; ?>/payer/go" method="POST" class="form">
                        <input type="hidden" id="paysyscurrent" name="paysys_current" value="" class="input_amount"/>
					<input type="hidden" id="is_ok" name="is_ok" value="-1" class="input_amount"/>
                        <div class="row align-items-center step2" style="display: none;">
                            <div class="col-sm-12">
                                <div id="requisites-block">
                                    <fieldset>
                                        <?php foreach($systems as $sys):?>
                                        <section class="fields-section" id="<?php echo $sys->id . '_field'; ?>" style="display: none">
                                            <?php foreach($sys->attr->find_all() as $field):?>
                                                <div class="form__item">
                                                    <label for="<?php echo $sys->id  . '_' . $field->name ?>"> <?php echo $field->visible_name ?></label>
                                                    <input type="text" id="<?php echo $sys->id  . '_' . $field->name ?>" name="<?php echo "$sys->id[$field->name]" ?>" placeholder="<?php echo $field->example ?>" class="form__input">
                                                </div>
                                            <?php endforeach; ?>
                                        </section>
                                    <?php endforeach; ?>
                                        <div class="form__element">
                                            <button id="prev-button" class="btn-blue btn-red" type="button">
                                                <?php echo __('Назад') ?>
                                            </button>
                                            <button id="pay-button" class="btn-blue btn-red" type="submit">
                                                <?php echo __('Оплатить') ?>
                                            </button>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center step1">
                            <div class="col-sm-12">
                                <div id="requisites-block">
                                    <fieldset>
                                        <div id="alternative" class="single-requisite">
                                        </div>
                                        <div class="alternative-method single-requisite">
                                            <section class="form__how-much-you-want-in">
                                                <span class="form__how-much-you-want-in__title">
                                                    <?php echo __('Укажите сумму пополнения') ?>
                                                </span>
                                                <div class="form__how-much-you-want-in__wrap-total-item">
                                                    <?php foreach([5000,10000,30000,50000,100000] as $v): ?>
                                                        <div class="form__how-much-you-want-in__total-item">
                                                            <label>
                                                                <input value="<?php echo $v; ?>" class="option-input checkbox agree" name="radio_amount" type="radio" />
                                                                <?php echo $v ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </section>
                                            <?php $current_sum = 0 ?>
                                            <section class="form__element form__amount">
                                                <label for="pay_amount" class="form__label form__label_amount required">
                                                    <?php echo __('Пополнить счет на') ?>
                                                </label>
                                                <div class="form__item">
                                                    <span class="form__item__your-amount"><?php echo __('Ввести сумму') ?></span>
                                                    <span id="currency" class="form__item_currency"><?php echo person::user()->currency($currency_id); ?></span>
                                                    <input type="text" id="pay_amount" name="amount" required="required" class="form__input" value="<?php echo $current_sum; ?>" tabindex="2">
                                                </div>
                                                <label class="form__label">
                                                    <?php echo __('Ваш тариф') ?>: <b><?php echo person::user()->percent ?>%</b>
                                                </label>
                                                <div class="form__item">
                                                    <span class="form__item__your-amount">
                                                        <?php echo __('Поступит на счет') ?>
                                                    </span>
                                                    <input type="text" id="account_balance" name="amount_calced" required="required" 
                                                       class="form__input" value="<?php echo $current_sum*person::user()->percent; ?>" tabindex="3">
                                                </div>
                                            </section>
                                            <div class="form__element">
                                                <button id="next-button" class="btn-blue btn-red" type="button">
                                                    <?php echo __('Далее') ?>
                                                </button>
                                            </div>
                                        </div>

                                    </fieldset>
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>
                        <p id="error_message" style="line-height: 120%; visibility: hidden;">&nbsp;</p>
                    </form>
                </div>
            </div>
            <div id="screen">
                <div id="payment_info"></div>
                <style>
                    #payment_info {
                        display: block;
                        position: absolute;
                        width: 100%;
                        opacity: 1;
                        background: #fff;
                        color: #000;
                    }
                </style>
                <div class="content">
                    <span class="preload-message">
                        <?php echo __('Ожидайте, идет оплата...') ?>
                    </span>
                    <div class="kart-loader">
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                        <div class="sheath">
                            <div class="segment"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
            <script>

                function add_error(message, hideMess) {
                    $('#error_message').css("visibility", "visible");
                    $('#error_message').text(message);

                    if(hideMess !== false) {
                        setTimeout(function() {
                            $('#error_message').css("visibility", "hidden");
                        }, 4000);
                    }
                }

                var min_sum_pay = parseInt(<?php echo $min_sum_pay; ?>);
                function check_min_sum() {
                    var new_val = parseInt($('#pay_amount').val());

                    if(new_val < min_sum_pay) {
                        new_val = min_sum_pay;
                        $("#pay_amount").val(min_sum_pay);
                        add_error('<?php echo __('Минимальная сумма пополнения') ?> - ' + min_sum_pay + ' <?php echo auth::user()->office->currency->code ?>');

//                                $('#enter-amount').slider({
//                                    value: new_val
//                                });

                        return false;
                    }
                    return true;
                }

                $(document).ready(function () {

                    //tabs
                    $('.tabs-item').click(function() {
                        $('.tabs-item').removeClass('active');
                        $(this).addClass('active');

                        $("#payment_methods .tumbs-inner").removeClass("tabs-item-active");

                        $('#pay-form-container').hide();
                        $('#payment_form .step2').hide();
                        $('#payment_form .step1').show();

                        var group = $(this).data('tabs');

                        $('.tabs-item').removeClass('return');

                        if($('.tabs-item.active').data('tabs')==group) {
                            $(this).addClass('return');
                        }

                        if(group=='0') {
                            $('#payment_methods .tumbs-item').addClass('active').show();
                        }
                        else {
                            $('#payment_methods .tumbs-item').removeClass('active').hide();
                            $('#payment_methods .tumbs-item[data-tabs='+group+']').addClass('active').show();
                        }
                    });

                    $('#next-button').click(function() {

                        if(check_min_sum()) {

                            var current_ps = $('#paysyscurrent').val();

                            if(!$('#'+current_ps+'_field .form__item').length) {
                                $('#pay-button').click();
                            }
                            else {
                                $('#payment_form .step1').hide('slow');
                                $('#payment_form .step2').show('slow');
                            }
                        }
                    });

                    $('#prev-button').click(function() {
                        $('#payment_form .step1').show('slow');
                        $('#payment_form .step2').hide('slow');
                    });


                    var options = {
                        dataType: 'json',
                        type: 'post',
                        async: false,
                        beforeSubmit: function () {
                            check_min_sum();
                        },
                        success: function (data) {
                            if (data.error && data.error.length > 0) {
                                add_error(data.error);
                                $("#bonuscode").val('');
                                $("#is_ok").val(data.is_ok);
                                return;
                            }
                            if (typeof yaCounter45912849 != 'undefined') {
                                yaCounter45912849.reachGoal('pay_begin');
                            }

                            $('#screen').toggle();
                            
                            if(data.iframe_source) {
                                $('iframe', window.parent.document).css('background-color', '#ccc');
                                window.location.href = data.iframe_source;
                                return;
                            }

                            if(data.data) {
                                var form = document.createElement('form');
                                form.hidden = true;
                                form.id = 'hidden_form';
                                form.method = 'POST';
                                form.action = data.source;

                                var html = '';

                                for(var i in data.data) {
                                    html += '<input name="' + i + '" value="' + data.data[i] + '">';
                                }

                                form.innerHTML = html;

                                document.body.appendChild(form);

                                $('#hidden_form').submit();
                            }

                            $('#no-popup-window').hide();

                            if(data.link.length>0) {
                                var win_opened = window.open("", '_blank');

                                if(!win_opened || win_opened.closed || typeof win_opened.closed=='undefined')
                                {
                                    window.location.href=data.link;
                                    return;
                                }

                                win_opened.location.href = data.link;
                            }
                            
                            /*
                             * перезапрашиваем статус платежа
                             */
                            if (data.payment_id) {
                                window.parent.win_opened = win_opened;
                                window.parent.payment_id_current=data.payment_id;
                            }

                            if(data.info && data.info.length>0) {
                                $('#payment_info').html("<h4>Уважаемый клиент!</h4><p>Ваш запрос успешно принят. Вам отправлено СМС с подробной инструкцией для завершения оплаты.</p><p>Просим Вас следовать указанным действиям.</p><p>Вы можете закрыть эту страницу, после подтверждения оплаты платеж будет обработан автоматически.</p><h5>Абонентам Билайн:</h5><ul><li>После списания суммы покупки на вашем счете должно остаться не менее 50 руб.;</li><li>Услуга становится доступной с момента расходования вами 150 руб. за услуги связи с момента подключения к сети Билайн;</li><li>Минимальная сумма платежа 10 руб.</li><li>Максимальный разовый - 15000 руб.</li><li>Максимальная сумма платежей за сутки - 15000 руб. максимум 10 транзакций</li><li>Максимальная сумма платежей за месяц -30000 руб.</li></ul><br><p>Недоступна мобильная коммерция абонентам:</p><ol><li>1. С тарифом “Простая логика”</li><li>2. Включенные услуги: “Безумные дни”, “Безлимит” внутри сети.</li></ol><p>Если вы пользуетесь тарифом с постоплатной системой расчетов то:</p><ul><li>Оплата возможна только со специального авансового счета*.</li><li>Услуга становится доступной с момента расходования вами 150 руб. за услуги связи с момента подключения к сети «Билайн».</li></ul>");
                            }
                        },
                    };

                    $('#payment_form').ajaxForm(options);

                    $('#pay_amount').keydown(function () {
                        $("input[type='radio']").prop('checked', false);
                    });

//                    $('#enter-amount').slider({
//                        min: 100,
//                        max: 15000,
//                        value: 500,
//                        slide: function (event, sl) {
//                            $("#pay_amount").val(sl.value);
//                        }
//
//                    }).slider("pips", {
//                        step: 4000,
//                        labels: { first: 100, last: 15000 }
//                    });

                    $.each($('#pay-systems').find('#payment_methods .tumbs-item'), function () {
                        var elem = $(this);
                        if (elem.find('[data-image]').length) {
                            var ps = elem.find('[data-image]').data('ps');
                            $("#pay_sys_" + ps).click(function () {
                                $("#pay-form-container").css('display', 'block');
                                $('#paysyscurrent').val(ps);
                                var img = elem.find('[data-image]').data('image');
                                $('#curr_logo').attr('src', '/games/payment/' + img);
                            });
                        }
                    });
                    $("#payment_methods .tumbs-inner").click(function() {
                        $("#payment_methods .tumbs-inner").removeClass("tabs-item-active");
                        $(this).addClass("tabs-item-active");

                        $('#payment_form .step2').hide();
                        $('#payment_form .step1').show();
                        $('.fields-section').hide();
                        var id = $(this).data('paymentid');
                        $('#'+id+'_field').show();
                    });

                    $("input[type='radio']").click(function() {
                        $("#pay_amount").val($(this).val());
                        $('#pay_amount').keyup();
                    });

                    $('#checkbonus').click(function(){
                         $.ajax({
                            url: '/payer/bonus/'+$('#bonuscode').val(),
                            type: 'POST',
                            data: {
                                'amount': $('#pay_amount').val()
                            },
                            dataType: 'json',
                            success: function(data){
                                if (data.error == 1) {
                                    add_error(data.text);
                                    $("#bonuscode").val('');
                                } else {
                                    add_error(data.text, false);
                                }

                                if(data.type == 'bezdep' || data.type == 'freespin_bezdep') {
                                    setTimeout(function() {
                                        window.location = window.location;
                                    }, 2000);
                                }
                            },
                        });
                    });
                    $("#payment_methods .tumbs-inner").eq(0).click();
                    $('#show_bonus_field').click(function() {
                        $('#bonus_field').toggle();
                    });
                    $('#show_bonus_list,#show_bonus_input').click(function() {
                        $('#bonus_list').toggle();
                        $('#bonus_input').toggle();
                    });


                    $('#bonus_field [data-code]').click(function() {

                        $('#error_message').css("visibility", "hidden");

                        if($(this).hasClass('bonus_disabled')) {
                            return;
                        }

                        var val = $(this).data('code');

                        if($(this).hasClass('tabs-item-active')) {
                               val = '';
                               $('#bonus_field [data-code]').removeClass('tabs-item-active');
                        }
                        else {
                           $('#bonus_field [data-code]').removeClass('tabs-item-active');
                           $(this).addClass('tabs-item-active');
                           $("#bonuscode").val(val);
                           $('#checkbonus').click();
                        }

                    });

                    $('#pay_amount').keyup(function() {
                        var new_val = parseInt($(this).val());
                        $('#bonus_field [data-code]').each(function() {
                            var e = $(this);
                            e.removeClass('bonus_disabled');
                            if(e.length && parseInt(e.data('min'))>new_val) {
                                 e.addClass('bonus_disabled');
                            }
                        });
                    });
                    $('#pay_amount').keyup();
                    
                    let percent = <?php echo person::user()->percent ?>;
                    
                    $('#pay_amount').keyup(function() {
                        let amount = parseInt($(this).val());
                        
                        let account_balance = amount/percent*100;
                        
                        if(isNaN(account_balance)) {
                            account_balance = 'Введите число';
                        } else {
                            account_balance = account_balance.toFixed(2);
                        }
                        
                        $('#account_balance').val(account_balance);
                    });
                    
                    $('#account_balance').keyup(function() {
                        let amount = parseInt($(this).val());
                        
                        let account_balance = percent*amount/100;
                        
                        if(isNaN(account_balance)) {
                            account_balance = 'Введите число';
                        } else {
                            account_balance = account_balance.toFixed(2);
                        }
                        
                        $('#pay_amount').val(account_balance);
                    });
                });
            </script>

            <style>
                .tumbs-inner.bonus_disabled {
                    opacity: 0.5;
                }
            </style>

            <?php if(false && th::isMobile()): ?>
            <style>
                #enter-amount {display: none;}
                .form__input {font-size: 14px;}
                .form__item_currency {font-size: 14px;}
                #pay-button {font-size: 14px;}
            </style>
            <?php endif; ?>
    </body>
</html>