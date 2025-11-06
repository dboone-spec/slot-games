<script>
    $(document).ready(function () {
        $('.confirm_code, [name="confirm_code"]').hide();

        function add_message(message, error) {
            var color = error ? 'red' : 'green';
            var message_text = '<label class="col-md-12" id="feedback_message" style="color: ' + color + ';">';
            for (var i in message) {
                message_text += message[i] + '<br>';
            }
            message_text += '</label>';
            $('#feedback_message').replaceWith(message_text);
        }

        $('#enable_telegram').change(function () {
            $('.confirm_code').show();
        });

        $('#send_tg_code').click(function () {
            $.ajax({
                url: '<?php echo $dir; ?>/profile/tgcode',
                dataType: 'json',
                success: function (data) {
                    if (data.error == 1) {
                        add_message([data.text], data.error);
                    } else {
                        $('[name="confirm_code"]').show();
                        $('#send_tg_code').remove();
                        add_message([data.text], data.error);
                    }
                }
            });
        });
        <?php if(!person::user()->phone_code || person::user()->phone_confirm): ?>
        $('.showcode').hide();
        <?php endif; ?>

        $('#sendsms').click(function () {
            $.ajax({
                url: '<?php echo $dir; ?>/profile/phone?phone=' + $('#phone_confirm').val(),
                success: function (data) {
                    if (data.error == 0) {
                        $('#phonetext').html('На указанный номер телефона отправлено СМС сообщение с кодом. Введите его:');
                        $('.showcode').show();
                    } else {
                        $('#phonetext').html(data.text);
                    }
                },
                dataType: 'json'
            });
        });
        $('#setcode').click(function () {
            $.ajax({
                url: '<?php echo $dir; ?>/profile/code?code=' + $('#code_confirm').val(),
                success: function (data) {
                    if (data.error == 0) {
                        $('#codetext').html('Ваш номер телефона подтвержден');
                    } else {
                        $('#codetext').html(data.text);
                    }
                },
                dataType: 'json'
            });
        });

        var optionsSettings = {
            dataType: 'json',
            type: 'post',
            beforeSubmit: function () {
                $('#text_settings').empty();
            },
            success: function (data) {
                var message_text = '';
                if (data.error) {
                    for (index in data.errors) {
                        message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                    }
                } else {
                    $('#pass1, #pass2').val("");
                    message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
                }
                $('#text_settings').append(message_text);
            },
            error: function (err) {
                var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
                $('#text_settings').append(err_message);
            }
        };
        $('#pass_settings').ajaxForm(optionsSettings);

    });
</script>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo __('Настройки аккаунта') ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="white-box">
                <h2 class="page-title" style="text-align: center;"><?php echo __('Сменить пароль') ?></h2>
                <form class="form-horizontal form-material" action="<?php echo $dir; ?>/profile/pass" method="post" id="pass_settings">
                    <div class="form-group">
                        <label class="col-md-6"><?php echo __('Введите новый пароль') ?></label>
                        <input class="col-md-6" type="password" id="pass1" name="password" value="" >
                    </div>
                    <div class="form-group">
                        <label class="col-md-6"><?php echo __('Подтвердите пароль') ?></label>
                        <input class="col-md-6" type="password" id="pass2" name="password_confirm" value="">
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button class="btn btn-success"><?php echo __('Сменить') ?></button>
                        </div>
                    </div>
                    <div id="text_settings"></div>
                </form>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="white-box">
                <h2 class="page-title" style="text-align: center;"><?php echo __('Номер телефона') ?></h2>
                <form class="form-horizontal form-material" action="<?php echo $dir; ?>/profile" method="post" id="settings">
                    <?php if(person::user()->phone_confirm): ?>
                        <label for="phone_confirm"><?php echo __('Ваш номер телефона') ?></label><br>
                    <?php else:?>
                        <label for="phone_confirm"><?php echo __('Подтвердите свой номер телефона') ?></label><br>
                    <?php endif;?>
                        <style>
                            .phone_placeholder:before {
                                content:"+";
                                position: absolute;
                                line-height: 25px;
                                left: 45px;
                                z-index: 50;
                            }
                        </style>
                    <div class="row phone_placeholder">
                        <input style="margin-left: 15px;" <?php if(person::$role=='kassa' && person::user()->phone_confirm): ?>disabled<?php endif; ?> class="col-md-4" type="text" placeholder="79191234567" value="<?php echo empty(person::user()->phone) ? '7' : person::user()->phone ?>" id="phone_confirm" class="input_phone" name="phone" size="10">
                        <?php if(!person::user()->phone_confirm): ?>
                            <button class="col-md-6" type="button" id="sendsms"><?php echo __('Отправить код на телефон') ?></button><br>
                        <?php endif;?>
                    </div>
                    <div class="row">
                        <span id="phonetext" class="" ></span>
                    </div>
                    <div class="row showcode">
                        <input class="col-md-4" type="text" value="" id="code_confirm" class="input_phone" name="amount" size="10">
                        <button class="col-md-6" type="button" class="pay_bonus" id="setcode" ><?php echo __('Подтвердить') ?></button><br>
                        <span id="codetext" class="" ></span>
                    </div>
                    <br>
                    <?php if(person::user()->phone_confirm): ?>
                        <div class="form-group">
                            <label class="col-md-12"><?php echo __('Авторизация через телеграм') ?></label>
                            <div class="col-md-12">
                                <input type="checkbox" name="telegram" id="enable_telegram" <?php echo person::user()->enable_telegram ? 'checked' : ''; ?> value="1" > <?php echo __('Включена') ?>
                                <?php if(!tgbot::phoneExists(person::user()->phone)): ?>
                                    
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group confirm_code">
                            <div class="col-md-12">
                                <input type="text" name="confirm_code">
                                <button class="btn btn-success" id="send_tg_code" onclick="return false;"><?php echo __('Отправить код подтверждения') ?></button>
                            </div>
                            <span id="feedback_message" class="" ></span>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($errors as $k => $v): ?>
                        <?php if($errors[$k]['error'] == 1): ?>
                            <div style="text-align: center; color: red"><?php echo $errors[$k]['text'] ?></div>
                        <?php else: ?>
                            <div style="text-align: center; color: green"><?php echo $errors[$k]['text'] ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button class="btn btn-success"><?php echo __('Сохранить') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>