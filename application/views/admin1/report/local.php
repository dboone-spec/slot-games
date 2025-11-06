


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Report at offices's local time </h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                                <div class="form-row">

                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Date from</span>
                                            </div>
                                            <input type="date" class="form-control form-control-sm" id="time_start" name="time_from" value="<?php echo date('Y-m-d',strtotime($time_from)) ?>" >
                                        </div>
                                    </div>


                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Date to</span>
                                            </div>
                                            <input type="date" class="form-control form-control-sm" id="time_end" name="time_to" value="<?php echo date('Y-m-d',strtotime($time_to)) ?>" >
                                        </div>
                                    </div>




                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                            </div>
                                            <?php echo form::select('office_id',$officesList,$office_id,['class'=>'form-control form-control-sm sd select2'])?>
                                        </div>
                                    </div>

                                     <?php if(Person::$role=='sa'): ?>


                                        <div class="form-group col-md-2">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm">Owner</span>
                                                </div>
                                                <?php echo form::select('owner',$owners,$owner,['class'=>'form-control form-control-sm select2'])?>
                                            </div>
                                        </div>

                                         <div class="w-100"></div>

                                        <div class="form-group col-md-1">
                                            <div class="custom-control custom-checkbox">
                                                <?php echo form::checkbox('is_test',1,(bool) $is_test,['id'=>'_isTestId','class'=>'custom-control-input'])?>
                                                <label class="custom-control-label" for="_isTestId">Test offices</label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-1">
                                            <div class="custom-control custom-checkbox">
                                                <?php echo form::checkbox('convert',1,(bool) $convert,['id'=>'_converId','class'=>'custom-control-input'])?>
                                                <label class="custom-control-label" for="_converId">Convert to EUR</label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-1">
                                            <div class="custom-control custom-checkbox">
                                                <?php echo form::checkbox('only_total',1,!!$only_total,['id'=>'_totalId','class'=>'custom-control-input']) ?>
                                                <label class="custom-control-label" for="_totalId">Show only total</label>
                                            </div>
                                        </div>

                                    <?php endif; ?>








                                    <div class="w-100"></div>

                                    <div class="non-form-control ml-auto">
                                        <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                    </div>
                                    <div>
                                        <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/reportlocal"><?php echo __('Очистить') ?></a>
                                    </div>
                                </div>
                        </form>


                </div>

                                     <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                <thead>

                                    <th class="tddate"> <?php echo __('Date')?> </th>
                                    <th class="tddate"> <?php echo __('Owner')?> </th>
                                    <th class="tddate"> <?php echo __('Office')?> </th>
                                     <th > <?php echo __('Currency')?> </th>
                                     <th > <?php echo __('In')?> </th>
                                    <th > <?php echo __('Out')?> </th>
                                    <th > <?php echo __('Win')?> </th>
                                    <th > <?php echo __('Count')?> </th>
                                    <th > <?php echo __('RTP')?> </th>
                                    <?php if (Person::$role=='sa'): ?>
                                        <th > <?php echo __('FS Cashback In')?> </th>
                                        <th > <?php echo __('FS LuckySpins In')?> </th>
                                        <th > <?php echo __('FS API In')?> </th>
                                    <?php endif; ?>




                                </thead>
                                <tbody>
                                    <?php foreach($data as $date=>$dayData):?>


                                        <?php if(!$only_total) foreach($dayData as $oid=>$officeData):?>

                                        <tr>
                                            <td > <?php echo $date ?>  </td>
                                            <td > <?php echo isset($owner_offices[$oid])?$owner_offices[$oid]:'' ?>  </td>
                                            <td > <?php echo Person::user()->officesName($oid,true) ?> </td>
                                            <td > <?php echo $officeData['currency']??'' ?> </td>
                                            <td > <?php echo th::number_format($officeData['in'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($officeData['out'] ?? 0) ?> </td>
                                            <td <?php if($officeData['win']<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($officeData['win'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($officeData['count'] ?? 0) ?> </td>
                                            <td <?php if($officeData['rtp']>=100):?>style="color:red"<?php endif; ?>> <?php echo $officeData['rtp'] ?? 0 ?>% </td>
                                            <?php if (Person::$role=='sa'): ?>
                                             <td > <?php echo th::number_format($officeData['cfsin'] ?? 0) ?> </td>
                                             <td > <?php echo th::number_format($officeData['lfsin'] ?? 0) ?> </td>
                                             <td > <?php echo th::number_format($officeData['afsin'] ?? 0) ?> </td>

                                             <?php endif; ?>
                                        </tr>
                                        <?php endforeach ?>


                                        <?php if($office_id==-1) :?>
            <!--                                <script>
                                                office_data_for_chart.series[0].push('<?php echo $totalOffice[$date]['win'] ?? 0; ?>');
                                                office_data_for_chart.series[1].push('<?php echo $totalOffice[$date]['count'] ?? 0; ?>');
                                            </script>-->
                                            <tr>
                                                <td > <?php echo $date ?>  </td>
                                                <td >&nbsp;</td>
                                                <td > Total </td>
                                                <td >&nbsp;</td>
                                                <td > <?php echo th::number_format($totalOffice[$date]['in'] ?? 0) ?> </td>
                                                <td > <?php echo th::number_format($totalOffice[$date]['out'] ?? 0) ?> </td>
                                                <td <?php if($totalOffice[$date]['win']<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($totalOffice[$date]['win'] ?? 0) ?> </td>
                                                <td > <?php echo th::number_format($totalOffice[$date]['count'] ?? 0) ?> </td>
                                                <td <?php if($totalOffice[$date]['rtp']>=100):?>style="color:red"<?php endif; ?>> <?php echo $totalOffice[$date]['rtp'] ?? 0 ?>% </td>
                                                <?php if (Person::$role=='sa'): ?>
                                                <td > <?php echo th::number_format($totalOffice[$date]['cfsin'] ?? 0) ?> </td>
                                                <td > <?php echo th::number_format($totalOffice[$date]['lfsin'] ?? 0) ?> </td>
                                                <td > <?php echo th::number_format($totalOffice[$date]['afsin'] ?? 0) ?> </td>

                                                <?php endif; ?>
                                            </tr>
                                        <?php endif ?>

                                    <?php endforeach ?>

                                    <?php foreach($total['offices'] as $o_id => $office_total): ?>
                                        <tr style="font-weight: bold; font-style: italic; ">
                                            <td > <?php echo __('Total') ?>  </td>
                                            <td > <?php echo isset($owner_offices[$o_id])?$owner_offices[$o_id]:'' ?> </td>
                                            <td > <?php echo Person::user()->officesName($o_id,true) ?> </td>
                                            <td> <?php echo $office_total['currency']??'' ?> </td>
                                            <td > <?php echo th::number_format($office_total['in'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($office_total['out'] ?? 0) ?> </td>
                                            <td <?php if($office_total['win']<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($office_total['win'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($office_total['count'] ?? 0) ?> </td>
                                            <td <?php if($office_total['rtp']>=100):?>style="color:red"<?php endif; ?>> <?php echo $office_total['rtp'] ?? 0 ?>% </td>
                                            <?php if (Person::$role=='sa'): ?>
                                            <td > <?php echo th::number_format($office_total['cfsin'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($office_total['lfsin'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($office_total['afsin'] ?? 0) ?> </td>

                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach ?>

                                    <?php foreach($total['currencies'] as $cur => $curr_total): ?>
                                        <tr style="font-weight: bold; font-style: italic; ">
                                            <td > <?php echo __('Total') ?>  </td>
                                            <td>  </td>
                                            <td > <?php echo $cur ?> </td>
                                            <td>  </td>
                                            <td > <?php echo th::number_format($curr_total['in'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($curr_total['out'] ?? 0) ?> </td>
                                            <td <?php if($curr_total['win']<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($curr_total['win'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($curr_total['count'] ?? 0) ?> </td>
                                            <td> </td>
                                            <?php if (Person::$role=='sa'): ?>
                                            <td > <?php echo th::number_format($curr_total['cfsin'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($curr_total['lfsin'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($curr_total['afsin'] ?? 0) ?> </td>

                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach ?>

                                        <tr style="font-weight: bold;">
                                            <td > <?php echo __('Total')?>  </td>
                                            <td > <?php echo __('Total')?> </td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td > <?php echo th::number_format($total['in'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($total['out'] ?? 0) ?> </td>
                                            <td <?php if($total['win']<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['win'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($total['count'] ?? 0) ?> </td>
                                            <td <?php if($total['rtp']>=100):?>style="color:red"<?php endif; ?>> <?php echo $total['rtp'] ?? 0 ?>% </td>
                                            <?php if (Person::$role=='sa'): ?>
                                            <td > <?php echo th::number_format($total['cfsin'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($total['lfsin'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($total['afsin'] ?? 0) ?> </td>

                                            <?php endif; ?>
                                        </tr>


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










