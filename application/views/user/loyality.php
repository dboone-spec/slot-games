<script>
    var options = {
        dataType: 'json',
        async: true,
        success: function (data) {
            if (!data.error) {
                $('#user_comp_in_popup').text(data.comp);
                $('#compswap_message').text(data.text);
                $('#compswap_message').css('color', 'green');
                $('#user_balance').text(data.balance);
                $('#exchange_points').val(data.comp);
            } else {
                $('#compswap_message').text(data.text);
                $('#compswap_message').css('color', 'red');
            }

            setTimeout(function () {
                $('#compswap_message').text('');
                $('#compswap_message').css('color', 'white');
            }, 3000);
        },
        error: function (data) {
            $('#compswap_message').text('Ошибка при обмене компоинтов');
            $('#compswap_message').css('color', 'red');

            setTimeout(function () {
                $('#compswap_message').text('');
                $('#compswap_message').css('color', 'white');
            }, 3000);
        }
    };
    $('#loyality_form').ajaxForm(options);

    /*
     * обмен компоинтов
     */
    var sum_comp = $('#exchange_points'),
            sum_rub = $('#money_value'),
            comp_current = <?php echo auth::user()->comp_current ?>,
            coefficient = <?php echo auth::user()->get_compoint_param('coeffs'); ?>;
    
    /*
     * отключаем поля т.к. у пользователя коэфф обмена 0
     */
    if(coefficient <= 0) {
        sum_comp.attr('disabled', 'disabled');
        sum_rub.attr('disabled', 'disabled');
    }

    sum_comp.keyup(function () {
        var rub = $(this).val();
        var value = rub == '' ? '' : (rub * coefficient).toFixed(2);

        if (!isNaN(value)) {
            if (value > (comp_current * coefficient).toFixed(2)) {
                $('#compswap_message').text('Недостаточно компоинтов на счету');
            } else {
                $('#compswap_message').text('')
            }
            sum_rub.val(value);
        }
    });

    sum_rub.keyup(function () {
        var points = $(this).val();
        var value = points == '' ? '' : (points / coefficient).toFixed(0);

        if (!isNaN(value)) {
            if (value > comp_current) {
                $('#compswap_message').text('Недостаточно компоинтов на счету');
            } else {
                $('#compswap_message').text('')
            }
            sum_comp.val(value);
        }
    });
</script>    
<?php if(th::isMobile()): ?>
<style>
    .popup-close {
        right:0;
    }
