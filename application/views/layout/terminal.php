<!DOCTYPE html>
<html>
	<head>

		<!-- Basic -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title>TERMINAL </title>

		<meta name="keywords" content="GAMING TECHNOLOGY" />
		<meta name="description" content="AGT ">

		<link rel="shortcut icon" href="/theme/interactive1/img/faviconempty.png">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">

		<!-- Web Fonts  -->
		<!--<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">-->
		<link href="/interactivetheme/css/fonts/google-fonts/stylesheet.css" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="/interactivetheme/vendor/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/fontawesome-free/css/all.min.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/animate/animate.min.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/simple-line-icons/css/simple-line-icons.min.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/owl.carousel/assets/owl.carousel.min.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/owl.carousel/assets/owl.theme.default.min.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/magnific-popup/magnific-popup.min.css">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="/interactivetheme/css/theme.css">
		<link rel="stylesheet" href="/interactivetheme/css/theme-elements.css">
		<link rel="stylesheet" href="/interactivetheme/css/theme-blog.css">
		<link rel="stylesheet" href="/interactivetheme/css/theme-shop.css">

		<!-- Current Page CSS -->
		<link rel="stylesheet" href="/interactivetheme/vendor/rs-plugin/css/settings.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/rs-plugin/css/layers.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/rs-plugin/css/navigation.css">
		<link rel="stylesheet" href="/interactivetheme/vendor/circle-flip-slideshow/css/component.css">

		<!-- Demo CSS -->


		<!-- Skin CSS -->
		<link rel="stylesheet" href="/interactivetheme/css/skins/default.css">

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="/interactivetheme/css/custom.css">

		<!-- Head Libs -->
		<script src="/interactivetheme/vendor/modernizr/modernizr.min.js"></script>
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
            .ajax-loader .ajax-loader-letters {
                color: #fc0100;
                font-size: 40px;
                -webkit-text-stroke: 1px #fff;
                text-stroke: 1px #fff;
            }
        </style>


	</head>
	<body id="home" class="home preloader-active">
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

		<div class="body">
            <style>
                #header {
                     z-index: 100001;
                }
                .mfp-zoom-out-cur #header {
                     z-index: 0;
                }
            </style>
			<div role="main" class="main">
				<?php echo $content ?>
			</div>
			<footer id="footer" class="mt-0 border-0 bg-color-light">
				<div class="footer-copyright bg-color-light">
					<div class="container py-2">
						<div class="row py-4">
							<div class="col d-flex align-items-center justify-content-center">
								<p>
                                                                    <img src="/interactivetheme/img/logo-30.png"/>
                                                                    <?php if (date('Y')==2019):  ?>
                                                                    <strong></strong> - Copyright 2019. All Rights Reserved.</p>
                                                                    <?php else:  ?>
                                                                    <strong></strong> - Copyright 2019 - <?php echo date('Y')?>. All Rights Reserved.</p>
                                                                    <?php endif ?>
							</div>
						</div>
					</div>
				</div>
			</footer>
		</div>

		<!-- Vendor -->
		<script src="/interactivetheme/vendor/jquery/jquery.min.js"></script>
		<script src="/interactivetheme/vendor/jquery.appear/jquery.appear.min.js"></script>
		<script src="/interactivetheme/vendor/jquery.easing/jquery.easing.min.js"></script>
		<!--<script src="/interactivetheme/vendor/popper/umd/popper.min.js"></script>-->
		<script src="/interactivetheme/vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="/interactivetheme/vendor/common/common.min.js"></script>
        <script src="/interactivetheme/vendor/jquery.validation/jquery.validate.min.js"></script>
		<!--<script src="/interactivetheme/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>-->
		<!--<script src="/interactivetheme/vendor/jquery.gmap/jquery.gmap.min.js"></script>-->
		<!--<script src="//afarkas.github.io/lazysizes/lazysizes.min.js"></script>-->
		<!--<script src="/interactivetheme/vendor/jquery.lazyload/jquery.lazyload.min.js"></script>-->
		<!--<script src="http://afarkas.github.io/lazysizes/lazysizes.min.js"></script>-->
		<script src="/interactivetheme/vendor/isotope/jquery.isotope.min.js"></script>
		<script src="/interactivetheme/vendor/owl.carousel/owl.carousel.min.js"></script>
		<script src="/interactivetheme/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
<!--		<script src="/interactivetheme/vendor/vide/jquery.vide.min.js"></script>
		<script src="/interactivetheme/vendor/vivus/vivus.min.js"></script>-->

		<!-- Theme Base, Components and Settings -->
		<script src="/interactivetheme/js/theme.js"></script>


		<!-- Theme Custom -->
		<script src="/interactivetheme/js/custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="/interactivetheme/js/theme.init.js"></script>

        <script src="/interactivetheme/js/views/view.contact.js"></script>
        <script src="/interactivetheme/js/views/view.user.js"></script>


        <script>
//            window.onerror = function(msg, url, line) {
//                alert("Message : " + msg );
//                alert("url : " + url );
//                alert("Line number : " + line );
//            }
            window.onload = function() {
                $('.preloader-cover').remove();
            }
        </script>

	</body>
</html>