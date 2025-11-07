<section class="section my-0">
    <div class="container py-5 ">
        <div class="row justify-content-center py-4">
            <div class="col-12 col-sm-auto text-center text-sm-left ">
                <span class="d-block font-weight-light negative-ls-1 text-5 ml-2 mb-1 appear-animation" data-appear-animation="fadeInUp"><em class="opacity-8"></em></span>
                <h1 class="font-weight-extra-bold text-color-dark negative-ls-3 text-10 mb-2 appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="200"><em>AGT software</em></h1>

            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-10 text-center">
                <p class="lead lead-2 mb-5 appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="600">
                    The main goal of AGT software company is to create high quality and popular games, each one to become a bestseller of its own.
                    This ideology allows the company to rapidly increase the number of new exciting games.
                    AGT software optimized for operators and players from different countries and supports multiple languages and currencies.
                    Innovative AGT software is focused on the needs of modern consumers and is constantly being updated with the latest trends of online gaming.
                </p>
                <a href="#demos" data-hash data-hash-offset="125" class="btn btn-primary btn-rounded btn-xl font-weight-semibold text-2 px-5 py-3 box-shadow-2 appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="800">VIEW GAMES</a>
            </div>
        </div>
    </div>
</section>
<section class="section pt-0 my-0 pb-0 min-height-screen border-0 bg-color-dark-scale-3" id="demos">
    <div class="container-fluid">
        <div class="row justify-content-center py-4 py-sm-0 bg-color-dark-scale-2">
            <div class="col-auto col-sm-12 col-md-auto">
                <ul class="nav nav-light nav-active-style-1 sort-source justify-content-center flex-column flex-sm-row" data-sort-id="portfolio" data-option-key="filter">
                    <li id="allbtn" class="nav-item" data-option-value=":not(.coming)"><a class="nav-link font-weight-semibold text-2 active" href="#">ALL</a></li>
                    <li class="nav-item" data-option-value=".classic"><a class="nav-link font-weight-semibold text-2" href="#">Classic</a></li>
                    <li class="nav-item" data-option-value=".hot"><a class="nav-link font-weight-semibold text-2" href="#">Hot</a></li>
                    <li class="nav-item" data-option-value=".tables"><a class="nav-link font-weight-semibold text-2" href="#">Table</a></li>
                    <!--<li class="nav-item" data-option-value=".coming"><a class="nav-link font-weight-semibold text-2" href="#">Coming soon</a></li>-->

                </ul>
            </div>
        </div>
        <div class="row min-height-screen">
            <div class="col min-height-screen">
                <div class="sort-destination-loader min-height-screen mt-5 pt-2 px-4">
                    <div class="row portfolio-list sort-destination overflow-visible" data-sort-id="portfolio">
                        <style>
                            .infogamebtn {
                                display: none; /*need block*/
                                position: absolute;
                                width: 10%;
                                top: 1%;
                                background: rgba(0,0,0,0.5);
                                text-align: center;
                                font-size: 30px;
                                left: 1%;
                                color: #ffffff;
                                z-index: 100;
                                cursor: pointer;
                                text-decoration: none;
                            }
                            .infogamebtn:hover {
                                text-decoration: none;
                                background: rgba(255,255,255,0.5);
                                color: #000000;
                            }
                        </style>

                        <?php foreach($games as $game): ?>
                            <div class="col-<?php echo th::isMobile() ? '6' : '12'; ?> col-sm-6 col-lg-4 col-xl-3 isotope-item <?php echo $game['category'] ?>">
                                <div class="appear-animation" data-appear-animation="fadeInUp" data-plugin-options="{'accY': -150}" data-appear-animation-delay="">
                                    <div class="portfolio-item hover-effect-1">
                                        <a class="infogamebtn" href="/interactive/info/<?php echo $game['name']; ?>">?</a>
                                        <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>">
                                            <span class="thumb-info thumb-info-no-zoom thumb-info-no-overlay thumb-info-no-bg border-0 border-radius-0">
                                                <span class="thumb-info-wrapper thumb-info-wrapper-demos m-0 border-radius-0">
                                                    <picture class="gamethumb img-fluid border-radius-0" alt="">
    <!--                                                                    <source type="image/webp" srcset="<?php echo UTF8::str_ireplace('.png','.webp',$game['image']); ?>">-->
                                                        <source type="image/png" srcset="<?php echo $game['image']; ?>">
                                                        <img src="<?php echo $game['image']; ?>" style="width: 100%">
                                                    </picture>
                                                </span>
						<?php if(!empty($game['label'])): ?>
                                                            <span class="slot-thumb-label <?php echo $game['label']; ?>">
                                                                <span> <?php echo $game['label']; ?> </span>
                                                            </span>
                                                            <?php endif; ?>
                                            </span>
                                        </a>
                                        <div style="float:left; min-width:0.5em">&nbsp;</div>
                                        <div style="float:left;">
                                            <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>" class="text-color-light text-decoration-none text-1 text-uppercase"><?php echo $game['visible_name'] ?></a>
                                        </div>
                                        <?php if(!empty($game['demo'])): ?>
                                            <div style="float:right; min-width:0.5em">&nbsp;</div>
                                            <div style="float:right">
                                                <a style="font-weight: Bold; text-decoration:underline" class="popup-youtube" href="<?php echo $game['demo'] ?>"> Video demo</a>
                                            </div>
                                        <?php endif ?>
                                        <div style="clear:both"></div>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>



                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<section class="section section-height-4 bg-color-primary border-0 my-0">
    <div class="container-fluid">
        <div class="row justify-content-center pb-4 mb-5">
            <div class="col-12 col-sm-auto text-center mt-5">
                <span class="d-block font-weight-light text-left text-color-light negative-ls-1 text-5 ml-2 appear-animation" data-appear-animation="fadeInUp"><em class="opacity-8">Start right now!</em></span>
                <h1 class="font-weight-extra-bold text-color-light text-10 mb-2 appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="200"><em>AGT software</em></h1>
				<?php /*
                <span class="d-block text-sm-right alternative-font text-color-light text-6 appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="400">Join The Happy Customers :)</span>
				*/ ?>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <a href="/interactive/contacts" class="btn btn-dark btn-rounded btn-xl font-weight-semibold text-2 px-5 py-3 mb-5 box-shadow-2 appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="800">API</a>
                <a href="/interactive/contacts" class="btn btn-dark btn-rounded btn-xl font-weight-semibold text-2 px-5 py-3 mb-5 box-shadow-2 appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="800">Contact us</a>

            </div>
        </div>
    </div>
