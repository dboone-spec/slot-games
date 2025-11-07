<!DOCTYPE html>
<html lang="zxx">
<head>
	<meta charset="UTF-8">
	<title>AGT Software</title>
	<!-- =============== META =============== -->
	<meta name="keywords" content="">
	<meta name="description" content="">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#131213">
    <meta name="msapplication-navbutton-color" content="#131213">
    <meta name="apple-mobile-web-app-status-bar-style" content="#131213">
	<link rel="shortcut icon" href="/theme/interactive1/img/favicon.png">
	<!-- =============== STYLE =============== -->
	<link rel="stylesheet" href="/theme/interactive1/css/critical.css">
	<style>
		.preloader-active .preloader-cover{
			height: 4000px;
			transform: translate(-50%,-50%) rotate(45deg);
		}
		.preloader-cover{
			height: 0;
			width: 4000px;
			top: 50%;
			left: 50%;
			transform: translate(-50%,-50%) rotate(45deg);
			background: #131213;
			position: fixed;
			z-index: 999;
			overflow: hidden;
			transition: .8s cubic-bezier(0.65, 0.05, 0.36, 1) .3s;
			overflow: hidden;
			will-change: top, left;
		}
	</style>
</head>

<body id="home" class="home preloader-active" style="">
	<!--=============== PRELOADER ===============-->
	<div class="preloader-cover">
		<div class="preloader">
			<div class="ajax-loader">
				<div class="ajax-loader-logo">
					<div class="ajax-loader-circle">
						<svg class="ajax-loader-circle-spinner" viewBox="0 0 500 500" xml:space="preserve">
							<circle cx="250" cy="250" r="239" />
						</svg>
					</div>
					<div class="ajax-loader-letters"></div>
				</div>
			</div>
		</div>
	</div>
	<!--============= PRELOADER END =============-->

	<!--=============== HEADER ===============-->
	<header class="header">
		<div class="container">
			<a href="#" class="nav-btn">
				<span></span>
				<span></span>
				<span></span>
			</a>
			<div class="row align-items-center">
				<!--=============== LOGO ===============-->
				<div class="col-3 col-sm-6 col-lg-3 logo-item">
                    <a href="/" class="logo">
                        <img width="85px" src="/theme/interactive1/img/logo.png" alt="logo">
                    </a>
				</div>
				<!--============= LOGO END =============-->
				<div class="col-6 nav-menu-cover">
					<!--============= NAV MENU =============-->
					<nav class="nav-menu">
						<ul class="nav-list">
							<li <?php if($active=='index'):?>class="active"<?php endif; ?>>
								<a href="/">Home</a>
							</li>
							<li <?php if($active=='news'):?>class="active"<?php endif; ?>>
								<a href="/interactive/news">News</a>
							</li>

							<li><a href="/interactive#games">games</a></li>

							<!--<li><a href="/interactive#aboutus">About us</a></li>-->
							<li><a href="/interactive#contacts">Contacts</a></li>
							<li><a style="" target="_blank" href="/files/agt_en.pdf">Presentation</a></li>
							<?php if(SBC_DOMAIN && auth::$user_id): ?>
								<li><a href="/login/logout">New player</a></li>
							<?php endif; ?>
						</ul>
					</nav>
					<!--=========== NAV MENU END ===========-->
				</div>
                <?php if(auth::$user_id && auth::user()->office_id==1049): ?>
                <div class="col-9 col-sm-6 col-lg-3 logo-item user-info" style="justify-content: flex-end;">
                    <ul style="overflow: hidden;width: 85px;font-size: smaller;">
                        <li>
                            Hi&nbsp;<?php echo auth::user()->name; ?>!
                        </li>
                        <li>
                            <?php echo auth::user()->amount(); ?> <?php echo auth::user()->office->currency->code; ?>
                        </li>
                        <li>
                            <a href="/login/logout">Logout</a>
                        </li>
                    </ul>
				</div>
                <?php endif; ?>
			</div>
		</div>
	</header>
	<!--============= HEADER END =============-->

	<!--=============== MAIN CONTENT ===============-->
	<main>
		<?php echo $content ?>
	</main>
	<!--================== MAIN CONTENT END ==================-->

	<!--======================= FOOTER =======================-->
	<footer>
		<div class="container">
