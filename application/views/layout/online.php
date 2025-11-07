<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" lang="en"><![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"><![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"><!--<![endif]-->
<head>

<!-- Basic Page Needs
================================================== -->
<meta charset="utf-8">
<title>Mango</title>

<!-- Mobile Specific
================================================== -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!-- CSS
================================================== -->
<link rel="stylesheet" type="text/css" href="/theme/centum/css/style.css">
<link rel="stylesheet" type="text/css" href="/theme/centum/css/boxed.css" id="layout">
<link rel="stylesheet" type="text/css" href="/theme/centum/css/colors/green.css" id="colors">


<!-- Java Script
================================================== -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>
<script src="/theme/centum/js/selectnav.js"></script>
<script src="/theme/centum/js/flexslider.js"></script>
<script src="/theme/centum/js/twitter.js"></script>
<script src="/theme/centum/js/tooltip.js"></script>
<script src="/theme/centum/js/effects.js"></script>
<script src="/theme/centum/js/fancybox.js"></script>
<script src="/theme/centum/js/carousel.js"></script>
<script src="/theme/centum/js/isotope.js"></script>
<script src="/theme/centum/js/jquery-easing-1.3.js"></script>
<script src="/theme/centum/js/jquery-transit-modified.js"></script>
<script src="/theme/centum/js/layerslider.transitions.js"></script>
<script src="/theme/centum/js/layerslider.kreaturamedia.jquery.js"></script>
<script src="/theme/centum/js/greensock.js"></script>
<script src="/theme/centum/js/counterup.min.js"></script>
<script src="/theme/centum/js/waypoints.min.js"></script>
<script src="/theme/centum/js/owl.carousel.min.js"></script>
<script src="/theme/centum/js/custom.js"></script>


<!-- REVOLUTION JS FILES -->
<script type="text/javascript" src="/theme/centum/js/jquery.themepunch.tools.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/jquery.themepunch.revolution.min.js"></script>

<!-- REVOLUTION STYLE SHEETS -->
<link rel="stylesheet" type="text/css" href="/theme/centum/css/revolutionslider.css">

<!-- SLIDER REVOLUTION 5.0 EXTENSIONS
	(Load Extensions only on Local File Systems !
	The following part can be removed on Server for On Demand Loading) -->
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.actions.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.carousel.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.kenburn.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.layeranimation.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.migration.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.navigation.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.parallax.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.slideanims.min.js"></script>
<script type="text/javascript" src="/theme/centum/js/extensions/revolution.extension.video.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>

</head>
<body>

<!-- Wrapper Start -->
<div id="wrapper">


<!-- Header
================================================== -->

<!-- 960 Container -->
<div class="container ie-dropdown-fix">

	<!-- Header -->
	<div id="header">

		<!-- Logo -->
		<div class="eight columns">
                    <div style="float:left">

                            <img src="/theme/centum/logo.jpg" />
                    </div>
			<div id="logo" style="float:left">

                            <a href="/online"><h1>&nbsp;&nbsp;&nbsp; MangoBet</h1></a>


			</div>

                    <div class="clear"></div>
		</div>
		<div class="eight columns">
            <?php if(!auth::$user_id): ?>
                <form style="height: 41px;" method="post" style="" action="/login/login" id="loginajax_form">
                    <label>
                        <input id-="login<?php mt_rand(10000,99999); ?>" style="margin: 0 auto;" type="text" name="login" autocomplete="off" placeholder="<?php echo __('Логин')?>" />
                        <i class="open_virtkeys fa fa-keyboard-o fa-3" style="cursor: pointer; position: absolute; margin-left: 156px; margin-top: -27px;" aria-hidden="true"></i>
                    </label>
                    <label>
                        <input style="margin: 0 auto;" type="password" name="password" value="" placeholder="<?php echo __('Пароль')?>"/>
                        <i class="open_virtkeys fa fa-keyboard-o fa-3" style="cursor: pointer; position: absolute; margin-left: 156px; margin-top: -27px;" aria-hidden="true"></i>
                    </label>
                    <input style="margin: 0 auto; padding: 11.5px 12px;" type="submit" class="button color submit_btn" value="<?php echo __('Войти'); ?>" name="Submit">
                </form>
                <?php if(defined('KIOSK') && KIOSK): ?>

                <?php else: ?>
                <a style="" href="/login/signin" class="fancybox.ajax auth_popup"><?php echo __('Регистрация'); ?></a>
                <?php endif; ?>
                <div class="virt_keys" style="display: none;">
                    <i class="close">X</i>
                    <div class="virt_keyswrap">
                        <?php for($i=1;$i<=9;$i++): ?>
                            <span keyval="<?php echo $i; ?>"><?php echo $i; ?></span>
                        <?php endfor; ?>
                        <span keyval="delone">&larr;</span>
                        <span keyval="<?php echo 0; ?>"><?php echo 0; ?></span>
                        <span keyval="clearall">X</span>
                    </div>
                </div>
                <style>
                    form#loginajax_form {
                        display: inline-flex;
                    }
                    .virt_keys {
                        position: absolute;
                        display: block;
                        cursor: pointer;
                        width: 15%;
                        margin-top: 2%;
                        margin-left: 8%;
                    }
                    .virt_keys .close {
                        position: absolute;
                        margin-left: 100%;
                        margin-top: -13%;
                        border: 1px solid;
                        border-radius: 40%;
                        padding: 1% 4%;
                    }
                    .virt_keyswrap {
                        display: flex;
                        flex-wrap: wrap;
                        align-items: center;
                        justify-content: center;
                    }
                    .virt_keyswrap span {
                        flex-grow: 1;
                        flex: 1 1 30%;
                        height: 40px;
                        line-height: 40px;
                        border: 1px solid;
                        text-align: center;
                        background: #303030;
                        color: #fff;
                    }
                    .virt_keyswrap span:hover {
                        color: #303030;
                        background: #fff;
                    }
                </style>
                <script>
                    $(document).ready(function () {

                        var $input;

                        $('.open_virtkeys').click(function() {
                            $('.virt_keys').toggle();
                        });

                        $('#loginajax_form input').on('focus', function() {
                            $input = $(':focus');
                        });

                        $('.virt_keys .close').click(function() {
                            $('.virt_keys').hide();
                        });

                        $('.virt_keys span').click(function() {
                            if($('.virt_keys').is(':visible')) {
                                $input.trigger('focus');
                                var keyval=$(this).attr('keyval');
                                if(keyval=='delone') {
                                    $input.val($input.val().slice(0,-1));
                                }
                                else if(keyval=='clearall') {
                                    $input.val('');
                                }
                                else {
                                    $input.val($input.val()+keyval);
                                }
                                $input.trigger('focus');
                            }
                        });

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
                                        $('body div').first().before(e);
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
            <?php else: ?>
            <div style="float: right;">
            <span style="line-height: 40px;padding: 10px;"><?php echo __('ID'); ?>: <?php echo auth::$user_id; ?></span>&nbsp;
            <span style="line-height: 40px;padding: 10px;"><?php echo __('Баланс'); ?>: <?php echo auth::user()->amount(); ?></span>
            <a style="float: right;" href="/login/logout" class="button color"><?php echo __('Выход'); ?></a>
            </div>
            <?php endif; ?>
		</div>


	</div>
	<!-- Header / End -->

	<!-- Navigation -->
	<div class="sixteen columns">

		<div id="navigation">
			<ul id="nav">
				<li><a <?php if($active=='index'): ?>id="current"<?php endif; ?>  href="/online"><?php echo __('Главная'); ?></a>

				</li>

				<li><a <?php if($active=='api'): ?>id="current"<?php endif; ?> href="/online/contacts"><?php echo __('API'); ?></a>

				</li>

				<li><a  <?php if($active=='contacts'): ?>id="current"<?php endif; ?>href="/online/contacts"><?php echo __('Контакты'); ?></a>

				</li>


			</ul>
            <?php if($active=='index'): ?>
            <div class="search-form">
                <input type="text" name="search_games_input" class="search-text-box">
            </div>
            <script>
                $('[name=search_games_input]').keyup(function() {
                    var val = $(this).val().trim().toLowerCase();

                    $('.portfolio-item').each(function() {
                        var $this = $(this);
                        $this.hide();
                        var txt = $(this).find('.item-description a').text().trim().toLowerCase();
                        if(txt.indexOf(val)+1>0) {
                            $this.show();
                        }
                    });
                    $('#portfolio-wrapper').isotope({
                            itemSelector : '.portfolio-item, .isotope-item',
                            layoutMode : 'fitRows'
                    });
                });
            </script>
            <?php endif; ?>
		</div>
		<div class="clear"></div>

	</div>
	<!-- Navigation / End -->

</div>
<!-- 960 Container / End -->
<?php echo $content ?>
</div>

<!-- Footer
================================================== -->

<!-- Footer Start -->
<div id="footer">
	<!-- 960 Container -->
	<div class="container">


		<!-- Footer / Bottom -->
		<div class="sixteen columns">
			<div id="footer-bottom">
				© Copyright
                                <?php if (date('Y')==2019):  ?>
                                    2019
                                <?php else:  ?>
                                2019 - <?php echo date('Y')?>
                                <?php endif ?>

                                by <a href="/">Mango Bet</a>. All rights reserved.
				<div id="scroll-top-top"><a href="#"></a></div>
			</div>
		</div>

	</div>
	<!-- 960 Container / End -->

</div>
<!-- Footer / End -->


<!-- Styles Switcher
================================================== -->
<link rel="stylesheet" type="text/css" href="/theme/centum/css/switcher.css">
<script src="/theme/centum/js/switcher.js"></script>
<?php echo Flash::render(); ?>
<script>
    $(window).load(function() {
        $('.auth_popup').fancybox({
            maxWidth	: 800,
            maxHeight	: 600,
            fitToView	: false,
            width		: '70%',
            height		: '70%',
            autoSize	: false,
            closeClick	: false
//            closeClick	: false,
//            openEffect	: 'none',
//            closeEffect	: 'none'
        });
        $('#messages').fancybox({
            fitToView	: false,
            autoSize	: false,
            padding     : 20,
            maxWidth      : 500,
            minHeight      : 320,
            maxHeight      : 320,
            closeClick	: true
        }).click();
    });
</script>
<style>
    #messages li {
        text-align: center;
        font-size: 20pt;
        line-height: 50pt;
    }
</style>

<?php echo block::rfid_listen(); ?>

</body>
</html>