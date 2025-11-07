<!--=============== MAIN SLIDER ===============-->
		<section class="main-slider-cover">
			<div class="main-slider">
				<div class="main-slider-for">
					<div class="main-slide-for">
                        <div class="container">
                            <img class="bglepr" src="/theme/interactive1/img/home1-slide10.jpg" alt="">
                            <picture class="leprechaun-main" alt="">
                                <source type="image/webp" srcset="/theme/interactive1/img/leprechaun.webp">
                                <img src="/theme/interactive1/img/leprechaun.png" />
                            </picture>
                        </div>
						<div class="container">
<!--							<div class="slide-info">
                                <h3 style="margin-bottom: 0px;">Watch our presentation</h3>
                                <small><a style="margin: 0;text-transform: inherit;" target="_blank" href="/files/agt_en.pdf">AGT presentation</a></small>
								<p>AGT is beautiful!</p>
								<a href="/files/prezentation.pdf">Watch AGT prezentation</a>
                                <br />
                                <a class="btn" target="_blank" href="/files/agt_en.pdf">Download</a>
							</div>-->
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--=============== MAIN SLIDER END ===============-->



<section class="s-blog our-games-block" id="games">
        <div class="container s-anim">
                <h2>Our games
<!--                    <button class="hamburger hamburger--press" type="button">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>-->
                </h2>
                <!--<p class="slogan">Select the category here</p>-->
                <div class="tab-wrap">
                    <ul class="tab-nav gallery-tabs header-icon" id="filter-games">
                        <li class="item" data-option-value="*">ALL</li>
                        <li class="item" data-option-value=".classic">Classic</li>
                        <li class="item" data-option-value=".hot">Hot</li>
                        <li class="item" data-option-value=".tables">Table</li>
						<li class="item" data-option-value=".arcade">Arcade</li>
                        <li class="search">
							<a class="icon" href="#">
								<i class="fa fa-search" aria-hidden="true"></i>
							</a>
							<form id="submit-search-form" action="#">
								<input type="search" name="search" placeholder="Search">
								<button id="submit-search-games" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
							</form>
						</li>
                    </ul>
                </div>
                <div class="row no-gutters">

                        <!--=============== POST-ITEM ===============-->


                         <?php foreach($games as $game): ?>
                            <div class="filtergame gamethumb col-<?php echo th::isMobile() ? '3' : '3'; ?> col-md-2 <?php echo $game['category']; ?>" >
                                <div class="prod-thumbnail" >
                                        <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>" >
                                            <picture class="gamethumb img-fluid border-radius-0 agtloadpic" alt="<?php echo $game['visible_name'] ?>">
                                                <!--<source type="image/webp" srcset="<?php echo UTF8::str_ireplace('.png','.webp',$game['image']); ?>">-->
                                                <img data-src="<?php echo $game['image']; ?>" style="width: 100%" />
                                            </picture>
                                        </a>
                                </div>
                                <div class="post-content" >
                                        <div class="meta top" >
                                            <span class="post-by" >

                                                <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>" ><?php echo $game['visible_name'] ?></a>

                                            </span>
                                            <span class="pull-right" >
                                                <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>" >Play demo</a>
                                            </span>

                                        </div>

                                </div>
                        </div>
                        <?php endforeach; ?>
                        <!--============= POST-ITEM END =============-->

                </div>
        </div>
</section>
<!--==================== S-BLOG ====================-->
		<section class="s-blog">
			<div class="container s-anim">
				<h2>latest news</h2>
				<!--<p class="slogan">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmmpor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>-->
				<div class="row">
                    <?php foreach($news as $new): ?>
					<!--=============== POST-ITEM ===============-->
					<div class="col-12 col-md-4 post-item">
						<div class="prod-thumbnail">
							<a href="/interactive/news">
                                <img class="lazy" src="/theme/interactive1/img/placeholder-all.png" data-src="/theme/interactive1/news/<?php echo $new->id; ?>.jpg" alt="img">
                            </a>
						</div>
						<div class="post-content">
							<div class="meta top">
								<!--<span class="post-by">By <a href="#">Sam Filips</a>  /</span>-->
								<!--<span class="post-date"><?php echo date('M d, Y',$new->created); ?></span>-->
							</div>
							<h5 class="title"><?php echo $new->title; ?></h5>
                            <p><?php echo $new->text ?></p>
