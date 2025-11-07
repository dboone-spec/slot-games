<div id="" class="popup-body popup-hg">

	<div class="popup-close"></div>

    <div class="popup-content popup-payments">
        <div class="popup-border">


			<ul class="popup-header-list">
				<li class="light-header active">
					<div class="light-header__twinkle_1"></div>
					<div class="light-header__twinkle_2"></div>
					<a class="light-header__title active show_popup" href="javascript:void(0);" data-href="/payment/in">Пополнение счета</a>
				</li>
				<li class="light-header">
					<div class="light-header__twinkle_1"></div>
					<div class="light-header__twinkle_2"></div>
					<a class="light-header__title open_popup" href="/payment/out">Получить выигрыш</a>
				</li>
				<li class="light-header">
					<div class="light-header__twinkle_1"></div>
					<div class="light-header__twinkle_2"></div>
					<a class="light-header__title open_popup" href="/payment/history">История платежей</a>
				</li>
			</ul>
            <div class="popup-center">

                <div class="payments-frame">

					<div dir="ltr" style="position: relative; left: 0px; top: 0px;" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" id="mCSB_1_container">
						
						<?php foreach ($select as $id=>$sys):?>
							<div class="item">
								<a class="open_popup" href=<?php echo "/payment/incase/$id"?>>
									<img alt="1" src="/games/payment/<?php echo "$id.png"?>" class="mCS_img_loaded">
								</a>
							</div>
						<?php endforeach ?>
						
					</div>

                </div>

            </div>

            <div class="popup-footer">
                <div class="popup-footer-content"></div>
            </div>

        </div>
    </div>

</div>

