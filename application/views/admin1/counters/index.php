<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Counters</h4>
                        <hr>


                         <form method="GET" class="form-horizontal">

                                <div class="form-row">




                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                            </div>
                                            <?php echo form::select('office_id',$officesList,$office_id,['class'=>'form-control form-control-sm select2'])?>
                                        </div>
                                    </div>


                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Brand</span>
                                            </div>
                                            <?php echo form::select('brand',$brandList,$brand,['id'=>'selBrand','class'=>'form-control form-control-sm'])?>


                                        </div>
                                    </div>



                                        <div class="form-group col-md-2">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm">Test offices?</span>
                                                </div>
                                                <?php echo form::select('is_test', ['-1'=>__('All'),'0'=>__('Production'),'1'=>__('Test')], $is_test, ['id'=>'_isTest','class'=>'form-control form-control-sm'])?>
                                            </div>
                                        </div>



                                    <div class="w-100"></div>

                                    <div class="non-form-control ml-auto">
                                        <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                    </div>
                                    <div>
                                        <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/counters"><?php echo __('Очистить') ?></a>
                                    </div>
                                </div>
                        </form>





                </div>








                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table id="table" class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                <thead>
                                    <tr>
                                        <th rowspan="3" class="tddate"> Game </th>
                                        <th rowspan="3" class="tddate"> Office </th>

                                            <th colspan="11"> Normal </th>
                                            <th colspan="5" rowspan="2"> DS </th>
                                            <th colspan="5" rowspan="2"> LS </th>
                                            <th colspan="5" rowspan="2"> FS API </th>
                                            <th colspan="5" rowspan="2"> Double </th>
                                    </tr>

                                    <tr>
                                        <th colspan="5"> All</th>
                                            <th colspan="3"> FreeGame </th>
                                            <th colspan="3"> Bonus </th>



                                    </tr>

                                    <tr>


                                        <th onclick="sortTable(2)" > In </th>
                                        <th onclick="sortTable(3)"> Out </th>
                                        <th onclick="sortTable(4)"> Win </th>
                                        <th onclick="sortTable(5)" > Count </th>
                                        <th onclick="sortTable(6)" > RTP </th>

                                        <th onclick="sortTable(7)"> Out </th>
                                        <th onclick="sortTable(8)" > Count </th>
                                        <th onclick="sortTable(9)" > RTP </th>

                                        <th onclick="sortTable(10)"> Out </th>
                                        <th onclick="sortTable(11)" > Count </th>
                                        <th onclick="sortTable(12)" > RTP </th>



                                        <th onclick="sortTable(13)" > In </th>
                                        <th onclick="sortTable(14)"> Out </th>
                                        <th onclick="sortTable(15)"> Win </th>
                                        <th onclick="sortTable(16)" > Count </th>
                                        <th onclick="sortTable(17)" > RTP </th>

                                        <th onclick="sortTable(18)" > In </th>
                                        <th onclick="sortTable(19)"> Out </th>
                                        <th onclick="sortTable(20)"> Win </th>
                                        <th onclick="sortTable(21)" > Count </th>
                                        <th onclick="sortTable(22)" > RTP </th>


                                        <th onclick="sortTable(23)" > In </th>
                                        <th onclick="sortTable(24)"> Out </th>
                                        <th onclick="sortTable(25)"> Win </th>
                                        <th onclick="sortTable(26)" > Count </th>
                                        <th onclick="sortTable(27)" > RTP </th>

                                        <th onclick="sortTable(28)" > In </th>
                                        <th onclick="sortTable(29)"> Out </th>
                                        <th onclick="sortTable(30)"> Win </th>
                                        <th onclick="sortTable(31)" > Count </th>
                                        <th onclick="sortTable(32)" > RTP </th>

                                    </tr>

                                </thead>
                                <tbody>
                                    <?php foreach($data as $date=>$row):?>
                                        <tr brand="<?php echo $row['brand']; ?>">
                                            <td class="tddate"><?php echo $row['visible_name'] ?> </td>
                                            <td class="tddate"><?php echo $row['office_id']??'-' ?> </td>
                                            <td onclick="sortTable(2)" > <?php echo th::number_format($row['in']) ?> </td>
                                            <td onclick="sortTable(3)"> <?php echo th::number_format($row['out']) ?> </td>
                                            <td onclick="sortTable(4)" style="<?php echo ($row['win']<0)?'color:red':''; ?>"> <?php echo th::number_format($row['win']) ?> </td>
                                            <td onclick="sortTable(5)" > <?php echo th::number_format($row['count']) ?> </td>
                                            <td onclick="sortTable(6)" > <?php echo $row['in']>0? (round($row['out']/$row['in'],2)*100) : 0 ?>% </td>

                                            <td onclick="sortTable(7)"> <?php echo th::number_format($row['free']) ?> </td>
                                            <td onclick="sortTable(8)" > <?php echo th::number_format($row['free_count']) ?> </td>
                                            <td onclick="sortTable(9)" > <?php echo $row['in']>0? (round($row['free']/$row['in'],2)*100) : 0 ?>% </td>

                                            <td onclick="sortTable(10)"> <?php echo th::number_format($row['bonus']) ?> </td>
                                            <td onclick="sortTable(11)" > <?php echo th::number_format($row['bonus_count']) ?> </td>
                                            <td onclick="sortTable(12)" > <?php echo $row['in']>0? (round($row['bonus']/$row['in'],2)*100) : 0 ?>% </td>


                                            <td onclick="sortTable(13)" > <?php echo th::number_format($row['fs_cash_in']) ?> </td>
                                            <td onclick="sortTable(14)"> <?php echo th::number_format($row['fs_cash_out']) ?> </td>
                                            <td onclick="sortTable(15)" style="<?php echo $row['fs_cash_win']<0?'color:red':''; ?>"> <?php echo th::number_format($row['fs_cash_win']) ?> </td>
                                            <td onclick="sortTable(16)" > <?php echo th::number_format($row['fs_cash_count']) ?> </td>
                                            <td onclick="sortTable(17)" > <?php echo $row['fs_cash_in']>0? (round($row['fs_cash_out']/$row['fs_cash_in'],2)*100) : 0 ?>% </td>

                                            <td onclick="sortTable(18)" > <?php echo th::number_format($row['fs_lucky_in']) ?> </td>
                                            <td onclick="sortTable(19)"> <?php echo th::number_format($row['fs_lucky_out']) ?> </td>
                                            <td onclick="sortTable(20)" style="<?php echo $row['fs_lucky_win']<0?'color:red':''; ?>"> <?php echo th::number_format($row['fs_lucky_win']) ?> </td>
                                            <td onclick="sortTable(21)" > <?php echo th::number_format($row['fs_lucky_count']) ?> </td>
                                            <td onclick="sortTable(22)" > <?php echo $row['fs_lucky_in']>0? (round($row['fs_lucky_out']/$row['fs_lucky_in'],2)*100) : 0 ?>% </td>

                                            <td onclick="sortTable(23)" > <?php echo th::number_format($row['fs_api_in']) ?> </td>
                                            <td onclick="sortTable(24)"> <?php echo th::number_format($row['fs_api_out']) ?> </td>
                                            <td onclick="sortTable(25)" style="<?php echo $row['fs_api_win']<0?'color:red':''; ?>"> <?php echo th::number_format($row['fs_api_win']) ?> </td>
                                            <td onclick="sortTable(26)" > <?php echo th::number_format($row['fs_api_count']) ?> </td>
                                            <td onclick="sortTable(27)" > <?php echo $row['fs_api_in']>0? (round($row['fs_api_out']/$row['fs_api_in'],2)*100) : 0 ?>% </td>


                                            <td onclick="sortTable(28)" > <?php echo th::number_format($row['double_in']) ?> </td>
                                            <td onclick="sortTable(29)"> <?php echo th::number_format($row['double_out']) ?> </td>
                                            <td onclick="sortTable(30)" style="<?php echo $row['double_win']<0?'color:red':''; ?>"> <?php echo th::number_format($row['double_win']) ?> </td>
                                            <td onclick="sortTable(31)" > <?php echo th::number_format($row['double_count']) ?> </td>
                                            <td onclick="sortTable(32)" > <?php echo $row['double_in']>0? (round($row['double_out']/$row['double_in'],2)*100) : 0 ?>% </td>
                                        </tr>
                                    <?php endforeach ?>
                                        <tr style="font-weight: bold;">
                                            <td class="tddate">Total</td>
                                            <td class="tddate">&nbsp;</td>
                                            <?php foreach($total as $k=>$v): ?>
                                            <td style="<?php echo $v<0?'color:red':''; ?>"><?php echo strpos($k,'rtp')===FALSE?th::number_format($v):$v.'%'; ?></td>
                                            <?php endforeach; ?>
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
    td:first-child {
        position: fixed;
        position: -webkit-sticky;
        position: sticky;
        left: 0px;
        background: #fff;
    }
