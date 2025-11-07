<!DOCTYPE html>
<html lang="ru">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" type="image/png" sizes="16x16" href="/admin/plugins/images/favicon.png">
		<title>Admin</title>
		<!-- Bootstrap Core CSS -->
		<link href="/admin/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Menu CSS -->
		<link href="/admin/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
		<!-- toast CSS -->
		<link href="/admin/plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
		<!-- morris CSS -->
		<link href="/admin/plugins/bower_components/morrisjs/morris.css" rel="stylesheet">
		<!-- chartist CSS -->
		<link href="/admin/plugins/bower_components/chartist-js/dist/chartist.min.css" rel="stylesheet">
		<link href="/admin/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css" rel="stylesheet">
		<!-- animation CSS -->
		<link href="/admin/css/animate.css" rel="stylesheet">
		<!-- Custom CSS -->
		<link href="/admin/css/style.css" rel="stylesheet">
		<!-- color CSS -->
		<link href="/admin/css/colors/default.css" id="theme" rel="stylesheet">
		<link href="/admin/css/themify-icons.css?v=1" rel="stylesheet">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	    <![endif]-->

		<!-- our files -->
		<link href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet">
		<script src="/admin/plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <?php foreach ($scripts as $src): ?>
            <script src="<?php echo $src; ?>"></script>
        <?php endforeach; ?>
        <script src="/js/jquery.form.js"></script>
		<!-- our files -->
        <style>
            <?php if(th::isMobile()): ?>
            .table {
                width: auto;
            }
            <?php endif; ?>