<!--							<div class="meta bottom">
								<span class="post-comments">
									<a href="#">Comments 2</a>
								</span>
								<span class="post-tags">Tags: <a href="#">Lorem, </a><a href="#">Drones</a></span>
							</div>-->
							<!--<a href="blog.html" class="btn">more</a>-->
						</div>
					</div>
					<!--============= POST-ITEM END =============-->
                    <?php endforeach; ?>
				</div>
			</div>
            <a  name="games"></a>
		</section>
		<!--================== S-BLOG END ==================-->
<!--=================== S-CONTACTS ===================-->
<section class="s-blog" id="aboutus">
        <div class="container s-anim">
                <h2>About us</h2>
                <div class="slider-testimonials">
                        <div class="slide-testimonial">
                                <div class="testimonial-item">
                                        <img class="lazy" src="/theme/interactive1/img/ppl/alexsei.png" data-src="/theme/interactive1/img/ppl/alexsei.png" alt="img">
                                        <h5 class="name">Alex Grigin</h5>
                                        <div class="prof">CEO</div>
                                        <div class="testimon-content">Glad to see you on the site. <br /> We are ready to consider and discuss options for business.
                                            <br />
                                            <br />
                                        </div>
                                </div>
                        </div>
                        <div class="slide-testimonial">
                                <div class="testimonial-item">
                                        <img class="lazy" src="/theme/interactive1/img/ppl/ivan.png" data-src="/theme/interactive1/img/ppl/ivan.png" alt="img">
                                        <h5 class="name">Ivan Shiryaev</h5>
                                        <div class="prof">Head of Events & Exhibitions Department site-domain</div>
                                        <div class="testimon-content">Successful, more than 15 years of experience in implementing projects and events, including organizing exhibitions in the field of gambling.</div>
                                </div>
                        </div>

                </div>
                <br />
                <div class="row">
                    <div class="col-12 col-md-6 post-item" style="margin:0 auto;text-align: center;">
                    <!--site-domain was founded in 2020 by professionals with 15 years experience in the gambling industry.--> 
                    site-domain creates products that meet the highest modern software requirements and is focused on meeting the needs and wishes of all participants in the gambling process. site-domain can offer unique marketing tools and solutions for all partners in today's gambling industry.
                    <h6 style="margin:15px auto;">OUR ADVANTAGES</h6>
                    <div class="row" style=" text-align: left;">
                    <ul class="col-6 col-md-6" style="font-size: smaller; ">
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> 80+ modern games with unique mathematics and gamification elements</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Accrual of FS through API</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Unique Daily Spins system</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Restoring game with bonus mode if there was a trouble with connection or device</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Exclusive jackpot based on poker game</li>
                        <!--<li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> White label</li>-->
                    </ul>
                    <ul class="col-6 col-md-6" style="font-size: smaller; ">
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Unique design and great graphics</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Excitable maths</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Support multiple platforms</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Horizontal and vertical mobile UI</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Full HD for all devices</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Fast game loading</li>
                        <li><img style="height: 15px" src="/theme/interactive1/img/red0.png" /> Language and denomination customization</li>
                    </ul>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 post-item" style="margin:0 auto;text-align: center;margin-top: 15px;">
                        Contact our managers and get a commercial offer
                        <br />
                        <!--<a class="btn" href="#contacts">Contact</a>-->
                    </div>
                </div>
        </div>
</section>

