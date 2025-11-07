<section class="section pt-0 my-0 pb-0 min-height-screen border-0 bg-color-dark-scale-3" id="demos">
    <div class="container-fluid">
        <div class="row justify-content-center py-4 py-sm-0 bg-color-dark-scale-2" id="stick_panel">
            <?php if(auth::$user_id): ?>
            <div class="col-auto col-sm-3 col-md-auto" style="color: #fff;">
                <!--<i class="fas fa-dollar-sign"></i>-->
                Balance: <?php echo $user->amount(); ?> <?php echo $user->office->currency->code; ?>
            </div>
            <?php endif; ?>
            <div class="col-auto col-sm-6 col-md-auto">
                <ul class="nav nav-light nav-active-style-1 sort-source justify-content-center flex-column flex-sm-row" style="min-height: 30px;" data-sort-id="portfolio" data-option-key="filter">
                    <li id="allbtn" class="nav-item" data-option-value="*"><a class="nav-link font-weight-semibold text-2 active" href="#">ALL</a></li>
                </ul>
            </div>
            <?php if(auth::$user_id): ?>
            <div class="col-auto col-sm-3 col-md-auto" style="text-align: right; color: #fff;">
                <i class="fas fa-tv"></i>
                <?php echo auth::$user_id; ?>
                <a href="/login/logout">Log out</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="row min-height-screen">
            <div class="col min-height-screen">
                <div class="sort-destination-loader min-height-screen pt-2 px-4">
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

                        <?php foreach($games as $game=>$vname): ?>
                            <div class="game-block col-<?php echo th::isMobile() ? '3' : '12'; ?> col-sm-2 col-lg-2 col-xl-1 isotope-item">
                                <div class="appear-animation" data-appear-animation="fadeInUp" data-plugin-options="{'accY': -150}" data-appear-animation-delay="">
                                    <div class="portfolio-item hover-effect-1">
                                        <a class="infogamebtn" href="/interactive/info/<?php echo $game; ?>">?</a>
                                        <a href="/games/agt/<?php echo $game; ?>">
                                            <span class="thumb-info thumb-info-no-zoom thumb-info-no-overlay thumb-info-no-bg border-0 border-radius-0">
                                                <span class="thumb-info-wrapper thumb-info-wrapper-demos m-0 border-radius-0">
                                                    <picture class="gamethumb img-fluid border-radius-0" alt="<?php echo $vname ?>">
                                                        <source type="image/png" srcset="/games/agt/sqthumb/<?php echo $game; ?>.png">
                                                        <img src="/games/agt/sqthumb/<?php echo $game; ?>.png" style="width: 100%" />
                                                    </picture>
                                                </span>
                                            </span>
                                        </a>
                                        <div style="float:left; min-width:0.5em;display: none;">&nbsp;</div>
                                        <div style="float:left;display: none;">
                                            <a href="/games/agt/<?php echo $game; ?>" class="text-color-light text-decoration-none text-1 text-uppercase"><?php echo $vname; ?></a>
                                        </div>
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
<style>
<?php if(th::isMobile()): ?>
        .col, .col-1, .col-10, .col-11, .col-12, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-auto, .col-lg, .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-auto, .col-md, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-auto, .col-sm, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-auto, .col-xl, .col-xl-1, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-auto {
            padding-left: 0.1em !important;
            padding-right: 0.1em !important;
        }
        .portfolio-list .portfolio-item {
            margin:0;
        }
