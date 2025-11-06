<section class="pc-container">
    <div class="pcoded-content">

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-6">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            Presentation
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/presentation.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <!--<a href="/files/agt_en_11.02.2022.pdf" target="_blank">-->
							<a href="/files/agt_en.pdf?v=4" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/theme/admin1/images/pdf.png"/></h6>
                                        <h2 class="m-b-0 text-uppercase">Presentation</h2>
                                    </div>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            Logo
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/logodownload.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/logo.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/theme/admin1/logo/Logo_dark.png"/></h6>
                                        <h2 class="m-b-0 text-uppercase">PNG</h2>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/logo_ai.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/theme/admin1/logo/Logo_light.png"/></h6>
                                        <h2 class="m-b-0 text-uppercase">Vectors</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        
			<div class="col-md-6">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            Games PSD resources
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/psddownload.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="/files/promo/gamesPSD.zip" target="_blank">
                                    <div class="row align-items-center m-l-0">

                                        <div class="col-auto">
                                            <h6 class="text-muted m-b-10"><img src="/theme/admin1/images/psd.png"/></h6>
                                            <h2 class="m-b-0 text-uppercase">PSD</h2>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <form id="psd_form" class="form-horizontal form-material">
                                    <div class="form-group mx-sm-3 mb-2">
                                        <label>Select game</label>
                                        <?php echo Form::select('game_id',$games,-1,['id'=>'gameselect2']); ?>
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <a style="color: #fff" data-href="/files/promo/psd/" target="_blank" href="/files/promo/psd/<?php echo key($games); ?>.psd" id="down-game2" class="btn btn-primary mb-2">Download</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		
		</div>
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            MISC
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/misc.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/GamesNames.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10">
                                            <img src="/games/agt/images/games/stalker/ui/top.png"/>
                                        </h6>
                                        <h2 class="m-b-0">Games name images</h2>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/AGT-Game-Image.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10">
                                            <img src="/games/agt/images/games/stalker/icons/icon11.png"/>
                                        </h6>
                                        <h2 class="m-b-0">Games characters images</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

		<div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            Games description
                        </h1>
                        <hr>
                        <div class="row">
                            <a href="/files/AGT_games_description_EN.pdf" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10">
                                            <img src="/theme/admin1/images/game_descr.png"/>
                                        </h6>
                                        <h2 class="m-b-0">Games description</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            Games info
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/gamesinfo.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/AGT_games_info.xls?v=129" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/theme/admin1/images/excel.jpg"/></h6>
                                        <h2 class="m-b-0">Games info
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/gamesinfo.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							<a href="/enter/currency">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 105px" src="/theme/admin1/images/currency.jpg"/></h6>
                                        <h2 class="m-b-0">Currencies
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/currenciesinfo.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							
							<a href="/enter/promo/langs">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 105px" src="/theme/admin1/images/iso.png"/></h6>
                                        <h2 class="m-b-0">Languages
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/languages.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							
							 <a href="/enter/countries">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 105px" src="/theme/admin1/images/country.jpg"/></h6>
                                        <h2 class="m-b-0">Prohibited areas
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/prohibited.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							<a href="/files/promo/LS.pdf" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 105px"  src="/theme/admin1/images/ls.png"/></h6>
                                        <h2 class="m-b-0">Lucky Spins
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/lsinfo.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
						</div>
						<div class="row">
							<a href="/files/promo/DS.pdf" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 84px" src="/theme/admin1/images/ds.png"/></h6>
                                        <h2 class="m-b-0">Daily Spins
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/dsinfo.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							
							 <a href="/files/promo/FSAPI.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 84px" src="/theme/admin1/images/fsapi.png"/></h6>
                                        <h2 class="m-b-0">FS API
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/fsapiinfo.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							<a href="/files/promo/JP.pdf" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 84px" src="/theme/admin1/images/jp.png"/></h6>
                                        <h2 class="m-b-0">Jackpots
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/jpinfo.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							
							 <a href="/files/promo/100cashback.pdf" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 84px" src="/theme/admin1/images/100.png"/></h6>
                                        <h2 class="m-b-0">100% Cashback
                                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/100cashbackinfo.html">
                                                &#x1F517;
                                            </a>
                                        </h2>
                                    </div>
                                </div>
                            </a>
							
							<a href="/files/promo/blfr.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img style="height: 84px" src="/theme/admin1/images/blfr.png"/></h6>
                                        <h2 class="m-b-0">Black Friday Promotion
                                        </h2>
                                    </div>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            Download all promos of game
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/downloadthumbs.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form id="jp_office_form" class="form-horizontal form-material">
                                    <div class="form-group mx-sm-3 mb-2">
                                        <label>Select game</label>
                                        <?php echo Form::select('game_id',$games,-1,['id'=>'gameselect']); ?>
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <a style="color: #fff" data-href="/files/promo/" target="_blank" href="/files/promo/<?php echo key($games); ?>.zip" id="down-game" class="btn btn-primary mb-2">Download</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if($office_id>0): ?>
                                <samp>
                                <?php echo (htmlentities(th::jpWidgetAGT($office_id,[$jp_width.'px',$jp_height.'px'],[],$bgimg))); ?>
                                </samp>
                                <hr />
                                <?php echo th::jpWidgetAGT($office_id,[$jp_width.'px',$jp_height.'px'],[],$bgimg); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1:1 images
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/allpic.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt1x1_450-450cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1_450-450cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,450x450]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1x1_600-600cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1_600-600cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,600x600]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1x1_250-250cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1_250-250cycle.webp"/></h6>
                                        <h5 class="m-b-0 text-uppercase">Animated [WEBP,250x250]</h5>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/games/agt/sqthumb/jokers20.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,250x250]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1x1_450-450.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1_450-450.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,450x450]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1x1_600-600.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1_600-600.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,600x600]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1:1.5 (2x3) images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt1x1.5_400-600cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1.5_400-600cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,400x600]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1x1.5_400-600.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1.5_400-600.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,400x600]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1:1.33 (3x4) images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt1x1.33_420-560cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1.33_420-560cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,420x560]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1x1.33_420-560.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1x1.33_420-560.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,420x560]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1.67x1 (5x3) images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt1.666x1_400-240cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.666x1_400-240cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,400x240]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1.666x1_400-240.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.666x1_400-240.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,400x240]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1.4:1 (7x5) images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt1.4x1_420-300cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.4x1_420-300cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,420x300]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1.4x1_420-300.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.4x1_420-300.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,420x300]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1.33:1 (4x3) images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt1.33x1_400-300cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.33x1_400-300cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,400x300]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1.33x1_400-300.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.33x1_400-300.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,400x300]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
						<div class="row">
                            <a href="/files/promo/agt1.33x1_640-480cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.33x1_640-480cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,640x480]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1.33x1_640-480.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.33x1_640-480.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,640x480]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1.5:1 (3x2) images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt1.5x1_600-400cycle.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.5x1_600-400cycle.webp"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Animated [WEBP,600x400]</h4>
                                    </div>
                                </div>
                            </a>
                            <a href="/files/promo/agt1.5x1_600-400.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/agt1.5x1_600-400.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG & JPG,600x400]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">1.7:1 images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/agt17.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/games/agt/thumb/happysanta.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG,400x225]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">62:79 images</h1>
                        <hr>
                        <div class="row">
                            <a href="/files/promo/png248x316.zip" target="_blank">
                                <div class="row align-items-center m-l-0">

                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10"><img src="/files/promo/preview/png248x316.png"/></h6>
                                        <h4 class="m-b-0 text-uppercase">Static [PNG,248x316]</h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12" id="jpWidgetAGT">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">Jackpots Widget
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/jpwidgetinfo.html">
                                &#x1F517;
                            </a>
                            <a class="btn btn-success" href="/files/Jackpot widget.pdf">README</a>
                        </h1>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form id="jp_office_form" class="form-horizontal form-material" action="#jpWidgetAGT">
                                    <div class="form-group mx-sm-3 mb-2">
                                        <label>Select office</label>
                                        <?php echo Form::select('office_id',$offices,$office_id); ?>
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <?php foreach(['121x121','138x106','138x156','400x225','250x250','225x225','400x200'] as $size): ?>
                                        <button type="submit" class="btn btn-primary mb-2" name="size" value="<?php echo $size; ?>"><?php echo $size; ?></button>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <label>Width</label>
                                        <?php echo Form::input('jp_width',$jp_width); ?>&nbsp;px
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <label>Height</label>
                                        <?php echo Form::input('jp_height',$jp_height); ?>&nbsp;px
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <label>Backround image URL (if need)</label>
                                        <?php echo Form::input('bgimg',$bgimg); ?>
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <button type="submit" class="btn btn-primary mb-2">Generate</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if($office_id>0): ?>
                                <samp>
                                <?php echo (htmlentities(th::jpWidgetAGT($office_id,[$jp_width.'px',$jp_height.'px'],[],$bgimg))); ?>
                                </samp>
                                <hr />
								<h2>Preview:</h2>
                                <?php echo th::jpWidgetAGT($office_id,[$jp_width.'px',$jp_height.'px'],[],$bgimg); ?>
                                <?php endif; ?>								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<style>
    samp {
        background-color: rgba(200,200,200,0.7);
    }
</style>
<script>
<?php if(!empty($_GET)): ?>
    document.getElementById("jp_office_form").scrollIntoView();
<?php endif; ?>

    document.getElementById("gameselect").addEventListener("change", function() {
        var a=document.getElementById('down-game');
        a.href=a.getAttribute('data-href')+this.value+'.zip?v=4';
    });
	document.getElementById("gameselect2").addEventListener("change", function() {
        var a=document.getElementById('down-game2');
        a.href=a.getAttribute('data-href')+this.value+'.psd?v=6';
    });
</script>