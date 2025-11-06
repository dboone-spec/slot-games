<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Day report UTC</h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                            <div class="form-row">

                                <div class="form-group col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Date from (00:00:00)</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_start"
                                               name="time_from"
                                               value="<?php echo date('Y-m-d', strtotime($time_from)) ?>">
                                    </div>
                                </div>


                                <div class="form-group col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                  id="inputGroup-sizing-sm">Date to (23:59:59)</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_end"
                                               name="time_to" value="<?php echo date('Y-m-d', strtotime($time_to)) ?>">
                                    </div>
                                </div>


                                <div class="form-group col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                        </div>
                                        <?php echo form::select('office_id', $officesList, $office_id, ['class' => 'form-control form-control-sm select2']) ?>
                                    </div>
                                </div>

                                <?php if (Person::user()->showOwners()): ?>


                                    <div class="form-group col-md-3">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Owner</span>
                                            </div>
                                            <?php echo form::select('owner', $owners, $owner, ['class' => 'form-control form-control-sm select2']) ?>
                                        </div>
                                    </div>

                                <?php endif; ?>

                                <div class="w-100"></div>

                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('is_test', 1, (bool)$is_test, ['id' => '_isTestId', 'class' => 'custom-control-input']) ?>
                                        <label class="custom-control-label" for="_isTestId">Test offices</label>
                                    </div>
                                </div>

                                <?php if (Person::$role == 'sa'): ?>
                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('isTestUser', 1, (bool)$isTestUser, ['id' => 'isTestUser', 'class' => 'custom-control-input']) ?>
                                        <label class="custom-control-label" for="isTestUser">Test users</label>
                                    </div>
                                </div>
                                <?php endif; ?>

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

                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('by_month', 1, !!$by_month, ['id' => '_totalMId', 'class' => 'custom-control-input']) ?>
                                        <label class="custom-control-label" for="_totalMId">Show by month</label>
                                    </div>
                                </div>

                                <div class="w-100"></div>

                                <div class="non-form-control ml-auto">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit"
                                           value="<?php echo __('Поиск') ?>"/>
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-round btn-outline-secondary"
                                       href="/enter/report"><?php echo __('Очистить') ?></a>
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
                                <th class="tddate"> <?php echo __('Owner') ?> </th>
                                <th class="tddate"> <?php echo __('Office') ?> </th>
                                <th> <?php echo __('Cur') ?> </th>
                                <th> <?php echo __('In') ?> </th>
                                <th> <?php echo __('Out') ?> </th>
                                <th> <?php echo __('Win') ?> </th>
                                <th> <?php echo __('Count') ?> </th>
                                <th> <?php echo __('RTP') ?> </th>
                                <th> <?php echo __('JP') ?> </th>
                                <th> <?php echo __('DS in') ?> </th>
                                <th> <?php echo __('LS in') ?> </th>
                                <th> <?php echo __('FS API in') ?> </th>
                                <th> <?php echo __('DS out') ?> </th>
                                <th> <?php echo __('LS out') ?> </th>
                                <th> <?php echo __('FS API out') ?> </th>
                                <?php if (Person::$role == 'sa'): ?>
                                    <th> <?php echo __('Promo<br>out') ?> </th>
                                    <th> <?php echo __('Promo<br>count') ?> </th>
                                    <th> <?php echo __('Users') ?> </th>
                                    <th> <?php echo __('New<br>users') ?> </th>
                                <?php endif; ?>


                                </thead>
                                <tbody>
                                <?php foreach ($data as $date => $dayData): ?>


                                    <?php if (!$only_total) foreach ($dayData as $oid => $officeData): ?>

                                        <tr>
                                            <td <?php echo (date('N', strtotime($date)) == 5) ? 'style="color: #0a6ad2;"' : ''; ?> > <?php echo $date; ?>  </td>
                                            <td> <?php echo isset($owner_offices[$oid]) ? $owner_offices[$oid] : '' ?>  </td>
                                            <td> <?php echo Person::user()->officesName($oid, true) ?> </td>
                                            <td> <?php echo $officeData['currency'] ?? '' ?> </td>
                                            <td> <?php echo th::float_format($officeData['in'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['out'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td <?php if ($officeData['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::float_format($officeData['win'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['count'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td <?php if ($officeData['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $officeData['rtp'] ?? 0 ?>
                                                %
                                            </td>
                                            <td> <?php echo th::float_format($officeData['jp'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['cfsin'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['lfsin'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['afsin'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['cfsout'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['lfsout'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['afsout'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <?php if (Person::$role == 'sa'): ?>
                                                <td> <?php echo th::float_format($officeData['promoout'], $officeData['mult'] ?? 2) ?> </td>
                                                <td> <?php echo $officeData['promocnt'] ?> </td>
                                                <td> <?php echo th::float_format($officeData['users'], $officeData['mult'] ?? 2) ?> </td>
                                                <td> <?php echo th::float_format($officeData['newusers'], $officeData['mult'] ?? 2) ?> </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach ?>


                                    <?php if ($office_id == -1) : ?>
                                        <!--                                <script>
                                                office_data_for_chart.series[0].push('<?php echo $totalOffice[$date]['win'] ?? 0; ?>');
                                                office_data_for_chart.series[1].push('<?php echo $totalOffice[$date]['count'] ?? 0; ?>');
                                            </script>-->
                                        <tr>
                                            <td <?php echo (date('N', strtotime($date)) == 5) ? 'style="color: #0a6ad2;"' : ''; ?> > <?php echo $date; ?>  </td>
                                            <td>&nbsp;</td>
                                            <td> Total</td>
                                            <td>&nbsp;</td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['in'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['out'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td <?php if ($totalOffice[$date]['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::float_format($totalOffice[$date]['win'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['count'] ?? 0, $totalOffice['mult'] ?? 2) ?> </td>
                                            <td <?php if ($totalOffice[$date]['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $totalOffice[$date]['rtp'] ?? 0 ?>
                                                %
                                            </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['jp'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['cfsin'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['lfsin'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['afsin'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['cfsout'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['lfsout'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['afsout'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <?php if (Person::$role == 'sa'): ?>
                                                <td> <?php echo th::float_format($totalOffice[$date]['promoout'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                                <td> <?php echo $totalOffice[$date]['promocnt'] ?? 0 ?> </td>
                                                <td> <?php echo th::float_format($totalOffice[$date]['users'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                                <td> <?php echo th::float_format($totalOffice[$date]['newusers'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endif ?>

                                <?php endforeach ?>

                                <?php if($only_total && $convert): ?>
                                <?php foreach ($total['owners'] as $owner_name => $owner_total): ?>
                                    <tr class="statrow total" style="font-weight: bold; font-style: italic; " data-owner="<?php echo str_replace(' ', '', $owner_name); ?>">
                                        <td> <?php echo __('Total') ?>  </td>
                                        <td> <?php echo $owner_name; ?> </td>
                                        <td> <?php echo __('Total') ?>  </td>
                                        <td> EUR </td>
                                        <td> <?php echo th::float_format($owner_total['in'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['out'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td <?php if ($owner_total['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::float_format($owner_total['win'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['count'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td <?php if ($owner_total['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $owner_total['rtp'] ?? 0 ?>
                                            %
                                        </td>
                                        <td> <?php echo th::float_format($owner_total['jp'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['cfsin'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['lfsin'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['afsin'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['cfsout'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['lfsout'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($owner_total['afsout'] ?? 0, $owner_total['mult'] ?? 2) ?> </td>
                                        <?php if (Person::$role == 'sa'): ?>
                                            <td> <?php echo th::float_format($owner_total['promoout'] ?? 0, $owner_total['mult'] ?? 2) ?>   </td>
                                            <td> <?php echo $owner_total['promocnt'] ?? 0 ?>   </td>
                                            <td> <?php echo th::number_format($owner_total['users'] ?? 0) ?> </td>
                                            <td> <?php echo th::float_format($owner_total['newusers'] ?? 0, $owner_total['mult'] ?? 2) ?>   </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach ?>
                                <?php endif ?>

                                <?php foreach ($total['offices'] as $o_id => $office_total): ?>
                                    <tr class="statrow <?php echo str_replace(' ','',$office_total['owner']); ?>" style="<?php if(!($only_total && $convert)): ?>font-weight: bold;<?php endif; ?> font-style: italic; <?php if($only_total && $convert): ?>display: none;<?php endif; ?>">
                                        <td> <?php echo __('Total') ?>  </td>
                                        <td> <?php echo isset($owner_offices[$o_id]) ? $owner_offices[$o_id] : '' ?> </td>
                                        <td> <?php echo Person::user()->officesName($o_id, true) ?> </td>
                                        <td> <?php echo $office_total['currency'] ?? '' ?> </td>
                                        <td> <?php echo th::float_format($office_total['in'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['out'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td <?php if ($office_total['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::float_format($office_total['win'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['count'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td <?php if ($office_total['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $office_total['rtp'] ?? 0 ?>
                                            %
                                        </td>
                                        <td> <?php echo th::float_format($office_total['jp'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['cfsin'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['lfsin'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['afsin'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['cfsout'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['lfsout'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['afsout'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <?php if (Person::$role == 'sa'): ?>
                                            <td> <?php echo th::float_format($office_total['promoout'] ?? 0, $office_total['mult'] ?? 2) ?>   </td>
                                            <td> <?php echo $office_total['promocnt'] ?? 0 ?>   </td>
                                            <td> <?php echo th::number_format($office_total['users'] ?? 0) ?> </td>
                                            <td> <?php echo th::float_format($office_total['newusers'] ?? 0, $office_total['mult'] ?? 2) ?>   </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach ?>

                                <?php if(!$only_total && !$convert): ?>

                                <?php foreach ($total['currencies'] as $cur => $curr_total): ?>
                                    <tr style="font-weight: bold; font-style: italic; ">
                                        <td> <?php echo __('Total') ?>  </td>
                                        <td></td>
                                        <td> <?php echo $cur ?> </td>
                                        <td></td>
                                        <td> <?php echo th::float_format($curr_total['in'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['out'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td <?php if ($curr_total['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::float_format($curr_total['win'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['count'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td></td>
                                        <td> <?php echo th::float_format($curr_total['jp'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['cfsin'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['lfsin'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['afsin'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['cfsout'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['lfsout'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['afsout'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <?php if (Person::$role == 'sa'): ?>
                                            <td> <?php echo th::float_format($curr_total['promoout'] ?? 0, $curr_total['mult'] ?? 2) ?>  </td>
                                            <td> <?php echo $curr_total['promocnt'] ?? 0 ?>  </td>
                                            <td>  <?php echo th::number_format($curr_total['users'] ?? 0) ?> </td>
                                            <td> <?php echo th::float_format($curr_total['newusers'] ?? 0, $curr_total['mult'] ?? 2) ?>  </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach ?>

                                <?php endif; ?>

                                <tr style="font-weight: bold;">
                                    <td> <?php echo __('Total') ?>  </td>
                                    <td> <?php echo __('Total') ?> </td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td> <?php echo th::number_format($total['in'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['out'] ?? 0) ?> </td>
                                    <td <?php if ($total['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['win'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['count'] ?? 0) ?> </td>
                                    <td <?php if ($total['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $total['rtp'] ?? 0 ?>
                                        %
                                    </td>
                                    <td> <?php echo th::number_format($total['jp'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['cfsin'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['lfsin'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['afsin'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['cfsout'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['lfsout'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['afsout'] ?? 0) ?> </td>
                                    <?php if (Person::$role == 'sa'): ?>
                                        <td> <?php echo th::number_format($total['promoout'] ?? 0) ?></td>
                                        <td> <?php echo $total['promocnt'] ?></td>
                                        <td> <?php echo th::number_format($total['users'] ?? 0) ?> </td>
                                        <td> <?php echo th::number_format($total['newusers'] ?? 0) ?></td>
                                    <?php endif; ?>
                                </tr>


                                </tbody>
                            </table>


                        </div>

                        <?php if ($convert): ?>

                            <div>
                                <a class="btn btn-sm btn btn-primary btn-round"
                                   href="<?php echo url::query(['xls' => 'go']) ?>"> Export to excel </a>
                            </div>


                            <br>
                            To check the exchange rate:<br>
                            1) Go to the link <a href="https://www.xe.com/currencytables/" target="_blank">https://www.xe.com/currencytables/</a>
                            <br>
                            2) Checking last date of month and choose currency EUR<br>
                            3) Click Confirm<br>
                            4) Press the key combination Ctrl+F<br>
                            5) In the window, insert the currency, for example TRY<br>
                        <?php endif; ?>

                    </div>


                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>

<style>
    .statrow.total {
        cursor: pointer;
    }
</style>

<script>
    function showChart() {
        let search = window.location.search;

        if (search.length == 0) {
            search = '?chart=1';
        } else {
            search += '&chart=1';
        }

        window.open(window.location.origin + window.location.pathname + search, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=800,width=1024');
    }

    $('.statrow').hide();
    $('.statrow.total').show();


    $('.statrow').click(function() {

        let d=$(this).data('owner');

        $('.statrow.'+d).not('.total').toggle();
        $('.statrow').not('.total').not('.'+d).hide();

        $('.statrow.total[data-owner="'+d+'"]').after($('.statrow.'+d));
    });
</script>



