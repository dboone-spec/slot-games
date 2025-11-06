

<div id="comment_form">
    <?php if (Cookie::get('lang') == 'en' || I18n::$lang == 'en'): ?>
        <a href="<?php echo $dir; ?>/lang/set/ru">
            EN
        </a>
    <?php elseif (Cookie::get('lang') == 'ru' || I18n::$lang == 'ru'): ?>
        <a href="<?php echo $dir; ?>/lang/set/en">
            RU
        </a>
    <?php endif; ?>
	<form method="post" id="auth_form" style="height: 194px;" action="/enter/login/login">

		<div style="float:left">

                        <div id="error" style="text-align: center; color: red; display: none"></div>

                    
                        <table>
                            <tr>
                                <td> 
                                    Login
                                </td>
                                <td> 
                                    <input type="text" value="" name="login" placeholder="<?php echo __('Логин') ?>" />
                                </td>   
                            </tr>
                            
                            <tr>
                                <td> 
                                    Password
                                </td>
                                <td> 
                                    <input type="password" name="password" value="" placeholder="<?php echo __('Пароль') ?>"/>
                                </td>
                            </tr>
                            
                            
                              <tr style="display:none"  id="telegram" >
                                <td> 
                                    Telegram username<br>
                                    (case sensitive)
                                </td>
                                <td> 
                                    <input type="text" value="" name="telegram" placeholder="username"  />
                                </td>
                            </tr>
                            
                              <tr style="display:none" id="code">
                                <td> 
                                    Secret code
                                </td>
                                <td> 
                                    <input type="text" value="" name="code"  placeholder="0000"  />
                                </td>
                            </tr>

		
                        </table>
                        
           
			

			<input type="submit" class="submit_btn" value="<?php echo __('Войти'); ?>" name="Submit">
			<br>
                        <br>
                        <br>
                        <br>
                        
                        <a href="/htmlpages/telegramhelp.html" target="_blank"/>Telegram help </a>

		</div>


	</form>

</div>


<script>
    
    $(function() {
    
        var options = { 
            beforeSubmit:function(){
                $('#error').show();   
                $('#error').html('Wait');
            },
            
            dataType:'json',
            success:       function(data){
               
                $('#error').html(data.error);
               
                if (data.status=='login'){
                    window.location.replace('/enter');
                }
                
                if (data.status=='code'){
                   $('#code').show();   
                }
                
                if (data.status=='telegram'){
                   $('#telegram').show();    
                }


            }
        }; 

        $('#auth_form').ajaxForm(options);
    });
  </script>

