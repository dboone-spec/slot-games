<section class="pc-container">
    <div class="pcoded-content">
        <?php if(person::$role != 'cashier'): ?>
            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ form-element ] start -->
                <div class="col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <h1>Manuals
                                <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/manuals.html">
                                    &#x1F517;
                                </a>
                            </h1>
                            <hr>
                            <a href="/files/api2.2.14.pdf" target="_blank">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-auto">
                                        <i class="fas fa-book f-36 text-danger"></i>
                                    </div>
                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10">Download</h6>
                                        <h2 class="m-b-0">API</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] start -->
        <?php endif; ?>
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">
                            Terminal software and guide
                            <a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/terminal.html">
                                &#x1F517;
                            </a>
                        </h1>
                        <hr>
                        <div class="row">
                            <?php if(person::$role=='cashier' && person::user()->my_office->workmode==1): ?>
                            <a href="/files/terminal_agt<?php echo person::user()->office_id; ?>.zip">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-auto">
                                        <i class="fas fa-box f-36 text-danger"></i>
                                    </div>
                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10">Download</h6>
                                        <h2 class="m-b-0 text-uppercase">Chrome plugin</h2>
                                    </div>
                                </div>
                            </a>
                            <?php endif; ?>
                            <a href="/files/terminal.pdf" target="_blank">
                                <div class="row align-items-center m-l-0">
                                    <div class="col-auto">
                                        <i class="fas fa-book f-36 text-danger"></i>
                                    </div>
                                    <div class="col-auto">
                                        <h6 class="text-muted m-b-10">Download</h6>
                                        <h2 class="m-b-0 text-uppercase">Guide</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>