<!--=================== S-CONTACTS ===================-->
<section class="s-contacts s-main-contacts" id="contacts">
        <div class="container s-anim">
                <h2>Contact us</h2>

                <ul class="about-cont" style="margin: 22px auto; max-width: 1170px;" >
						<li style="width:100%;" >
							<a href="mailto:info@site-domain.com" >
								<i class="fa fa-envelope" aria-hidden="true"></i>
								<span >info@site-domain.com</span>
							</a>
						</li>

                        <li style="width:100%;" >
                            <i class="fa fa-home" aria-hidden="true"></i>
                            <span > Lizuma iela 1 k-11, LV-1006, Riga, Latvia</span>
						</li>
					</ul>
                <br />
                <!--<p class="slogan">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmmpor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>-->
                <form id='contactform' action="/interactive/send" name="contactform">
                        <ul class="form-cover">
                                <li class="inp-name"><input id="name" type="text" name="name" placeholder="Name"></li>
                                <li class="inp-phone"><input id="subject" type="text" name="subject" placeholder="Phone"></li>
                                <li class="inp-email"><input id="email" type="email" name="email" placeholder="E-mail"></li>
                                <li class="inp-text"><textarea id="comments" name="message" placeholder="Message"></textarea></li>
                        </ul>

                        <div class="btn-form-cover">
                                <button id="submit-comment" type="submit" class="btn">submit</button>
                        </div>
                </form>
                <div id="message"></div>
        </div>
</section>
<!--================= S-CONTACTS END =================-->



		<!--=============== S-CLIENTS ===============-->
		<?php /*
		<div class="s-clients s-clients-home">
			<div class="container">
				<div class="row align-items-center clients-cover">

                                        <div class="col-2 col-sm-2">
                                        </div>

					<div class="col-2 col-sm-2">
						<img class="lazy" src="/theme/interactive1/img/placeholder-all.png" data-src="/theme/interactive1/img/logos/supabets.png" alt="img">
					</div>
                                        <div class="col-2 col-sm-2">
						<img class="lazy" src="/theme/interactive1/img/placeholder-all.png" data-src="/theme/interactive1/img/logos/bmm.png" alt="img">
					</div>
                                        <div class="col-2 col-sm-2">
						<img class="lazy" src="/theme/interactive1/img/placeholder-all.png" data-src="/theme/interactive1/img/logos/tri.png" alt="img">
					</div>
					<div class="col-2 col-sm-2">
						<img class="lazy" src="/theme/interactive1/img/placeholder-all.png" data-src="/theme/interactive1/img/logos/aadvark.png" alt="img">
					</div>


                                        <div class="col-2 col-sm-2">
                                        </div>
				</div>
			</div>
		</div>
		*/ ?>
		<!--============= S-CLIENTS END =============-->

        <style>
            <?php if(th::isMobile()): ?>
                .leprechaun-main {
                    width: 580px;
                    position: absolute;
                    display: block;
                    right: 15%;
                    zoom: 0.44;
                    top: 0;
                }
            <?php else: ?>
                .leprechaun-main {
                    width: 580px;
                    position: absolute;
                    display: block;
                    left: 0;
                    zoom: 0.6;
                    top:15%;
                    -moz-transform: scale(-1, 1);
                    -webkit-transform: scale(-1, 1);
                    -o-transform: scale(-1, 1);
                    -ms-transform: scale(-1, 1);
                    transform: scale(-1, 1);
                }
            <?php endif; ?>
            .gamethumb .post-content {
                display: none;
            }
            .post-content {
                text-align: justify;
            }
            .post-content .title{
                text-align: center;
            }
            #filter-games li {
                cursor: pointer;
            }
            #filter-games .item:before {
                top: 100%;
            }
            #filter-games li.search.active form {
                z-index: 2;
                top: -8px;
                margin-top: 0;
                right: 0;
                width: 423px;
                flex-wrap: nowrap;
            }
            #filter-games li.search.active form input {
                width: 377px;
            }
            #filter-games .search {
                color: #fff;
                font-weight: 700;
                font-size: 20px;
                line-height: 30px;
                text-align: center;
                letter-spacing: 1.4px;
                text-transform: uppercase;
                padding: 0 14px;
                transition: .3s ease;
                position: relative;
                border-bottom: none;
            }
            #submit-search-games {
                width: 45px;
            }

            <?php if(th::isMobile()): ?>

            @media (max-width: 767px) {
                .leprechaun-main {
                    zoom: 0.44;
                    right: 15%;
                    left: auto;
                }
            }

            <?php endif; ?>

            @media (max-width: 575px) {

                .leprechaun-main {
                    zoom: 0.34;
                    right: 0;
                    left: auto;
                }

                .filtergame .pull-right {
                    display: none;
                }

                .tab-nav.gallery-tabs {
                    display: flex;
                }
                #filter-games li.search.active form {
                    left: auto;
                    top: -8px;
                    right: 0;
                    width: 87vw;
                }
                #filter-games li.search.active input {
                    width: calc(100% - 45px);
                }

