<div id="" class="popup-body popup-hg">

    <div class="popup-close"></div>

    <div class="popup-content popup-payments">
        <div class="popup-border">


            <ul class="popup-header-list">
                <li class="light-header" >
                    <div class="light-header__twinkle_1"></div>
                    <div class="light-header__twinkle_2"></div>
                    <a class="light-header__title show_popup" href="javascript:void(0);" data-href="/payment/in" >Пополнение счета</a>
                </li>
                <li class="light-header">
                    <div class="light-header__twinkle_1"></div>
                    <div class="light-header__twinkle_2"></div>
                    <a class="light-header__title open_popup" href="/payment/out" >Получить выигрыш</a>
                </li>
                <li class="light-header" >
                    <div class="light-header__twinkle_1"></div>
                    <div class="light-header__twinkle_2"></div>
                    <a class="light-header__title open_popup" href="/payment/history" >История платежей</a>
                </li>
            </ul>
            <div class="popup-center" style="margin-top: 120px">
                <?php if (auth::user()->bonus > 0) : ?>
                    <div class="user-bar__block _cash" style="margin: 50px auto; position: absolute">
                        <div class="user-bar__data">
                            <div class="user-bar__data-title">Бонусов:</div>
                            <div class="user-bar__data-value">
                                <a href="#"><?php echo number_format(auth::user()->bonus, 2) ?> RUB</a>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (auth::user()->bonusbreak > 0) : ?>
                    <div class="user-bar__block _points  disabled" style="margin: 100px auto; position: absolute">
                        <div class="user-bar__data">
                            <div class="user-bar__data-title">
                                Отыгрыш бонуса
                            </div>
                            <div class="user-bar__data-value">
                                <div class="scale scale-user-bar">
                                    <div style="width: <?php echo round(100 * auth::user()->bonuscurrent / auth::user()->bonusbreak) ?>%;" class="snake"></div>
                                    <div class="percent">
                                        <?php echo round(100 * auth::user()->bonuscurrent / auth::user()->bonusbreak) ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>

            <div class="popup-footer">
                <div class="popup-footer-content"></div>
            </div>

        </div>
    </div>

</div>