<!--			<div class="row">
				<div class="col-12 col-sm-6 col-lg-3 footer-info">
					<h5>about us</h5>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
				</div>
				<div class="col-6 col-sm-4 col-lg-2 blok-link">
					<h5>Shop</h5>
					<ul>
						<li><a href="#">DJI</a></li>
						<li><a href="#">Parrot</a></li>
						<li><a href="#">Hubsan</a></li>
						<li><a href="#">Mi</a></li>
					</ul>
				</div>
				<div class="col-6 col-sm-4 col-lg-2 blok-link">
					<h5>discount</h5>
					<ul>
						<li><a href="#">lorem ipsum</a></li>
						<li><a href="#">dolor sit amet</a></li>
						<li><a href="#">tempor incididunt</a></li>
						<li><a href="#">enim ad minim</a></li>
					</ul>
				</div>
				<div class="col-12 col-sm-4 col-lg-2 blok-link">
					<h5>support</h5>
					<ul>
						<li><a href="#">Help</a></li>
						<li><a href="#">Documentation</a></li>
						<li><a href="#">Privacy Policy</a></li>
					</ul>
				</div>
				<div class="col-12 col-sm-6 col-lg-3 footer-subscribe">
					<h5>subscribe</h5>
					<form action="/" class="subscribe-form">
						<input type="email" name="subscribe" placeholder="E-mail">
						<input type="submit" value="send">
					</form>
				</div>
			</div>-->
			<div class="footer-bottom">
				<div class="row align-items-center">
					<div class="col-12 col-md-3">
                        <a href="/" class="logo footer-logo"><img width="125px" src="/theme/interactive1/img/logo.png" alt="logo"></a>
					</div>
					<div class="col-12 col-md-6">
						<ul class="footer-menu" style="white-space: nowrap;">
                            <li>
								<a href="/">Home</a>
							</li>
							<li>
								<a href="/interactive/news">News</a>
							</li>

							<li><a href="/interactive#games">games</a></li>
							<!--<li><a href="/interactive#aboutus">About us</a></li>-->
							<li><a href="/interactive#contacts">Contacts</a></li>
							<li><a style="" target="_blank" href="/files/agt_en.pdf">Presentation</a></li>
							<?php if(!SBC_DOMAIN): ?>
							<li><a href="https://admin.site-domain.com">Back office</a></li>
							<?php endif; ?>
							<?php if(SBC_DOMAIN && auth::$user_id): ?>
							<li><a href="/login/logout">New player</a></li>
							<?php else: ?>
							<li><a href="/login">Demo</a></li>
							<?php endif; ?>
						</ul>
					</div>
