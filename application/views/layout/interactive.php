<!DOCTYPE html>
<html>
	<head>

		<!-- Basic -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<title>AGT </title>

		<meta name="keywords" content="GAMING TECHNOLOGY" />
		<meta name="description" content="AGT ">



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




	</head>
	<body class="loading-overlay-showing" data-plugin-page-transition data-loading-overlay data-plugin-options="{'hideDelay': 500}">
		<div class="loading-overlay">
			<div class="bounce-loader">
				<div class="bounce1"></div>
				<div class="bounce2"></div>
				<div class="bounce3"></div>
			</div>
		</div>

		<div class="body">
			<header id="header" style="z-index: 100001;" class="header-transparent header-transparent-not-fixed header-no-min-height" data-plugin-options="{'stickyEnabled': true, 'stickyEnableOnBoxed': true, 'stickyEnableOnMobile': true, 'stickyStartAt': 52, 'stickySetTop': '0'}">
				<div class="header-body border-top-0 header-body-bottom-border">

					<div class="header-container container container-lg">
						<div class="header-row">
							<div class="header-column">
								<div class="header-row">
									<div class="header-logo">
										<a href="/interactive">
											<img alt="AGTLogo" width="134" src="/interactivetheme/img/logo.png">
                                                                                        <strong></strong>
										</a>
									</div>
								</div>
							</div>
							<div class="header-column justify-content-end">
								<div class="header-row">
									<div class="header-nav header-nav-line header-nav-top-line header-nav-top-line-with-border order-2 order-lg-1">
										<div class="header-nav-main header-nav-main-square header-nav-main-effect-2 header-nav-main-sub-effect-1">
											<?php if(!DEMO_DOMAIN): ?>
                                                <div class="login-interactive-form" style="height: 53px;margin-top: 23px;">
                                                <?php if(empty(auth::$user_id)): ?>
                                                    <form method="post" style="" action="/login/login">
                                                        <label>
                                                            <input style="margin: 0 auto;" type="text" name="login" autocomplete="off" placeholder="<?php echo __('Логин')?>" />
                                                        </label>
                                                        <label>
                                                            <input style="margin: 0 auto;" type="password" name="password" value="" placeholder="<?php echo __('Пароль')?>"/>
                                                        </label>
                                                        <input style="margin: 0 auto; " type="submit" class="button color submit_btn" value="<?php echo __('Войти'); ?>" name="Submit">
                                                    </form>
                                                <?php else: ?>
                                                    User ID: <?php echo auth::$user_id; ?>
                                                    <span>Balance: <?php echo auth::user()->amount(); ?></span>
                                                <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
											<nav class="collapse">
												<ul class="nav nav-pills" id="mainNav">
													<li class="dropdown ">
                                                                                                                <a class="<?php if ($active=='index') {echo "active";} ?>"
                                                                                                                   style=" font-size:16px"
                                                                                                                   href="/interactive">
															Home
														</a>

													</li>
													<li class="dropdown">
														<a class="<?php if ($active=='demo'){echo "active";} ?>" style=" font-size:16px" href="/interactive/demo">
															Video demo
														</a>

													</li>
													<li class="dropdown">
														<a class="" style=" font-size:16px" href="#">
															API
														</a>

													</li>
													<li class="dropdown">
														<a class="<?php if ($active=='contacts'){echo "active";} ?>" style=" font-size:16px" href="/interactive/contacts">
															Contacts
														</a>

													</li>


												</ul>
											</nav>
										</div>
										<button class="btn header-btn-collapse-nav" data-toggle="collapse" data-target=".header-nav-main nav">
											<i class="fas fa-bars"></i>
										</button>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</header>

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