<?php if(!$enter_code OR !$answer['success']): ?>
    <form method="POST" action="/login/phonecode">
        <div class="row">
            <div style="padding-top:5px; color:#fff;" class="form-field amount-money">
                <label for="phone_code"><?php echo isset($answer['text']) ? $answer['text'] : 'Для получения нового пароля введите код из смс' ?></label>
                <input value="<?php echo $answer['remind'] ?>" id="remind" class="input_amount" name="remind" type="text" style="display: none">
                <input value="" id="phone_code" class="input_amount" name="code" type="text">
            </div>
        </div>
        <button type="submit" class="invalid pay_submit">Получить</button>
    </form>
<?php else: ?>
    <article class="text">
        <p>Новый пароль отправлен в смс.</p>
    </article>
<?php endif; ?>
