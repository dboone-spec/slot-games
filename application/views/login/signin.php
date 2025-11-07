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
                <div class="title_section"><h3><span class="boldtxt"><?php echo __('Регистрация'); ?></span> </h3></div>
                <div class="sec_desc">

                    <div id="comment_form">

                        <form method="post" id="signinajax_form" action="/login/signinajax">
                            <div id="errors" style="color:red">
                                <?php foreach($errors as $e): ?>
                                    <?php echo $e ?><br />
                                <?php endforeach ?>
                            </div>


<!--                            <div class="form_row">
                                <?php echo FORM::input('name',$u->name,array('placeholder' => __('Логин'),'required'=>'required')) ?>
                            </div>-->

                            <div class="form_row">
                                <?php echo FORM::input('email',$u->email,array('placeholder' => 'Email','required'=>'required')) ?>
                            </div>

                            <div class="form_row">
                                <?php echo FORM::password('password',null,array('placeholder' => __('Пароль'),'required'=>'required')) ?>
                            </div>

                            <div class="form_row">
                                <?php echo FORM::input('comment',$u->email,array('placeholder' => __('Комментарий'))) ?>
                            </div>

<!--                            <div class="form_row">
                                <?php echo FORM::password('password_confirm',null,array('placeholder' => __('Повторите пароль'),'required'=>'required')) ?>
                            </div>-->

<!--                            <div class="form_row">
                                <?php echo FORM::input('email_repeat',$u->email,array('placeholder' => 'Email repeat','required'=>'required')) ?>
                            </div>

                            <div class="form_row">
                                <?php echo FORM::input('phone',$u->email,array('placeholder' => __('Телефон'),'required'=>'required')) ?>
                            </div>

                            <div class="form_row">
                                <?php echo FORM::input('visible_name',$u->email,array('placeholder' => __('Сайт'),'required'=>'required')) ?>
                            </div>-->



                            <input type="submit" class="submit_btn" value="<?php echo __('Регистрация'); ?>" name="Submit">
                            <br><br>
                            <!--<a href='<?php echo $link ?>login'><?php echo __('Войти') ?></a> <a href='<?php echo $link ?>login/forget' onclick="alert('<?php echo __('Записывать надо') ?>!')"><?php echo __('Забыл пароль') ?></a>-->


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
        $('#signinajax_form').ajaxForm({
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
    #signinajax_form {
        width: 50%;
        margin: 0 auto;
    }
</style>