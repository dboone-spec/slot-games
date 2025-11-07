<style>
    .play-info{
        -webkit-transition: all 0.3s;
        transition: all 0.3s;
        color: #FFF;
        font-weight: 600;
        left: 0;
        position: absolute;
        z-index: 2;
        font-size: 17px;
        bottom: 0%;
    }
</style>


<section class="section my-0">

				</section>
				<section class="section pt-0 my-0 pb-0 min-height-screen border-0 bg-color-dark-scale-3" id="demos">
					<div class="container-fluid">
						<div class="row justify-content-center py-4 py-sm-0 bg-color-dark-scale-2">
							<div class="col-auto col-sm-12 col-md-auto">
								<ul class="nav nav-light nav-active-style-1 sort-source justify-content-center flex-column flex-sm-row" data-sort-id="portfolio" data-option-key="filter">
									<li class="nav-item" data-option-value="*"><a class="nav-link font-weight-semibold text-2 active" href="#">ALL</a></li>
									<li class="nav-item" data-option-value=".classic"><a class="nav-link font-weight-semibold text-2" href="#">Classic</a></li>
									<li class="nav-item" data-option-value=".hot"><a class="nav-link font-weight-semibold text-2" href="#">Hot</a></li>

								</ul>
							</div>
						</div>
						<div class="row min-height-screen">
							<div class="col min-height-screen">
								<div class="sort-destination-loader min-height-screen mt-5 pt-2 px-4">
									<div class="row portfolio-list sort-destination overflow-visible" data-sort-id="portfolio">


                                                                                <?php foreach ($games as $game): if (!empty($game['demo'])):?>
										<div class="col-12 col-sm-6 col-lg-4 col-xl-3 isotope-item <?php echo $game['category']?> px-4">
											<div class="appear-animation" data-appear-animation="fadeInUp" data-plugin-options="{'accY': -150}" data-appear-animation-delay="">
												<div class="portfolio-item hover-effect-1">
													<a class="popup-youtube" href="<?php echo $game['demo']?>">
														<span class="thumb-info thumb-info-no-zoom thumb-info-no-overlay thumb-info-no-bg border-0 border-radius-0">
															<span class="thumb-info-wrapper thumb-info-wrapper-demos m-0 border-radius-0">
																<picture class="img-fluid border-radius-0" alt="">
                                                                    
                                                                    <source type="image/png" srcset="<?php echo $game['image']; ?>">
                                                                    <img src="<?php echo $game['image']; ?>" style="width: 100%">
                                                                </picture>
                                                                                                                            <span class="play-info">
												<img src="/interactivetheme/img/play.png"

											</span>
															</span>
														</span>
													</a>
													<h2 class="font-weight-semibold text-3 text-center"><a href="index-classic.html" class="text-color-light text-decoration-none text-1 text-uppercase"><?php echo $game['visible_name'] ?></a></h2>
												</div>
											</div>
										</div>
                                                                                <?php endif; endforeach ; ?>



									</div>
								</div>
							</div>
						</div>

					</div>
				</section>
