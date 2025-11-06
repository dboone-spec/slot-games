<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Month report UTC <?php echo $months[intval($m)] . " $y" ?> </h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Month</span>
                                        </div>
                                        <?php echo form::select('m', $months, $m, ['class' => "form-control form-control-sm"]) ?>
                                    </div>
                                </div>


                                <div class="form-group col-md-1">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Year</span>
                                        </div>
                                        <?php echo form::select('y', $years, $y, ['class' => "form-control form-control-sm"]) ?>
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

                                <?php if (Person::user()->showOwners()): ?>


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


                                <div class="w-100"></div>

                                <div class="non-form-control ml-auto">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit"
                                           value="<?php echo __('Поиск') ?>"/>
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-round btn-outline-secondary"
                                       href="/enter/reportmonth"><?php echo __('Очистить') ?></a>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-1">
                                    <a href="javascript:currToggle();">Currencies filter</a>
                                </div>

                                <div class="form-group col-md-1">
                                    <a href="javascript:allToggle();">Check all currencies</a>
                                </div>

                                <div class="form-group col-md-1">
                                    <a href="javascript:asiaToggle();">Check Asia currencies</a>
                                </div>
                            </div>

                            <div id="currencyList" <?php if(count($currenciesChoosed)==count($currencies)): ?> style="display: none;"<?php endif; ?> class="form-row">

                                <?php foreach ($currencies as $currency): ?>

                                    <div class="form-group col-md-1">
                                        <div class="custom-control custom-checkbox">
                                            <?php echo form::checkbox('curr[]',$currency,in_array($currency,$currenciesChoosed),['id'=>"id{$currency}" ,'class'=>'custom-control-input'] )?>
                                            <label class="custom-control-label" for="<?php echo "id{$currency}" ?>"><?php echo $currency ?> </label>
                                        </div>
                                    </div>


                                <?php endforeach; ?>

                            </div>
                        </form>


                    </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">

                            <?php if ($bad): ?>
                                No exchange rates found. Usually rates are loaded after 8:00 UTC on the first day of the next month after selected.
                            <?php else: ?>
                                <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                    <thead>

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


                                    <?php foreach ($data as $row): ?>
                                    <?php if(!empty($currenciesChoosed) && !in_array($row['code'],$currenciesChoosed)) continue; ?>
                                        <tr>
                                            <td> <?php echo $row['owner'] ?>  </td>
                                            <td> <?php echo $row['office'] ?> </td>
                                            <td> <?php echo $row['code'] ?? '' ?> </td>
                                            <td> <?php echo th::number_format($row['in'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($row['out'] ?? 0) ?> </td>
                                            <td <?php if ($row['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::number_format($row['win'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($row['count'] ?? 0) ?> </td>
                                            <td <?php if ($row['rtp'] >= 100): ?>style="color:red"<?php endif; ?>> <?php echo $row['rtp'] ?? 0 ?>
                                                %
                                            </td>
                                            <td> <?php echo $row['rate'] ?> </td>
                                            <td> <?php echo th::number_format($row['curencyIn'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($row['curencyOut'] ?? 0) ?> </td>
                                            <td <?php if ($row['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::number_format($row['curencyWin'] ?? 0) ?> </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <tr>
                                        <td></td>
                                        <td><b>Total</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><?php echo th::number_format($total['count'] ?? 0) ?></b></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><?php echo th::number_format($total['curencyIn'] ?? 0) ?> </b></td>
                                        <td><b><?php echo th::number_format($total['curencyOut'] ?? 0) ?></b></td>
                                        <td <?php if ($total['win'] < 0): ?>style="color:red"<?php endif; ?>>
                                            <b><?php echo th::number_format($total['curencyWin'] ?? 0) ?> </b></td>
                                    </tr>


                                    <?php foreach ($byCurrency as $currency => $row): ?>
                                        <tr>
                                            <td> <?php echo 'Total per Currency' ?>  </td>
                                            <td></td>
                                            <td> <?php echo $currency ?> </td>
                                            <td> <?php echo th::number_format($row['in'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($row['out'] ?? 0) ?> </td>
                                            <td <?php if ($row['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::number_format($row['win'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($row['count'] ?? 0) ?> </td>
                                            <td
                                            </td>
                                            <td> <?php echo $row['rate'] ?> </td>
                                            <td> <?php echo th::number_format($row['curencyIn'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($row['curencyOut'] ?? 0) ?> </td>
                                            <td <?php if ($row['win'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::number_format($row['curencyWin'] ?? 0) ?> </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <tr>
                                        <td></td>
                                        <td><b>Total</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><?php echo th::number_format($total['count'] ?? 0) ?></b></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><?php echo th::number_format($total['curencyIn'] ?? 0) ?> </b></td>
                                        <td><b><?php echo th::number_format($total['curencyOut'] ?? 0) ?></b></td>
                                        <td <?php if ($total['win'] < 0): ?>style="color:red"<?php endif; ?>>
                                            <b><?php echo th::number_format($total['curencyWin'] ?? 0) ?> </b></td>
                                    </tr>

                                    <?php foreach ($byExternalName as $name => $row): ?>
                                        <tr>
                                            <td> <?php echo 'Total per Operator' ?>  </td>
                                            <td>  <?php echo $name ?>   </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td
                                            </td>
                                            <td> <?php echo th::number_format($row['count'] ?? 0) ?> </td>
                                            <td
                                            </td>
                                            <td></td>
                                            <td> <?php echo th::number_format($row['curencyIn'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($row['curencyOut'] ?? 0) ?> </td>
                                            <td <?php if ($row['curencyWin'] < 0): ?>style="color:red"<?php endif; ?>> <?php echo th::number_format($row['curencyWin'] ?? 0) ?> </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <tr>
                                        <td></td>
                                        <td><b>Total</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><?php echo th::number_format($total['count'] ?? 0) ?></b></td>
                                        <td></td>
                                        <td></td>
                                        <td><b><?php echo th::number_format($total['curencyIn'] ?? 0) ?> </b></td>
                                        <td><b><?php echo th::number_format($total['curencyOut'] ?? 0) ?></b></td>
                                        <td <?php if ($total['win'] < 0): ?>style="color:red"<?php endif; ?>>
                                            <b><?php echo th::number_format($total['curencyWin'] ?? 0) ?> </b></td>
                                    </tr>


                                    </tbody>
                                </table>
                            <?php endif; ?>


                        </div>
                        <?php if (!$bad): ?>

                            <div>
                                <a class="btn btn-sm btn btn-primary btn-round"
                                   href="<?php echo url::query(['xls' => 'go']) ?>"> Export to excel </a>
                            </div>


                            <br>
                            To check the exchange rate:<br>
                            1) Go to the link <a href="https://www.xe.com/currencytables/" target="_blank">https://www.xe.com/currencytables/</a>
                            <br>
                            2) Checking DATE <?php echo $timeTo ?> and choose currency EUR<br>
                            3) Click Confirm<br>
                            4) Press the key combination Ctrl+F<br>
                            5) In the window, insert the currency, for example TRY<br>
                            <br><br>
                            6) If currency not exists at xe.com, find out it at <a href="https://coinmarketcap.com/" target="_blank">https://coinmarketcap.com/</a>, for example TRX<br>
                            7) Choose "EUR" currency at settings<br>
                            8) Press the key combination Ctrl+F<br>
                            9) In the window, find "See historical data"<br>
                            10) Click "See historical data" and find DATE <?php echo $timeTo ?><br>
                            11) Use "Close**" column
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>

<script>
    function currToggle() {
        $('#currencyList').toggle();
    }
    function asiaToggle() {
        $('#currencyList input').attr('checked',false);
        ['VND','KHR','LAK','MMK','THB','BND','PHP','MYR','IDR','SGD'].forEach(function(v) {
            $('#id'+v).attr('checked',true);
        });
    }
    function allToggle() {
        $('#currencyList input').attr('checked',true);
    }
</script>

<style>
    #time_start:after {
        content: "00:00:00";
        position: absolute;
        display: block;
        width: 54px;
        left: 83px;
        right: 0;
        overflow: hidden;
    }

    #time_end:after {
        content: "23:59:59";
        position: absolute;
        display: block;
        width: 54px;
        left: 83px;
        right: 0;
        overflow: hidden;
    }
</style>






