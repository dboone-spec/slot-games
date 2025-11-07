<section class="page-header page-header-modern bg-color-light-scale-1 page-header-md" style="padding-bottom: 0px; margin-bottom: 0px; margin-top: 0px; padding-top: 100px;">
					<div class="container">
                                            
						<div class="row">

							<div class="col-md-12 align-self-center p-static order-2 text-center">

								<h1 class="text-dark font-weight-bold text-8"><?php echo $game->visible_name ?></h1>

							</div>

							
						</div>
					</div>
				</section>

<div class="container py-4">
            <div class="row">
                    <div class="col">
                            <div class="blog-posts single-post">

                                    <article class="post post-large blog-single-post border-0 m-0 p-0">

                                            <div class="post-image ml-0">
                                                    <div >
                                                            <div class="row mx-0">
                                                                    <div class="col-6  p-0">
                                                                            <a href="/games/<?php echo $game->brand ?? 'agt'; ?>/<?php echo $game->name ?>">
                                                                                    <span class="thumb-info thumb-info-no-borders thumb-info-centered-icons">
                                                                                            <span class="thumb-info-wrapper">
                                                                                                    <img src="<?php echo $game->image ?>" class="img-fluid" alt="">
                                                                                                    
                                                                                            </span>
                                                                                    </span>
                                                                            </a>
																			
																			<?php if ($img): ?>
																					  

																				
																					<div class="lightbox" data-plugin-options="{'delegate': 'a', 'type': 'image', 'gallery': {'enabled': true}, 'mainClass': 'mfp-with-zoom', 'zoom': {'enabled': true, 'duration': 300}}">
																						<div class="owl-carousel owl-theme  show-nav-hover" data-plugin-options="{'items': 3, 'margin': 10, 'loop': true, 'nav': true, 'dots': false}">
																							
																																	<?php foreach ($img as $image): ?>
																																		<div>
																								<a class="img-thumbnail img-thumbnail-no-borders img-thumbnail-hover-icon" href="/games/agt/screen/<?php echo $game->name.'/'.$image ?>">
																									<img class="img-fluid" src="/games/agt/screen/<?php echo $game->name ?>/small/<?php echo $image ?>" alt="Project Image">
																								</a>
																							</div>
																																	<?php endforeach; ?>
																						</div>
																					</div>
																				
																						
																					<?php endif ?>
																			
																			
                                                                            
                                                                    </div>
                                                                    <div class="col-6  p-0">
                                                                          <table class="table table-striped" style="margin-left: 15px;">

										<tbody>
											<tr>
												<td>
													Game name
												</td>
												<td>
													<?php echo $game->visible_name ?>
												</td>

											</tr>
                                                                                        <?php if ($txtConf) :?>
											<tr>
												<td>
													Configuration
												</td>
												<td>
													<?php echo $txtConf ?> 
												</td>

											</tr>
                                                                                        <?php endif ?>
											<tr>
												<td>
													Supported Platform
												</td>
												<td>
                                                                                                    <img src="/theme/interactive/images/res/comp.jpg" />
                                                                                                    <img src="/theme/interactive/images/res/note.jpg" />
                                                                                                    <img src="/theme/interactive/images/res/phone.jpg" /> 
                                                                                                    <img src="/theme/interactive/images/res/phoneg.jpg" /> 
                                                                                                    <img src="/theme/interactive/images/res/pl.jpg" /> 
                                                                                                    <img src="/theme/interactive/images/res/plg.jpg" /> 
                                                                                                    

											</tr>
                                                                                        <tr>
												<td>
													Recommended browser
												</td>
												<td>
                                                                                                    <img src="/theme/interactive/images/browser.png" />
                                                                                                    
												</td>

											</tr>
                                                                                        <tr>
												<td>
													Supported OS
												</td>
												<td>
												 <img src="/theme/interactive/images/os.png" />
                                                                                                 

												</td>
                                                                                                
											</tr>
                                                                                        <tr>
												<td>
													Vertical mobile version
												</td>
												<td>
                                                                                                    Yes
                                                                                                </td>
                                                                                                
											</tr>
                                                                                        <tr>
												<td>
													Horizontal mobile version
												</td>
												<td>
                                                                                                    Yes
												</td>
                                                                                                
											</tr>
                                                                                       
                                                                                        <tr>
												<td>
													Game resolution
												</td>
												<td>
                                                                                                    16:9
												</td>
                                                                                                
											</tr>

										</tbody>
									</table>
                                                                    </div>
                                                                   
                                                            </div>
                                                    </div>
                                            </div>
                                    

                                            <div class="post-content ml-0">

                                         

                                                <div class="post-meta" style="font-size:16px">
            

                                                    <?php echo $game->text ?>
 
                                                    <?php if(!empty($game->demo)) :?>
                                                    <iframe width="100%" height="660"  src="<?php echo $game->embedDemo() ?>" frameborder="0" allow="accelerometer;  encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                    <?php endif; ?>

                                                     </div>
                                               
                                    </article>

                            </div>
                    </div>
            </div>
                
                
           <div class="row">
                <div class="col">
                        <h4>Other games</h4>
                        <div class="owl-carousel owl-theme show-nav-hover" data-plugin-options="{'items': 4, 'margin': 10, 'loop': true, 'nav': true, 'dots': false}">
                                <?php foreach ($other as $o): ?>
                                    <div>
                                        <a href="/interactive/info/<?php echo $o['name']?>"> <img alt="" class="img-fluid rounded" src="<?php echo $o['image'] ?>"> </a>
                                    </div>
                                <?php endforeach; ?>
                                
                        </div>
                </div>
        </div>

    </div>