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

<body id="home" class="home preloader-active" style="background-image: url(/theme/interactive1/img/bg-body.svg);">
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
					<div class="ajax-loader-letters">AGT</div>
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
				<div class="col-12 col-sm-6 col-lg-3 logo-item">
                    <a href="/" class="logo"><img width="125px" src="/theme/interactive1/img/logo.png" alt="logo"></a>
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
								<a href="/interactive1/news">News</a>
							</li>

							<li><a href="/interactive1#games">games</a></li>
							
							<li><a href="/interactive1#contacts">Contacts</a></li>
						</ul>
					</nav>
					<!--=========== NAV MENU END ===========-->
				</div>
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
						<ul class="footer-menu">
                            <li>
								<a href="/">Home</a>
							</li>
							<li>
								<a href="/interactive1/news">News</a>
							</li>

							<li><a href="/interactive1#games">games</a></li>
							<li><a href="https://admin.site-domain.com">Back office</a></li>
							<li><a href="/interactive1#contacts">Contacts</a></li>
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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<link rel="stylesheet" href="/theme/interactive1/css/style.css">

    <style>
        .ajax-loader .ajax-loader-letters {
            color: #fc0100;
            font-size: 40px;
            -webkit-text-stroke: 1px #fff;
            text-stroke: 1px #fff;
        }
        .to-top img {
            width: 25px;
        }

        .s-blog {
            padding: 56px 0 60px;
        }

        @media (max-width: 767px) {
            .s-blog {
                padding: 36px 0 36px;
            }
        }

        .nav-btn:hover span:nth-child(2) {
            margin-left: 0;
        }
    </style>

	<!--=============== TEMPLATE SCRIPT ===============-->
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
	<script src="/theme/interactive1/js/slick.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js"></script>
	<script src="/theme/interactive1/js/scripts.js"></script>

    <script>
        window.onload = function() {
            (function() {
                $('.gamethumb').each(function() {
                    var $src = $(this).find('img').attr('src');
                    var s = '<source type="image/webp" srcset="'+$src.replace('.png','.webp')+'">';
                    $(this).prepend(s);
                });
            })(document,window);
        }
    </script>
</body>
</html>
