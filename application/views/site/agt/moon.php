<!DOCTYPE html>
<html lang="en" translate="no">
    <head>
        <title><?php echo $game->name; ?></title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
        <meta name="google" content="notranslate" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!--        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">-->

        <link href="css/fontroboto.css" rel="stylesheet" type="text/css"/>
        <?php if(in_array(auth::user()->office_id,[5563])): ?>
        <link rel="shortcut icon" href="/theme/interactive1/img/faviconempty.png">
        <?php else: ?>
        <link rel="shortcut icon" href="/theme/interactive1/img/favicon.png">
        <?php endif; ?>

        <link rel="stylesheet" href="/games/agt/moon/css/normalize.css?v=<?php echo th::ver(); ?>">
        <link rel="stylesheet" href="/games/agt/moon/css/style.css?v=<?php echo th::ver(); ?>">
        <link rel="stylesheet" href="/games/agt/moon/css/media.css?v=<?php echo th::ver(); ?>">
        <link rel="stylesheet" href="/games/agt/moon/css/interactive.css?v=<?php echo th::ver(); ?>">
        <link rel="stylesheet" href="/games/agt/moon/css/wenk.min.css?v=<?php echo th::ver(); ?>">
        <link rel="stylesheet" href="/games/agt/moon/css/noty.css?v=<?php echo th::ver(); ?>">
        <link rel="stylesheet" href="/games/agt/css/flags.min.css?v=<?php echo th::ver(); ?>">
        <link rel="stylesheet" href="/games/agt/moon/css/freakflags.css?v=<?php echo th::ver(); ?>">
        <style>
			html,body {
				overflow-x: hidden;
			}
            #loading::after{
                content: " ";
                position: fixed;
                display: block;
                color: #fff;
                z-index: 888;
                top: 0;
                background: #0f1427;
                left: 0;
                right: 0;
                bottom: 0;
                text-align: center;
                padding-top: 25%;
            }
            .divtemplate {
                display: none;
            }
            .prize {
                display: none;
            }
            .prize-img {
                display: flex;
                width: 36px;
                margin-right: 6px;
            }
            #noty_layout__topCenter {
                text-align: center;
                left: calc(50% - 120px);
                top: 10%;
            }
            .modal-btn-close {
                float: right;
                border-radius: 25px;
                padding: 1px 5px;
                border: 1px solid #fff;
                cursor: pointer;
            }
            @media (max-width: 991px) {
                #noty_layout__topCenter {
                    left: 50%;
                }
            }
        </style>
    </head>
    <body>
        <header class="header fixed">
            <div class="container flex header__container">
                <a href="#!" class="header__logo">
                    <?php if(!$game->branded): ?>
                    <img class="header__logo-img" src="/games/agt/moon/img/logo.png" alt="">
                    <?php else: ?>
                    <img class="header__logo-img" src="/games/agt/moon/img/logo<?php echo $game->name; ?>.png" alt="">
                    <?php endif; ?>
                </a>
                <div class="header__wrapper">
                    <div class="flex header_wrap mobile-hide">
                        <button class="header__top-menu-btn header__volume-btn" configkey="playsound" onclick="moonGame.setConfig('playsound',+(moonGame.getConfig('playsound')=='0')); this.checked=(moonGame.getConfig('playsound')=='0'); ">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.28663 8.36666L9.42887 4.09313C10.0919 3.63182 11 4.10627 11 4.914L11 19.0442C11 19.86 10.0758 20.3325 9.4145 19.8549L3.24352 15.398C2.46259 14.834 2 13.9293 2 12.966V10.8293C2 9.84729 2.48057 8.92748 3.28663 8.36666ZM19.7044 8.19431C19.1801 6.99518 18.4137 5.91731 17.4533 5.02829L18.4722 3.92749C19.5843 4.95688 20.4717 6.20494 21.0788 7.59341C21.6858 8.98187 21.9995 10.4808 22 11.9962C22.0005 13.5115 21.6879 15.0107 21.0819 16.3996C20.4758 17.7885 19.5892 19.0371 18.4779 20.0673L17.4582 18.9672C18.418 18.0775 19.1836 16.9991 19.7071 15.7996C20.2305 14.6001 20.5005 13.3054 20.5 11.9967C20.4995 10.688 20.2287 9.39344 19.7044 8.19431ZM16.0264 14.2328C15.7213 14.9197 15.2786 15.5367 14.7257 16.0459L15.7418 17.1493C16.4455 16.5013 17.0089 15.716 17.3972 14.8418C17.7856 13.9676 17.9906 13.023 17.9997 12.0665C18.0088 11.11 17.8217 10.1617 17.4501 9.28032C17.0784 8.3989 16.53 7.603 15.8388 6.94176L14.8019 8.02567C15.345 8.54522 15.7759 9.17056 16.0679 9.86311C16.3599 10.5557 16.5069 11.3007 16.4998 12.0523C16.4926 12.8038 16.3315 13.5459 16.0264 14.2328Z" fill="#81819C"/>
                            </svg>
                        </button>
                        <button class="header__music-btn" configkey="musicbgnum" onclick="moonGame.setConfig('musicbgnum',+(parseInt(moonGame.getConfig('musicbgnum'))=='0')); this.checked=(parseInt(moonGame.getConfig('musicbgnum'))=='0'); " val="0">
                            <svg viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2287 0.199401C14.0351 -0.52315 16 0.807185 16 2.75271V12.7986C16 14.4555 14.6569 15.7986 13 15.7986C11.3431 15.7986 10 14.4555 10 12.7986C10 11.1418 11.3431 9.79865 13 9.79865C13.5464 9.79865 14.0587 9.94474 14.5 10.2V2.75271C14.5 1.86838 13.6068 1.26368 12.7858 1.59212L6.78576 3.99212C6.31119 4.18195 6 4.64158 6 5.15271V14.2986C6 15.9555 4.65685 17.2986 3 17.2986C1.34315 17.2986 0 15.9555 0 14.2986C0 12.6418 1.34315 11.2986 3 11.2986C3.54643 11.2986 4.05874 11.4447 4.5 11.7V5.15271C4.5 4.02822 5.18462 3.01702 6.22868 2.5994L12.2287 0.199401Z" fill="#81819C"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex header_wrap">
                        <button class="header__help-btn header__top-menu-btn" translate="how_to_play">How to play</button>
                        <a href="javascript:model.openPromoInfo();" class="prize">
                            <picture class="prize-img">
                                <source type="image/webp" srcset="/games/agt/images/common/ui/prize0.webp">
                                <img src="/games/agt/images/common/ui/prize0.png" />
                            </picture>
                        </a>
                        <div class="header__balance-win header__top-menu-btn">
                            <span class="header__balance" id="balance"></span>
                        </div>
                        <div class="header__burger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16"  vievbox="0 0 24 16">
                            <polygon points="0,0 24,0 24,2 0,2"/>
                            <polygon points="0,7 24,7 24,9 0,9"/>
                            <polygon points="0,14 24,14 24,16 0,16"/>
                            </svg>
                        </div>
                        <ul class="header__menu-list">
                            <div class="header__menu-item-top">
                                <li class="header__menu-item">
                                    <b style="color: #fff;">ID: <?php echo auth::$user_id; ?></b>
                                </li>
                                <li class="header__menu-item" style="display: flex;align-items: center;">
                                    <?php if(auth::user()->office->enable_bia>0 && $game->name!='aerobet'): ?>
                                    <a class="ds-link" href="javascript:moonGame.openDS();" >DS</a>
                                    <?php else: ?>
                                    &nbsp;
                                    <?php endif; ?>
                                    <a style="color: #fff; margin-left: 10px;" class="nav-link fullscreen-btn" href="javascript:model.toggleFullscreen();" >
                                        <svg style="color: white" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fullscreen" viewBox="0 0 16 16"> <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1h-4zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5zM.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5z" fill="white"></path> </svg>
                                    </a>
									<?php if(auth::user()->office->show_game_history>0): ?>
									<a style="color: #fff;" class="nav-link fullscreen-btn" href="javascript:model.openHistory();" >
										<svg style="width: 20px; height: 20px;" width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M5.52786 16.7023C6.6602 18.2608 8.3169 19.3584 10.1936 19.7934C12.0703 20.2284 14.0409 19.9716 15.7434 19.0701C17.446 18.1687 18.766 16.6832 19.4611 14.8865C20.1562 13.0898 20.1796 11.1027 19.527 9.29011C18.8745 7.47756 17.5898 5.96135 15.909 5.02005C14.2282 4.07875 12.2641 3.77558 10.3777 4.16623C8.49129 4.55689 6.80919 5.61514 5.64045 7.14656C4.47171 8.67797 3.89482 10.5797 4.01579 12.5023M4.01579 12.5023L2.51579 11.0023M4.01579 12.5023L5.51579 11.0023" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M12 8V12L15 15" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</a>
									<?php endif; ?>
                                </li>
                                <li class="header__menu-item">
                                    <a l="<?php echo I18n::$lang; ?>" class="nav-link dropdown-toggle lang-select" href="#" id="dropdown09" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="flag-label"><?php echo UTF8::strtoupper(I18n::$lang); ?></span>
                                        <span class="fflag fflag-<?php echo UTF8::strtoupper(I18n::$lang); ?> ff-md"> </span>
                                    </a>
                                </li>
                            </div>
                            <?php $langs=th::getLangsTranslate(arr::get($_GET,'forcelang')); ?>
                            <?php if(count(array_keys($langs))>1): ?>
                            <div class="dropdown-menu lang-select-list" aria-labelledby="dropdown09">
                                <?php foreach($langs as $k=>$lang): ?>
                                <?php if($k==I18n::$lang) continue; ?>
                                <a class="dropdown-item" l="<?php echo $k; ?>" href="javascript:void(0);" onclick="selectLang(this.getAttribute('l'));">
                                    <span class="flag-label"><?php echo UTF8::strtoupper($k); ?></span>
                                    <span class="fflag fflag-<?php echo UTF8::strtoupper($k); ?> ff-md"> </span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <hr />
                            <?php if(false): ?>
                            <li class="header__menu-item">
                                <input checked configkey="animation_type" onchange="moonGame.setConfig('animation_type',+this.checked)" type="checkbox" id="switch-animation-btn"/>
                                <label for="switch-animation-btn" class="toggle">
                                    <div class="slider"></div>
                                </label>
                                <span class="header__menu-link">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        Animation
                                </span>
                            </li>
                            <?php else: ?>
                                <?php if(!$game->branded): ?>
                                    <div class="header__menu-group">
                                        <li class="header__menu-item">
                                            <span class="header__menu-link">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-play"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                                    <b translate="animation">Animation</b>
                                            </span>
                                        </li>
                                        <li class="header__menu-item">
                                            <input name="switch-animation-btn" configkey="animation_type" val="7" onchange="moonGame.setConfig('animation_type',7)" type="radio" id="switch-animation-btn7"/>
                                            <label for="switch-animation-btn7" class="toggle toggle-radio">
                                                <div class="slider"></div>
                                            </label>
                                            <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                                    <svg fill="#CACACA" height="200px" width="200px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve" stroke="#CACACA"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Rocket_1_"> <path d="M63.9999542,8.8992481c0-2.7851996-0.7939987-4.9892998-2.3593979-6.5497999 C55.3563538-3.9113514,38.8339539,2.7459486,24.019455,17.5105476c-0.5382996,0.5366001-1.0302982,1.1086006-1.5033989,1.6948013 c-0.1553001-0.0642014-0.3339996-0.1216011-0.5394993-0.1694012c-4.2666016-0.9970989-17.960001,1.3291016-21.9463005,17.2549 c-0.1025887,0.4081993,0.0615,0.8358994,0.4101,1.0713005c0.169,0.1152,0.3643113,0.1717987,0.5596,0.1717987 c0.206,0,0.4121-0.0634003,0.5869-0.1903992c5.4105997-3.9248009,9.7511997-5.5033989,13.5403004-4.9222984 c-0.2614889,0.4740982-0.5269003,0.9365005-0.8010006,1.3783989c-0.2451992,0.3955002-0.1855993,0.9071999,0.1435118,1.235302 l1.569088,1.5639992l-2.9371996,2.9263c-0.1884995,0.1875-0.2939997,0.4422989-0.2939997,0.7080002 c0,0.2655983,0.1055002,0.5205002,0.2939997,0.7080002l9.8291121,9.7967987 c0.1952877,0.1944008,0.4500885,0.2919998,0.7059994,0.2919998c0.255888,0,0.510788-0.097599,0.706089-0.2919998 l2.9410992-2.9315987l1.5734997,1.5683975c0.1934013,0.1923027,0.4483128,0.2919998,0.7061005,0.2919998 c0.1805992,0,0.3623009-0.0489006,0.5244007-0.1484985c0.4132996-0.2549019,0.8422985-0.5024986,1.283699-0.7461014 c0.6509018,3.8078995-0.9258003,8.1761017-4.913599,13.6377029c-0.2480011,0.3398972-0.2567997,0.7987976-0.022501,1.1474991 c0.1884995,0.2812004,0.5020008,0.4423981,0.830101,0.4423981c0.0801105,0,0.1611118-0.0098,0.2411995-0.0293007 c15.9404984-3.9638977,18.2959137-17.6093979,17.3134995-21.8652992c-0.0601006-0.2597008-0.1386986-0.4641991-0.2238998-0.6455002 c0.635498-0.5021973,1.2546997-1.0267982,1.833313-1.6035004C57.1034546,29.2185478,63.9999542,17.0662479,63.9999542,8.8992481z M16.0575562,30.5486488c-3.9013996-0.8223-8.1884995,0.3213005-13.2792997,3.5625 c2.5039001-6.9482002,7.0644999-10.2305012,10.6426001-11.7763996c2.6826-1.1582012,5.1239986-1.4824009,6.6864986-1.4824009 c0.4190006,0,0.7734013,0.0233994,1.0537014,0.0596008L16.0575562,30.5486488z M23.6366673,48.6179466l-8.4121113-8.384697 l2.2304993-2.2217026l7.2674999,7.2423019l1.1445999,1.1404991L23.6366673,48.6179466z M41.5331535,50.5730476 c-1.5468979,3.5830002-4.8417969,8.1483994-11.8368988,10.6514015c3.2461014-5.0694008,4.3935013-9.3389015,3.5703011-13.2294998 l9.6737976-5.089901C43.1141548,44.2195473,42.9872551,47.203949,41.5331535,50.5730476z M34.6893539,44.8650475 c-1.7586861,0.8467026-3.4335976,1.6514015-4.970686,2.5438995L16.4364567,34.1716499 c0.7172985-1.2282028,1.3803997-2.5621014,2.052-3.9399014l4.0607986-7.6671009 c0.8499012-1.3155994,1.7915001-2.5506992,2.8822994-3.6380997C38.9843559,5.4187484,54.916954-1.5236515,60.2284546,3.7654486 c1.1758003,1.1718998,1.7714996,2.8993998,1.7714996,5.1337996c0,7.5449009-6.8251991,19.4169998-16.982399,29.5410023 C42.1884537,41.2595482,38.3759537,43.0925484,34.6893539,44.8650475z"></path> <path d="M42.724556,12.8533487c-1.1395988,1.1337996-1.767601,2.6426001-1.767601,4.2480001 c0,1.6064987,0.6280022,3.1152992,1.767601,4.2490997c1.1748009,1.1688995,2.7178001,1.7539005,4.2607002,1.7539005 c1.5429993,0,3.0859985-0.585001,4.2598-1.7539005c1.139698-1.1338005,1.766613-2.642601,1.766613-4.2490997 c0-1.6054001-0.626915-3.1142006-1.766613-4.2480001C48.8974533,10.5154486,45.0741539,10.5154486,42.724556,12.8533487z M49.8349533,19.9324493c-1.5722847,1.5643997-4.1279984,1.5643997-5.7001991,0 c-0.7588005-0.7559013-1.1777992-1.7608013-1.1777992-2.8311005c0-1.0692997,0.4189987-2.0742006,1.1777992-2.8301001 c0.7861023-0.7821999,1.8183022-1.1728001,2.850502-1.1728001c1.032299,0,2.0634995,0.3906002,2.8496971,1.1728001 c0.7587128,0.7558994,1.1767159,1.7608004,1.1767159,2.8301001C51.0116692,18.171648,50.5936661,19.176548,49.8349533,19.9324493z"></path> <path d="M16.464756,47.1472473c-0.3896008-0.3915977-1.0223999-0.3915977-1.4139996-0.0018997l-9.9950886,9.9619026 c-0.3907118,0.3895988-0.3926115,1.0223999-0.0020003,1.4139977c0.1952887,0.1963005,0.4511886,0.2940025,0.7080002,0.2940025 c0.2548885,0,0.5107884-0.097702,0.7060885-0.2920036l9.9951-9.9618988 C16.8534565,48.1716499,16.8554554,47.5388489,16.464756,47.1472473z"></path> <path d="M5.7245564,51.9734497c0.2549,0,0.5106997-0.097702,0.7061114-0.2919998l6.5819998-6.5605011 c0.3905888-0.3897018,0.3915882-1.0224991,0.0018883-1.4141006c-0.3895998-0.3915977-1.0223999-0.392601-1.4139996-0.0019989 l-6.5820999,6.5606003c-0.3906002,0.3895988-0.3916001,1.0224991-0.0019002,1.4141006 C5.2118564,51.8757477,5.4677563,51.9734497,5.7245564,51.9734497z"></path> <path d="M18.5018559,50.5837479l-6.5819998,6.5606003c-0.3906002,0.3895988-0.3915997,1.0224991-0.0018997,1.4141006 c0.1953001,0.1962013,0.4510994,0.2938995,0.7080002,0.2938995c0.2547998,0,0.5107107-0.0976982,0.7059994-0.2919998 l6.5821009-6.5605011c0.3906116-0.389698,0.3916111-1.0224991,0.0018997-1.4141006 C19.5263557,50.194149,18.8934555,50.1931496,18.5018559,50.5837479z"></path> </g> </g></svg>
                                                    Rocket
                                            </span>
                                        </li>
                                        <li class="header__menu-item">
                                            <input name="switch-animation-btn" configkey="animation_type" val="1" onchange="moonGame.setConfig('animation_type',1)" type="radio" id="switch-animation-btn1"/>
                                            <label for="switch-animation-btn1" class="toggle toggle-radio">
                                                <div class="slider"></div>
                                            </label>
                                            <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                                    Bitcoin
                                            </span>
                                        </li>
    
                                        <li class="header__menu-item">
                                            <input name="switch-animation-btn" configkey="animation_type" val="2" onchange="moonGame.setConfig('animation_type',2)" type="radio" id="switch-animation-btn2"/>
                                            <label for="switch-animation-btn2" class="toggle toggle-radio">
                                                <div class="slider"></div>
                                            </label>
                                            <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                                    <svg xmlns="http://www.w3.org/2000/svg"  fill="currentColor" viewBox="0 0 640 512"><defs><style>.fa-secondary{opacity:.4}</style></defs><path d="M452.6 352H58.7l89.7-215.5A170 170 0 0 1 435 92.1l53.1 62.7a55.94 55.94 0 0 0-24.2 45.3 54.08 54.08 0 0 0-8.2 11.4L384 192z" class="fa-secondary"/><path d="M480 384H32a32 32 0 0 0-32 32v32a32 32 0 0 0 32 32h448a32 32 0 0 0 32-32v-32a32 32 0 0 0-32-32zm160-144c0-12.1-8.2-21.9-19.2-25.2 5.5-10.1 4.4-22.8-4.2-31.4s-21.3-9.7-31.4-4.2c-3.3-11-13.1-19.2-25.2-19.2s-21.9 8.2-25.2 19.2c-10.1-5.5-22.8-4.4-31.4 4.2s-9.7 21.3-4.2 31.4c-11 3.3-19.2 13.1-19.2 25.2s8.2 21.9 19.2 25.2c-5.5 10.1-4.4 22.8 4.2 31.4a25.45 25.45 0 0 0 31.4 4.2c3.3 11 13.1 19.2 25.2 19.2s21.9-8.2 25.2-19.2c4 2.1 8.2 3.6 12.5 3.6a26.9 26.9 0 0 0 18.9-7.8c8.6-8.6 9.7-21.3 4.2-31.4 11-3.3 19.2-13.1 19.2-25.2z" class="feather"/></svg>
                                                    Santa
                                            </span>
                                        </li>
    
                                        <li class="header__menu-item">
                                            <input name="switch-animation-btn" configkey="animation_type" val="3" onchange="moonGame.setConfig('animation_type',3)" type="radio" id="switch-animation-btn4"/>
                                            <label for="switch-animation-btn4" class="toggle toggle-radio">
                                                <div class="slider"></div>
                                            </label>
                                            <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                               fill="currentColor" viewBox="0 0 512 512" xml:space="preserve">
                                                        <g>
                                                        <g>
                                                        <path d="M512,106.834L511.999,0H405.165v15.261h80.78l-75.9,75.9c-43.138-40.457-99.141-62.658-158.562-62.658
                                                              c-0.004,0,0.001,0-0.003,0c-61.966,0-120.232,24.133-164.052,67.948L69.05,114.833l10.792,10.791l18.379-18.381
                                                              c1.279-1.279,2.571-2.539,3.876-3.783l37.116,37.116l10.791-10.791l-36.473-36.473c38.748-32.085,87.006-49.549,137.952-49.548
                                                              c55.344,0,107.512,20.617,147.764,58.194l-10.791,10.791c-26.309-24.401-58.52-41.27-93.616-48.949
                                                              c-36.57-8.002-74.547-5.747-109.823,6.519l5.013,14.415c32.615-11.341,67.73-13.424,101.548-6.024
                                                              c32.238,7.054,61.837,22.511,86.071,44.848L260.714,240.494l-96.318-96.318l-10.791,10.791l96.318,96.318l-50.672,50.672h-99.204
                                                              L0,402.005h109.995L109.994,512l100.048-100.047l-0.001-99.203l50.673-50.673l147.819,147.82
                                                              c-1.243,1.305-2.504,2.597-3.783,3.876l-18.38,18.379l10.792,10.791l18.38-18.379c43.819-43.821,67.951-102.082,67.95-164.054
                                                              c-0.001-59.418-22.199-115.419-62.654-158.558l75.9-75.899v80.782H512z M36.844,386.744v-0.001l27.131-27.13h77.621
                                                              l-27.131,27.131H36.844z M125.256,475.156v-77.621l27.131-27.131l0.001,77.62L125.256,475.156z M79.237,344.351l27.132-27.132
                                                              h77.621l-27.132,27.132H79.237z M167.649,432.763l-0.001-77.621l27.132-27.132l0.001,77.621L167.649,432.763z M468.23,260.51
                                                              c0.001,50.946-17.463,99.203-49.549,137.951L271.506,251.285L388.442,134.35c22.334,24.233,37.789,53.829,44.843,86.066
                                                              c7.4,33.82,5.317,68.934-6.024,101.548l14.415,5.013c12.266-35.275,14.52-73.252,6.518-109.823
                                                              c-7.679-35.094-24.546-67.302-48.944-93.611l10.792-10.791C447.615,153.002,468.229,205.169,468.23,260.51z"/>                                                                                        </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                                                    Cupid
                                            </span>
                                        </li>
    
                                        <li class="header__menu-item">
                                            <input name="switch-animation-btn" configkey="animation_type" val="4" onchange="moonGame.setConfig('animation_type',4)" type="radio" id="switch-animation-btn5"/>
                                            <label for="switch-animation-btn5" class="toggle toggle-radio">
                                                <div class="slider"></div>
                                            </label>
                                            <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                         viewBox="0 0 512 512" fill="currentColor" xml:space="preserve">
                                                    <g>
                                                    <g>
                                                    <g>
                                                    <path d="M247.467,119.467c4.71,0,8.533-3.823,8.533-8.533V68.267h42.667c0.93,0,3.166,1.212,4.352,3.157l4.318,23.953
                                                          c0.836,4.634,5.222,7.723,9.907,6.886c4.642-0.836,7.723-5.274,6.886-9.907l-4.531-25.173c-0.119-0.666-0.316-1.297-0.58-1.911
                                                          c-3.567-8.158-12.126-14.071-20.352-14.071H256c-9.412,0-17.067,7.842-17.067,17.493v42.24
                                                          C238.933,115.644,242.756,119.467,247.467,119.467z"/>
                                                    <path d="M214.861,290.27c-70.246-12.766-138.018-30.097-165.88-68.403H76.8c4.71,0,8.533-3.823,8.533-8.533
                                                          S81.51,204.8,76.8,204.8H8.533c-4.71,0-8.533,3.823-8.533,8.533s3.823,8.533,8.533,8.533h20.352
                                                          c28.459,52.873,109.022,71.757,182.921,85.197c0.521,0.094,1.033,0.137,1.536,0.137c4.045,0,7.637-2.884,8.388-7.006
                                                          C222.566,295.561,219.494,291.115,214.861,290.27z"/>
                                                    <path d="M503.467,230.4h-8.533v-51.2c0-4.71-3.823-8.533-8.533-8.533s-8.533,3.823-8.533,8.533v51.2H460.8v-17.067
                                                          c0-22.596-16.794-40.371-38.818-42.291c-10.53-10.027-57.694-51.575-116.489-51.575c-54.537,0-74.948,36.147-81.092,51.2H98.261
                                                          l-40.115-56.158c-0.273-0.384-0.58-0.742-0.913-1.075c-10.197-10.206-16.495-11.034-23.1-11.034
                                                          c-9.25,0-17.067,7.817-17.067,17.067V179.2c0,4.71,3.823,8.533,8.533,8.533c4.71,0,8.533-3.823,8.533-8.533v-59.733
                                                          c3.132,0,4.898,0,10.539,5.547l42.249,59.145c1.604,2.244,4.19,3.576,6.946,3.576h133.094
                                                          c7.381,6.144,27.631,17.067,78.532,17.067c57.813,0,104.44-13.995,113.681-16.964c13.841,0.538,24.559,11.503,24.559,25.498v51.2
                                                          c0,14.831-10.769,25.6-25.6,25.6h-62.131l2.364-24.789c0.444-4.693-2.995-8.858-7.688-9.31c-4.745-0.435-8.858,2.995-9.31,7.689
                                                          L324.48,441.105c-1.169,1.544-3.174,2.628-4.216,2.628H256V273.067h59.733c4.71,0,8.533-3.823,8.533-8.533
                                                          S320.444,256,315.733,256h-68.267c-4.71,0-8.533,3.823-8.533,8.533v179.2c0,9.412,7.654,17.067,17.067,17.067h64.265
                                                          c8.158,0,16.691-5.683,20.292-13.508c0.401-0.87,0.649-1.792,0.742-2.748L354.372,307.2h63.761
                                                          c24.329,0,42.667-18.338,42.667-42.667v-17.067h17.067v51.2c0,4.71,3.823,8.533,8.533,8.533s8.533-3.823,8.533-8.533v-51.2h8.533
                                                          c4.71,0,8.533-3.823,8.533-8.533C512,234.223,508.177,230.4,503.467,230.4z M364.518,182.844l1.51-3.029
                                                          c2.116-4.215,0.401-9.335-3.814-11.452c-4.233-2.116-9.344-0.393-11.452,3.823l-6.682,13.372
                                                          c-12.032,1.297-24.977,2.176-38.588,2.176c-40.951,0-58.718-7.526-65.135-11.375c4.727-11.665,20.736-39.825,65.135-39.825
                                                          c41.515,0,77.824,24.559,95.377,38.775C392.021,177.604,379.452,180.446,364.518,182.844z"/>
                                                    <path d="M418.133,273.067c4.71,0,8.533-3.823,8.533-8.533S422.844,256,418.133,256H384c-4.71,0-8.533,3.823-8.533,8.533
                                                          s3.823,8.533,8.533,8.533H418.133z"/></g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                                    <g></g><g></g><g></g><g></g><g></g><g></g>
                                                    </svg>
                                                    Plane
                                            </span>
                                        </li>
    
                                        <li class="header__menu-item">
                                            <input name="switch-animation-btn" configkey="animation_type" val="0" onchange="moonGame.setConfig('animation_type',0)" type="radio" id="switch-animation-btn3"/>
                                            <label for="switch-animation-btn3" class="toggle toggle-radio">
                                                <div class="slider"></div>
                                            </label>
                                            <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-slash"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                                    <b translate="off">Off</b>
                                            </span>
                                        </li>
                                    </div>
                                    <hr />
                                <?php endif; ?>
                            <!-- MUSIC -->
                            <div class="header__menu-group">
                                <li class="header__menu-item">
                                    <span class="header__menu-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-music"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                                            <b translate="music">Music</b>
                                    </span>
                                </li>
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="1" onchange="moonGame.setConfig('musicbgnum',1)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num1"/>
                                    <label for="switch-music-btn-num1" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-slack"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"></path><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"></path><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"></path><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"></path><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"></path><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"></path><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"></path></svg>
                                            Space
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="2" onchange="moonGame.setConfig('musicbgnum',2)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num2"/>
                                    <label for="switch-music-btn-num2" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-life-buoy"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                                            Relax
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="3" onchange="moonGame.setConfig('musicbgnum',3)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num3"/>
                                    <label for="switch-music-btn-num3" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-aperture"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>
                                            Alien
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="4" onchange="moonGame.setConfig('musicbgnum',4)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num4"/>
                                    <label for="switch-music-btn-num4" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-slack"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"></path><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"></path><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"></path><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"></path><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"></path><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"></path><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"></path></svg>
                                            Bells
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="5" onchange="moonGame.setConfig('musicbgnum',5)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num5"/>
                                    <label for="switch-music-btn-num5" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-life-buoy"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                                            Jazz
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="6" onchange="moonGame.setConfig('musicbgnum',6)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num6"/>
                                    <label for="switch-music-btn-num6" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-aperture"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>
                                            Arcade
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="7" onchange="moonGame.setConfig('musicbgnum',7)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num7"/>
                                    <label for="switch-music-btn-num7" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-life-buoy"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                                            Fun
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="8" onchange="moonGame.setConfig('musicbgnum',8)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num8"/>
                                    <label for="switch-music-btn-num8" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-life-buoy"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                                            Miracle
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="9" onchange="moonGame.setConfig('musicbgnum',9)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num9"/>
                                    <label for="switch-music-btn-num9" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-life-buoy"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                                            Love
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="10" onchange="moonGame.setConfig('musicbgnum',10)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num10"/>
                                    <label for="switch-music-btn-num10" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-life-buoy"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>
                                            Rocket
                                    </span>
                                </li>
    
                                <li class="header__menu-item">
                                    <input configkey="musicbgnum" val="0" onchange="moonGame.setConfig('musicbgnum',0)" type="radio" name="switch-music-btn-num" id="switch-music-btn-num0"/>
                                    <label for="switch-music-btn-num0" class="toggle toggle-radio">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link" onclick="this.previousElementSibling.click()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell-off"><path d="M13.73 21a2 2 0 0 1-3.46 0"></path><path d="M18.63 13A17.89 17.89 0 0 1 18 8"></path><path d="M6.26 6.26A5.86 5.86 0 0 0 6 8c0 7-3 9-3 9h14"></path><path d="M18 8a6 6 0 0 0-9.33-5"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                            <b translate="off">Off</b>
                                    </span>
                                </li>
                            </div>
                            <hr />
                            <?php endif; ?>
                            <div class="header__menu-group">
                                <li class="header__menu-item">
                                    <input checked configkey="playsound" onchange="moonGame.setConfig('playsound',+this.checked)" type="checkbox" id="switch-sounds-btn"/>
                                    <label for="switch-sounds-btn" class="toggle">
                                        <div class="slider"></div>
                                    </label>
                                    <span class="header__menu-link">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-volume-2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                                        <b translate="sounds">Sounds</b>
                                    </span>
                                </li>
                            </div>
                            <?php if(!arr::get($_GET,'no_close',0)): ?>
                            <hr />
                            <li class="header__menu-item" style="margin-top:16px;">
                                <a class="header__menu-link" href="javascript:closeGame()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    <b translate="close_game">Close game</b>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                        <!-- <div class="header__top-menu-btn header__chat">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <g clip-path="url(#clip0_2293_33956)">
                                    <path d="M7.9999 2.49538C8.98481 2.49663 9.94826 2.78146 10.7739 3.31566C11.5994 3.84976 12.2516 4.61031 12.6517 5.50531C13.0518 6.40021 13.1826 7.39121 13.0282 8.35857C12.8739 9.32579 12.4411 10.2279 11.782 10.9558C11.1231 11.6837 10.2661 12.2061 9.31492 12.4599C8.36358 12.7137 7.35883 12.6881 6.42187 12.386L6.06271 12.2695L5.72671 12.4436L4.84112 12.9019L4.86555 12.0059L4.87713 11.595L4.57203 11.3185C3.80953 10.6306 3.27439 9.72932 3.03699 8.73313C2.79958 7.73688 2.87105 6.6926 3.24207 5.7377C3.61318 4.7827 4.26631 3.96181 5.11554 3.38318C5.96486 2.80444 6.97032 2.49477 7.9999 2.49538ZM8.00008 1.59961C6.78908 1.59926 5.60629 1.96345 4.60754 2.64416C3.60854 3.32487 2.8402 4.29034 2.40355 5.41354C1.9669 6.53667 1.88231 7.76509 2.16093 8.93697C2.43955 10.1089 3.06843 11.1694 3.96473 11.9793L3.89775 14.3996L6.14268 13.2386C7.24583 13.5958 8.42935 13.6273 9.55016 13.3292C10.6709 13.0312 11.6807 12.4166 12.4572 11.5595C13.2338 10.7025 13.7439 9.64012 13.9258 8.50076C14.1077 7.36138 13.9534 6.19402 13.4818 5.14007C13.0101 4.08615 12.2413 3.19079 11.2684 2.56247C10.2955 1.93408 9.16009 1.59961 8.00008 1.59961Z" fill="#0E0A1A"/>
                                    <path d="M5.30256 14.0611L5.3023 14.061C4.43965 14.381 3.35503 14.6597 2.67709 14.6444C2.17534 14.6324 1.87017 14.104 2.11471 13.6655C2.55807 12.8728 2.67412 12.226 2.34405 11.5399L2.34375 11.54L2.34405 11.5399L1.82339 10.4779C1.51874 9.70594 1.34408 8.84665 1.34408 7.9987C1.34408 4.31688 4.3286 1.33236 8.01042 1.33236C11.6922 1.33236 14.6767 4.31688 14.6767 7.9987C14.6767 11.6805 11.6922 14.665 8.01042 14.665C7.12247 14.665 6.09785 14.4284 5.30256 14.0611ZM6.01075 7.9987C6.01075 7.63051 5.71193 7.3317 5.34375 7.3317C4.97557 7.3317 4.67675 7.63051 4.67675 7.9987C4.67675 8.36688 4.97557 8.6657 5.34375 8.6657C5.71193 8.6657 6.01075 8.36688 6.01075 7.9987ZM8.67742 7.9987C8.67742 7.63051 8.3786 7.3317 8.01042 7.3317C7.64223 7.3317 7.34342 7.63051 7.34342 7.9987C7.34342 8.36688 7.64223 8.6657 8.01042 8.6657C8.3786 8.6657 8.67742 8.36688 8.67742 7.9987ZM11.3441 7.9987C11.3441 7.63051 11.0453 7.3317 10.6771 7.3317C10.3089 7.3317 10.0101 7.63051 10.0101 7.9987C10.0101 8.36688 10.3089 8.6657 10.6771 8.6657C11.0453 8.6657 11.3441 8.36688 11.3441 7.9987Z" fill="#81819C" stroke="#81819C" stroke-width="0.000666667"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_2293_33956">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div> -->
                    </div>
                </div>
            </div>
        </header>
        <main id="loading">
            <div class="container">
                <div class="flex content-wrap">
                    <div class="flex content-top">
                        <div class="content-top__history">
                            <button class="btn-history">
                                <?php if($game->name=='aerobet'): ?>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <mask id="path-1-inside-1_182_59824" fill="white">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22ZM11.25 7.03052V11.6643C11.25 12.3995 11.5444 13.1041 12.0675 13.6208L13.9729 15.5029L15.0271 14.4358L13.1216 12.5536C12.8838 12.3188 12.75 11.9985 12.75 11.6643V7.03052H11.25Z"/>
                                        </mask>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22ZM11.25 7.03052V11.6643C11.25 12.3995 11.5444 13.1041 12.0675 13.6208L13.9729 15.5029L15.0271 14.4358L13.1216 12.5536C12.8838 12.3188 12.75 11.9985 12.75 11.6643V7.03052H11.25Z" fill="#81819C"/>
                                        <path d="M11.25 7.03052V5.53052H9.75V7.03052H11.25ZM12.0675 13.6208L11.0133 14.6879H11.0133L12.0675 13.6208ZM13.9729 15.5029L12.9188 16.5701L13.986 17.6242L15.0401 16.5571L13.9729 15.5029ZM15.0271 14.4358L16.0942 15.4899L17.1483 14.4227L16.0812 13.3686L15.0271 14.4358ZM13.1216 12.5536L14.1757 11.4864L14.1757 11.4864L13.1216 12.5536ZM12.75 7.03052H14.25V5.53052H12.75V7.03052ZM20.5 12C20.5 16.6944 16.6944 20.5 12 20.5V23.5C18.3513 23.5 23.5 18.3513 23.5 12H20.5ZM12 3.5C16.6944 3.5 20.5 7.30558 20.5 12H23.5C23.5 5.64873 18.3513 0.5 12 0.5V3.5ZM3.5 12C3.5 7.30558 7.30558 3.5 12 3.5V0.5C5.64873 0.5 0.5 5.64873 0.5 12H3.5ZM12 20.5C7.30558 20.5 3.5 16.6944 3.5 12H0.5C0.5 18.3513 5.64873 23.5 12 23.5V20.5ZM12.75 11.6643V7.03052H9.75V11.6643H12.75ZM13.1216 12.5536C12.8838 12.3188 12.75 11.9985 12.75 11.6643H9.75C9.75 12.8005 10.205 13.8895 11.0133 14.6879L13.1216 12.5536ZM15.0271 14.4358L13.1216 12.5536L11.0133 14.6879L12.9188 16.5701L15.0271 14.4358ZM13.9599 13.3817L12.9058 14.4488L15.0401 16.5571L16.0942 15.4899L13.9599 13.3817ZM12.0675 13.6208L13.9729 15.5029L16.0812 13.3686L14.1757 11.4864L12.0675 13.6208ZM11.25 11.6643C11.25 12.3995 11.5444 13.1041 12.0675 13.6208L14.1757 11.4864C14.2232 11.5334 14.25 11.5975 14.25 11.6643H11.25ZM11.25 7.03052V11.6643H14.25V7.03052H11.25ZM11.25 8.53052H12.75V5.53052H11.25V8.53052Z" fill="#81819C" mask="url(#path-1-inside-1_182_59824)"/>
                                    </svg>
                                <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewbox="0 0 15 14">
                                <path fill-rule="nonzero" d="M7.993.669c-2.45 0-4.62 1.33-5.74 3.36l-1.61-1.61v4.55h4.55l-1.96-1.96c.91-1.75 2.66-2.94 4.76-2.94 2.87 0 5.25 2.38 5.25 5.25s-2.38 5.25-5.25 5.25c-2.31 0-4.2-1.47-4.97-3.5h-1.47c.77 2.8 3.36 4.9 6.44 4.9 3.71 0 6.65-3.01 6.65-6.65 0-3.64-3.01-6.65-6.65-6.65zm-1.05 3.5v3.57l3.29 1.96.56-.91-2.8-1.68v-2.94h-1.05z"/>
                                </svg>
                                <?php endif; ?>
                            </button>
                            <div class="flex history-wrap" id="rate_history">
                            </div>
                        </div>
                        <div class="content-top__play-screen">
                            <div style="width:100%;height:100%;position:absolute;z-index:0;">
                                <div style="width:100%;display: flex;overflow: hidden;height:100%;align-items: center;justify-content: center; position: relative">
                                    <div id="chartagt" class="stripe">
                                        <h1 id="rate" style="position: absolute; z-index: 1; bottom: <?php echo $game->name=='aerobet'?'0':'40%'; ?>; font-size:12vmin;color:#fff;display: table-cell;vertical-align: middle;"></h1>
                                    </div>
<!--                                    <div class="snippet" data-title=".dot-floating">
                                        <div class="stage">
                                            <div class="dot-floating"></div>
                                        </div>
                                    </div>-->
                                </div>
                                <div class="content-top__text-h" style="width:100%;height:100%;position:absolute;top:0;left:0;display:table;text-align: center;">
                                    <!-- <h1 id="rate" style="font-size:12vmin;color:#fff;display: table-cell;vertical-align: middle;"></h1> -->
                                </div>
                                <div class="container container-loading">
                                    <div id="main-loading" class="Loading" style="display: none;">
                                        <span data-charge='100'></span>
                                    </div>
                                </div>
                                <span class="round-label"></span>
                            </div>
                        </div>
                        <?php $currency=auth::user()->office->currency; ?>
                        <div class=" flex content-top__control-wrap">
                            <div class="flex content-top__control-panel control-panel-1">
                                <button data-wenk-pos="left" class="btn-ctrl-panel-visibility" id="btn-ctrl-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewbox="0 0 12 12">
                                    <path d="M 6 0 L 6 12 M 0 6 L 12 6" stroke-width="2"/>
                                    </svg>
                                </button>
                                <div class="flex content-top__control-panel-inner">
                                    <div class="flex bet-auto-group">
                                        <div class="flex control-panel__cash-out-ctrl control-panel__cash-out-ctrl-1" data-target="panel-one">
                                            <label class="auto-cash-label" translate="auto_cash_out" for="auto-cash-out-1">Auto Cash Out</label>
                                            <input class="auto-cash-switch" type="checkbox" value="1" name="auto-cash-out-1" id="auto-cash-out-1">
                                            <div class="auto-cash-wrapper auto-cash-input">
                                                <button class="bet-increment number-minus auto-cash-out-inc" style="display: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                        <path d="M 0 5 L 10 5" stroke-width="2" />
                                                    </svg>
                                                </button>
                                                <input class="number-input auto-cash-input wenk-align--center wenk-length--small"
                                                       required="required"
                                                       type="text"
                                                       inputmode="decimal"
                                                       name="auto-cash-input-1"
                                                       id="auto-cash-input-1"
                                                       value="1.20"
                                                       min="1.20"
                                                       max="9999"
                                                       step="0.01"
                                                       data-wenk-pos="top"
                                                       onkeydown="this.forrevert = '';
                                                               return event.key != 'Enter';">
                                                <button class="bet-increment number-plus auto-cash-out-inc" style="display: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                        <path d="M 0 5 L 10 5 M 5 0 L 5 10" stroke-width="2" />
                                                    </svg>
                                                </button>
                                            </div>
                                                <!--                      <button class="auto-cash-input-reset" type="reset" disabled>
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 12 12">
                                                                          <path d="M 1 1 L 9 9 M 9 1 L 1 9" stroke-width="2" />
                                                                        </svg>
                                                                      </button>-->
                                            <!--</form>-->
                                        </div>
                                        <button class="btn-main btn-main--bet" name="bet_btn" translate="bet_button" bet_state="bet" value="1">BET</button>
                                        <span class="rate-btn"></span>
                                    </div>
                                    <div class="flex bet-control-wrap">
                                        <div class="tabs-bet-auto" data-path="panel-one">
                                            <input type="radio" id="tabs-bet-auto__radio-1" name="tabs" num="1" value="spin" checked />
                                            <input type="radio" id="tabs-bet-auto__radio-2" name="tabs" num="1" value="auto" />
                                            <span class="tabs-bet-auto__active"></span>
                                            <label class="tabs-bet-auto__tab tabs-bet-auto__tab-1" translate="<?php if($game->name=='aerobet'): ?>auto_button<?php else: ?>bet_button<?php endif; ?>" for="tabs-bet-auto__radio-1"><?php if($game->name=='aerobet'): ?>Auto<?php else: ?>Bet<?php endif; ?></label>
                                            <label class="tabs-bet-auto__tab tabs-bet-auto__tab-2" translate="auto_button" for="tabs-bet-auto__radio-2">Auto</label>
                                            <span class="tabs-bet-auto__glider"></span>
                                        </div>
                                        <div class="flex bet-wrap">
                                            <button type="button" class="bet-increment number-minus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                <path d="M 0 5 L 10 5" stroke-width="2" />
                                                </svg>
                                            </button>
                                            <input class="number-input bet-input"
                                                   required="required"
                                                   type="text"
                                                   inputmode="decimal"
                                                   name="bet-input-1"
                                                   id="bet-input-1"
                                                   
                                                    <?php if($game->name=='aerobet'): ?>
														realvalue="<?php echo $minBet; ?>"
                                                       value="<?php echo th::number_format($minBet,'.',$currency->mult).' '.$currency->code; ?>"
													   step="<?php echo $minBet; ?>"
                                                    <?php else: ?>
													realvalue="<?php echo $currency->formatBet(10,true,true); ?>"
                                                       value="<?php echo $currency->formatBet(10,true); ?>"
													   step="<?php echo 1/pow(10,$currency->mult); ?>"
                                                    <?php endif; ?>
                                                   
                                           min="<?php echo $minBet; ?>"
                                           max="<?php echo $maxBet; ?>"
                                                    autocomplete="off"
                                                   mode="<?php echo $game->name=='aerobet'?'plus':'set'; ?>"
                                                    onkeydown="return event.key != 'Enter';">
                                            <button type="button" class="bet-increment number-plus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                <path d="M 0 5 L 10 5 M 5 0 L 5 10" stroke-width="2" />
                                                </svg>
                                            </button>
                                            <div class="flex fs-panel-wrap" data-proc="23">
                                                <div class="fs-header-status"></div>
                                                <div class="fs-status"></div>
                                            </div>
                                            <div class="flex btns-bet-wrap">
                                                <?php foreach($bets as $bV): ?>
                                            <button type="button" class="btn-multi" data-ctrl="1"
                                                    <?php if($game->name=='aerobet'): ?>
                                                    data-number="<?php echo $currency->formatBet($bV/$currency->moon_min_bet/10,true,true); ?>"
                                                <?php else: ?>
                                                    data-number="<?php echo $currency->formatBet($bV,true,true); ?>"
                                                <?php endif; ?>
                                            >
                                                <?php if($game->name=='aerobet'): ?>
                                                    <?php echo '+ '.$currency->formatBet($bV/$currency->moon_min_bet/10); ?>
                                                <?php else: ?>
                                                    <?php echo $currency->formatBet($bV); ?>
                                                <?php endif; ?>
                                            </button>
                                        <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex content-top__control-panel control-panel-2<?php if($game->name!='aerobet'): ?> is-hidden<?php endif; ?>">
                                <button data-wenk-pos="left" class="btn-ctrl-panel-visibility" id="btn-ctrl-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewbox="0 0 12 12">
                                    <path d="M 0 6 L 12 6" stroke-width="2"/>
                                    </svg>
                                </button>
                                <button data-wenk-pos="left" class="btn-ctrl-panel-visibility is-hidden" id="btn-ctrl-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewbox="0 0 12 12">
                                    <path d="M 6 0 L 6 12 M 0 6 L 12 6" stroke-width="2"/>
                                    </svg>
                                </button>
                                <div class="flex content-top__control-panel-inner">
                                    <div class="flex bet-auto-group">

                                        <div class="flex control-panel__cash-out-ctrl control-panel__cash-out-ctrl-1" data-target="panel-two">
                                            <label class="auto-cash-label" translate="auto_cash_out" for="auto-cash-out-2">Auto Cash Out</label>
                                            <input class="auto-cash-switch" type="checkbox" value="2" name="auto-cash-out-2" id="auto-cash-out-2">
                                            <div class="auto-cash-wrapper auto-cash-input">
                                                <button class="bet-increment number-minus auto-cash-out-inc" style="display: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                        <path d="M 0 5 L 10 5" stroke-width="2" />
                                                    </svg>
                                                </button>
                                                <input class="number-input auto-cash-input wenk-align--center wenk-length--small"
                                                       required="required"
                                                       type="text"
                                                       inputmode="decimal"
                                                       name="auto-cash-input-2"
                                                       id="auto-cash-input-2"
                                                       value="1.20"
                                                       step="0.01"
                                                       min="1.20"
                                                       max="9999"
                                                       data-wenk-pos="top"
                                                       onkeydown="this.forrevert = '';
                                                               return event.key != 'Enter';">
                                                <button class="bet-increment number-plus auto-cash-out-inc" style="display: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                        <path d="M 0 5 L 10 5 M 5 0 L 5 10" stroke-width="2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <button class="btn-main btn-main--bet" translate="bet_button" name="bet_btn" bet_state="bet" value="2">BET</button>
                                        <span class="rate-btn"></span>
                                    </div>
                                    <div class="flex bet-control-wrap">
                                        <div class="tabs-bet-auto" data-path="panel-two">
                                            <input type="radio" id="tabs-bet-auto__radio-3" name="tabs2" num="2" value="spin" checked />
                                            <input type="radio" id="tabs-bet-auto__radio-4" name="tabs2" num="2" value="auto" />
                                            <span class="tabs-bet-auto__active"></span>
                                            <label class="tabs-bet-auto__tab tabs-bet-auto__tab-3" translate="<?php if($game->name=='aerobet'): ?>auto_button<?php else: ?>bet_button<?php endif; ?>" for="tabs-bet-auto__radio-3"><?php if($game->name=='aerobet'): ?>Auto<?php else: ?>Bet<?php endif; ?></label>
                                            <label class="tabs-bet-auto__tab tabs-bet-auto__tab-4" translate="auto_button" for="tabs-bet-auto__radio-4">Auto</label>
                                            <span class="tabs-bet-auto__glider"></span>
                                        </div>
                                        <div class="flex bet-wrap">
                                            <button class="bet-increment number-minus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                <path d="M 0 5 L 10 5" stroke-width="2" />
                                                </svg>
                                            </button>
                                            <input class="number-input bet-input"
                                                   required="required"
                                                   type="text"
                                                   inputmode="decimal"
                                                   name="bet-input-2"
                                                   id="bet-input-2"
                                                   
                                                <?php if($game->name=='aerobet'): ?>
													realvalue="<?php echo $minBet; ?>"
                                                    value="<?php echo th::number_format($minBet,'.',$currency->mult).' '.$currency->code; ?>"
													step="<?php echo $minBet; ?>"
                                                <?php else: ?>
													realvalue="<?php echo $currency->formatBet(10,true,true); ?>"
                                                    value="<?php echo $currency->formatBet(10,true); ?>"
													step="<?php echo 1/pow(10,$currency->mult); ?>"
                                                <?php endif; ?>
                                                   
                                           min="<?php echo $minBet; ?>"
                                           max="<?php echo $maxBet; ?>"
                                                    autocomplete="off"
                                                   mode="<?php echo $game->name=='aerobet'?'plus':'set'; ?>"
                                                    onkeydown="return event.key != 'Enter';">
                                            <button class="bet-increment number-plus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                <path d="M 0 5 L 10 5 M 5 0 L 5 10" stroke-width="2" />
                                                </svg>
                                            </button>
                                            <div class="flex fs-panel-wrap">
                                                <div class="fs-header-status"></div>
                                                <div class="fs-status"></div>
                                            </div>
                                            <div class="flex btns-bet-wrap">

                                                <?php foreach($bets as $bV): ?>
                                            <button type="button" class="btn-multi" data-ctrl="2"
                                                    <?php if($game->name=='aerobet'): ?>
                                                    data-number="<?php echo $currency->formatBet($bV/$currency->moon_min_bet/10,true,true); ?>"
                                                <?php else: ?>
                                                    data-number="<?php echo $currency->formatBet($bV,true,true); ?>"
                                                <?php endif; ?>
                                            >
                                                <?php if($game->name=='aerobet'): ?>
                                                    <?php echo '+ '.$currency->formatBet($bV/$currency->moon_min_bet/10); ?>
                                                <?php else: ?>
                                                    <?php echo $currency->formatBet($bV); ?>
                                                <?php endif; ?>
                                            </button>
                                        <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex content-top__control-panel control-panel-3 is-hidden">
                                <button data-wenk-pos="left" class="btn-ctrl-panel-visibility " id="btn-ctrl-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewbox="0 0 12 12">
                                    <path d="M 0 6 L 12 6" stroke-width="2"/>
                                    </svg>
                                </button>
                                <div class="flex content-top__control-panel-inner">
                                    <div class="flex bet-auto-group">

                                        <div class="flex control-panel__cash-out-ctrl control-panel__cash-out-ctrl-3" data-target="panel-three">
                                            <label class="auto-cash-label" translate="auto_cash_out" for="auto-cash-out-3">Auto Cash Out</label>
                                            <input class="auto-cash-switch" type="checkbox" value="3" name="auto-cash-out-3" id="auto-cash-out-3">
                                            <!--<form data-wenk-pos="top" class="control-panel__cash-out-form wenk-align--center wenk-length--small">-->
                                                <input class="number-input auto-cash-input wenk-align--center wenk-length--small"
                                                       required="required"
                                                       type="text"
                                                       inputmode="decimal"
                                                       name="auto-cash-input-3"
                                                       id="auto-cash-input-3"
                                                       value="1.20"
                                                       step="0.01"
                                                       min="1.20"
                                                       data-wenk-pos="top"
                                                       onkeydown="this.forrevert = '';
                                                               return event.key != 'Enter';">
                                                <!--                      <button class="auto-cash-input-reset" type="reset" disabled>
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 12 12">
                                                                          <path d="M 1 1 L 9 9 M 9 1 L 1 9" stroke-width="2" />
                                                                        </svg>
                                                                      </button>-->
                                            <!--</form>-->
                                        </div>
                                        <button class="btn-main btn-main--bet" name="bet_btn" translate="bet_button" bet_state="bet" value="3">BET</button>
                                    </div>
                                    <div class="flex bet-control-wrap">
                                        <div class="tabs-bet-auto" data-path="panel-three">
                                            <input type="radio" id="tabs-bet-auto__radio-5" name="tabs3" num="3" value="spin" checked />
                                            <input type="radio" id="tabs-bet-auto__radio-6" name="tabs3" num="3" value="auto" />
                                            <span class="tabs-bet-auto__active"></span>
                                            <label class="tabs-bet-auto__tab tabs-bet-auto__tab-5" translate="<?php if($game->name=='aerobet'): ?>auto_button<?php else: ?>bet_button<?php endif; ?>" for="tabs-bet-auto__radio-5"><?php if($game->name=='aerobet'): ?>Auto<?php else: ?>Bet<?php endif; ?></label>
                                            <label class="tabs-bet-auto__tab tabs-bet-auto__tab-6" translate="auto_button" for="tabs-bet-auto__radio-6">Auto</label>
                                            <span class="tabs-bet-auto__glider"></span>
                                        </div>
                                        <div class="flex bet-wrap">
                                            <button class="bet-increment number-minus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                <path d="M 0 5 L 10 5" stroke-width="2" />
                                                </svg>
                                            </button>
                                            <input class="number-input bet-input"
                                                   required="required"
                                                   type="text"
                                                   inputmode="decimal"
                                                   name="bet-input-3"
                                                   id="bet-input-3"
                                                   realvalue="<?php echo $currency->formatBet(10,true,true); ?>"
                                                <?php if($game->name=='aerobet'): ?>
                                                    value="<?php echo $currency->formatBet(10,true).' '.$currency->code; ?>"
                                                <?php else: ?>
                                                    value="<?php echo $currency->formatBet(10,true); ?>"
                                                <?php endif; ?>
                                                   step="<?php echo 1/pow(10,$currency->mult); ?>"
                                           min="<?php echo $minBet; ?>"
                                           max="<?php echo $maxBet; ?>"
                                                    autocomplete="off"
                                                   mode="<?php echo $game->name=='aerobet'?'plus':'set'; ?>"
                                                    onkeydown="return event.key != 'Enter';">
                                            <button class="bet-increment number-plus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewbox="0 0 10 10">
                                                <path d="M 0 5 L 10 5 M 5 0 L 5 10" stroke-width="2" />
                                                </svg>
                                            </button>
                                            <div class="flex fs-panel-wrap">
                                                <div class="fs-header-status"></div>
                                                <div class="fs-status"></div>
                                            </div>
                                            <div class="flex btns-bet-wrap">
                                                <button type="button" class="btn-multi" data-ctrl="3"
                                                        data-number="<?php echo $currency->formatBet(10,true); ?>">
                                                        <?php echo $currency->formatBet(10); ?>
                                                </button>
                                                <button type="button" class="btn-multi" data-ctrl="3"
                                                        data-number="<?php echo $currency->formatBet(20,true); ?>">
                                                        <?php echo $currency->formatBet(20); ?>
                                                </button>
                                                <button type="button" class="btn-multi" data-ctrl="3"
                                                        data-number="<?php echo $currency->formatBet(50,true); ?>">
                                                        <?php echo $currency->formatBet(50); ?>
                                                </button>
                                                <button type="button" class="btn-multi" data-ctrl="3"
                                                        data-number="<?php echo $currency->formatBet(100,true); ?>">
                                                        <?php echo $currency->formatBet(100); ?>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex content-bottom">
                        <div class="bet-switch">
                            <input type="radio" id="bet-switch__radio-1" name="resswitch" checked />
                            <input type="radio" id="bet-switch__radio-2" name="resswitch" />
                            <input type="radio" id="bet-switch__radio-4" name="resswitch" />
                            <input type="radio" id="bet-switch__radio-3" name="resswitch" />
                            <label class="bet-switch__tab bet-switch__tab-1" for="bet-switch__radio-1" translate="all_bets" data-path="allbets">All Bets</label>
                            <label class="bet-switch__tab bet-switch__tab-2" for="bet-switch__radio-2" translate="my_bets" data-path="mybets">My Bets</label>
                            <label class="bet-switch__tab bet-switch__tab-4" for="bet-switch__radio-4" translate="top" data-path="topbets">Top</label>
                            <?php if(!$game->branded): ?>
                            <label class="bet-switch__tab bet-switch__tab-3" for="bet-switch__radio-3" data-path="chat">Chat</label>
                            <?php endif; ?>
                            <span class="bet-switch__glider"></span>
                        </div>
                        <div class="content-middle__bet-total" style="display: none;">
                            <div class="bet-total-count">
                                <span class="bet-total-count-label" translate="all_moonbets_count">Total bets</span>
                                <span class="bet-total-count-number" id="bet-total-count-number">0</span>
                            </div>
                            <div class="bet-history-prev-area">
                                <button class="bet-history-prev-btn" id="bet-history-prev-btn" translate="moon_prev_round_btn">Previous round</button>
                            </div>
                        </div>
                        <div class="content-bottom__bet-list-wrap">
                            <ul class="bets-list">
                                <li class="flex any-bets" data-target="allbets">
                                    <div class="flex any-bets__header">
                                        <span class="header-item" translate="user">User</span>
                                        <span class="header-item" translate="bet">Bet</span>
                                        <span class="header-item" translate="multiplier">Mult.</span>
                                        <span class="header-item" translate="cash_out">Cash out</span>
                                    </div>
                                    <ul class="any-bets__list" id="allbet_history">
                                        <li class="flex any-bets__item divtemplate">
                                            <div class="flex bets-item bet-item-1"><div class="ava"></div>  d...2</div>
                                            <div class="flex bets-item"><div class="bet-item-2">100$</div></div>
                                            <div class="flex bets-item"><div class="bet-item-3">-</div></div>
                                            <div class="flex bets-item"><div class="bet-item-4">-</div></div>
                                        </li>
                                    </ul>
                                </li>
                                <li class="flex any-bets hidden" data-target="mybets">
                                    <div class="flex any-bets__header">
                                        <span class="header-item" translate="date">Date</span>
                                        <span class="header-item" translate="bet">Bet</span>
                                        <span class="header-item" translate="multiplier">Mult.</span>
                                        <span class="header-item" translate="cash_out">Cash out</span>
                                    </div>
                                    <ul class="any-bets__list" id="userbet_history">
                                        <li class="flex any-bets__item divtemplate">
                                            <div class="flex bets-item bet-item-1">11:26</div>
                                            <div class="flex bets-item">100$</div>
                                            <div class="flex bets-item"><div class="bet-item-3">1.53x</div></div>
                                            <div class="flex bets-item"><div class="bet-item-4">-</div></div>
                                        </li>
                                    </ul>
                                </li>
                                <li class="flex any-bets hidden" data-target="topbets">
                                    <div class="top-switch topwinrateall">
                                        <input type="radio" id="topwin-switch__radio-1" name="topswitch" checked />
                                        <input type="radio" id="toprate-switch__radio-2" name="topswitch" />
                                        <label class="top-switch__tab top-switch__tab-1" for="topwin-switch__radio-1" translate="top_wins" data-path="topwin">Top wins</label>
                                        <label class="top-switch__tab top-switch__tab-2" for="toprate-switch__radio-2" translate="top_rates" data-path="toprate">Top rates</label>
                                        <span class="top-switch__glider"></span>
                                    </div>
                                    <div class="top-switch topwinrate" data-target="topwin">
                                        <input type="radio" id="topwinday-switch__radio-1" name="topswitchr" checked />
                                        <input type="radio" id="topwinmonth-switch__radio-2" name="topswitchr" />
                                        <input type="radio" id="topwinyear-switch__radio-3" name="topswitchr" />
                                        <label class="top-switch__tab top-switch__tab-1" for="topwinday-switch__radio-1" translate="day" data-path="topwind">Day</label>
                                        <label class="top-switch__tab top-switch__tab-2" for="topwinmonth-switch__radio-2" translate="month" data-path="topwinm">Month</label>
                                        <label class="top-switch__tab top-switch__tab-3" for="topwinyear-switch__radio-3" translate="year" data-path="topwiny">Year</label>
                                        <span class="top-switch__glider"></span>
                                    </div>
                                    <div class="top-switch topwinrate hidden" data-target="toprate">
                                        <input type="radio" id="toprateday-switch__radio-1" name="topswitchw" checked />
                                        <input type="radio" id="topratemonth-switch__radio-2" name="topswitchw" />
                                        <input type="radio" id="toprateyear-switch__radio-3" name="topswitchw" />
                                        <label class="top-switch__tab top-switch__tab-1" for="toprateday-switch__radio-1" translate="day" data-path="toprated">Day</label>
                                        <label class="top-switch__tab top-switch__tab-2" for="topratemonth-switch__radio-2" translate="month" data-path="topratem">Month</label>
                                        <label class="top-switch__tab top-switch__tab-3" for="toprateyear-switch__radio-3" translate="year" data-path="topratey">Year</label>
                                        <span class="top-switch__glider"></span>
                                    </div>
                                </li>
                                <?php foreach(['win','rate'] as $top): ?>
                                    <?php foreach(['d','m','y'] as $period): ?>
                                    <li class="flex any-bets any-top hidden" data-target="top<?php echo $top.$period; ?>">
                                        <div class="flex any-bets__header">
                                            <span class="header-item" translate="date">Date</span>
                                            <span class="header-item" translate="<?php echo $top; ?>"><?php echo UTF8::strtoupper($top); ?></span>
                                        </div>
                                        <ul class="any-bets__list" id="toplist<?php echo $top.$period; ?>">
                                            <li class="flex any-bets__item divtemplate">
                                                <div class="flex bets-item bet-item-1">11:26</div>
                                                <div class="flex bets-item">100$</div>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php if(!$game->branded): ?>
                        <div class="chat" data-target="chat">
                            <div class="head">
                                Chat
                                <a class="arrow"></a>
                            </div>
                            <div class="chat-content">
                                <div class="chat-input">
                                    <div class="chat-input-left">
                                        <input autocomplete="off" type="text" id="chat-message" translate="message_text" name="message" placeholder="Message text">
                                    </div>
                                    <div class="chat-input-right">
                                        <input autocomplete="off" type="button" id="send-chat" translate="send" value="Send">
                                    </div>
                                </div>
                                <div class="chat-message" id="chat_history">
                                    <div class="chat-outer divtemplate">
                                        <div class="chat-date">01:53:55</div>
                                        <div class="chat-user">U****3:</div>
                                        <div class="chat-text">ok</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
        <footer class="footer">
            <div class="container">
                <ul class="flex futer__nav-list">
                    <li class="futer__nav-item">
                        <a id="bet-switch__link-1" href="#!" class="bet-switch futer__nav-link" translate="all_bets" data-path="allbets">All bets</a>
                    </li>
                    <li class="futer__nav-item">
                        <a id="bet-switch__link-2" href="#!" class="bet-switch futer__nav-link" translate="my_bets" data-path="mybets">My bets</a>
                    </li>
<!--                    <li class="futer__nav-item">
                        <a id="bet-switch__link-3" href="#!" class="bet-switch futer__nav-link" data-path="topbets">Top</a>
                    </li>-->
                    <?php if(!$game->branded): ?>
                    <li class="futer__nav-item">
                        <a href=" javascript:document.querySelector('.content-bottom').classList.toggle('chat-active'); " class="futer__nav-link">Chat</a>
                    </li>
                    <?php endif; ?>
<!--                    <li class="futer__nav-item">
                        <a href="#!" class="futer__nav-link">Statistics</a>
                    </li>-->
                </ul>
            </div>
        </footer>
        <div id="animation_popup">
            <div class="modal-box">
                <div class="modal-body">
                    <div class="animation-popup-content">
                        <div class="row">
                            <div class="col col-md-12 text-big" translate="moon_choose_anim" style="text-align: center;width: 100%;">
                                You can choose animation later inside menu
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-12" style="text-align: center;width: 100%;display: block;position: relative;">
                                <div class="hand-tooltip-img-div">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="container container-loading" style="position: relative;margin-top: 20px;">
                                <div id="animation-popup-loading" class="Loading">
                                    <span data-charge='100'></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-12 text-big" style="text-align: center;width: 100%;" translate="moon_choose_anim2">
                                Choose animation
                            </div>
                        </div>
                        <div class="row choose-tooltip">
                            <div class="col col-md-4" onclick="moonGame.setConfig('animation_type',7); moonGame.animationMusicGroups()">
                                <img width="120px" src="/games/agt/moon/img/icons/rocketfull.png" />
                                <br />
                                Rocket
                            </div>
                            <div class="col col-md-4" onclick="moonGame.setConfig('animation_type',1); moonGame.animationMusicGroups()">
                                <img width="120px" src="/games/agt/moon/img/icons/bitcoinfull.png" />
                                <br />
                                Bitcoin
                            </div>
                            <div class="col col-md-8" onclick="moonGame.setConfig('animation_type',2); moonGame.animationMusicGroups()">
                                <img width="120px" src="/games/agt/moon/img/icons/santafull.png" />
                                <br />
                                Santa
                            </div>
                            <div class="col col-md-8" onclick="moonGame.setConfig('animation_type',3); moonGame.animationMusicGroups()">
                                <img width="120px" src="/games/agt/moon/img/icons/cupidonfull.png" />
                                <br />
                                Cupid
                            </div>
                            <div class="col col-md-8" onclick="moonGame.setConfig('animation_type',4); moonGame.animationMusicGroups()">
                                <img width="120px" src="/games/agt/moon/img/icons/planefull.png" />
                                <br />
                                Plane
                            </div>
<!--                            <div class="col col-md-8" onclick="moonGame.setConfig('animation_type',0); moonGame.animationMusicGroups()">
                                <img width="120px" src="/games/agt/moon/img/icons/noanimation.png" />
                                <br />
                                Off
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="rules_popup">
            <div class="modal-box">
                <div class="modal-body">
                    <span class="modal-btn-close">X</span>
                    <?php $rules_path='site/agt/moonrules/tothemoon'; ?>
                    <?php if($game->name=='aerobet'): ?>
                        <?php $rules_path='site/agt/moonrules/aerobet'; ?>
                    <?php endif; ?>
                    <?php
                    $v=View::factory($rules_path);
                    $v->currency=$currency;
					$v->minBet=$minBet;
					$v->maxBet=$maxBet;
					$v->maxWin=$maxWin;
                    echo $v->render();
                    ?>
                </div>
            </div>
        </div>
        <div id="fs-popup" data-content="" style="display: none;">
            <div class="bg"></div>
            <div id="fs-popup-label"></div>
            <div id="fs-popup-yes" onmouseup="javascript:yesFS()"></div>
            <div id="fs-popup-no" onmouseup="javascript:noFS()"></div>
            <div id="fs-rules"></div>
            <div id="fs-popup-close" onmouseup="javascript:noFS()">
                <span></span>
            </div>
        </div>
        <div id="ls-panel" style="display:none;">
            <div class="container">
<!--                <div class="row">-->
<!--                    <div class="col-3">-->
<!--                        1-->
<!--                    </div>-->
<!--                    <div class="col-3">-->
<!--                        2-->
<!--                    </div>-->
<!--                    <div class="col-3">-->
<!--                        3-->
<!--                    </div>-->
<!--                    <div class="col-3">-->
<!--                        4-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="row">-->
<!--                    <div class="col-12">-->
<!--                        <hr style="margin:0">-->
<!--                    </div>-->
<!--                </div>-->
                <div class="rowgrbg lsgroup">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="text-center" style="margin-bottom: 0;color:#ff1e00;">LUCKY SPINS</h1>
                        </div>
                    </div>

                    <div class="row">

                    <div class="slidewrapper">
                        <div class="card divtemplate luckyspingame clickable" style="width: 9rem;--bs-card-border-width:0px;--bs-card-spacer-y:0">
                            <img src="https://content.site-domain.com/games/agt/sqthumb/aladdin.png" class="card-img-top" style="border-radius:0px">
                            <div class="card-body">
                                <p class="card-text text-center" style="line-height: 1.1em; font-size: 0.9rem; ">
                                    <span style="color:#ff1e00;font-weight:bold;font-size:0.6rem;height: 1rem;overflow: hidden;"></span>
                                    <span style="color:#363636;font-weight:bold;font-size:0.7rem"></span>
                                    <span style="color:#ffffff;background:#e55c00;font-weight:bold;font-size: 0.6rem;overflow: hidden;height: 0.9rem;"></span>
                                    <span style="color:#363636;font-weight:bold;font-size:0.6rem"></span>
                                    <span style="color:#363636;font-weight:bold;font-size:0.6rem"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <?php if(auth::user()->office->enable_bia>0): ?>
                <div class="rowgrbg fsbgroup">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="text-center" style="margin-bottom: 0;color:#ff1e00;">DAILY SPINS</h1>
                            <div class="text-center dailyspins">
                                <div>
                                    <span></span>
                                    <span style="background: #ffc000; padding: 2px 4px;color:#fff;"></span>
                                </div>
                                <span style="color:#ff1e00;padding:10px 10%;"></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!--<div class="rowgrbg fsgroup">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="text-center" style="margin-bottom: 0;color:#ff1e00;">AVAILABLE FREE SPINS</h1>
                            <div class="text-center freespins">
                                <div>
                                    <span></span>
                                    <span style="background: #ffc000; padding: 2px 4px;color:#fff;"></span>
                                </div>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>-->

                <div class="rowgrbg topwingroup">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="text-center" style="margin-bottom: 0;color:#ff1e00;">TOP WINS</h1>
                        </div>
                    </div>

                    <div class="row">

                        <div class="slidewrapper">
                            <div class="card divtemplate topwin clickable" style="width: 9rem;--bs-card-border-width:0px;--bs-card-spacer-y:0">
                                <img src="https://content.site-domain.com/games/agt/sqthumb/aladdin.png" class="card-img-top" style="border-radius:0px">
                                <div class="card-body">
                                    <p class="card-text text-center" style="line-height: 1.2em; font-size: 0.9rem; ">
                                        <span style="color:#ff1e00;font-weight:bold;font-size:0.6rem;height: 1rem;overflow: hidden;"></span>
                                        <span style="color:#363636;font-weight:bold;font-size:0.5rem;line-height: 0.5rem"></span>
                                        <span style="color:#ffffff;background:#e55c00;font-weight:bold;font-size: 0.8rem;overflow: hidden;height: 1.5rem;line-height:1.5rem"></span>
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="slidebtn">
            </div>
        </div>

        <style>
            .topwin.divtemplate,
            .luckyspingame.divtemplate {
                display: none !important;
            }
            .dailyspins {
                display: flex;
                flex-direction: column;
                font-weight: bold;
            }
            .rowgrbg {
                background: rgb(200,200,200);
                background: linear-gradient(0deg, rgba(200,200,200,1) 0%, rgba(255,255,255,1) 100%);
            }
            #ls-panel .container {
                padding: 0;
            }
            .row {
                /*display: flex;*/
                /*flex-wrap: wrap;*/
            }
            .slidewrapper {
                overflow: auto;
                display: flex;
            }
            .slidewrapper .card {
                flex: 1 0 50%;
                padding: 0 5px;
            }
            .card.topwin {
                padding-bottom: 50px;
            }
            /*.slidewrapper .card {*/
            /*    min-height: 275px;*/
            /*}*/
            /*.slidewrapper .card.slidesmall {*/
            /*    min-height: 240px;*/
            /*}*/
            .col-3 {
                display: inline-flex;
            }
            .card {
                position: relative;
                display: inline-flex;
                flex-direction: column;
                min-width: 0;
                word-wrap: break-word;
                max-width: 33%;
            }
            #ls-panel{
                position: fixed;
                z-index: 888;
                display: block;
                top: 40px;
                left: max(calc(-100vw + 25px),calc(-100vh + 30px));
                font-family: Roboto;
            }
            #ls-panel .container{
                max-width: min(calc(100vw - 25px),calc(100vh - 30px));
                min-width: min(calc(100vw - 25px),calc(100vh - 30px));
                float: left;
                background: rgba(255,255,255,0.9);
                min-height: 100vh;
                max-height: 100vh;
                overflow: auto;
            }
            @media screen and (orientation:landscape) {
                #ls-panel {
                    left: max(calc(-100vw + 30px),calc(-35vw + 30px));
                }
                #ls-panel .container{
                    max-width: min(calc(100vw - 30px),calc(35vw - 30px));
                    min-width: min(calc(100vw - 30px),calc(35vw - 30px));
                }
            }
            #ls-panel *::-webkit-scrollbar{
                display: none;
            }
            #ls-panel .slidebtn{
                display: block;
                width: 25px;
                height: 50px;
                border-radius: 0 25px 25px 0;
                background-color: rgba(255,255,255,0.9);
                float: left;
                /*margin-left: -0.5px;*/
                margin-top: calc(50vh - 22px);
            }
            #ls-panel .slidebtn::before{
                content: '';
                display: block;
                border: 2px solid #ff1e00;
                width: 2px;
                height: 22px;
                margin-left: 4px;
                margin-top: 13px;
                float: left;
                border-radius: 3px;
                background: #ff1e00;
            }
            #ls-panel .slidebtn::after{
                content: '';
                display: block;
                border: 2px solid #ff1e00;
                width: 2px;
                height: 16px;
                margin-left: 4px;
                margin-top: 16px;
                float: left;
                border-radius: 3px;
                background: #ff1e00;
            }
            .card p{
                display: flex;
                flex-direction: column;
            }
        </style>

        <!--      <h1 id="rate" > make a bet</h1>
              <h2 id="balance"></h2>
            <form id="makebet" action="">
              <input id="bet_amount"value="100" autocomplete="off" />
              <button id="bet_btn">Make bet</button>
            </form>-->

        <script src="js/lib/zepto.min.js?v=<?php echo th::ver(); ?>"></script>
        <script src="js/lib/phaser.min.js?v=<?php echo th::ver(); ?>"></script>
        <script src="js/lib/noty.min.js?v=<?php echo th::ver(); ?>"></script>
        <script src="js/lib/jsonpack.js?v=<?php echo th::ver(); ?>"></script>


        <?php if(KOHANA::$environment == KOHANA::DEVELOPMENT): ?>

        <script src="/games/agt/moon/js/drag.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/moon/js/bet-auto-switch.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/moon/js/bets-switch.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/moon/js/top-switch.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/moon/js/visibility-ctrl-panel.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/moon/js/add-bet.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/moon/js/history-open.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/moon/js/menu-open.js?v=<?php echo th::ver(); ?>"></script>
        <!--<script src="/games/agt/moon/js/demo-btn.js?v=<?php echo th::ver(); ?>"></script>-->

        <script src="/games/agt/js/util/math.js?v=<?php echo th::ver(); ?>"></script>

        <script src="/games/agt/js/mc/model.js?v=<?php echo th::ver(); ?>"></script>
        <script src="/games/agt/js/gameConstants.js?v=<?php echo th::ver(); ?>"></script>
        <?php if($game->name=='aerobet'): ?>
        <script src="/games/agt/moon/js/aerogame.js?v=<?php echo th::ver(); ?>"></script>
        <?php else: ?>
        <script src="/games/agt/moon/js/game.js?v=<?php echo th::ver(); ?>"></script>
        <?php endif; ?>
        <?php elseif($game->name=='aerobet'): ?>
        <script src="/games/agt/moon/aerobet.js?v=<?php echo th::ver(); ?>"></script>
        <?php else: ?>
        <script src="/games/agt/moon/tothemoon.js?v=<?php echo th::ver(); ?>"></script>
        <?php endif; ?>
        <script>

            <?php if(defined('LOCAL') && LOCAL): ?>
            window.isAGTLocal=true;
            <?php endif; ?>

            window.fsback_enable=false;
            <?php if(auth::user()->office->enable_bia>0): ?>
            window.fsback_enable=true;
            <?php endif; ?>

            window.promopanel_enable=false;
            <?php if(auth::user()->promopanel_enable() && $game->name!='aerobet'): ?>
            window.promopanel_enable=true;
            <?php endif; ?>

            window.showversion=false;
            <?php if(auth::user()->office->showfakeversion): ?>
            window.showversion='11.540';
            <?php endif; ?>

            window.strict_go_another_game=false;
            <?php if(auth::user()->office->strictGoAnotherGame()): ?>
            window.strict_go_another_game=true;
            <?php endif; ?>

            window.notAllowCancelBet=false;
            <?php if(auth::user()->api==='9'): ?>
            window.notAllowCancelBet=true;
            <?php endif; ?>

            var yesFS = function () {
//                StateLoad.fsPopup.onChildInputDown.dispatch({'act':'accept'});
                moonGame.processFS('accept');
            }

            var noFS = function () {
                moonGame.processFS('decline');
            }

            var ws, ws_connected = false;
            var connectTryTimeout = 2000;

            var C = {
                lines_choose: []
            };

            try {
                LS15 = localStorage;
            } catch (e) {
                LS15 = {};
            }

            <?php if(DEMO_MODE): ?>
                    LS15 = {};
            <?php endif; ?>

            LS15.KpZevaOVbk = '<?php echo empty(auth::$token) ? 'demo' : auth::$token; ?>';
            LS15.WKqhbUlTze = Date.now() + 24 * 60 * 60 * 1000;
            <?php if(DEMO_MODE): ?>
                    LS15.WKqhbUlTze = Date.now() + 20 * 60 * 1000;
            <?php endif; ?>
            LS15.syMtvvgLJj = '<?php echo auth::$user_id; ?>';
            window.gamename = '<?php echo $game->name; ?>';
            window.ver = 'a<?php echo th::ver(); ?>';

            <?php if(isset($_SERVER['HTTP_CF_IPCOUNTRY'])): ?>
                const clientAGTcountry='<?php echo strtolower($_SERVER['HTTP_CF_IPCOUNTRY']); ?>';
            <?php endif; ?>

            <?php if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])): ?>
            const clientAGTIP='<?php echo strtolower($_SERVER['HTTP_CF_CONNECTING_IP']); ?>';
            <?php endif; ?>

            window.forceMobile=false;
            window.noCloseGame=false;
            <?php if(arr::get($_GET,'force_mobile',0)): ?>
                window.forceMobile=true;
            <?php endif; ?>
            <?php if(arr::get($_GET,'no_close',0)): ?>
                window.noCloseGame=true;
            <?php endif; ?>

            window.brandedBy = false;
            <?php if(!!$game->branded): ?>
                window.brandedBy = '<?php echo $game->name; ?>';
            <?php endif; ?>

            function closeGame() {
                window.parent.postMessage('closeGame','*');

                var closeurl = model.getQueryString('closeurl');
                if(typeof closeurl=='string') {
                    try {
                        if(window.noCloseGame) {
                            window.location = closeurl;
                        }
                        else {
                            window.top.location = closeurl;
                        }
                    }
                    catch(e) {
                        window.location = closeurl;
                    }
                }
                else {
                    try {
                        if(window.self !== window.top) {
                            window.top.location.reload();
                        }
                        else {
                            window.location = window.location.origin;
                        }
                    }
                    catch(e) {
                        window.location = window.location.origin;
                    }

                }
            }

            var currency_icon='<?php echo $currency->sym(); ?>';
            var currency_code='<?php echo $currency->code; ?>';
            <?php if(in_array(auth::user()->office_id,[1029,1038,1041,1043,1045,1046,1047,1050,1117,1120,1201,1219,1213,1497,2172,1672])): ?>
                window.whitelogo = '1029';
            <?php elseif($game->name=='aerobet'): ?>
                window.whitelogo = 'tvbet';
            <?php elseif(in_array(auth::user()->office_id,[5563])): ?>
				window.whitelogo = 'logoempty';
			<?php endif; ?>
            var model = new Model();

            model.lang='<?php echo I18n::$lang; ?>';
            <?php if(arr::get($_GET,'forcelang')): ?>
                window.forcelang='<?php echo arr::get($_GET,'forcelang'); ?>';
            <?php endif; ?>
			
            model.langs=<?php echo json_encode(th::getLangsTranslate(arr::get($_GET,'forcelang'))); ?>;
            model.mult=<?php echo $currency->mult; ?>;

            if(model.mult>2) {
                $('.bet-control-wrap').addClass('small-mult');
            }

            const MoonAgtStackBets=<?php echo auth::user()->office->moon_delayed_bets?'true':'false'; ?>;

            let ws_moon='<?php echo Kohana::$config->load('static.moon_wss_url'); ?>';
            <?php if(TERMINAL): ?>
			ws_moon='<?php echo Kohana::$config->load('static.terminal_wss_url'); ?>';
			<?php elseif($game->name=='xplane'): ?>
                ws_moon='<?php echo Kohana::$config->load('static.xplane_wss_url'); ?>';
            <?php elseif($game->name=='aerobet'): ?>
                ws_moon='<?php echo Kohana::$config->load('static.aerobet_wss_url'); ?>';
            <?php endif; ?>

            var moonGame = new MoonGame(ws_moon+<?php echo auth::user()->office_id; ?>);

            moonGame.moon_min_bet=<?php echo $minBet; ?>;
			moonGame.moon_max_bet=<?php echo $maxBet; ?>;
			moonGame.moon_max_win=<?php echo $maxWin; ?>;
			moonGame.default_amount=moonGame.moon_min_bet*10;

            $('#ls-panel').draggableTouch();

        </script>

        <?php if(auth::user()->api==6): ?>
        <script src="js/everymatrix.js?v=<?php echo th::ver(); ?>">
        </script>
        <?php endif; ?>

        <script src="/js/agtunique.js?v=<?php echo th::ver(); ?>">
        </script>

        <div id="rate_history"></div>

        <?php if($game->name=='aerobet'): ?>
            <link rel="stylesheet" href="/games/agt/moon/css/aerobet.css?v=<?php echo th::ver(); ?>">
        <?php endif; ?>

    </body>
</html>