/*            @media (max-width: 767px) {
                .fix-header #page-wrapper {
                    margin-top: 500px;
                }
            }*/
        </style>
	</head>

	<body class="fix-header">
		<!-- ============================================================== -->
		<!-- Preloader -->
		<!-- ============================================================== -->
		<div class="preloader">
			<svg class="circular" viewBox="25 25 50 50">
			<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
			</svg>
		</div>
		<!-- ============================================================== -->
		<!-- Wrapper -->
		<!-- ============================================================== -->
		<div id="wrapper">
			<!-- ============================================================== -->
			<!-- Topbar header - style you can find in pages.scss -->
			<!-- ============================================================== -->
                        <nav class="navbar navbar-default navbar-static-top m-b-0">
                            <div class="navbar-header">
                                <!-- /Logo -->
                                    <ul class="nav navbar-top-links navbar-left pull-left">
                                        <li><a href="javascript:void(0)" class="open-close waves-effect waves-light"><i class="ti-menu"></i></a></li>
                                    
                                    </ul>
                                
                                <ul class="nav navbar-top-links navbar-right pull-right">
                                    <?php if (count(person::user()->balances())): ?>
                                            <li>
                                                <a href="#">
                                                    <b class="hidden-xs">
                                                        <?php echo __('Балансы') ?>:
                                                        <?php foreach (person::user()->balances() as $v): ?>
                                                            <?php echo th::number_format($v->amount) . ' ' . $v->code ?>/
                                                        <?php endforeach; ?>
                                                    </b>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php if (false && person::$role == 'rmanager'): ?>
                                            <li>
                                                <a class="profile-pic hidden-xs" href="#">
                                                    <img src="/admin/plugins/images/users/varun.jpg" alt="user-img" width="36" class="img-circle">
                                                    <?php echo __('Ваш менеджер'); ?>: <?php echo Person::agent()->visible_name; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <li>
                                        <a class="profile-pic" href="#">
                                            <?php echo person::rolelist(Person::$role); ?> <b><?php echo Person::user()->name; ?></b>
                                        </a>
                                    </li>
                                    <li>
                                        <?php if (Cookie::get('lang') == 'en' || I18n::$lang == 'en'): ?>
                                            <a href="<?php echo $dir; ?>/lang/set/ru">
                                                <!--<img width="25px" src="/assets/img/en_EN.png" alt="en">-->
                                                EN
                                            </a>
                                        <?php elseif (Cookie::get('lang') == 'ru' || I18n::$lang == 'ru'): ?>
                                            <a href="<?php echo $dir; ?>/lang/set/en">
                                                <!--<img width="25px" src="/assets/img/ru_RU.png" alt="ru">-->
                                                RU
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <a href="<?php echo $dir; ?>/login/logout" class="btn btn-danger pull-right m-l-20 waves-effect waves-light"><?php echo __('Выход'); ?></a>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.navbar-header -->
                            <!-- /.navbar-top-links -->
                            <!-- /.navbar-static-side -->
                        </nav>
			<!-- End Top Navigation -->
			<!-- ============================================================== -->
			<!-- Left Sidebar - style you can find in sidebar.scss  -->
			<!-- ============================================================== -->
			<div class="navbar-default sidebar" role="navigation">
				<div class="sidebar-nav slimscrollsidebar">
					<div class="sidebar-head">
						<h3>
                                                    <span class="fa-fw open-close">
                                                        <i class="ti-close ti-menu"></i>
                                                    </span>
                                                    <span class="hide-menu">Navigation</span>
                                                </h3>
					</div>
					<ul class="nav" id="side-menu">
                                        <?php
                                        $icons=[
                                                'user'=>'fa-user',
                                                'persons'=>'fa-users',
                                                'payer'=>'fa-money',
                                                'bet'=>'fa-database',
                                                'paymentstat'=>'fa-bar-chart',
                                                'operationstat'=>'fa-bar-chart',
                                                'userhistory'=>'fa-clock-o',
                                                'operation'=>'fa-usd',
                                                'payment'=>'fa-credit-card',
                                                'profile'=>'fa-cog',
                                                'dashboard'=>'fa-tachometer',
                                                'office'=>'fa-building-o',
                                                'terminal'=>'fa-gamepad',
                                                'manuals'=>'fa-book',
                                                'jackpot'=>'fa-bomb',
                                        ];
                                        ?>
                                        <?php foreach($menus as $menu=>$menu_name): ?>
                                        <?php if($menu=='lang') continue; ?>
                                                <li>
                                                    <a href="<?php echo $dir; ?>/<?php echo $menu; ?>" class="waves-effect <?php if($menu==$current_menu): ?>active<?php endif; ?>">
                                                        <i class="fa <?php echo arr::get($icons,$menu,''); ?> fa-fw" aria-hidden="true"></i>
                                                        <?php echo $menu_name; ?></a>
                                                </li>
                                        <?php endforeach; ?>
                        <?php if(in_array(person::$role,['kassa','administrator'])): ?>
                                <style>
                                    #blockoffice {
                                        color: #fff !important;
                                    }
                                    #blockoffice:hover {
                                        color: #54667a !important;
                                    }
                                </style>
                                <li>
                                    <a id="blockoffice" class="btn pull-right btn-danger" href="<?php echo $dir; ?>/dashboard/blockoffice"><?php echo __('Заблокировать ППС'); ?></a>
                                </li>
                        <?php endif; ?>
					</ul>
                    <script>
                        $(document).ready(function() {
                            $('#side-menu li').first().css('padding-top','10px');
                        });
                    </script>
				</div>

			</div>
			<!-- ============================================================== -->
			<!-- End Left Sidebar -->
			<!-- ============================================================== -->
			<!-- ============================================================== -->
			<!-- Page Content -->
			<!-- ============================================================== -->
			<div id="page-wrapper">
				<div class="container-fluid">
					<?php echo $content ?>
				</div>
				<!-- /.container-fluid -->
				<footer class="footer text-center">
                    <?php echo date('Y'); ?> &copy;
                    <div class="pull-right">
                        <?php echo __('Телефон службы поддержки'); ?>: <?php echo th::tp_contact('white','phone'); ?>
                    </div>
                </footer>
			</div>
			<!-- ============================================================== -->
			<!-- End Page Content -->
			<!-- ============================================================== -->
		</div>
		<!-- ============================================================== -->
		<!-- End Wrapper -->
		<!-- ============================================================== -->
		<!-- ============================================================== -->
		<!-- All Jquery -->
		<!-- ============================================================== -->
		<!-- Bootstrap Core JavaScript -->
		<script src="/admin/bootstrap/dist/js/bootstrap.min.js"></script>
		<!-- Menu Plugin JavaScript -->
		<script src="/admin/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
		<!--slimscroll JavaScript -->
		<script src="/admin/js/jquery.slimscroll.js"></script>
		<!--Wave Effects -->
		<script src="/admin/js/waves.js"></script>
		<!--Counter js -->
		<script src="/admin/plugins/bower_components/waypoints/lib/jquery.waypoints.js"></script>
		<script src="/admin/plugins/bower_components/counterup/jquery.counterup.min.js"></script>
		<!-- chartist chart -->
		<script src="/admin/plugins/bower_components/chartist-js/dist/chartist.min.js"></script>
		<script src="/admin/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js"></script>
		<!-- Sparkline chart JavaScript -->
		<script src="/admin/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js"></script>
		<!-- Custom Theme JavaScript -->
		<script src="/admin/js/custom.min.js?v=1"></script>
		<script src="/admin/js/dashboard1.js"></script>
		<script src="/admin/plugins/bower_components/toast-master/js/jquery.toast.js"></script>
	</body>

</html>
