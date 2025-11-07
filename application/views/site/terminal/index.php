<section class="section pt-0 my-0 pb-0 min-height-screen border-0 bg-color-dark-scale-3" id="demos">
    <div class="container-fluid">
        <div class="row justify-content-center py-4 py-sm-0 bg-color-dark-scale-2" id="stick_panel">
            <?php if(auth::$user_id): ?>
            <div class="col-auto col-sm-3 col-md-auto" style="color: #fff;">
                <!--<i class="fas fa-dollar-sign"></i>-->
                Balance: <?php echo auth::user()->amount(); ?> <?php echo auth::user()->office->currency->code; ?>
            </div>
            <?php endif; ?>
            <div class="col-auto col-sm-6 col-md-auto">
                <ul class="nav nav-light nav-active-style-1 sort-source justify-content-center flex-column flex-sm-row" style="min-height: 30px;" data-sort-id="portfolio" data-option-key="filter">
                    <li id="allbtn" class="nav-item" data-option-value="*"><a class="nav-link font-weight-semibold text-2 active" href="#">ALL</a></li>
                    <li class="nav-item" data-option-value=".classic"><a class="nav-link font-weight-semibold text-2" href="#">Classic</a></li>
                    <li class="nav-item" data-option-value=".hot"><a class="nav-link font-weight-semibold text-2" href="#">Hot</a></li>
                    <li class="nav-item" data-option-value=".tables"><a class="nav-link font-weight-semibold text-2" href="#">Table</a></li>
                    <!--<li class="nav-item" data-option-value=".coming"><a class="nav-link font-weight-semibold text-2" href="#">Coming soon</a></li>-->

                </ul>
            </div>
            <?php if(auth::$user_id): ?>
            <div class="col-auto col-sm-3 col-md-auto" style="text-align: right; color: #fff;">
                <i class="fas fa-tv"></i>
                <?php echo auth::$user_id; ?>
                <?php if(true || auth::user()->office->tg_cashusers): ?>
                    <a href="/login/logout">Log out</a>
                <?php endif; ?>
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

                        <?php foreach($games as $game): ?>
                            <div class="game-block col-<?php echo th::isMobile() ? '3' : '12'; ?> col-sm-2 col-lg-2 col-xl-1 isotope-item <?php echo ($game['category'] == 'moon') ? 'moon1' : $game['category']; ?>">
                                <div class="appear-animation" game_id="<?php echo $game['game_id']; ?>" data-appear-animation="fadeInUp" data-plugin-options="{'accY': -150}" data-appear-animation-delay="">
                                    <div class="portfolio-item hover-effect-1">
                                        <a class="infogamebtn" href="/interactive/info/<?php echo $game['name']; ?>">?</a>
                                        <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>">
                                            <span class="thumb-info thumb-info-no-zoom thumb-info-no-overlay thumb-info-no-bg border-0 border-radius-0">
                                                <span class="thumb-info-wrapper thumb-info-wrapper-demos m-0 border-radius-0">
                                                    <picture class="gamethumb img-fluid border-radius-0" alt="<?php echo $game['visible_name'] ?>">
                                                        <?php $jpg=UTF8::str_ireplace('thumb','thumbclear',UTF8::str_ireplace('.png','.jpg',$game['image'])); ?>
                                                        <!--<source type="image/webp" srcset="<?php echo UTF8::str_ireplace('.png','.webp',$game['image']); ?>">-->
                                                        <source type="image/png" srcset="<?php echo $jpg; ?>">
                                                        <img src="<?php echo $jpg; ?>" style="width: 100%" />
                                                    </picture>
                                                </span>
                                            </span>
                                        </a>
                                        <div style="float:left; min-width:0.5em;display: none;">&nbsp;</div>
                                        <div style="float:left;display: none;">
                                            <a href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; ?>" class="text-color-light text-decoration-none text-1 text-uppercase"><?php echo $game['visible_name'] ?></a>
                                        </div>
                                        <?php /*if(!empty($game['demo'])): ?>
                                            <div style="float:right; min-width:0.5em">&nbsp;</div>
                                            <div style="float:right">
                                                <a style="font-weight: Bold; text-decoration:underline" class="popup-youtube" href="<?php echo $game['demo'] ?>"> Video demo</a>
                                            </div>
                                        <?php endif*/ ?>
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
<script>
        /*document.addEventListener('DOMContentLoaded', function(){

            $('body').bind('touchmove', function(e){
                e.preventDefault();
            });

            function getElToScroll(direction) {
                if(direction=='top') {
                    var scroll = $(window).scrollTop()-$(window).height()+compensationScroll;
                    if(scroll<0) {
                        scroll=0;
                    }
                }
                else if(direction=='down') {
                    var scroll = $(window).scrollTop()+$(window).height()-$(".appear-animation").eq(0).height()/2;
                }

                console.log(direction,scroll);

                var elements = $(".appear-animation");
                var el;
                for (var i=0; i<elements.length; i++) {
                    el = $(elements[i]);
                    if (el.offset().top > scroll){
                        return el;
                    }
                }
                return false;
            }

            var lastDirectionScroll=0;

            fromClickArrow=false;

            well.addEventListener('wheel', function(e) {
                if (e.deltaY < 0) {
                    scdir = 'down';
                }
                if (e.deltaY > 0) {
                    scdir = 'up';
                }
                e.stopPropagation();
            });
            well.addEventListener('wheel', _scrollY);

            $(window).scroll(function(e) {

                var dir=null;

                var st = $(this).scrollTop();
                if (st > lastDirectionScroll){
                    dir='down';
                   // downscroll code
                } else {
                    dir='up';
                  // upscroll code
                }
                lastDirectionScroll = st;

                if(!fromClickArrow) {
                    var eee = getElToScroll(dir);
                    if(eee) {
                        fromClickArrow=true;
                        $('html, body').animate({ scrollTop: eee.offset().top }, 50,'swing',function() {
                        setTimeout(function() {
                            fromClickArrow=false;
                        },100);
                    });
                    }
                }

                if($(".appear-animation").eq(0).attr('game_id')==getElToScroll('top').attr('game_id')) {
                    $('.arrows-prev').hide();
                }
                else {
                    $('.arrows-prev').show();
                }

                var down=getElToScroll('down');


                if(!down || $(".appear-animation").last().attr('game_id')==getElToScroll('down').attr('game_id')) {
                    $('.arrows-next').hide();
                }
                else {
                    $('.arrows-next').show();
                }
            });

            compensationScroll=0;

            $('.arrows-prev').click(function() {
                var el = getElToScroll('top');
                if(el) {
                    var toTop=el.offset().top;
                    fromClickArrow=true;
                    $('html, body').animate({ scrollTop: toTop }, 50,'swing',function() {
                        fromClickArrow=false;
                        setTimeout(function() {
                            fromClickArrow=false;
                        },100);
                    });
                    compensationScroll=0;
                }
            });

            $('.arrows-next').click(function() {

                var el = getElToScroll('down');
                console.log('click',el.offset().top);
                if(el) {
                    var toBottom=el.offset().top;
                    fromClickArrow=true;
                    $('html, body').animate({ scrollTop: toBottom }, 50,'swing',function() {
                        compensationScroll=toBottom-$(window).scrollTop();
                        setTimeout(function() {
                            fromClickArrow=false;
                        },100);
                    });
                }

            });
        });

        window.onbeforeunload = function () {
            window.scrollTo(0, 0);
        }

	var pnls = 6,
		scdir, hold = false;

	function _scrollY(obj) {
		var slength, plength, pan, step = $('.game-block').height()/4,
			vh = window.innerHeight / 100,
			vmin = Math.min(window.innerHeight, window.innerWidth) / 100;
		if ((this !== undefined && this.id === 'well') || (obj !== undefined && obj.id === 'well')) {
			pan = this || obj;
			plength = parseInt(pan.offsetHeight / vh);
		}
        pan=this;
		if (pan === undefined) {
			return;
		}
		plength = plength || parseInt(pan.offsetHeight / vmin);
		slength = parseInt(pan.style.transform.replace('translateY(', ''));
		if (scdir === 'up' && Math.abs(slength) < (plength - plength / pnls)) {
			slength = slength - step;
		} else if (scdir === 'down' && slength < 0) {
			slength = slength + step;
		} else if (scdir === 'top') {
			slength = 0;
		}
		if (hold === false) {
			hold = true;
			pan.style.transform = 'translateY(' + slength + 'vh)';
			setTimeout(function() {
				hold = false;
			}, 1000);
		}
		console.log(scdir + ':' + slength + ':' + plength + ':' + (plength - plength / pnls));
	}
	/*[swipe detection on touchscreen devices]*/
	function _swipe(obj) {
		var swdir,
			sX,
			sY,
			dX,
			dY,
			threshold = 100,
			/*[min distance traveled to be considered swipe]*/
			slack = 50,
			/*[max distance allowed at the same time in perpendicular direction]*/
			alT = 500,
			/*[max time allowed to travel that distance]*/
			elT, /*[elapsed time]*/
			stT; /*[start time]*/
		obj.addEventListener('touchstart', function(e) {
			var tchs = e.changedTouches[0];
			swdir = 'none';
			sX = tchs.pageX;
			sY = tchs.pageY;
			stT = new Date().getTime();
			//e.preventDefault();
		}, false);

		obj.addEventListener('touchmove', function(e) {
			e.preventDefault(); /*[prevent scrolling when inside DIV]*/
		}, false);

		obj.addEventListener('touchend', function(e) {
			var tchs = e.changedTouches[0];
			dX = tchs.pageX - sX;
			dY = tchs.pageY - sY;
			elT = new Date().getTime() - stT;
			if (elT <= alT) {
				if (Math.abs(dX) >= threshold && Math.abs(dY) <= slack) {
					swdir = (dX < 0) ? 'left' : 'right';
				} else if (Math.abs(dY) >= threshold && Math.abs(dX) <= slack) {
					swdir = (dY < 0) ? 'up' : 'down';
				}
				if (obj.id === 'well') {
					if (swdir === 'up') {
						scdir = swdir;
						_scrollY(obj);
					} else if (swdir === 'down' && obj.style.transform !== 'translateY(0)') {
						scdir = swdir;
						_scrollY(obj);

					}
					e.stopPropagation();
				}
			}
		}, false);
	}
	/*[assignments]*/

    /*document.addEventListener('DOMContentLoaded', function(){

        var well = document.getElementsByClassName('portfolio-list')[0];
        well.style.transform = 'translateY(0)';
        well.addEventListener('wheel', function(e) {
            if (e.deltaY < 0) {
                scdir = 'down';
            }
            if (e.deltaY > 0) {
                scdir = 'up';
            }
            e.stopPropagation();
        });
        well.addEventListener('wheel', _scrollY);
        _swipe(well);
    });*/
</script>
<!--    <script>
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