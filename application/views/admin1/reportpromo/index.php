<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Report Promo</h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Date from (00:00:00)</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_start"
                                               name="time_from"
                                               value="<?php echo date('Y-m-d', strtotime($time_from)) ?>">
                                    </div>
                                </div>


                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                  id="inputGroup-sizing-sm">Date to (23:59:59)</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_end"
                                               name="time_to" value="<?php echo date('Y-m-d', strtotime($time_to)) ?>">
                                    </div>
                                </div>


                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                        </div>
                                        <?php echo form::select('office_id', $officesList, $office_id, ['class' => 'form-control form-control-sm select2']) ?>
                                    </div>
                                </div>

                                <?php if (Person::$role == 'sa'): ?>


                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Owner</span>
                                            </div>
                                            <?php echo form::select('owner', $owners, $owner, ['class' => 'form-control form-control-sm select2']) ?>
                                        </div>
                                    </div>

                                <?php endif; ?>

                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('is_test', 1, (bool)$is_test, ['id' => '_isTestId', 'class' => 'custom-control-input']) ?>
                                        <label class="custom-control-label" for="_isTestId">Test offices</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('convert', 1, (bool)$convert, ['id' => '_converId', 'class' => 'custom-control-input']) ?>
                                        <label class="custom-control-label" for="_converId">Convert to EUR</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('only_total', 1, !!$only_total, ['id' => '_totalId', 'class' => 'custom-control-input']) ?>
                                        <label class="custom-control-label" for="_totalId">Show only total</label>
                                    </div>
                                </div>


                                <div class="w-100"></div>

								<div class="non-form-control ml-auto">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit"
                                           value="<?php echo __('Поиск') ?>"/>
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-round btn-outline-secondary"
                                       href="/enter/reportpromo"><?php echo __('Очистить') ?></a>
                                </div>
                                <!--<div>
                                        <a class="btn btn-sm btn-round btn-outline-warning" href="javascript:showChart();"><?php echo __('Chart') ?></a>
                                    </div>-->
                            </div>
                        </form>


                    </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                <thead>

                                <th class="tddate"> <?php echo __('Date') ?> </th>
                                <th class="tddate"> <?php echo __('Office') ?> </th>
                                <th class="tddate"> <?php echo __('Office name') ?> </th>
                                <th> Time</th>


                                <th> <?php echo __('In') ?> </th>
                                <th> <?php echo __('Out') ?> </th>
                                <th> <?php echo __('Win') ?> </th>
                                <th> <?php echo __('Count') ?> </th>
                                <th> Users</th>

                                <th> PROMO OUT</th>
                                <th> PROMO COUNT</th>

                                <th> Max PROMO OUT</th>
                                <th> Max PROMO COUNT</th>
                                <th> Promo Win</th>
                                <th> Denials</th>

                                </thead>
                                <tbody>
                                <?php foreach ($data as $row): ?>
                                    <tr>
                                        <td> <?php echo $row['date']; ?> </td>
                                        <td> <?php echo $row['office_id']; ?> </td>
                                        <td> <?php echo $row['visible_name']; ?> </td>
                                        <td> <?php echo $row['time']; ?> </td>

                                        <td> <?php echo th::float_format($row['in'], $row['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($row['out'], $row['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($row['win'], $row['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($row['count'], 0); ?> </td>
                                        <td> <?php echo th::float_format($row['users'], 0) ?> </td>

                                        <td> <?php echo th::float_format($row['promo_out'], $row['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($row['promo_count'], $row['mult'] ?? 2) ?> </td>

                                        <td> <?php echo th::float_format($row['max_promo_out'], $row['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($row['max_promo_count'], 0) ?> </td>
                                        <td> <?php echo th::float_format($row['max_promo_win'], $row['mult'] ?? 2) ?> </td>

                                        <td> <?php echo th::float_format($row['cancel_count'], 0); ?> </td>


                                    </tr>

                                <?php endforeach ?>


                                </tbody>
                            </table>


                        </div>
                    </div>


                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>


