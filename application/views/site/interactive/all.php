
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


                                                                                <?php foreach ($games as $game):?>
										<div class="col-12 col-sm-6 col-lg-4 col-xl-3 isotope-item <?php echo $game['category']?> px-4">
											<div class="appear-animation" data-appear-animation="fadeInUp" data-plugin-options="{'accY': -150}" data-appear-animation-delay="">
												<div class="portfolio-item hover-effect-1">
													<a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; echo in_array($game['brand'],['agt','egt']) ? '' : '/'; ?>">
														<span class="thumb-info thumb-info-no-zoom thumb-info-no-overlay thumb-info-no-bg border-0 border-radius-0">
															<span class="thumb-info-wrapper thumb-info-wrapper-demos m-0 border-radius-0">
																<picture class="img-fluid border-radius-0" alt="">
                                                                                                                                   
                                                                                                                                    <source type="image/png" srcset="<?php echo $game['image']; ?>">
                                                                                                                                    <img src="<?php echo $game['image']; ?>" style="width: <?php echo ($game['brand']=='egt') ? 100 : 100; ?>%">
                                                                                                                                </picture>
															</span>
														</span>
													</a>
                                                                                                    <div style="float:left; min-width:15px">&nbsp;</div>
                                                                                                    <div style="float:left;">
                                                                                                        <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; echo in_array($game['brand'],['agt','egt']) ? '' : '/';?>" class="text-color-light text-decoration-none text-1 text-uppercase"><?php echo $game['visible_name'] ?></a>
                                                                                                    </div>
                                                                                                    <?php if (!empty($game['demo'])): ?>
                                                                                                        <div style="float:right; min-width:15px">&nbsp;</div>
                                                                                                        <div style="float:right">
                                                                                                         <a style="font-weight: Bold; text-decoration:underline" class="popup-youtube" href="<?php echo $game['demo']?>"> Video demo</a>
                                                                                                        </div>
                                                                                                    <?php endif ?>
                                                                                                    <div style="clear:both"></div>

												</div>
											</div>
										</div>
                                                                                <?php endforeach ; ?>



									</div>
								</div>
							</div>
						</div>

					</div>
				</section>

