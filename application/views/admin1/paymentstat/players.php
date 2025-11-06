<style>
    .datatable td,.datatable th{
        border: 1px solid black;
        text-align: center;
        vertical-align: middle;
        min-width: 150px;
        padding: 2px 4px;
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


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Cash statistics by user</h4>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <form method="GET" class="form-horizontal">
                                    <div>
                                        <table>
                                            <tr>
                                                <td style="width:150px">
                                                    <label><?php echo __('Date range') ?></label>
                                                </td>
                                                <?php if(person::$role!='cashier'): ?>
                                                <td style="width:150px">
                                                    <label><?php echo __('Office') ?></label>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input hidden="hidden" type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d',($time_from)) ?>" >
                                                    <input hidden="hidden" type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d',($time_to)) ?>" >
                                                    <input type="text" id="time_range" name="time_range" value="" >
                                                </td>
                                                <?php if(person::$role!='cashier'): ?>
                                                <td>
                                                    <?php echo form::select('office_id',$offices,$current_office) ?>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                        </table>
                                        <br>

                                    </div>



                                    <div class="form-group">
                                        <input class="btn btn-primary" type="submit" value="<?php echo __('Find') ?>" />
                                        <a class="btn btn-default" href="<?php echo $dir ?>/operationstat"><?php echo __('Очистить'); ?></a>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="datatable<?php // echo $dir    ?>" >
                                            <?php foreach($all as $currency => $person): ?>
                                                <?php foreach($person as $person_id => $val): ?>
                                                    <?php foreach($val as $type => $v): ?>
                                                        <tr>
                                                            <th><?php echo __('Итого'); ?></th>
                                                            <th>USER ID: <?php echo $person_id ?></th>
                                                            <th>DROP: <?php echo th::number_format($v['total_in']); ?></th>
                                                            <th>HANDPAY: <?php echo th::number_format($v['total_out']); ?></th>
                                                            <th>WIN: <?php echo th::number_format($v['total']); ?></th>
                                                            <th>PROFIT: <?php echo th::number_format(100*$v['total']/$v['total_in']); ?>%</th>
                                                        </tr>
                                                    <?php endforeach ?>
                                                <?php endforeach ?>
                                            <?php endforeach ?>
                                        </table>
                                    </div>
                                </div>
                                <br />
                                <br />
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="datatable <?php // echo $dir    ?>" >
                                            <tr>
                                                <?php foreach($headers as $header): ?>
                                                    <th><?php echo $header; ?></th>
                                                <?php endforeach ?>
                                            </tr>
                                            <?php foreach($data as $date => $value): ?>
                                                <tr>
                                                    <td><?php echo date('Y-m-d',$value['payed']); ?></td>
                                                    <?php if(person::$role!='cashier'): ?>
                                                    <td><?php echo $value['office_id']; ?></td>
                                                    <td><?php echo $value['updated_id']; ?></td>
                                                    <?php endif; ?>
                                                    <td><?php echo th::number_format($value['amount_in']); ?></td>
                                                    <td><?php echo th::number_format(-1 * $value['amount_out']); ?></td>
                                                    <td><?php echo th::number_format($value['amount']); ?></td>
                                                    <th><?php echo $value['amount_in']==0?0:th::number_format(100*$value['amount']/$value['amount_in']); ?>%</th>
                                                </tr>
                                            <?php endforeach ?>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
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
</section>
<script>
    $(function () {
        $('#time_range').daterangepicker({
            timePicker: false,
            timePicker24Hour: false,
            timePickerSeconds: false,
            autoApply: true,
            isCustomDate: function(date) {
                console.log(date);
            },
            startDate: moment('<?php echo date('Y-m-d',($time_from)) ?>'),
            endDate: moment('<?php echo date('Y-m-d',($time_to)) ?>'),
            locale: {
                format: 'Y/M/DD'
            }
        });
        $('#time_range').on('apply.daterangepicker', function(ev, picker) {
            $('#time_start').val(picker.startDate.format('YYYY-MM-DD'));
            $('#time_end').val(picker.endDate.format('YYYY-MM-DD'));

            $(this).val(picker.startDate.format('Y/M/DD') + ' - ' + picker.endDate.format('Y/M/DD'));
        });
//        $("#time_end").datepicker({dateFormat: "yy-mm-dd"});
    });
</script>