/*                #filter-games .search {
                    background: #292929;
                    box-shadow: 0px 0px 24px rgba(0, 0, 0, 0.15);
                    line-height: 38px;
                    width: 100%;
                }*/
                #filter-games .search .icon {
                    font-size: 18px;
                }
                #filter-games li {
                    margin: 0 auto;
                    display: block;
                }
            }
            .agtloadpic picture,.agtloadpic img {
                display: none !important;
            }
            .agtloadpic {
                display: block;
                width: 80px;
                height: 142px;
                margin: 0 auto;
            }
            .agtloadpic:after {
                content: " ";
                display: block;
                width: 64px;
                height: 64px;
                margin: 8px;
                border-radius: 50%;
                border: 6px solid #fff;
                border-color: #fff transparent #fff transparent;
                animation: agtloadpic 1.2s linear infinite;
            }
            @keyframes agtloadpic {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>


        <style>
            .hamburger {
                font: inherit;
                display: inline-block;
                overflow: visible;
                margin: 0;
                padding: 15px;
                cursor: pointer;
                transition-timing-function: linear;
                transition-duration: .15s;
                transition-property: opacity,filter;
                text-transform: none;
                color: inherit;
                border: 0;
                background-color: transparent
            }

            .hamburger.is-active:hover,.hamburger:hover {
                opacity: .7
            }
            .hamburger.is-active .hamburger-inner,.hamburger.is-active .hamburger-inner:after,.hamburger.is-active .hamburger-inner:before {
                background-color: #fff
            }
            .hamburger-box {
                position: relative;
                display: inline-block;
                width: 40px;
                height: 20px
            }

            .hamburger-inner {
                top: 50%;
                display: block;
                margin-top: -2px
            }

            .hamburger-inner,.hamburger-inner:after,.hamburger-inner:before {
                position: absolute;
                width: 40px;
                height: 2px;
                transition-timing-function: ease;
                transition-duration: .15s;
                transition-property: transform;
                border-radius: 4px;
                background-color: #fff;
            }

            .hamburger-inner:after,.hamburger-inner:before {
                display: block;
                content: ""
            }

            .hamburger-inner:before {
                top: -5px;
            }

            .hamburger-inner:after {
                bottom: -5px;
            }

            /*.hamburger--press .hamburger-inner:after,.hamburger--press .hamburger-inner:before {
                transition: bottom .08s ease-out 0s,top .08s ease-out 0s,opacity 0s linear
            }

            .hamburger--press.is-active .hamburger-inner:after,.hamburger--minus.is-active .hamburger-inner:before {
                transition: bottom .08s ease-out,top .08s ease-out,opacity 0s linear .08s;
                opacity: 0
            }*/

            .hamburger--press.is-active .hamburger-inner {
                height: 4px;
            }

            .hamburger--press.is-active .hamburger-inner:before {
                top: -10px;
                height: 4px;
            }

            .hamburger--press.is-active .hamburger-inner:after {
                bottom: -10px;
                height: 4px;
            }

            .hamburger {
                display: none;
            }

            @media (max-width: 575px) {
                .hamburger {
                    display: inline-block;
                }
            }

            .our-games-block {
                margin-top: 100px;
            }

            @media (max-width: 991px) {
                .our-games-block {
                    margin-top: 60px;
                }
            }

            .main-slider-cover {
                top: 0;
            }
            #submit-comment {
                cursor: pointer;
            }
            #submit-comment:before {
                display: none;
            }
        </style>