<?php endif; ?>

        .sort-destination-loader.sort-destination-loader-loaded::-webkit-scrollbar {
            width: 1px;
        }

        .sort-destination-loader.sort-destination-loader-loaded::-webkit-scrollbar-track {
            box-shadow: inset 0 0 1px rgba(0, 0, 0, 0.3);
        }

        .sort-destination-loader.sort-destination-loader-loaded::-webkit-scrollbar-thumb {
            background-color: darkgrey;
            outline: 1px solid slategrey;
        }

        #stick_panel > div {
            flex-grow: 4;
        }
        html,body {
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        .portfolio-list {
            transition: 1s cubic-bezier(0.5, 0, 0.5, 1);
            margin-bottom: 40px;
        }

        @media (min-width: 1200px) {
            .col-xl-1 {
                flex: 0 0 12.5%;
                max-width: 12.5%;
            }
        }
</style>

<div class="" style="position: fixed; right: 1.4%; bottom: 5%; height: 220px;">
    <div class="panel arrows-prev"></div>
    <div class="panel arrows-next"></div>
</div>

<style>
    .sort-destination-loader.sort-destination-loader-loaded {
        overflow-y: scroll;
        height: 100vh;
    }
    .scroll-to-top {
        display: none !important;
    }
    .arrows {
        width: 48px;
        height: 48px;
        /*border-color: #000;*/
        /*position: fixed;*/
        z-index: 8000;
        margin-top: -31px;
    }
    .panel {
        border-radius: 100px;
        height: 100px;
        width: 100px;
        display: block;
        position: absolute;
        z-index: 8000;
        background: rgba(85, 85, 85, 0.6);
        cursor: pointer;
        right: 0;
    }
    .panel.arrows-prev {
        top: 0;
        display: none;
    }
    .panel.arrows-next {
        bottom: 0;
    }

    .panel::after {
        border-color: rgba(255, 255, 255, 1);
        position: absolute;
        content: ' ';
        border-bottom: 10px solid rgba(255, 255, 255, 1);
        border-left: 10px solid rgba(255, 255, 255, 1);
        width: 40px;
        height: 40px;
        margin-top: -31px;
    }
/*    .panel:hover {
        background: rgba(0, 0, 0, 0.8);
    }
    .panel:hover::after {
        border-color: rgba(255, 255, 255, 0.8);
    }*/
    .panel.arrows-prev::after {
        transform: rotate(135deg);
        left:0;
        right:0;
        margin-left:auto;
        margin-right:auto;
        top: 65px;
    }
    .panel.arrows-next::after {
        transform: rotate(-45deg);
        left:0;
        right:0;
        margin-left:auto;
        margin-right:auto;
        bottom: 38px;
    }
    #footer {
        display:none;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){

        var canUp=false;
        var canDown=true;

        function getRowFunction(direction) {
            const grid = document.querySelector('.portfolio-list');


            var stick_panel_height = $('#stick_panel').height()+grid.offsetTop;

//            const gridChildren = Array.from(grid.children);

            const gridChildren = Array.from(document.querySelectorAll('.game-block:not([style*="display:none"])'));


            const gridNum = gridChildren.length;
            const baseOffset = gridChildren[0].offsetTop-stick_panel_height;
            const breakIndex = gridChildren.findIndex(item => (item.offsetTop-stick_panel_height) > baseOffset);
            const numPerRow = (breakIndex === -1 ? gridNum : breakIndex-1);


            const active = grid.querySelector('.activerow');
            var activeIndex = Array.from(grid.children).indexOf(active);

            var isTopRow = activeIndex <= 0;
            var isBottomRow = activeIndex >= gridNum - numPerRow;
            var isLeftColumn = activeIndex % numPerRow === 0;
            var isRightColumn = activeIndex % numPerRow === numPerRow - 1 || activeIndex === gridNum - 1;


            const updateActiveItem = (active, next, activeClass) => {
                active.classList.remove(activeClass);
                next.classList.add(activeClass);
            };


            canUp = !isTopRow;
            canDown = !isBottomRow;


            if(direction=='down' && !isBottomRow) {
                var child=gridChildren[activeIndex + numPerRow];
                var extra=0;
                if(($(window).scrollTop()+window.innerHeight)>$('.game-block').last().offset().top) {
                    canDown=false;
                    extra=100;
                }
                updateActiveItem(active,child,'activerow');
                return $(child).position().top+extra;
            }
            else if(direction=='up' && !isTopRow) {
                var child=gridChildren[activeIndex - numPerRow];
                if((activeIndex - numPerRow)<=1) {
                    canUp=false;
                }
                if(!child) {
                    return 0;
                }
                updateActiveItem(active,child,'activerow');
                return $(child).position().top;
            }
            return false;
        }

        $('.game-block').eq(0).addClass('activerow');

        $('.arrows-prev').click(function() {
            var goup=getRowFunction('up');

            $('.arrows-prev').hide();
            if(canUp) {
                $('.arrows-prev').show();
            }

            $('.arrows-next').hide();
            if(canDown) {
                $('.arrows-next').show();
            }

            if(goup>=0) {
                $('.sort-destination-loader').animate({ scrollTop: goup }, 50);
            }
        });
        $('.arrows-next').click(function() {

            var godown=getRowFunction('down');
            $('.arrows-prev').hide();
            if(canUp) {
                $('.arrows-prev').show();
            }

            $('.arrows-next').hide();
            if(canDown) {
                $('.arrows-next').show();
            }

            if(godown) {
                $('.sort-destination-loader').animate({ scrollTop: godown }, 150);
            }
        });

        $(window).on('wheel',function() {
            $('.game-block').eq(0).addClass('activerow');
        });

        $('.nav-item').on('click',function() {
            $('.game-block').eq(0).addClass('activerow');
        });

        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        };
});
</script>