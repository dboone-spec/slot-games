<div class="pc-container">

    <div class="pcoded-content">
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-xl-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row pb-2">
                            <form action="">
                                <?php echo Form::label('office_id','Office'); ?>
                                <?php echo Form::select('office_id',$officesList,$office_id); ?>

                                <?php echo Form::label('owner','Owner'); ?>
                                <?php echo Form::select('owner',$owners,$owner); ?>

                                <?php echo Form::hidden('booga','1'); ?>

                                <?php echo Form::submit('filter','filter'); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- support-section start -->
            <div style="display: none" class="col-xl-6 col-md-12">
                <div class="card flat-card">
                    <div class="row-table">
                        <div class="col-sm-6 card-body br">
                            <div class="row">
                                <div class="col-sm-4">
                                    <i class="icon feather icon-users text-primary mb-1 d-block"></i>
                                </div>
                                <div class="col-sm-8 text-md-center">
                                    <h5>1000</h5>
                                    <span>Customers</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 d-none d-md-table-cell d-lg-table-cell d-xl-table-cell card-body br">
                            <div class="row">
                                <div class="col-sm-4">
                                    <i class="icon feather icon-globe text-primary mb-1 d-block"></i>
                                </div>
                                <div class="col-sm-8 text-md-center">
                                    <h5>$1252</h5>
                                    <span>Revenue</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <i class="icon feather icon-trending-up text-primary mb-1 d-block"></i>
                                </div>
                                <div class="col-sm-8 text-md-center">
                                    <h5>600</h5>
                                    <span>Growth</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-table">
                        <div class="col-sm-6 card-body br">
                            <div class="row">
                                <div class="col-sm-4">
                                    <i class="icon feather icon-rotate-ccw text-primary mb-1 d-block"></i>
                                </div>
                                <div class="col-sm-8 text-md-center">
                                    <h5>3550</h5>
                                    <span>Returns</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 d-none d-md-table-cell d-lg-table-cell d-xl-table-cell card-body br">
                            <div class="row">
                                <div class="col-sm-4">
                                    <i class="icon feather icon-download text-primary mb-1 d-block"></i>
                                </div>
                                <div class="col-sm-8 text-md-center">
                                    <h5>3550</h5>
                                    <span>Downloads</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <i class="icon feather icon-shopping-cart text-primary mb-1 d-blockz"></i>
                                </div>
                                <div class="col-sm-8 text-md-center">
                                    <h5>100%</h5>
                                    <span>Order</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card support-bar overflow-hidden">
                            <div class="card-body pb-0">
                                <h2 class="m-0">53.94%</h2>
                                <span class="text-primary">RTP</span>
                                <p class="mb-3 mt-3">Number of conversions divided by the total visitors. </p>
                            </div>
                            <div id="support-chart"></div>
                            <div class="card-footer border-0 bg-primary text-white background-pattern-white">
                                <div class="row text-center">
                                    <div class="col">
                                        <h4 class="m-0 text-white">10</h4>
                                        <span>2018</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0 text-white">15</h4>
                                        <span>2017</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0 text-white">13</h4>
                                        <span>2016</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card support-bar overflow-hidden">
                            <div class="card-body pb-0">
                                <h2 class="m-0">1432</h2>
                                <span class="text-primary">Количество спинов по дням</span>
                                <p class="mb-3 mt-3">Total number of order delivered in this month.</p>
                            </div>
                            <div class="card-footer border-0">
                                <div class="row text-center">
                                    <div class="col">
                                        <h4 class="m-0">130</h4>
                                        <span>May</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0">251</h4>
                                        <span>June</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0 ">235</h4>
                                        <span>July</span>
                                    </div>
                                </div>
                            </div>
                            <div id="support-chart1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly bets statistics (by devices)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-auto m-b-10">
                                <h3 class="mb-1"><?php echo th::number_format($betstats['total_count']); ?></h3>
                                <span>Total count</span>
                            </div>
                            <div class="col-auto m-b-10">
                                <h3 class="mb-1"><?php echo count($betstats['dates'])>0?th::number_format(floor($betstats['total_count']/count($betstats['dates']))):0; ?></h3>
                                <span>Average</span>
                            </div>
                        </div>
                        <div id="account-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Owners statistics</h5>
                    </div>
                    <div class="card-body">
                        <div id="owners-chart"></div>
                    </div>
                </div>
            </div>
            <!-- support-section end -->
            <!-- customer-section start -->
            <div class="col-xl-6 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h6>Top games for month</h6>
                        <!--<span>It takes continuous effort to maintain high customer satisfaction levels Internal and external.</span>-->
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="col">
                                <div id="satisfaction-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: none" class="card table-card">
                    <div class="card-header">
                        <h5>New Products</h5>
                    </div>
                    <div class="pro-scroll" style="height:255px;position:relative;">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover m-b-0">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>HeadPhone</td>
                                            <td><img src="/theme/admin1/images/widget/p1.jpg" alt="" class="img-20"></td>
                                            <td>
                                                <div><label class="badge badge-light-warning">Pending</label></div>
                                            </td>
                                            <td>$10</td>
                                            <td><a href="#!"><i class="icon feather icon-edit f-16  text-success"></i></a><a href="#!"><i class="feather icon-trash-2 ml-3 f-16 text-danger"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>Iphone 6</td>
                                            <td><img src="/theme/admin1/images/widget/p2.jpg" alt="" class="img-20"></td>
                                            <td>
                                                <div><label class="badge badge-light-danger">Cancel</label></div>
                                            </td>
                                            <td>$20</td>
                                            <td><a href="#!"><i class="icon feather icon-edit f-16  text-success"></i></a><a href="#!"><i class="feather icon-trash-2 ml-3 f-16 text-danger"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>Jacket</td>
                                            <td><img src="/theme/admin1/images/widget/p3.jpg" alt="" class="img-20"></td>
                                            <td>
                                                <div><label class="badge badge-light-success">Success</label></div>
                                            </td>
                                            <td>$35</td>
                                            <td><a href="#!"><i class="icon feather icon-edit f-16 text-success"></i></a><a href="#!"><i class="feather icon-trash-2 ml-3 f-16 text-danger"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>Sofa</td>
                                            <td><img src="/theme/admin1/images/widget/p4.jpg" alt="" class="img-20"></td>
                                            <td>
                                                <div><label class="badge badge-light-danger">Cancel</label></div>
                                            </td>
                                            <td>$85</td>
                                            <td><a href="#!"><i class="icon feather icon-edit f-16 text-success"></i></a><a href="#!"><i class="feather icon-trash-2 ml-3 f-16 text-danger"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>Iphone 6</td>
                                            <td><img src="/theme/admin1/images/widget/p2.jpg" alt="" class="img-20"></td>
                                            <td>
                                                <div><label class="badge badge-light-success">Success</label></div>
                                            </td>
                                            <td>$20</td>
                                            <td><a href="#!"><i class="icon feather icon-edit f-16 text-success"></i></a><a href="#!"><i class="feather icon-trash-2 ml-3 f-16 text-danger"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>HeadPhone</td>
                                            <td><img src="/theme/admin1/images/widget/p1.jpg" alt="" class="img-20"></td>
                                            <td>
                                                <div><label class="badge badge-light-warning">Pending</label></div>
                                            </td>
                                            <td>$50</td>
                                            <td><a href="#!"><i class="icon feather icon-edit f-16 text-success"></i></a><a href="#!"><i class="feather icon-trash-2 ml-3 f-16 text-danger"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>Iphone 6</td>
                                            <td><img src="/theme/admin1/images/widget/p2.jpg" alt="" class="img-20"></td>
                                            <td>
                                                <div><label class="badge badge-light-danger">Cancel</label></div>
                                            </td>
                                            <td>$30</td>
                                            <td><a href="#!"><i class="icon feather icon-edit f-16 text-success"></i></a><a href="#!"><i class="feather icon-trash-2 ml-3 f-16 text-danger"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display: none" class="col-xl-6 col-md-12">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card prod-p-card background-pattern">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5">Total Profit</h6>
                                        <h3 class="m-b-0">$1,783</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-money-bill-alt text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card prod-p-card bg-primary background-pattern-white">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white">Total Orders</h6>
                                        <h3 class="m-b-0 text-white">15,830</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-database text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card prod-p-card bg-primary background-pattern-white">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white">Average Price</h6>
                                        <h3 class="m-b-0 text-white">$6,780</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card prod-p-card background-pattern">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5">Product Sold</h6>
                                        <h3 class="m-b-0">6,784</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tags text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card feed-card">
                    <div class="card-header">
                        <h5>Feeds</h5>
                    </div>
                    <div class="feed-scroll" style="height:385px;position:relative;">
                        <div class="card-body">
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="bell" class="badge-light-primary feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">You have 3 pending tasks. <span class="text-muted float-right f-14">Just Now</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="shopping-cart" class="badge-light-danger feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">New order received <span class="text-muted float-right f-14">30 min ago</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="file-text" class="badge-light-success feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">You have 3 pending tasks. <span class="text-muted float-right f-14">Just Now</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="bell" class="badge-light-primary feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">You have 4 tasks Done. <span class="text-muted float-right f-14">1 hours ago</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="file-text" class="badge-light-success feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">You have 2 pending tasks. <span class="text-muted float-right f-14">Just Now</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="shopping-cart" class="badge-light-danger feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">New order received <span class="text-muted float-right f-14">4 hours ago</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="shopping-cart" class="badge-light-danger feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">New order Done <span class="text-muted float-right f-14">Just Now</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-25 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="file-text" class="badge-light-success feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">You have 5 pending tasks. <span class="text-muted float-right f-14">5 hours ago</span></h6>
                                    </a>
                                </div>
                            </div>
                            <div class="row m-b-0 align-items-center">
                                <div class="col-auto p-r-0">
                                    <i data-feather="bell" class="badge-light-primary feed-icon"></i>
                                </div>
                                <div class="col">
                                    <a href="#!">
                                        <h6 class="m-b-5">You have 4 tasks Done. <span class="text-muted float-right f-14">2 hours ago</span></h6>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- customer-section end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>