</style>
    <div id="loyality-popup" class="popup-body">

        <div class="popup-body popup-sm popup-dark">

            <div class="popup-close"></div>

            <div class="popup-content" style="width: 100%;"><!-- add here class of popup -->
                <div class="popup-border">
                    <div class="light-header active">
                        <div class="light-header__twinkle_1"></div>
                        <div class="light-header__twinkle_2"></div>
                        <span class="light-header__title">
                            <?php echo __('Обмен компоинтов') ?></span>
                    </div>

                    <div class="popup-center loyality" style="word-wrap: break-word">
                        <div class="loyality__top">
                            <p>
                                <span>У Вас компоинтов: </span>
                                <span id="user_comp_in_popup" class="text-yellow"><?php echo floor(auth::user()->comp_current) ?></span>
                                <span id="compswap_message" style="font-size: 20px;"></span>
                            </p>
                            <p>
                                <span>Мы предлагаем вам честную программу лояльности, в которой собрали только ценные привилегии. Никаких скрытых условий и многократных отыгрышей, никаких сложных расчетов и размытых формулировок. Условия программы просты и понятны.</span>
                                <span class="text-yellow">За каждые 100 <?php echo auth::user()->currency(); ?> ставок вы получаете 1 компоинт.</span> 
                                <span>Чем больше компоинтов, тем выше ваш уровень. Чем выше уровень, тем более ценные привилегии вы получаете.</span>
                            </p>
                        </div>
                        <div class="exchanger bg-block">
                        <form id="loyality_form" class="form" action="/user/compswap" method="POST" novalidate="novalidate">
                            <div class="grid-wrapper">
                                <div class="grid-wrapper-cols pd-lg-sides">
                                    <div class="grid-col__8-12">
                                        <div class="grid-wrapper-cols">
                                            <div class="grid-col__5-12">
                                                <div class="fld-wrap fld-lbl">
                                                    <input id="exchange_points" name="compoints" required="required" class="fld points_count fld" value="<?php echo floor(auth::user()->comp_current) ?>" type="text" style="text-align: center;">
                                                    <label class="lbl-on" style="margin: 0 auto;">Компоинтов</label>
                                                </div>
                                            </div>
                                            <div class="grid-col__2-12">
                                                <i class="ico ico-arr"></i>
                                            </div>
                                            <div class="grid-col__5-12">
                                                <div class="fld-wrap fld-lbl fld-rub">
                                                    <input id="money_value" class="fld" value="<?php echo (floor(auth::user()->comp_current) * auth::user()->get_compoint_param('coeffs')) ?>" maxlength="6" type="text" style="text-align: center;">
                                                    <label for="loyality-money" class="lbl-on" style="margin: 0 auto;"><?php echo auth::user()->currency(); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid-col__4-12">
                                        <button class="btn btn-blue btn-md" type="submit">
                                            <span>Обмен</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                        <?php for($i = 1; $i <= 6; $i++): ?>
                            <div class="grid-wrapper-cols pd-sm-both" style="text-align: center">
                                <div class="grid-col__4-12">
                                    <div class="ico ico-level-lg">
                                        <div class="level level_<?php echo $i; ?>"></div>
                                    </div>
                                </div>
                                <div class="grid-col__4-12">
                                    <h6><?php echo $comp_config['names'][$i]; ?></h6>
                                </div>
                                <div class="grid-col__4-12">
                                    <span class="status-block__text">Баллов:
                                        <b class="text-yellow"><?php echo $comp_config['levels'][$i]; ?></b>
                                    </span>
                                    <div class="clear"></div>
                                    <span class="status-block__text">Курс:
                                        <b class="text-yellow"><?php echo 100 . ':' . 100 * $comp_config['coeffs'][$i] ?></b>
                                    </span>
                                </div>
                            </div>
                        <?php endfor; ?>
                        <?php for($i = 7; $i <= 11; $i++): ?>
                            <div class="grid-wrapper-cols pd-sm-both" style="text-align: center">
                                <div class="grid-col__4-12">
                                    <div class="ico ico-level-lg">
                                        <div class="level-next level_<?php echo $i; ?>"></div>
                                    </div>
                                </div>
                                <div class="grid-col__4-12">
                                    <h6><?php echo $comp_config['names'][$i]; ?></h6>
                                </div>
                                <div class="grid-col__4-12">
                                    <span class="status-block__text">Баллов:
                                        <b class="text-yellow"><?php echo $comp_config['levels'][$i]; ?></b>
                                    </span>
                                    <div class="clear"></div>
                                    <span class="status-block__text">Курс:
                                        <b class="text-yellow"><?php echo 100 . ':' . 100 * $comp_config['coeffs'][$i] ?></b>
                                    </span>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