<!--					<div class="col-12 col-md-3">
						<ul class="soc-link">
							<li><a target="_blank" href="https://www.facebook.com/rovadex"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
							<li><a target="_blank" href="https://www.instagram.com/rovadex"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
							<li><a target="_blank" href="https://www.youtube.com"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
							<li><a target="_blank" href="https://vimeo.com/"><i class="fa fa-vimeo-square" aria-hidden="true"></i></a></li>
						</ul>
					</div>-->
				</div>
			</div>
			<div class="copyright">
				<p>site-domain  © 2019 - <?php echo date('Y')?>. All Rights Reserved.</p>
			</div>
		</div>
	</footer>
	<!--===================== FOOTER END =====================-->

	<!--======================= TO TOP =======================-->
	<a class="to-top" href="#home">
		<i class="fa fa-chevron-up" aria-hidden="true"></i>
		<span>
            <img src="/theme/interactive1/img/cherry.png" alt="" style="cursor: none;">
		</span>
	</a>
	<!--===================== TO TOP END =====================-->

	<!-- =============== STYLE =============== -->
	<link rel="stylesheet" href="/theme/interactive1/css/slick.min.css">
	<link rel="stylesheet" href="/theme/interactive1/css/bootstrap-grid.css">
	<link rel="stylesheet" href="/theme/interactive1/css/font-awesome.min.css">
	<link rel="stylesheet" href="/theme/interactive1/css/jquery.fancybox.min.css" />
	<link rel="stylesheet" href="/theme/interactive1/css/style.css">

    <style>
        .ajax-loader .ajax-loader-letters {
            color: #fc0100;
            font-size: 40px;
            -webkit-text-stroke: 1px #fff;
            text-stroke: 1px #fff;
            width: 85px;
            height: 35px;
            background: url(/theme/interactive1/img/logo.png) no-repeat;
            background-size: contain;
            top: 31px;
            left: 6px;
        }
        .to-top img {
            width: 25px;
        }

        .s-blog {
            padding: 0px 0 60px;
        }

        @media (max-width: 767px) {
            .s-blog {
                padding: 0px 0 36px;
            }
        }

        .nav-btn:hover span:nth-child(2) {
            margin-left: 0;
        }

        header.header-scroll {
            border-color: rgba(255,0,0,.7);
        }
        h2::before {
            background-color: #ff0000;
        }

        a {
            color: #fff;
            font-weight: 700;
            position: relative;
        }

        .testimonial-item:before {
            border-color: #fff;
        }

        .btn {
            border-color: #ff0000;
        }

        .btn:hover {
            background-color: #ff0000;
        }

        @media (max-width: 767px) {
            .to-top span {
                background: #ff0000;
            }
        }

        .testimonial-item .prof {
            line-height: 12px;
        }
        .to-top:hover i {
            color: #ff0000;
        }

        .to-top:hover span {
            border-color: #ff0000;
        }

        input[type="search"]:focus, input[type="text"]:focus, input[type="tel"]:focus, input[type="email"]:focus, input[type="search"]:focus, textarea:focus {
    border-color: #fff;
}

    .slide-info a:not(.btn) {
        font-size: 100%;
        color: #ff0000;
    }

    .isMobile .main-slide-for img, .post-thumbnail img,.post-item .prod-thumbnail img {
        object-fit: contain;
    }

    .title-line-left:before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 135px;
        height: 2px;
        background-color: #ff0000;
        transform: translate(-50%,0);
    }

    .offer-item-content {
        text-align: center;
    }

    .offer-item-content p {
        text-align: justify;
    }

    .post-item .prod-thumbnail img {
/*        width: 60%;
        margin: 0 auto;*/
    }

    .gamethumb {
        transition: transform .5s ease-in-out;
    }

    .gamethumb:hover {
        transform: scale(1.05);
    }
    .gamethumb {
        text-transform: uppercase;
    }
    .prod-thumbnail {
        padding: 1px !important;
    }

    .title a:hover,.gallery-tabs .item:hover{
        color: #ff0000;
    }

    .gallery-tabs .item {
        border-bottom: none;
    }

    .gallery-tabs .item.active {
        color: #fff;
    }

    .gallery-tabs .item.active:before {
        background-color: #fff;
    }

    .gallery-tabs .item:before {
        background-color: #ff0000;
    }

    .gallery-tabs li .icon:hover {
        color: #ff0000;
    }

    .gallery-tabs li.search form button {
        border-color: #ff0000;
        background: #ff0000;
    }
    .gallery-tabs li.search form button:hover {
        color: #ff0000;
    }
    .nav-list li a:hover {
        color: #ff0000;
    }
    .nav-list>li>a:hover:before {
        background-color: #ff0000;
    }
	main {
        background: rgba(19, 18, 19, 1);
        background-image: url(/theme/interactive1/img/bg-body.svg);
    }

    @media (max-width: 575px) {
        .to-top:hover i {
            color: #fff;
        }
    }

    </style>
	<style>
        .iframe_agt_widget {
            width: 100%;
            height: 100%;
            position: absolute;
        }
    </style>
	<!--=============== TEMPLATE SCRIPT ===============-->
	<script src="/theme/interactive1/js/jquery-2.2.4.min.js"></script>
	<script src="/theme/interactive1/js/masonry.pkgd.min.js"></script>
	<script src="/theme/interactive1/js/jquery.fancybox.min.js"></script>
	<script src="/theme/interactive1/js/slick.min.js"></script>
	<script src="/theme/interactive1/js/jquery.lazy.min.js"></script>
	<script src="/theme/interactive1/js/jquery.lazy.plugins.min.js"></script>
	<script src="/theme/interactive1/js/scripts.js?v=15"></script>

    <script>

        jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
            return function( elem ) {
                return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
            };
        });


        $.fn.onAny = function(cb){
            for(var k in this[0])
              if(k.search('on') === 0)
                this.on(k.slice(2), function(e){
                  // Probably there's a better way to call a callback function with right context, $.proxy() ?
                  cb.apply(this,[e]);
                });
            return this;
        };

        window.onload = function() {
            (function() {

                $('.our-games-block .hamburger').click(function() {
                    $(this).toggleClass('is-active');
                    $('.our-games-block .tab-wrap').toggle();
                });

                function checkTabs() {
                    $('.our-games-block .tab-wrap').show();
                    if($('.our-games-block .hamburger').is(':visible')) {
                        if(!$('.our-games-block .hamburger').hasClass('is-active')) {
                            $('.our-games-block .tab-wrap').hide();
                        }
                    }
                }

                checkTabs();
                $(window).resize(checkTabs);

                $('.prod-thumbnail a').click(function() {
                    window.stop();
                    $( 'body' ).addClass( 'preloader-active' );
                    $( '.preloader-cover' ).show();
                });

                var allPic = $('.gamethumb.img-fluid').length;
                var currPic = 0;

                var canShowJp=true;
                if($('.iframe_agt_widget').length) {
                    $('.iframe_agt_widget').hide();
                }

                $('.gamethumb.img-fluid').each(function() {
                    var self = $(this);
                    var $img = $(this).find('img');
                    var $src = $img.attr('data-src');
                    $img.one("load", function() {
                        self.removeClass('agtloadpic');
                        currPic++;

                        if(currPic>=allPic) {

                            //JP INIT
                            if(canShowJp && $('.iframe_agt_widget').length) {
                                canShowJp=false;

                                let jpthumb=$('.filtergame:visible').eq(0).clone();
								jpthumb.addClass('jpwidget');
				jpthumb.removeClass('filtergame');
                                jpthumb.html('');
                                jpthumb.append($('.iframe_agt_widget'));
                                $('.filtergame:visible').eq(1).after(jpthumb);
                                $('.iframe_agt_widget').show();

                                $('.iframe_agt_widget').on("load", function() {
                                    $('.gamethumb.img-fluid').each(function() {
                                        var $srcInner = $(this).find('img').attr('data-src');
                                        var s = '<source type="image/webp" srcset="'+$srcInner.replace('.png','.webp')+'">';
                                        $(this).prepend(s);
                                    });
                                });
                            }
                            else if(!$('.iframe_agt_widget').length) {

                                $('.gamethumb.img-fluid').each(function() {
                                    var $srcInner = $(this).find('img').attr('data-src');
                                    var s = '<source type="image/webp" srcset="'+$srcInner.replace('.png','.webp')+'">';
                                    $(this).prepend(s);
                                });
                            }
                        }
                    });
                    $img.attr('src',$src);
                });

                $('#filter-games li').not('.search').click(function() {
                    var s = $(this).data('optionValue');
                    $('.filtergame').not('.jpwidget').hide();
                    $('.filtergame'+s).show();
					$('.filtergame:visible').eq(1).after($('.jpwidget'));
                });

                $('#submit-search-games').click(function(e) {
                    e.preventDefault();
                    $('#submit-search-form').keyup();
                });

                function searchGames(val) {
                    
					$('.filtergame').not('.jpwidget').hide();
					 
					if(val.length) {
                        $('.filtergame:Contains(\''+val+'\')').show();
                    }
                    else {
                        $('.filtergame').not('.branded').show();
						$('.jpwidget').show();
                    }
                }

                $('#submit-search-form input').onAny(function(e) {
                    var val = $(this).val();
                    searchGames(val);
                });

                $('#submit-search-form').keyup(function() {
                    var $this = $(this).find('input');
                    var val = $this.val();

                    searchGames(val);
                });

            })(document,window);
        }
    </script>
	
	<div class="agepopup-overlay" style="opacity: 1;"></div>
	<div class="agepopup-inner">
