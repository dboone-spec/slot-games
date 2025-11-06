


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Game statistic <?php echo date('F',mktime(0,0,0,$month,2,$year) )." $year";  ?></h4>
                        <hr>

                        <form method="GET" class="form-horizontal">

                                <div class="form-row">

                                    <div class="form-group col-md-1">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Month</span>
                                            </div>
                                            <?php echo form::select('month',[1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12'],$month,['class'=>'form-control form-control-sm']) ?>
                                        </div>
                                    </div>


                                    <div class="form-group col-md-1">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Year</span>
                                            </div>
                                            <?php $ys = range(2017,date('Y')); $years = array_combine($ys, $ys); ?>
                                            <?php echo form::select('year',$years,$year,['class'=>'form-control form-control-sm']) ?>
                                        </div>
                                    </div>




                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                            </div>
                                            <?php echo form::select('office_id',$officesList,$office_id,['class'=>'form-control form-control-sm select2'])?>
                                        </div>
                                    </div>

                                     <?php if(Person::$role=='sa' || Person::$user_id==1214): ?>

                                        <div class="form-group col-md-2">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm">Partner</span>
                                                </div>
                                                <?php echo form::select('parnerId',$partnersList,$partnerId,['class'=>'form-control form-control-sm select2'])?>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-1">
                                            <div class="custom-control custom-checkbox">
                                                <?php echo form::checkbox('is_test',1,(bool) $is_test,['id'=>'_isTestId','class'=>'custom-control-input'])?>
                                                <label class="custom-control-label" for="_isTestId">Test offices</label>
                                            </div>
                                        </div>

                                        







                                    <?php endif; ?>


									 <div class="form-group col-md-1">
                                             <div class="custom-control custom-checkbox">
                                                 <?php echo form::checkbox('eur',1,(bool) $eur,['id'=>'_eur','class'=>'custom-control-input'])?>
                                                 <label class="custom-control-label" for="_eur">Convert to EUR</label>
                                             </div>
                                         </div>



                                    <div class="w-100"></div>

                                    <div class="non-form-control ml-auto">
                                        <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                    </div>
                                    <div>
                                        <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/statisticgamemonth"><?php echo __('Очистить') ?></a>
                                    </div>
                                </div>
                        </form>
                        <hr>
                        Offices: <?php echo implode(', ',$offices); ?>
                        <hr>



                </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                <thead>
                                    <tr>
                                        <th rowspan="3" class="tddate"> <?php echo __('Game')?> </th>
                                        <?php if ($currency): ?>
                                            <th rowspan="3" class="tddate"> <?php echo __('Currency')?> </th>
                                        <?php endif; ?>
                                    </tr>

                                    <tr>
                                            <th colspan="6"> <?php echo __('Normal')?> </th>
                                            <th colspan="6"> <?php echo __('Double')?> </th>
                                    </tr>

                                    <tr>

                                        <th class="adminSortable" > <?php echo __('In')?> </th>
                                        <th class="adminSortable"> <?php echo __('Out')?> </th>
                                        <th class="adminSortable"> <?php echo __('Win')?> </th>
                                        <th class="adminSortable" > <?php echo __('Count')?> </th>
                                        <th class="adminSortable" > <?php echo __('RTP')?> </th>
                                        <th class="adminSortable" > <?php echo __('Avg bet')?> </th>

                                        <th class="adminSortable" > <?php echo __('In')?> </th>
                                        <th class="adminSortable" > <?php echo __('Out')?> </th>
                                        <th class="adminSortable" > <?php echo __('Win')?> </th>
                                        <th class="adminSortable" > <?php echo __('Count')?> </th>
                                        <th class="adminSortable" > <?php echo __('RTP')?> </th>
                                        <th class="adminSortable" > <?php echo __('Avg bet')?> </th>

                                    </tr>

                                </thead>
                                <tbody>
                                    <?php  foreach($data as $game=>$gameData):?>
                                        <tr >
                                            <td class="tddate"> <?php echo $game  ?> </td>
                                            <?php if ($currency): ?>
                                                <td> <?php echo $currency->name ?> </td>
                                            <?php endif; ?>

                                                    <td adminSortableValue="<?php echo $gameData['normal']['in'] ?? 0; ?>"> <?php echo th::number_format($gameData['normal']['in'] ?? 0) ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['normal']['out'] ?? 0; ?>"> <?php echo th::number_format($gameData['normal']['out'] ?? 0) ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['normal']['win'] ?? 0; ?>" <?php if( ($gameData['normal']['win']??0 )<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($gameData['normal']['win'] ?? 0) ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['normal']['count'] ?? 0; ?>"> <?php echo $gameData['normal']['count'] ?? 0 ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['normal']['rtp'] ?? 0; ?>"> <?php echo $gameData['normal']['rtp'] ?? 0 ?>% </td>
                                                    <td adminSortableValue="<?php echo $gameData['normal']['avgbet'] ?? 0; ?>"> <?php echo $gameData['normal']['avgbet'] ?? 0 ?> </td>

                                                    <td adminSortableValue="<?php echo $gameData['double']['in'] ?? 0; ?>"> <?php echo th::number_format($gameData['double']['in'] ?? 0) ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['double']['out'] ?? 0; ?>"> <?php echo th::number_format($gameData['double']['out'] ?? 0) ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['double']['win'] ?? 0; ?>" <?php if(isset($gameData['double']) && $gameData['double']['win']<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($gameData['double']['win'] ?? 0) ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['double']['count'] ?? 0; ?>"> <?php echo $gameData['double']['count'] ?? 0 ?> </td>
                                                    <td adminSortableValue="<?php echo $gameData['double']['rtp'] ?? 0; ?>"> <?php echo $gameData['double']['rtp'] ?? 0 ?>% </td>
                                                    <td adminSortableValue="<?php echo $gameData['double']['avgbet'] ?? 0; ?>"> <?php echo $gameData['double']['avgbet'] ?? 0 ?> </td>
                                        </tr>

                                    <?php endforeach ?>

                                    <tr>
                                        <td class="tddate"> <?php echo __('Total')?> </td>
                                                <?php if ($currency): ?>
                                                    <td> <?php echo $currency->name ?> </td>
                                                <?php endif; ?>
                                                <td> <?php echo th::number_format($total['normal']['in'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['normal']['out'] ?? 0) ?> </td>
                                                <td <?php if( ($total['normal']['win'] ?? 0) <0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['normal']['win'] ?? 0) ?> </td>
                                                <td> <?php echo $total['normal']['count'] ?? 0 ?> </td>
                                                <td> <?php echo $total['normal']['rtp'] ?? 0 ?>% </td>
                                                <td> <?php echo $total['normal']['avgbet'] ?? 0 ?> </td>

                                                <td> <?php echo th::number_format($total['double']['in'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['double']['out'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['double']['win'] ?? 0) ?> </td>
                                                <td> <?php echo $total['double']['count'] ?? 0 ?> </td>
                                                <td> <?php echo $total['double']['rtp'] ?? 0 ?>% </td>
                                                <td> <?php echo $total['double']['avgbet'] ?? 0 ?> </td>

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



<style>
    .statrow:not(.total) {
        font-style: italic;
    }
</style>


<script>
        function redraw() {
            $('tr:visible').css('background-color','rgba(114, 103, 239, 0)');
            $('tr:visible:even').each(function(k,el) {
                $(el).css('background-color','rgba(114, 103, 239, 0.03)');
            });
        }

        $(function(){
                $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});
        });

        $('.statrow').hide();
        $('.statrow.total').show();

        redraw();
</script>