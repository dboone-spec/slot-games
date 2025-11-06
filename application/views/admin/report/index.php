  <style>
                .datatable td,.datatable th{
                    border: 1px solid black;
                    text-align: center;
                    vertical-align: middle;
                    min-width: 150px;
                    padding: 2px 4px;
                }
                .datatable td:nth-child(3),.datatable th:nth-child(3),
                .datatable td:nth-child(4),.datatable th:nth-child(4),
                .datatable td:nth-child(6),.datatable th:nth-child(6),
                .datatable td:nth-child(5),.datatable th:nth-child(5){
                    text-align: right;
                }
                .tddate {
                    min-width: 90px !important;
                }
                .datatable th {
                    background-color: #6d9dff;
                }


                .datatable tr:nth-child(even) {
                    background-color: #dce7fe;
                }


                #head, #left_col{
                    display: grid;
                    position: absolute;
                    grid-row-gap: 0px;
                    grid-column-gap: 0px;
                    background-color: white;
                    border-bottom: 1px solid black !important;
                    border-right: 1px solid black !important;
                }
                #head div, #left_col div{
                    padding: 10px;
                    text-align: center;
                    border-left: 1px solid black !important;
                    border-top: 1px solid black !important;
                }
                .sort{
                    cursor: pointer;
                    position: relative;
                }
                .desc::before{
                    content: '\25bc';
                    position: absolute;
                    bottom: 5px;
                    left: calc(50% - 7px);
                }
                .asc::before{
                    content: '\25b2';
                    position: absolute;
                    bottom: 5px;
                    left: calc(50% - 7px);
                }
                thead {
                    /*visibility: hidden;*/
                }
                #left_col {
                    /*visibility: hidden;*/
                    /*background: none;*/
                }
                .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {padding: 5px;}
            </style>
<script>
var office_data_for_chart = {
    labels: [],
    series: [
        [],
        [],
        [],
        [],
        [],
    ]
};
</script>
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo __('Office report')?></h1>

            <div class="row">
                <div class="col-sm-12" >
                    <form method="GET" class="form-horizontal">
                        <div >
                            <table>
                                <tr>
                                    <td style="width:150px">
                                        <label><?php echo __('Date from')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Date to')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Office')?></label>
                                    </td>
                                    <?php if(Person::$role=='sa'): ?>
                                    <td style="width:150px">
                                        <label><?php echo __('Owner')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Test offices')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Convert')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Show only total')?></label>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                 <tr>
                                    <td>
                                        <input type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d',strtotime($time_from)) ?>" >
                                    </td>
                                    <td>
                                        <input type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d',strtotime($time_to)) ?>" >
                                    </td>
                                    <td>
                                        <?php echo form::select('office_id',$officesList,$office_id)?>
                                    </td>
                                    <?php if(Person::$role=='sa'): ?>
                                    <td>
                                        <?php echo form::select('owner',$owners,$owner)?>
                                    </td>
                                    <td>
                                        &nbsp;&nbsp;&nbsp;<?php echo form::checkbox('is_test',1,(bool) $is_test)?>
                                    </td>
                                    <td>
                                        &nbsp;&nbsp;&nbsp;<?php echo form::checkbox('convert',1,(bool) $convert)?>
                                    </td>
                                    <td>
                                        &nbsp;&nbsp;&nbsp;<?php echo form::checkbox('only_total',1,!!$only_total) ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            </table>
                            <br>

                        </div>



                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="<?php echo __('Find')?>" />
                            <a class="btn btn-default" href="/enter/report"><?php echo __('Clear')?></a>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 all_games">

                </div>
            </div>


             <div class="row">
                <div class="col-sm-12" id='scrollblock' style="overflow-x: scroll;  padding-left: 0px; margin-left: 15px;">
                    <div id="head"></div>
                    <div id="left_col"></div>

                      <table class="datatable"  >

                        <tr>
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
                             <th > <?php echo __('FS API In')?> </th>
                            <th > <?php echo __('Users')?> </th>
                                <th > <?php echo __('New users')?> </th>
                            <?php endif; ?>

                        </tr>


                        <?php foreach($data as $date=>$dayData):?>

<!--                            <script>
                                office_data_for_chart.lables.push('<?php echo $date; ?>');
                            </script>-->

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
                                 <td > <?php echo th::number_format($officeData['afsin'] ?? 0) ?> </td>
                                 <td > <?php echo th::number_format($officeData['users']) ?> </td>
                                 <td > <?php echo th::number_format($officeData['newusers']) ?> </td>
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
                                    <td > <?php echo th::number_format($totalOffice[$date]['afsin'] ?? 0) ?> </td>
                                    <td > <?php echo th::number_format($totalOffice[$date]['users'] ?? 0) ?> </td>
                                    <td > <?php echo th::number_format($totalOffice[$date]['newusers'] ?? 0) ?> </td>
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
                                <td > <?php echo th::number_format($office_total['afsin'] ?? 0) ?> </td>
                                <td > - </td>
                                <td > <?php echo th::number_format($office_total['newusers'] ?? 0) ?>   </td>
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
                                <td > <?php echo th::number_format($curr_total['afsin'] ?? 0) ?> </td>
                                <td> - </td>
                                <td > <?php echo th::number_format($curr_total['newusers'] ?? 0) ?>  </td>
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
                                <td > <?php echo th::number_format($total['afsin'] ?? 0) ?> </td>
                                <td> - </td>
                                <td > <?php echo th::number_format($total['newusers'] ?? 0) ?></td>
                                <?php endif; ?>
                            </tr>



                        <tbody>
                        </tbody>
                    </table>

                    </div>
            </div>


          </div>
    </div>

</div>

<div class="ct-chart ct-perfect-fourth"></div>

<script>


        $(function(){

//                new Chartist.Line('.ct-chart', office_data_for_chart, {
//                    width: 800,
//                    height: 600
//                });
                $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});
        });
</script>
<style>
    .hasDatepicker {
        width: 87px;
    }
</style>