<!--		<img src="/games/agt/images/games/megahot100/icons/icon0.png" alt="">-->
		<img src="/theme/interactive1/img/logo.png" width="145px" alt="" class="img-fluid">
		<p>Please confirm that you are <span class="age">18</span> or older before entering this site: </p>
		<p><button id="ageconfirm" class="yes">Yes, I'm over 18</button><button id="agenotconfirm" class="no">No, I'm not</button></p>
	</div>
	<style>
		.agepopup-overlay {
			background: rgba(0,0,0,0.5)!important;
			position: fixed;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 99998;
		}
		.agepopup-inner:before {
			content: "";
			background: url(/games/agt/images/games/megahot100/icons/icon0.png) no-repeat;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			position: absolute;
			opacity: 0.15;
		}
		.agepopup-inner {
			padding: 40px 10px;
			top: 50%!important;
			transform: translateY(-50%);
			max-height: 500px;
			width: 420px;
			position: fixed;
			border-radius: 25px;
			color: white;
			z-index: 99999;
			background: rgba(0,0,0,0.9);
			left: calc(50% - 210px);
		}
		.agepopup-inner img {
			display: block;
			margin: 0 auto 30px auto;
			position: relative;
		}

		.agepopup-inner p {
			width: 100%;
			text-align: center;
			position: relative;
			margin-bottom: 20px;
		}
		.agepopup-inner .age {
			color: rgb(148 247 42 / 80%);
		}
		.agepopup-inner button {
			display: inline-grid;
			width: 45%;
			max-width: 330px;
			padding: 12px;
			text-align: center;
			font-size: 12px;
			font-weight: 800;
			border-radius: 32px;
			cursor: pointer;
			background: rgba(200,200,200,0.5);
			font-family: sans-serif;
		}
		.agepopup-inner button:first-child {
			background: rgb(148 247 42 / 80%);
		}
	</style>
	<script>

		try {
			LS16 = localStorage;
		}
		catch(e) {
			LS16 = {};
		}

		function clearAgeVerification() {
			document.getElementsByClassName('agepopup-overlay')[0].remove();
			document.getElementsByClassName('agepopup-inner')[0].remove();
		}


		if(LS16.ZVQFY) {
			clearAgeVerification();
		}
		else {
			let btnAgeYes=document.getElementById('ageconfirm');
			btnAgeYes.addEventListener('click',function() {
				LS16.ZVQFY=Date.now();
				clearAgeVerification();
			});
			let btnAgeNo=document.getElementById('agenotconfirm');
			btnAgeNo.addEventListener('click',function() {
				window.location='https://google.com';
			});
		}


	</script>
</body>
</html>