<script>
    $(function () {

        var topGamesSeries = [];
        var topGamesLabels = [];
        <?php foreach($gamestats as $gs): ?>
            topGamesSeries.push(<?php echo $gs['count']; ?>);
            topGamesLabels.push("<?php echo $gs['game'].' ('.$gs['count'].')'; ?>");
        <?php endforeach; ?>

        var options = {
            chart: {
                height: 260,
                type: 'pie',
            },
            series: topGamesSeries,
            labels: topGamesLabels,
            legend: {
                show: true,
                offsetY: 50,
            },
            dataLabels: {
                enabled: true,
                dropShadow: {
                    enabled: false,
                }
            },
            theme: {
                monochrome: {
                    enabled: true,
                    color: '#7267EF',
                }
            },
            responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 320,

                        },
                        legend: {
                            position: 'bottom',
                            offsetY: 0,
                        }
                    }
                }]
        }
        var chart = new ApexCharts(document.querySelector("#satisfaction-chart"), options);
        chart.render();
    });

    var betStatsLabels = [];
    var betStatsSeries = [];
    <?php foreach($betstats['dates'] as $bdate): ?>
        betStatsLabels.push("<?php echo $bdate; ?>");
    <?php endforeach; ?>
    <?php foreach($betstats['data'] as $bsk=>$bsv): ?>
        betStatsSeries.push({
            name: '<?php echo $bsk; ?>',
            type: '<?php echo $bsk=='total'?'column':'line';?>',
            data: [<?php echo implode(',',$owners[$bsv]); ?>]
        });
    <?php endforeach; ?>

    if(betStatsSeries.length>0) {
        $(function() {
            var options = {
                chart: {
                    height: 350,
                    type: 'line',
                    stacked: false,
                },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%'
                    }
                },
//                colors: ['#7267EF', '#c7d9ff'],
                series: betStatsSeries,
                fill: {
                    opacity: [0.85, 1],
                },
                labels: betStatsLabels,
                markers: {
                    size: 0
                },
                xaxis: {
//                    type: 'datetime'
                },
                yaxis: {
                    min: 0
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y) {
                            return y;

                        }
                    }
                },
                legend: {
                    labels: {
                        useSeriesColors: true
                    },
                    markers: {
                        customHTML: [
                            function() {
                                return ''
                            },
                            function() {
                                return ''
                            }
                        ]
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#account-chart"),
                options
            );
            chart.render();
        });
    }


    var ownerStatsLabels = [];
    var ownerStatsSeries = [];
    <?php foreach($ownerstats['dates'] as $odate): ?>
    ownerStatsLabels.push("<?php echo $odate; ?>");
    <?php endforeach; ?>
    <?php foreach($ownerstats['data'] as $owner=>$data): ?>
	<?php if(arr::get($_GET,'no1023','0')=='1' && $owner==1023): continue; ?><?php endif; ?>
        ownerStatsSeries.push({
            name: '<?php echo $owners[$owner]??$owner; ?>',
            data: <?php echo json_encode(array_map(function($v,$k) use($owners) {
                return ['x'=>$k,'y'=>$v];
            },$data,array_keys($data))); ?>
        });
    <?php endforeach; ?>


    if(ownerStatsSeries.length>0) {

        $(function() {
            var options = {
                series: ownerStatsSeries,
                chart: {
                    type: 'line',
                    height: 350,
                },
                stroke: {
                    width: 3,
                    curve: 'smooth',
                },
                xaxis: {
                    type: 'datetime',
                },
            }
            var chart = new ApexCharts(
                document.querySelector("#owners-chart"),
                options
            );
            chart.render();
        });
    }

</script>