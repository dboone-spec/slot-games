<section class="pc-container">
    <div class="pcoded-content">
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">Events
							<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/Freespins/<?php echo I18n::$lang; ?>/<?php echo Request::current()->controller(); ?>.html">
                                &#x1F517;
                            </a>
						</h1>
                        <hr>
                        <?php if (!empty($errors)): ?>
                            <?php foreach ($errors as $err): ?>
                                <div style="color: red;" class="danger">
                                    <?php echo $err; ?>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                        <?php else: ?>
                            <div style="color: green;" class="success">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="table-responsive">
                                <div id="report-table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">

                                                <a href="/enter/events/item" class="btn btn-success">
                                                    <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<?php echo __('New event') ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-10">
                                            <form method="GET" class="form-horizontal">
                                                <div class="form-group">
                                                    <?php echo form::select('office_id', $offices, $current_office, ['class' => 'select2']) ?>
                                                </div>
                                                <div class="form-group">
                                                    <input class="btn btn-primary" type="submit"
                                                           value="<?php echo __('Find') ?>"/>
                                                    <a class="btn btn-default"
                                                       href="<?php echo $dir ?>/events"><?php echo __('Очистить'); ?></a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <hr/>
                                            <table id="button-select"
                                                   class="table table-striped table-bordered nowrap dataTable"
                                                   style="cursor: pointer" role="grid"
                                                   aria-describedby="report-table_info">
                                                <thead>
                                                <th>ID</th>
                                                <th>Partner</th>
                                                <th>Office</th>
                                                <th>Type</th>
                                                <th>Active</th>
                                                <th>Once</th>
                                                <th>Next time (UTC)</th>
                                                <th>Next calc time (UTC)</th>
                                                <th>Amount</th>
                                                <th>Count</th>
                                                <th>Week day</th>
                                                <th>Time to start (UTC)</th>
                                                <th>Duration</th>
                                                <th>Starts</th>
                                                <th>Ends</th>
                                                <th>Created</th>
                                                <th>Games</th>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($events as $event): ?>
                                                    <tr role="row"
                                                        <?php if ($event->checkEventIfReady()): ?>
                                                            style="color: green"
                                                        <?php endif; ?>
                                                        class="odd clickable-row" event_id="<?php echo $event->id; ?>">
                                                        <td>
                                                            <?php echo $event->id; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $event->partner; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $offices[$event->office_id]; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $event->type; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $event->active; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $event->once; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (time() >= $event->ends): ?>
                                                                Ended
                                                            <?php else: ?>
                                                                <?php echo date('d.m.Y H:i:s', $event->startTime()); ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (time() >= $event->ends): ?>
                                                                Ended
                                                            <?php else: ?>
                                                                <?php echo date('d.m.Y H:i:s', $event->startTime() + $event->duration); ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <b>
                                                                <?php echo th::float_format($event->fs_amount, $event->office->currency->mult); ?>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            <b>
                                                                <?php echo $event->fs_count; ?>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            <b>
                                                                <?php echo $event->dow >= 0 ? $daysofweek[$event->dow] : __('Everyday'); ?>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            <b>
                                                                <?php echo str_pad($event->h >= 0 ? $event->h : 0, 2, '0', STR_PAD_LEFT) .
                                                                    ':' . str_pad($event->m >= 0 ? $event->m : 0, 2, '0', STR_PAD_LEFT); ?>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            <b>
                                                                <?php echo $event->duration(); ?>
                                                            </b>
                                                        </td>
                                                        <td>
                                                            <?php echo date('d.m.Y H:i:s', $event->starts); ?>
                                                        </td>
                                                        <td>
                                                            <?php echo date('d.m.Y H:i:s', $event->ends); ?>
                                                        </td>
                                                        <td>
                                                            <?php echo date('d.m.Y H:i:s', $event->created); ?>
                                                        </td>
                                                        <td>
                                                            <?php echo implode(',', $event->gameList()); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                            <?php echo $page ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $('.clickable-row').click(function () {
        window.location = '/enter/events/item/' + $(this).attr('event_id')
    });
</script>