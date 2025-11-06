<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Day report UTC EUR</h4>
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
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Date to (23:59:59)</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_end"
                                               name="time_to" value="<?php echo date('Y-m-d', strtotime($time_to)) ?>">
                                    </div>
                                </div>


                                <div class="form-group col-md-1">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                        </div>
                                        <?php echo form::select('office_id', $officesList, $office_id, ['class' => 'form-control form-control-sm']) ?>
                                    </div>
                                </div>

                                <?php if (Person::user()->showOwners()): ?>


                                    <div class="form-group col-md-1">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Owner</span>
                                            </div>
                                            <?php echo form::select('owner', $owners, $owner, ['class' => 'form-control form-control-sm']) ?>
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
                                       href="/enter/reporteur"><?php echo __('Очистить') ?></a>
                                </div>
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
                                <th> <?php echo __('Currency') ?> </th>
                                <th> <?php echo __('In') ?> </th>
                                <th> <?php echo __('Out') ?> </th>
                                <th> <?php echo __('Win') ?> </th>
                                <th> <?php echo __('Count') ?> </th>
                                <th> <?php echo __('RTP') ?> </th>
                                <th> <?php echo __('Rate (EUR)') ?> </th>
                                <th> <?php echo __('In (EUR)') ?> </th>
                                <th> <?php echo __('Out (EUR)') ?> </th>
                                <th> <?php echo __('Win (EUR)') ?> </th>


                                </thead>
                                <tbody>
                                <?php foreach ($data as $date => $dayData): ?>

                                    <?php $is_conv = !$only_total; ?>

                                    <?php if (!$only_total) foreach ($dayData as $oid => $officeData): ?>

                                        <tr>
                                            <td <?php echo (date('N', strtotime($date)) == 5) ? 'style="color: #0a6ad2;"' : ''; ?> > <?php echo $date; ?>  </td>
                                            <td> <?php echo isset($owner_offices[$oid]) ? $owner_offices[$oid] : '' ?>  </td>
                                            <td> <?php echo Person::user()->officesName($oid, true) ?> </td>
                                            <td>
                                                <?php $is_conv = ($is_conv && !isset($day_rate[$date]) && !isset($day_rate[$date][$officeData['currency']])); ?>
                                                <?php echo $officeData['currency'] ?? '' ?>
                                                <?php echo !isset($day_rate[$date]) && !isset($day_rate[$date][$officeData['currency']]) ? ' (no rate)' : ''; ?>
                                            </td>
                                            <td> <?php echo th::float_format($officeData['in'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['out'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td <?php if ($officeData['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::float_format($officeData['win'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($officeData['count'] ?? 0, $officeData['mult'] ?? 2) ?> </td>
                                            <td <?php if ($officeData['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $officeData['rtp'] ?? 0 ?>
                                                %
                                            </td>
                                            <td><?php echo (isset($day_rate[$date]) && isset($day_rate[$date][$officeData['currency']]))?$day_rate[$date][$officeData['currency']]:'-'; ?> </td>
                                            <td><?php echo (isset($day_rate[$date]) && isset($day_rate[$date][$officeData['currency']]))?th::float_format($day_rate[$date][$officeData['currency']]*($officeData['in'] ?? 0), $officeData['mult'] ?? 2):'-'; ?> </td>
                                            <td><?php echo (isset($day_rate[$date]) && isset($day_rate[$date][$officeData['currency']]))?th::float_format($day_rate[$date][$officeData['currency']]*($officeData['out'] ?? 0), $officeData['mult'] ?? 2):'-'; ?> </td>
                                            <td><?php echo (isset($day_rate[$date]) && isset($day_rate[$date][$officeData['currency']]))?th::float_format($day_rate[$date][$officeData['currency']]*($officeData['win'] ?? 0), $officeData['mult'] ?? 2):'-'; ?> </td>
                                        </tr>
                                    <?php endforeach ?>


                                    <?php if ($office_id == -1) : ?>
                                        <!--                                <script>
                                                office_data_for_chart.series[0].push('<?php echo $totalOffice[$date]['win'] ?? 0; ?>');
                                                office_data_for_chart.series[1].push('<?php echo $totalOffice[$date]['count'] ?? 0; ?>');
                                            </script>-->
                                        <tr style="font-weight: bold; font-style: italic; ">
                                            <td <?php echo (date('N', strtotime($date)) == 5) ? 'style="color: #0a6ad2;"' : ''; ?> > <?php echo $date; ?>  </td>
                                            <td>&nbsp;</td>
                                            <td> Total day</td>
                                            <td>&nbsp;</td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['in'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['out'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td <?php if ($totalOffice[$date]['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::float_format($totalOffice[$date]['win'] ?? 0, $totalOffice[$date]['mult'] ?? 2) ?> </td>
                                            <td> <?php echo th::float_format($totalOffice[$date]['count'] ?? 0, $totalOffice['mult'] ?? 2) ?> </td>
                                            <td <?php if ($totalOffice[$date]['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $totalOffice[$date]['rtp'] ?? 0 ?>
                                                %
                                            </td>
                                            <td>&nbsp;</td>
                                            <td> <?php echo !$is_conv?th::float_format($totalOffice[$date]['inEUR'] ?? 0, $totalOffice[$date]['mult'] ?? 2):'-'; ?> </td>
                                            <td> <?php echo !$is_conv?th::float_format($totalOffice[$date]['outEUR'] ?? 0, $totalOffice[$date]['mult'] ?? 2):'-'; ?> </td>
                                            <td> <?php echo !$is_conv?th::float_format($totalOffice[$date]['winEUR'] ?? 0, $totalOffice[$date]['mult'] ?? 2):'-'; ?> </td>
                                        </tr>
                                    <?php endif ?>

                                <?php endforeach ?>

                                <?php foreach ($total['offices'] as $o_id => $office_total): ?>
                                    <tr style="font-weight: bold; font-style: italic; ">
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
                                        <td></td>
                                        <td> <?php echo th::float_format($office_total['inEUR'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['outEUR'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($office_total['winEUR'] ?? 0, $office_total['mult'] ?? 2) ?> </td>
                                    </tr>
                                <?php endforeach ?>

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
                                        <td></td>
                                        <td> <?php echo th::float_format($curr_total['inEUR'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['outEUR'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                        <td> <?php echo th::float_format($curr_total['winEUR'] ?? 0, $curr_total['mult'] ?? 2) ?> </td>
                                    </tr>
                                <?php endforeach ?>

                                <tr style="font-weight: bold">
                                    <td> <?php echo __('Total') ?>  </td>
                                    <td> <?php echo __('Total') ?> </td>
                                    <td><?php echo !$is_conv ? 'EUR' : ''; ?></td>
                                    <td>&nbsp;</td>
                                    <td> <?php echo th::number_format($total['in'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['out'] ?? 0) ?> </td>
                                    <td <?php if ($total['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['win'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['count'] ?? 0) ?> </td>
                                    <td <?php if ($total['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $total['rtp'] ?? 0 ?>
                                        %
                                    </td>
                                    <td></td>
                                    <td> <?php echo th::number_format($total['inEUR'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['outEUR'] ?? 0) ?> </td>
                                    <td> <?php echo th::number_format($total['winEUR'] ?? 0) ?> </td>
                                </tr>


                                </tbody>
                            </table>


                        </div>

                        To check the exchange rate:<br>
                        1) Go to the link <a href="https://www.xe.com/currencytables/" target="_blank">https://www.xe.com/currencytables/</a> <br>
                        2) Checking DATE, for example <?php echo date('Y-m-d',time()-Date::DAY) ?> and choose currency EUR<br>
                        3) Click Confirm<br>
                        4) Press the key combination Ctrl+F<br>
                        5) In the window, insert the currency, for example TRY<br>

                    </div>


                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>



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
</script>