<div class="popup-body popup-lg popup-loyality-new">

    <div class="popup-close"></div>

    <div class="popup-content popup-collection popup-profile">
        <div class="popup-border">

            <ul class="popup-header-list">
                <li class="light-header active" style="width: 100%;">
                    <div class="light-header__twinkle_1"></div>
                    <div class="light-header__twinkle_2"></div>
                    <a class="light-header__title active" data-show-popup="true" href="#popup-profile">Обмен компоинтов</a>
                </li>
            </ul>
            <div class="popup-center">

                <div class="loyality__top">
                    <p>
                        <span>У Вас компоинтов: </span>
                        <span id="user_comp_in_popup" class="text-yellow"><?php echo floor(auth::user()->comp_current) ?></span>
                        <span id="compswap_message" style="font-size: 20px;"></span>
                    </p>
                    <p>
                        <span>Мы предлагаем вам честную программу лояльности, в которой собрали только ценные привилегии. Никаких скрытых условий и многократных отыгрышей, никаких сложных расчетов и размытых формулировок. Условия программы просты и понятны.</span>
                        <span class="text-yellow">За каждые 100 <?php echo auth::user()->currency(); ?>  ставок вы получаете 1 компоинт.</span> 
                        <span>Чем больше компоинтов, тем выше ваш уровень. Чем выше уровень, тем более ценные привилегии вы получаете.</span>
                    </p>
                </div>

                <div class="loyality__center">
                    <div class="exchanger bg-block">
                        <form id="loyality_form" class="form" action="/user/compswap" method="POST" novalidate="novalidate">
                            <div class="grid-wrapper">
                                <div class="grid-wrapper-cols pd-lg-sides">
                                    <div class="grid-col__8-12">
                                        <div class="grid-wrapper-cols">
                                            <div class="grid-col__5-12">
                                                <div class="fld-wrap fld-lbl">
                                                    <input id="exchange_points" name="compoints" required="required" class="fld points_count fld" value="<?php echo floor(auth::user()->comp_current) ?>" type="text" style="text-align: center;">
                                                    <label class="lbl-on" style="margin: 0 auto;">Компоинтов</label>
                                                </div>
                                            </div>
                                            <div class="grid-col__2-12">
                                                <i class="ico ico-arr"></i>
                                            </div>
                                            <div class="grid-col__5-12">
                                                <div class="fld-wrap fld-lbl fld-rub">
                                                    <input id="money_value" class="fld" value="<?php echo (floor(auth::user()->comp_current) * auth::user()->get_compoint_param('coeffs')) ?>" maxlength="6" type="text" style="text-align: center;">
                                                    <label for="loyality-money" class="lbl-on" style="margin: 0 auto;"><?php echo auth::user()->currency(); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid-col__4-12">
                                        <button class="btn btn-blue btn-md" type="submit">
                                            <span>Обменять компоинты</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="" data-historyloyality="hide">
                        <div class="grid-wrapper loyality__list">
                            <div class="grid-wrapper-cols pd-lg-sides">
                                <div class="grid-col__5-12">
                                    <div class="loyality__left">
                                        <div class="loyality-title text-blue-l" style="text-align: center">Лестница статусов</div>
                                        <p class="loyality-text" style="margin: 0 auto;">
                                            Чем активнее вы играете, тем выше становится ваш статус в программе лояльности. Каждый новый уровень &ndash; это еще больше возможностей наслаждаться эксклюзивными бонусами и преимуществами клуба Вулкан!
                                        </p>
                                    </div>
                                </div>
                                <div class="grid-col__7-12">
                                    <div class="loyality__right">
                                        <?php for ($i = 1; $i <= 6; $i++): ?>
                                            <div class="loyality__level">
                                                <div class="loyality__level-top"><?php echo $comp_config['names'][$i]; ?></div>
                                                <div class="loyality__level-center">
                                                    <div class="ico ico-level-lg">
                                                        <div class="level level_<?php echo $i; ?>"></div>
                                                    </div>
                                                </div>
                                                <div><?php echo $comp_config['levels'][$i] ?></div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-block loyality__list-bottom" style="margin-top: 0 !important;">
                            <div class="grid-wrapper loyality__list">
                                <div class="grid-wrapper-cols pd-lg-sides">
                                    <div class="grid-col__5-12">
                                        <div class="loyality__left">
                                            <div class="loyality__left-text" style="text-align: center">
                                                Курсы обмена:
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid-col__7-12">
                                        <div class="loyality__right">
                                            <?php
                                            for ($i = 1; $i <= 6; $i++): ?>
                                                <div class="loyality__level">
                                                    <div class="text-center"><?php echo 100 . ':' . 100 * $comp_config['coeffs'][$i] ?></div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="" data-historyloyality="hide">
                        <div class="grid-wrapper loyality__list">
                            <div class="grid-wrapper-cols pd-lg-sides">
                                <div class="grid-col__5-12"></div>
                                <div class="grid-col__6-12">
                                    <div class="loyality__right">
                                        <?php for ($i = 7; $i <= 11; $i++): ?>
                                            <div class="loyality__level">
                                                <div class="loyality__level-top"><?php echo $comp_config['names'][$i]; ?></div>
                                                <div class="loyality__level-center">
                                                    <div class="ico ico-level-lg">
                                                        <div class="level-next level_<?php echo $i; ?>"></div>
                                                    </div>
                                                </div>
                                                <div><?php echo $comp_config['levels'][$i] ?></div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-block loyality__list-bottom" style="margin-top: 0 !important;">
                            <div class="grid-wrapper loyality__list">
                                <div class="grid-wrapper-cols pd-lg-sides">
                                    <div class="grid-col__5-12">
                                        <div class="loyality__left">
                                            <div class="loyality__left-text" style="text-align: center">
                                                Курсы обмена:
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid-col__6-12">
                                        <div class="loyality__right">
                                            <?php for ($i = 7; $i <= 11; $i++):?>
                                                <div class="loyality__level">
                                                    <div class="text-center"><?php echo 100 . ':' . 100 * $comp_config['coeffs'][$i] ?></div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="popup-footer">
                        <div class="popup-footer-content">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>