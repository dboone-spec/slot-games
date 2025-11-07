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


        <style>
            html,body {
                margin: 0;
                height: 100%;
                overflow: hidden;
            }
        </style>

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


			<div role="main" class="main">
				<section class="section pt-0 my-0 pb-0 min-height-screen border-0 bg-color-dark-scale-3" id="demos">
					<div class="container-fluid">

						<div class="row min-height-screen">
							<div class="col min-height-screen">
								<div class="min-height-screen mt-5 pt-2 px-4">
									<div class="row portfolio-list overflow-visible">


                                                                                <?php $i=1; foreach ($games as $game):  ?>
										<div class="col-12 col-sm-6 col-lg-4 col-xl-3 pageitem page<?php echo ceil($i/12) ?> px-4" >

												<div class="portfolio-item  itembox" id="item<?php echo $i ?>" >
													<a id="game<?php echo $i ?>" href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>">
														<span class="thumb-info thumb-info-no-zoom thumb-info-no-overlay thumb-info-no-bg border-0 border-radius-0">
															<span class="thumb-info-wrapper thumb-info-wrapper-demos m-0 border-radius-0">
																<picture class="gamethumb img-fluid border-radius-0" alt="">
                                                                <!--                                                                <source type="image/webp" srcset="<?php echo UTF8::str_ireplace('.png','.webp',$game['image']); ?>">-->
                                                                                                                                    <source type="image/png" srcset="<?php echo $game['image']; ?>">
                                                                                                                                    <img src="<?php echo $game['image']; ?>" style="width: 100%">
                                                                                                                                </picture>
															</span>
														</span>
													</a>
                                                                                                    <div style="float:left; min-width:15px">&nbsp;</div>
                                                                                                    <div style="float:left;">
                                                                                                        <a  href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>" class="text-color-light text-decoration-none text-1 text-uppercase"><?php echo $game['visible_name'] ?></a>
                                                                                                    </div>

                                                                                                    <div style="clear:both"></div>

												</div>

										</div>
                                                                                <?php $i++; endforeach ; ?>



									</div>
								</div>
							</div>
						</div>

				</section>
		</div>

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
<!--		<script src="/interactivetheme/vendor/owl.carousel/owl.carousel.min.js"></script>-->
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

            $(function() {

                var count=19;
                var pages=Math.ceil(count/12);
                var posX=1;
                var posY=1;
                var page=1;
                var pos=posX+(posY-1)*4+(page-1)*12;

                $('.pageitem').hide();
                $('.page'+page).show();
                $('.itembox').removeClass('box-active');
                $('#item'+pos).addClass('box-active');


                function moveRect(e){

                    switch(e.keyCode){

                        case 37:  // если нажата клавиша влево
                            if (posX>1){
                                posX--;
                            }
                            else{
                                if (page>1){
                                    page--;
                                    posX=4;
                                    $('.pageitem').hide();
                                    $('.page'+page).show();
                                }

                            }
                            break;
                        case 38:   // если нажата клавиша вверх
                            if (posY>1){
                                posY--;
                            }
                            break;
                        case 39:   // если нажата клавиша вправо
                            if (posX<4){
                                posX++;
                            }
                            else{
                                if (page<pages){
                                    page++;
                                    posX=1;
                                    $('.pageitem').hide();
                                    $('.page'+page).show();
                                }

                            }
                            break;
                        case 40:   // если нажата клавиша вниз
                            if (posY<3){
                                posY++;
                            }
                            break;

                        case 13:   // если нажата клавиша вниз
                            pos=posX+(posY-1)*4+(page-1)*12;
                            let link=$('#game'+pos).attr('href');
                            window.location.replace(link);
                            break;
                    }

                    pos=posX+(posY-1)*4+(page-1)*12;
                    $('.itembox').removeClass('box-active');
                    $('#item'+pos).addClass('box-active');

                }

                addEventListener("keydown", moveRect);


              });





        </script>

	</body>
</html>