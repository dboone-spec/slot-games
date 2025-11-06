


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Dynamic report Local time</h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Date from (00:00:00)</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_start" name="time_from" value="<?php echo date('Y-m-d',strtotime($time_from)) ?>" >
                                    </div>
                                </div>


                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Date to (23:59:59)</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_end" name="time_to" value="<?php echo date('Y-m-d',strtotime($time_to)) ?>" >
                                    </div>
                                </div>


                                <div class="form-group col-md-1">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                        </div>
                                        <?php echo form::select('office_id',$officesList,$office_id,['class'=>'form-control form-control-sm'])?>
                                    </div>
                                </div>

                                <?php if(Person::$role=='sa'): ?>


                                    <div class="form-group col-md-1">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Owner</span>
                                            </div>
                                            <?php echo form::select('owner',$owners,$owner,['class'=>'form-control form-control-sm'])?>
                                        </div>
                                    </div>

                                <?php endif; ?>

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

                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('by_month',1,!!$by_month,['id'=>'_totalMId','class'=>'custom-control-input']) ?>
                                        <label class="custom-control-label" for="_totalMId">Show by month</label>
                                    </div>
                                </div>









                                <div class="non-form-control">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/reportdynamic"><?php echo __('Очистить') ?></a>
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

                                <th > <?php echo __('Users')?> </th>
                                <th > <?php echo __('Bets count')?> </th>
                                <th > <?php echo __('AVG bet')?> </th>

                                <th > <?php echo __('LS first time users')?> </th>
                                <th > <?php echo __('LS more times users')?> </th>
                                <th > <?php echo __('LS total users')?> </th>

                                <th > <?php echo __('DS total users')?> </th>

                                <th > <?php echo __('LS IN')?> </th>
                                <th > <?php echo __('DS IN')?> </th>
                                <th > <?php echo __('LS OUT')?> </th>
                                <th > <?php echo __('DS OUT')?> </th>
                                <th > <?php echo __('JP COUNT')?> </th>
                                <th > <?php echo __('JP OUT')?> </th>


                                </thead>
                                <tbody>
                                <?php foreach($data as $date=>$dayData):?>

                                    <?php if(!$only_total) foreach($dayData as $oid=>$officeData):?>

                                        <tr>
                                            <td > <?php echo $date; ?>  </td>
                                            <td > <?php echo isset($owner_offices[$oid])?$owner_offices[$oid]:'' ?>  </td>
                                            <td > <?php echo Person::user()->officesName($oid,true) ?> </td>
                                            <td > <?php echo $officeData['currency']??'' ?> </td>

                                            <td > <?php echo $officeData['users']??'0' ?> </td>
                                            <td > <?php echo $officeData['count']??'0' ?> </td>
                                            <td > <?php echo th::float_format($officeData['avg'] ?? 0,$officeData['mult']??2) ?> </td>
                                            <td > <?php echo $officeData['ls1']??'0' ?> </td>
                                            <td > <?php echo $officeData['ls2']??'0' ?> </td>
                                            <td > <?php echo $officeData['lsall']??'0' ?> </td>
                                            <td > <?php echo $officeData['ds']??'0' ?> </td>
                                            <td > <?php echo th::float_format($officeData['ls_in'] ?? 0,$officeData['mult']??2) ?> </td>
                                            <td > <?php echo th::float_format($officeData['ds_in'] ?? 0,$officeData['mult']??2) ?> </td>
                                            <td > <?php echo th::float_format($officeData['ls_out'] ?? 0,$officeData['mult']??2) ?> </td>
                                            <td > <?php echo th::float_format($officeData['ds_out'] ?? 0,$officeData['mult']??2) ?> </td>
                                            <td > <?php echo $officeData['jp_count']??'0' ?> </td>
                                            <td > <?php echo th::float_format($officeData['jp_out'] ?? 0,$officeData['mult']??2) ?> </td>
                                        </tr>
                                    <?php endforeach ?>


                                    <?php if($office_id==-1) :?>
                                        <tr>
                                            <td > <?php echo $date; ?>  </td>
                                            <td >&nbsp;</td>
                                            <td > Total </td>
                                            <td >&nbsp;</td>
                                            <td > <?php echo $totalOffice[$date]['users'] ?? 0; ?> </td>
                                            <td > <?php echo $totalOffice[$date]['count'] ?? 0; ?> </td>
                                            <td > <?php echo th::float_format($totalOffice[$date]['avg'] ?? 0,$totalOffice[$date]['mult']??2); ?> </td>
                                            <td > <?php echo $totalOffice[$date]['ls1'] ?? 0; ?> </td>
                                            <td > <?php echo $totalOffice[$date]['ls2'] ?? 0; ?> </td>
                                            <td > <?php echo $totalOffice[$date]['lsall'] ?? 0; ?> </td>
                                            <td > <?php echo $totalOffice[$date]['ds'] ?? 0; ?> </td>
                                            <td > <?php echo th::float_format($totalOffice[$date]['ls_in'] ?? 0,$totalOffice[$date]['mult']??2); ?> </td>
                                            <td > <?php echo th::float_format($totalOffice[$date]['ds_in'] ?? 0,$totalOffice[$date]['mult']??2); ?> </td>
                                            <td > <?php echo th::float_format($totalOffice[$date]['ls_out'] ?? 0,$totalOffice[$date]['mult']??2); ?> </td>
                                            <td > <?php echo th::float_format($totalOffice[$date]['ds_out'] ?? 0,$totalOffice[$date]['mult']??2); ?> </td>
                                            <td > <?php echo $totalOffice[$date]['jp_count'] ?? 0; ?> </td>
                                            <td > <?php echo th::float_format($totalOffice[$date]['jp_out'] ?? 0,$totalOffice[$date]['mult']??2); ?> </td>
                                        </tr>
                                    <?php endif ?>

                                <?php endforeach ?>

                                <?php foreach($total['offices'] as $o_id => $office_total): ?>
                                    <tr style="font-weight: bold; font-style: italic; ">
                                        <td > <?php echo __('Total') ?>  </td>
                                        <td > <?php echo isset($owner_offices[$o_id])?$owner_offices[$o_id]:'' ?> </td>
                                        <td > <?php echo Person::user()->officesName($o_id,true) ?> </td>
                                        <td> <?php echo $office_total['currency']??'' ?> </td>
                                        <td> <?php echo $office_total['users']??'0' ?> </td>
                                        <td> <?php echo $office_total['count']??'0' ?> </td>
                                        <td > <?php echo th::float_format($office_total['avg'] ?? 0,$office_total['mult']??2) ?> </td>
                                        <td> <?php echo $office_total['ls1']??'0' ?> </td>
                                        <td> <?php echo $office_total['ls2']??'0' ?> </td>
                                        <td> <?php echo $office_total['lsall']??'0' ?> </td>
                                        <td> <?php echo $office_total['ds']??'0' ?> </td>
                                        <td > <?php echo th::float_format($office_total['ls_in'] ?? 0,$office_total['mult']??2) ?> </td>
                                        <td > <?php echo th::float_format($office_total['ds_in'] ?? 0,$office_total['mult']??2) ?> </td>
                                        <td > <?php echo th::float_format($office_total['ls_out'] ?? 0,$office_total['mult']??2) ?> </td>
                                        <td > <?php echo th::float_format($office_total['ds_out'] ?? 0,$office_total['mult']??2) ?> </td>
                                        <td> <?php echo $office_total['jp_count']??'0' ?> </td>
                                        <td > <?php echo th::float_format($office_total['jp_out'] ?? 0,$office_total['mult']??2) ?> </td>
                                    </tr>
                                <?php endforeach ?>

                                <?php foreach($total['currencies'] as $cur => $curr_total): ?>
                                    <tr style="font-weight: bold; font-style: italic; ">
                                        <td > <?php echo __('Total') ?>  </td>
                                        <td>  </td>
                                        <td > <?php echo $cur ?> </td>
                                        <td>  </td>
                                        <td > <?php echo $curr_total['users'] ?? 0 ?> </td>
                                        <td > <?php echo $curr_total['count'] ?? 0 ?> </td>
                                        <td > <?php echo th::float_format($curr_total['avg'] ?? 0,$curr_total['mult']??2) ?> </td>
                                        <td > <?php echo $curr_total['ls1'] ?? 0 ?> </td>
                                        <td > <?php echo $curr_total['ls2'] ?? 0 ?> </td>
                                        <td > <?php echo $curr_total['lsall'] ?? 0 ?> </td>
                                        <td > <?php echo $curr_total['ds'] ?? 0 ?> </td>
                                        <td > <?php echo th::float_format($curr_total['ls_in'] ?? 0,$curr_total['mult']??2) ?> </td>
                                        <td > <?php echo th::float_format($curr_total['ds_in'] ?? 0,$curr_total['mult']??2) ?> </td>
                                        <td > <?php echo th::float_format($curr_total['ls_out'] ?? 0,$curr_total['mult']??2) ?> </td>
                                        <td > <?php echo th::float_format($curr_total['ds_out'] ?? 0,$curr_total['mult']??2) ?> </td>
                                        <td > <?php echo $curr_total['jp_count'] ?? 0 ?> </td>
                                        <td > <?php echo th::float_format($curr_total['jp_out'] ?? 0,$curr_total['mult']??2) ?> </td>
                                    </tr>
                                <?php endforeach ?>

                                <tr style="font-weight: bold;">
                                    <td > <?php echo __('Total')?>  </td>
                                    <td > <?php echo __('Total')?> </td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td > <?php echo $total['users'] ?? 0 ?> </td>
                                    <td > <?php echo $total['count'] ?? 0 ?> </td>
                                    <td > <?php echo th::number_format($total['avg'] ?? 0) ?> </td>
                                    <td > <?php echo $total['ls1'] ?? 0 ?> </td>
                                    <td > <?php echo $total['ls2'] ?? 0 ?> </td>
                                    <td > <?php echo $total['lsall'] ?? 0 ?> </td>
                                    <td > <?php echo $total['ds'] ?? 0 ?> </td>
                                    <td > <?php echo th::number_format($total['ls_in'] ?? 0) ?> </td>
                                    <td > <?php echo th::number_format($total['ds_in'] ?? 0) ?> </td>
                                    <td > <?php echo th::number_format($total['ls_out'] ?? 0) ?> </td>
                                    <td > <?php echo th::number_format($total['ds_out'] ?? 0) ?> </td>
                                    <td > <?php echo $total['jp_count'] ?? 0 ?> </td>
                                    <td > <?php echo th::number_format($total['jp_out'] ?? 0) ?> </td>
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





