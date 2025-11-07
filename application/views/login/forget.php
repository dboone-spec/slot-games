
<div class="cardOfGoods">




<div class="cgContent">

<div class="company">




<div class="clear"></div>
<div class="registration">
	<br>
<p>
Для восстановления пароля введите данные, указанные при регистрации.
</p>
<?php if (isset($bad)){ ?>
<span class="alerttext">Такой логин или email не зарегистрирован</span><br/>
<?php } ?>

<br>
<form method="POST">
   Логин: <input name="name">
    <input type="submit" value="Восстановить по логину" name="button_login"/>
</form>
<br>
		
<form method="POST">
    Email: <input name="email">&nbsp;&nbsp;
    <input type="submit" value=" Восстановить по e-mail  " name="button_email"/>
</form>

</div>

</div>
</div>

</div>