</style>


<script>
    $( document ).ready(function() {
        $('input').addClass('form-control');
        $('input').addClass('form-control-sm');
        $('select').addClass('form-control');
        $('select').addClass('form-control-sm');
        $('.non-form-control input').removeClass('form-control');
        $('.non-form-control input').removeClass('form-control-sm');


    });


</script>
<script type="text/javascript">
    var sortedOn = 0;
    reverseArr = function(a) {
        var arr = [];
        for( var i = a.length-1; i--; ){
            arr.push( a[i] );
        };
        arr.push( a[a.length-1] );
        return arr;
    }
    function sortTable(sortOn) {
        var table = document.getElementById('table');
        var tbody = table.getElementsByTagName('tbody')[0];
        var rows = tbody.getElementsByTagName('tr');
        var rowArray = new Array();
        for (var i = 0, length = rows.length; i < length; i++) {
            rowArray[i] = new Object;
            rowArray[i].oldIndex = i;
            rowArray[i].freeze = false;
            rowArray[i].value = rows[i].getElementsByTagName('td')[sortOn].firstChild.nodeValue;
            if(i==rows.length-1) {
                rowArray[i].freeze = true;
            }
        }

        if (sortOn == sortedOn) {
//            rowArray.reverse();
            rowArray = reverseArr(rowArray);
        } else {
            sortedOn = sortOn;
            /*
             Decide which function to use from the three:RowCompareNumbers,
             RowCompareDollars or RowCompare (default).
             For first column, I needed numeric comparison.
             */
            if (sortedOn == 0) {
                rowArray.sort(RowCompareNumbers);
            } else {
                rowArray.sort(RowCompare);
            }
        }

        var newTbody = document.createElement('tbody');
        for (var i = 0, length = rowArray.length; i < length; i++) {
            newTbody.appendChild(rows[rowArray[i].oldIndex].cloneNode(true));
        }
        table.replaceChild(newTbody, tbody);
    }
    function RowCompare(a, b) {
        var aVal = parseFloat(a.value.replace(/ /g, ''));
        var bVal = parseFloat(b.value.replace(/ /g, ''));
        aVal = isNaN(aVal) ? 0 : aVal;
        bVal = isNaN(bVal) ? 0 : bVal;
        return (((aVal == bVal) || a.freeze || b.freeze) ? 0 : (aVal > bVal ? 1 : -1));
    }
// Compare number
    function RowCompareNumbers(a, b) {
        var aVal = parseInt(a.value);
        var bVal = parseInt(b.value);
        return (aVal - bVal);
    }
// compare currency
    function RowCompareDollars(a, b) {
        var aVal = parseFloat(a.value.substr(1));
        var bVal = parseFloat(b.value.substr(1));
        return (aVal - bVal);
    }
</script>





