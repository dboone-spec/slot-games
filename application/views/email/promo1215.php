<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      </head>
    <body>
<table style="border-collapse:collapse;background:#ffffff;color:#2d2d2d;font-family:Arial, sans-serif;font-size:16px;margin-right:auto;margin-left:auto" cellpadding="0" cellspacing="0" width="600">
    <tbody>
        <tr>
            <td style="line-height:0;vertical-align:top" height="160">
                <a href="https://<?php echo $user->get_domain(); ?>" style="display:block;width:600px;height:160px;min-height:160px;max-height:160px;line-height:0;text-decoration:none" target="_blank" rel="noopener">
                    <img src="<?php echo $user->get_domain() == 'casinovabank.com' ? "https://{$user->get_domain()}/assets/img/vabankbig.png" : "https://{$user->get_domain()}/assets/img/sys/anim/logo.gif"; ?>" style="display: block; margin:0 auto;border:0px solid #000000;vertical-align:baseline" />
                </a>
            </td>
        </tr>
        <tr>
            <td style="line-height: 1.5em;color: #00416c;font-size: 22px;font-weight: bold;text-align: center;padding: 0 50px 2px;">
                <div>
                    Здравствуйте!
                </div>
                <div style="color: #2d2d2d;font-size: 16px;font-weight: bold;padding: 14px 0 0 10px;">
                    Успей окунуться в красочный мир азартных игр!
                </div>
                <div style="color: #2d2d2d;font-size: 16px;font-weight: bold;padding: 14px 0 0 10px;">
                    Только с 12 по 15 октября введи промокод <span style="color: red; font-size: 20px;">1215</span> и получи <span style="color:red;">200%</span> бонусов за депозит!
                </div>
                <div style="color: #2d2d2d;font-size: 16px;font-weight: bold;padding: 14px 0 0 10px;">
                    Почувствуй вкус победы!!!
                </div>
                <br/>
                <a href="https://<?php echo $user->get_domain(); ?>" style="display:block;width:493px;height:300px;min-height:300px;max-height:300px;line-height:0;text-decoration:none" target="_blank" rel="noopener">
                    <img src="<?php echo "https://{$user->get_domain()}/uploads/mail/200bonusmail.jpg" ?>" style="display: block; margin:0 auto;border:0px solid #000000;vertical-align:baseline" />
                </a>
                <br/>
            </td>
        </tr>
        <tr>
            <td style="vertical-align:top;padding:0 48px 44px;line-height:1.4em">
                <a href="https://<?php echo $user->get_domain(); ?>" style="background: #001233; display: block;margin: 0 auto;width: 316px;height: 62px;text-transform: uppercase;text-decoration: none;line-height: 62px;text-align: center;font-size: 22px;color: #fff;font-weight: bold;" target="_blank" rel="noopener" data-snippet-id="4add2db2-4163-ddbb-2e44-2f5d13c8a919">Войти в&nbsp;казино</a>
            </td>
        </tr>
        <tr>
            <td style="text-align:center;vertical-align:top;background:#bbe3fc;font-size:11px;color:#636363;line-height:1.3em;padding:20px 45px">
                Вы получили это письмо, потому что зарегистрированы в&nbsp;<?php echo $user->get_domain(); ?> и подтвердили свое согласие с&nbsp;<a href="https://<?php echo $user->get_domain(). '/page/view/rule'; ?>" style="color:#0067ab !important;text-decoration:underline" target="_blank" rel="noopener">Правилами и Условиями</a>.
                <br><br>
                <a data-msys-unsubscribe="1" href="https://<?php echo $user->get_domain(). '/login/nospam?mail='.$user->email; ?>" style="color:#0067ab !important;text-decoration:underline" target="_blank" rel="noopener">Отписаться от рассылки</a>.
                <br><br>
                Служба поддержки: <a href="//e.mail.ru/compose/?mailto=mailto%3asupport@<?php echo $user->get_domain() ?>" style="color:#0067ab !important;text-decoration:underline" target="_blank" rel="noopener">support@<?php echo $user->get_domain(); ?></a> или <span syle="color:#0067ab !important;text-decoration:underline"><span class="js-phone-number highlight-phone" title="Позвонить через Веб-Агент">+7 (499) 112-09-93</span></span>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>