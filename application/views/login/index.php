<div id="contentarea_1" style="
    margin: 0 auto;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    display: block;
    width: 100%;
    ">
	<div class="section03">


        <div class="sub_section04" style="text-align: center;">
        	<div class="sec04_left">
            	<div class="title_section"><h3><span class="boldtxt"><?php echo __('Авторизация'); ?></span> </h3></div>
            <div class="sec_desc">




				<div id="comment_form">

						<form method="post" style="height: 194px;" action="/login/login" id="loginajax_form">

						<div style="float:left; width: 100%;">
							<?php if(isset($bad_login)){?>
								<div style="text-align: center; color: red"><?php echo __('Введен неверный логин или пароль').'! '.__('Попробуйте еще раз').'!'?></div>
							<?php } ?>

							<div class="form_row">
								<input style="margin: 0 auto;" type="text" name="login" placeholder="<?php echo __('Логин')?>" />
							</div>

							<div class="form_row">
								<input style="margin: 0 auto;" type="password" name="password" value="" placeholder="<?php echo __('Пароль')?>"/>
							</div>
                                <?php if(false): ?>
							<div class="form_row">
								<input id="remember" type="checkbox" name="remember" value="on" checked="checked" style="width: 80px"/>
								<label for="remember" class="celltext"><strong><?php echo __('Запомнить')?></strong></label>
							</div>
                                <?php endif; ?>
							<input style="margin: 0 auto;" type="submit" class="submit_btn" value="<?php echo __('Войти'); ?>" name="Submit">
							<br><br>

						</div>
                            <?php if(false): ?>
						<div style="float:left; padding-left: 32px; margin-left: 68px;">
							<span class="celltext"><?php echo __('Вход через соцсети'); ?>:</span><br>
							<a href="/sauth/auth/vk" class="signup"><img src="/images/vk.png" /></a>
							<a href="/sauth/auth/fb" class="signup"><img src="/images/fb.png" /></a>
							<br><br><br><br>
							<a href='<?php echo $link ?>login/signin' class="celltext"><?php echo __('Регистрация')?></a>
							<a href='<?php echo $link ?>login/forget' class="celltext"><?php echo __('Забыл пароль')?></a>
						</div>
                            <?php endif; ?>

						</form>

					</div>


			</div>




			</div>
            <!--<div class="sec04_right"><img alt="" src="images/payment.png"></div>-->
        <div class="clr"></div>
        </div>

    <div class="clr"></div>
    </div>



</div>

<script>
    $(document).ready(function () {
        $('#loginajax_form').ajaxForm({
            dataType: 'json',
            success: function (d) {
                $('.notification.error').remove();
                if(Object.keys(d.errors).length) {
                    $.each(d.errors,function(k,v) {
                        var e = $('<div/>', {
                            "class": 'notification error closeable',
                            text: v
                        });
                        $('[name='+k+']').after(e);
                        $(document.body).pixusNotifications({
                                speed: 300,
                                animation: 'fadeAndSlide',
                                hideBoxes: false
                        });
                    });
                }
                else if(d.refresh=='1') {
                    window.location = window.location;
                }
            }
        });
    });
</script>

<style>
    .form_row input {
        margin: 0 auto;
        width: 100%;
    }
    #loginajax_form {
        width: 50%;
        margin: 0 auto;
    }
</style>