</section>
<?php if(th::isMobile()): ?>
    <style>
        .col, .col-1, .col-10, .col-11, .col-12, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-auto, .col-lg, .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-auto, .col-md, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-auto, .col-sm, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-auto, .col-xl, .col-xl-1, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-auto {
            padding-left: 0.1em !important;
            padding-right: 0.1em !important;
        }
    </style>
<?php endif; ?>
    <!--<script>
        document.addEventListener('DOMContentLoaded', function(){
            $('.coming a').attr('href','javascript:void(0)');
            function cheat() {
                if($('.coming:visible').length) {
                    $('#allbtn a').click();
                    setTimeout(cheat,100);
                }
            };
            cheat();
        });

    </script>-->
<style>
    .slot-thumb-label {
        width: 50px;
        height: 50px;
        overflow: hidden;
        position: absolute;
        top: -2px;
        right: 0;
        z-index: 1;
        cursor: default;
    }
    .slot-thumb-label span{
        font-size: 12px;
        line-height: 12px;
        text-transform: uppercase;
        text-align: center;
        -webkit-transform: rotate(-45deg);
        transform: rotate(45deg);
        position: relative;
        padding: 3px 0;
        top: 11px;
        left: -18px;
        width: 100px;
        display: block;
        font-weight: 700;
    }
    .slot-thumb-label.new span{
        background-color: green;
        color: #fff;
    }
</style>