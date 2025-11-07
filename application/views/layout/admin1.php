<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php if(!in_array(person::user()->office_id,[5563])): ?>site-domain <?php endif; ?>Back Office</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    	<![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Phoenixcoded" />

    <!-- Favicon icon -->
    <?php if(in_array(person::user()->office_id,[5563])): ?>
		<link rel="icon" href="/theme/interactive1/img/faviconempty.png" type="image/x-icon">
	<?php else: ?>
		<link rel="icon" href="/theme/interactive1/img/favicon.png" type="image/x-icon">
	<?php endif; ?>

    <link rel="stylesheet" href="/theme/admin1/css/plugins/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/theme/admin1/css/plugins/daterangepicker.css">

    <!-- font css -->
    <link rel="stylesheet" href="/theme/admin1/fonts/font-awsome-pro/css/pro.min.css">
    <link rel="stylesheet" href="/theme/admin1/fonts/feather.css">
    <link rel="stylesheet" href="/theme/admin1/fonts/fontawesome.css">

    <!-- vendor css -->
    <link rel="stylesheet" href="/theme/admin1/css/style.css">
    <link rel="stylesheet" href="/theme/admin1/css/customizer.css">

    <script src="/theme/admin1/js/vendor-all.min.js"></script>
    <script src="/theme/admin1/js/plugins/bootstrap.min.js"></script>
    <script src="/theme/admin1/js/plugins/feather.min.js"></script>
    <script src="/theme/admin1/js/pcoded.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
    <script src="/theme/admin1/js/plugins/clipboard.min.js"></script>
    <script src="/theme/admin1/js/uikit.min.js"></script>

    <script src="/theme/admin1/js/plugins/moment.min.js"></script>
    <script src="/theme/admin1/js/plugins/daterangepicker.js"></script>

    <script src="/theme/admin1/js/plugins/jquery.dataTables.min.js"></script>
    <script src="/theme/admin1/js/plugins/dataTables.bootstrap4.min.js"></script>
    <script src="/theme/admin1/js/plugins/dataTables.select.min.js"></script>
    <script src="/theme/admin1/js/plugins/jquery.durationpicker.js"></script>

<!-- Apex Chart -->
<script src="/theme/admin1/js/plugins/apexcharts.min.js"></script>

    <link rel="stylesheet" href="/theme/admin1/css/plugins/select2/select2.css">
    <script src="/theme/admin1/js/plugins/select2/select2.js"></script>


</head>
<body class="">
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>
	<!-- [ Pre-loader ] End -->
	<!-- [ Mobile header ] start -->
	<div class="pc-mob-header pc-header">
		<div class="pcm-logo">
			<?php if(in_array(person::user()->office_id,[5563])): ?>
			<img src="/theme/interactive1/img/logoempty.png" alt="" class="logo logo-lg">
			<?php else: ?>
			<img src="/theme/interactive1/img/logo.png" alt="" class="logo logo-lg">
			<?php endif; ?>
		</div>
		<div class="pcm-toolbar">
			<a href="#!" class="pc-head-link" id="mobile-collapse">
				<div class="hamburger hamburger--arrowturn">
					<div class="hamburger-box">
						<div class="hamburger-inner"></div>
					</div>
				</div>
				<!-- <i data-feather="menu"></i> -->
			</a>
			<a href="#!" class="pc-head-link" id="headerdrp-collapse">
				<i data-feather="align-right"></i>
			</a>
			<a href="#!" class="pc-head-link" id="header-collapse">
				<i data-feather="more-vertical"></i>
			</a>
		</div>
	</div>
	<!-- [ Mobile header ] End -->

	<!-- [ navigation menu ] start -->
	<nav class="pc-sidebar ">
		<div class="navbar-wrapper">
			<div class="m-header">
				<a href="/enter" class="b-brand">
					<?php if(in_array(person::user()->office_id,[5563])): ?>
					<!-- ========   change your logo hear   ============ -->
					<img src="/theme/interactive1/img/logoempty.png" alt="" class="logo logo-lg">
					<img src="/theme/interactive1/img/logoempty.png" alt="" class="logo logo-sm">
					<?php else: ?>
					<!-- ========   change your logo hear   ============ -->
					<img src="/theme/interactive1/img/logo.png" alt="" class="logo logo-lg">
					<img src="/theme/interactive1/img/logo.png" alt="" class="logo logo-sm">
					<?php endif; ?>
				</a>
			</div>
			<?php echo $navbar ?>
		</div>
	</nav>
	<!-- [ navigation menu ] end -->
	<!-- [ Header ] start -->
	<header class="pc-header ">
		<div class="header-wrapper">
			<div class="mr-auto pc-mob-drp">
				<ul class="list-unstyled">

					<li class="dropdown pc-h-item pc-mega-menu">
						<a class="pc-head-link active dropdown-toggle arrow-none mr-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
							Quick menu
						</a>
						<?php echo $quickMenu ?>
					</li>
				</ul>




			</div>
			<div class="ml-auto">
				<ul class="list-unstyled">



					<li class="dropdown pc-h-item">
						<a class="pc-head-link dropdown-toggle arrow-none mr-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
							<img src="/theme/admin1/images/user/avatar-2.jpg" alt="user-image" class="user-avtar">
							<span>
                                                            <span class="user-name"><?php echo person::user()->name ?></span>

							</span>
						</a>
						<div class="dropdown-menu dropdown-menu-right pc-h-dropdown">
							<div class=" dropdown-header">
								<h6 class="text-overflow m-0">Welcome !</h6>
							</div>

							<a href="<?php echo $dir.'/profile' ?>" class="dropdown-item">
								<i data-feather="user"></i>
								<span>Profile</span>
							</a>
                                                        <a href="<?php echo $dir.'/login/logout' ?>" class="dropdown-item">
								<i data-feather="power"></i>
								<span>Logout</span>
							</a>
						</div>
					</li>
				</ul>
			</div>

		</div>
	</header>

	<!-- Modal -->
	<!--<div class="modal notification-modal fade" id="notification-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<ul class="nav nav-pill tabs-light mb-3" id="pc-noti-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="pc-noti-home-tab" data-toggle="pill" href="#pc-noti-home" role="tab" aria-controls="pc-noti-home" aria-selected="true">Notification</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pc-noti-news-tab" data-toggle="pill" href="#pc-noti-news" role="tab" aria-controls="pc-noti-news" aria-selected="false">News<span class="badge badge-danger ml-2 d-none d-sm-inline-block">4</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="pc-noti-settings-tab" data-toggle="pill" href="#pc-noti-settings" role="tab" aria-controls="pc-noti-settings" aria-selected="false">Setting<span class="badge badge-success ml-2 d-none d-sm-inline-block">Update</span></a>
						</li>
					</ul>
					<div class="tab-content pt-4" id="pc-noti-tabContent">
						<div class="tab-pane fade show active" id="pc-noti-home" role="tabpanel" aria-labelledby="pc-noti-home-tab">
							<div class="media">
								<img src="/theme/admin1/images/user/avatar-1.jpg" alt="images" class="img-fluid avtar avtar-l">
								<div class="media-body ml-3 align-self-center">
									<div class="float-right">
										<div class="btn-group card-option">
											<button type="button" class="btn shadow-none">
												<i data-feather="heart" class="text-danger"></i>
											</button>
											<button type="button" class="btn shadow-none px-0 dropdown-toggle arrow-none" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i data-feather="more-horizontal"></i>
											</button>
											<div class="dropdown dropdown-menu dropdown-menu-right">
												<a class="dropdown-item" href="#!"><i data-feather="refresh-cw"></i> reload</a>
												<a class="dropdown-item" href="#!"><i data-feather="trash"></i> remove</a>
											</div>
										</div>
									</div>
									<h6 class="mb-0 d-inline-block">Ashoka T.</h6>
									<p class="mb-0 d-inline-block f-12 text-muted"> • 06/20/2019 at 6:43 PM </p>
									<p class="my-3">Cras sit amet nibh libero in gravida nulla Nulla vel metus scelerisque ante sollicitudin.</p>
									<div class="p-3 border rounded">
										<div class="media align-items-center">
											<div class="media-body">
												<h6 class="mb-1 f-14">Death Star original maps and blueprint.pdf</h6>
												<p class="mb-0 text-muted">by<a href="#!"> Ashoka T </a>.</p>
											</div>
											<div class="btn-group d-none d-sm-inline-flex">
												<button type="button" class="btn shadow-none">
													<i data-feather="download-cloud"></i>
												</button>
												<button type="button" class="btn shadow-none px-0 dropdown-toggle arrow-none" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i data-feather="more-horizontal"></i>
												</button>
												<div class="dropdown dropdown-menu dropdown-menu-right">
													<a class="dropdown-item" href="#!"><i data-feather="refresh-cw"></i> reload</a>
													<a class="dropdown-item" href="#!"><i data-feather="trash"></i> remove</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr class="mb-4">
							<div class="media">
								<img src="/theme/admin1/images/user/avatar-2.jpg" alt="images" class="img-fluid avtar avtar-l">
								<div class="media-body ml-3 align-self-center">
									<div class="float-right">
										<div class="btn-group card-option">
											<button type="button" class="btn shadow-none px-0 dropdown-toggle arrow-none" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i data-feather="more-horizontal"></i>
											</button>
											<div class="dropdown dropdown-menu dropdown-menu-right">
												<a class="dropdown-item" href="#!"><i data-feather="refresh-cw"></i> reload</a>
												<a class="dropdown-item" href="#!"><i data-feather="trash"></i> remove</a>
											</div>
										</div>
									</div>
									<h6 class="mb-0 d-inline-block">Ashoka T.</h6>
									<p class="mb-0 d-inline-block  f-12 text-muted"> • 06/20/2019 at 6:43 PM </p>
									<p class="my-3">Cras sit amet nibh libero in gravida nulla Nulla vel metus scelerisque ante sollicitudin.</p>
									<img src="/theme/admin1/images/slider/img-slide-3.jpg" alt="images" class="img-fluid wid-90 rounded m-r-10 m-b-10">
									<img src="/theme/admin1/images/slider/img-slide-7.jpg" alt="images" class="img-fluid wid-90 rounded m-r-10 m-b-10">
								</div>
							</div>
							<hr class="mb-4">
							<div class="media mb-3">
								<img src="/theme/admin1/images/user/avatar-3.jpg" alt="images" class="img-fluid avtar avtar-l">
								<div class="media-body ml-3 align-self-center">
									<div class="float-right">
										3 <i data-feather="heart" class="text-danger fill-danger"></i>
									</div>
									<h6 class="mb-0 d-inline-block">Ashoka T.</h6>
									<p class="mb-0 d-inline-block  f-12 <text-muted></text-muted>"> • 06/20/2019 at 6:43 PM </p>
									<p class="my-3">Nulla vitae elit libero, a pharetra augue. Aenean lacinia bibendum nulla sed consectetur.</p>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="pc-noti-news" role="tabpanel" aria-labelledby="pc-noti-news-tab">
							<div class="pb-3 border-bottom mb-3 media">
								<a href="#!"><img src="/theme/admin1/images/news/img-news-2.jpg" class="wid-90 rounded" alt="..."></a>
								<div class="media-body ml-3">
									<p class="float-right mb-0 text-success"><small>now</small></p>
									<a href="#!"><h6>This is a news image</h6></a>
									<p class="mb-2">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy.</p>
								</div>
							</div>
							<div class="pb-3 border-bottom mb-3 media">
								<a href="#!"><img src="/theme/admin1/images/news/img-news-1.jpg" class="wid-90 rounded" alt="..."></a>
								<div class="media-body ml-3">
									<p class="float-right mb-0 text-muted"><small>3 mins ago</small></p>
									<a href="#!"><h6>Industry's standard dummy</h6></a>
									<p class="mb-2">Lorem Ipsum is simply dummy text of the printing and typesetting.</p>
									<a href="#" class="badge badge-light">Html</a>
									<a href="#" class="badge badge-light">UI/UX designed</a>
								</div>
							</div>
							<div class="pb-3 border-bottom mb-3 media">
								<a href="#!"><img src="/theme/admin1/images/news/img-news-2.jpg" class="wid-90 rounded" alt="..."></a>
								<div class="media-body ml-3">
									<p class="float-right mb-0 text-muted"><small>5 mins ago</small></p>
									<a href="#!"><h6>Ipsum has been the industry's</h6></a>
									<p class="mb-2">Lorem Ipsum is simply dummy text of the printing and typesetting.</p>
									<a href="#" class="badge badge-light">JavaScript</a>
									<a href="#" class="badge badge-light">Scss</a>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="pc-noti-settings" role="tabpanel" aria-labelledby="pc-noti-settings-tab">
							<h6 class="mt-2"><i data-feather="monitor" class="mr-2"></i>Desktop settings</h6>
							<hr>
							<div class="custom-control custom-switch">
								<input type="checkbox" class="custom-control-input" id="pcsetting1" checked>
								<label class="custom-control-label f-w-600 pl-1" for="pcsetting1">Allow desktop notification</label>
							</div>
							<p class="text-muted ml-5">you get lettest content at a time when data will updated</p>
							<div class="custom-control custom-switch">
								<input type="checkbox" class="custom-control-input" id="pcsetting2">
								<label class="custom-control-label f-w-600 pl-1" for="pcsetting2">Store Cookie</label>
							</div>
							<h6 class="mb-0 mt-5"><i data-feather="save" class="mr-2"></i>Application settings</h6>
							<hr>
							<div class="custom-control custom-switch">
								<input type="checkbox" class="custom-control-input" id="pcsetting3">
								<label class="custom-control-label f-w-600 pl-1" for="pcsetting3">Backup Storage</label>
							</div>
							<p class="text-muted mb-4 ml-5">Automaticaly take backup as par schedule</p>
							<div class="custom-control custom-switch">
								<input type="checkbox" class="custom-control-input" id="pcsetting4">
								<label class="custom-control-label f-w-600 pl-1" for="pcsetting4">Allow guest to print file</label>
							</div>
							<h6 class="mb-0 mt-5"><i data-feather="cpu" class="mr-2"></i>System settings</h6>
							<hr>
							<div class="custom-control custom-switch">
								<input type="checkbox" class="custom-control-input" id="pcsetting5" checked>
								<label class="custom-control-label f-w-600 pl-1" for="pcsetting5">View other user chat</label>
							</div>
							<p class="text-muted ml-5">Allow to show public user message</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light-danger btn-sm" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-light-primary btn-sm">Save changes</button>
				</div>
			</div>
		</div>
	</div>-->
	<!-- [ Header ] end -->

<!-- [ Main Content ] start -->
<?php echo $content ?>
<!-- [ Main Content ] end -->
    <!-- Warning Section start -->
    <!-- Older IE warning message -->
    <!--[if lt IE 11]>
        <div class="ie-warning">
            <h1>Warning!!</h1>
            <p>You are using an outdated version of Internet Explorer, please upgrade
               <br/>to any of the following web browsers to access this website.
            </p>
            <div class="iew-container">
                <ul class="iew-download">
                    <li>
                        <a href="http://www.google.com/chrome/">
                            <img src="/theme/admin1/images/browser/chrome.png" alt="Chrome">
                            <div>Chrome</div>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.mozilla.org/en-US/firefox/new/">
                            <img src="/theme/admin1/images/browser/firefox.png" alt="Firefox">
                            <div>Firefox</div>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.opera.com">
                            <img src="/theme/admin1/images/browser/opera.png" alt="Opera">
                            <div>Opera</div>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.apple.com/safari/">
                            <img src="/theme/admin1/images/browser/safari.png" alt="Safari">
                            <div>Safari</div>
                        </a>
                    </li>
                    <li>
                        <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                            <img src="/theme/admin1/images/browser/ie.png" alt="">
                            <div>IE (11 & above)</div>
                        </a>
                    </li>
                </ul>
            </div>
            <p>Sorry for the inconvenience!</p>
        </div>
    <![endif]-->
    <!-- Warning Section Ends -->
    <!-- Required Js -->




<script>


    $(document).ready(function() {
        $('.select2').select2({dropdownAutoWidth : true});
    });

    // header option
    $('#pct-toggler').on('click', function() {
        $('.pct-customizer').toggleClass('active');

    });
    // header option
    $('#cust-sidebrand').change(function() {
        if ($(this).is(":checked")) {
            $('.theme-color.brand-color').removeClass('d-none');
            $('.m-header').addClass('bg-dark');
        } else {
            $('.m-header').removeClassPrefix('bg-');
            $('.m-header > .b-brand > .logo-lg').attr('src', '/theme/admin1/images/logo-dark.svg');
            $('.theme-color.brand-color').addClass('d-none');
        }
    });
    // Header Color
    $('.brand-color > a').on('click', function() {
        var temp = $(this).attr('data-value');
        // $('.header-color > a').removeClass('active');
        // $('.pcoded-header').removeClassPrefix('brand-');
        // $(this).addClass('active');
        if (temp == "bg-default") {
            $('.m-header').removeClassPrefix('bg-');
        } else {
            $('.m-header').removeClassPrefix('bg-');
            $('.m-header > .b-brand > .logo-lg').attr('src', '/theme/admin1/images/logo.svg');
            $('.m-header').addClass(temp);
        }
    });
    // Header Color
    $('.header-color > a').on('click', function() {
        var temp = $(this).attr('data-value');
        // $('.header-color > a').removeClass('active');
        // $('.pcoded-header').removeClassPrefix('brand-');
        // $(this).addClass('active');
        if (temp == "bg-default") {
            $('.pc-header').removeClassPrefix('bg-');
        } else {
            $('.pc-header').removeClassPrefix('bg-');
            $('.pc-header').addClass(temp);
        }
    });
    // sidebar option
    $('#cust-sidebar').change(function() {
        if ($(this).is(":checked")) {
            $('.pc-sidebar').addClass('light-sidebar');
            $('.pc-horizontal .topbar').addClass('light-sidebar');
            // $('.m-header > .b-brand > .logo-lg').attr('src', '/theme/admin1/images/logo-dark.svg');
        } else {
            $('.pc-sidebar').removeClass('light-sidebar');
            $('.pc-horizontal .topbar').removeClass('light-sidebar');
            // $('.m-header > .b-brand > .logo-lg').attr('src', '/theme/admin1/images/logo.svg');
        }
    });
    $.fn.removeClassPrefix = function(prefix) {
        this.each(function(i, it) {
            var classes = it.className.split(" ").map(function(item) {
                return item.indexOf(prefix) === 0 ? "" : item;
            });
            it.className = classes.join(" ");
        });
        return this;
    };
</script>
<script>

	$(document).on('copy',function (e) {
		e.preventDefault();
		let targetText='';

		let text=window.getSelection().toString();

		targetText=text.split("\r").map(function(row) {
			row = row.split("\t").map(function(cell) {
				let tryNum=parseFloat(cell.replace(' ',''));

				if(typeof tryNum=='number') {
					cell=cell.replaceAll(' ','').replaceAll('.',',');
				}

				return cell;
			}).join("\t");

			return row;
		}).join("\r");

		navigator.clipboard.writeText(targetText).then(function() {
			console.log('success paste',targetText);
		}).catch(function() {
			console.log('error paste',targetText);
		});
	});
</script>
<!-- custom-chart js -->
<script src="/theme/admin1/js/pages/dashboard-sale.js"></script>
<style>
    .dataTable,
    .dataTable tr > td,
    .dataTable > thead  th {
        border: 1px solid #c0c0c0 !important;
        border-collapse: collapse;
    }
    .dataTable tr td:nth-child(odd){
		/*background: #b8d1f3;*/
	}
	/*  Define the background color for all the EVEN table columns  */
	.dataTable tr td:nth-child(even),
    .dataTable thead th:nth-child(even){
		/*background: rgba(114, 103, 239, 0.43) !important;*/
	}
    table.dataTable tbody > tr.selected, table.dataTable tbody > tr > .selected {
        color: #fff;
    }
    .logo {
        width: 85px;
    }
	
	 /* new styles (fixes) */

	 /* .form-select.col-md-1 {
		 min-width: 15%;
	 } */

	 .form-select-2.col-md-2 {
		 min-width: 25%;
	 }

	/* .form-select-1 > .input-group {
        display: flex;
        flex-wrap: no-wrap
    } */

	.form-select-1 .select2 {
		/*width: 100%;*/
	}

	.input-group:has(select) {
		flex-wrap: nowrap;
	}

	.input-group:has(.select2) {
		min-width: 10vw;
	}
</style>
<script>
	function sortAgtAdmin() {
		let el=$(this);

		if(typeof this.sortCell=='undefined') {
			this.sortCell='asc';
		}

		if(this.sortCell=='desc') {
			this.sortCell='asc'
		}
		else {
			this.sortCell='desc';
		}

		$(el).parents('table').find('.adminSortable').removeClass('sorting_asc');
		$(el).parents('table').find('.adminSortable').removeClass('sorting_desc');
		$(el).parents('table').find('.adminSortable').removeClass('superSortAsc');
		$(el).parents('table').find('.adminSortable').removeClass('superSortDesc');

		$(el).addClass('sorting_'+this.sortCell);
		$(el).addClass('superSort'+this.sortCell);

		let tbody = $(this).parents('table').find('tbody');
		let eq=el.index();
		let newTbody = tbody.find('tr').toArray().sort(function(a, b) {
			a=$(a);
			b=$(b);

			if(!a.find('[adminSortableValue]').length) {
				return 0;
			}


			if(!b.find('[adminSortableValue]').length) {
				return 0;
			}

			var aVal = parseFloat(a.find('[adminSortableValue]').eq(eq).attr('adminSortableValue')),
				bVal = parseFloat(b.find('[adminSortableValue]').eq(eq).attr('adminSortableValue'));

			if(this.sortCell=='desc') {
				return aVal - bVal;
			}
			else {
				return bVal - aVal;
			}

		}.bind(this));

		tbody.empty();

		tbody.append($(newTbody).clone(true,true));
	}
	$('.adminSortable').each(function(i,el) {
		$(el).addClass('sorting');
		$(el).addClass('superSort');
		$(el).on('click',sortAgtAdmin);
	});
</script>
</body>
</html>
