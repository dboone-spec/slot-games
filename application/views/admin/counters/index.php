 <style>
                .datatable td,.datatable th {
                    border: 1px solid black;
                    text-align: center;
                    vertical-align: middle;
                    min-width: 70px;
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

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Counters</h1>

            <div class="row">
                <div class="col-sm-12" >
                    <form method="GET" class="form-horizontal">
                        <div >
                            <table>
                                <tr>

                                    <td style="width:150px">
                                        <label>Office</label>
                                    </td>

                                    <td style="width:150px">
                                        <label>Brand</label>
                                    </td>

                                    <?php if(Person::$role=='sa'): ?>
                                    <td style="width:150px">
                                        <label>Test offices</label>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                 <tr>

                                    <td>
                                        <?php echo form::select('office_id',$officesList,$office_id)?>
                                    </td>
                                    <td>
                                        &nbsp;&nbsp;<?php echo form::select('brand',$brandList,$brand,['id'=>'selBrand'])?>
                                    </td>
                                    <?php if(Person::$role=='sa'): ?>
                                    <td style="width:150px">
                                        &nbsp;&nbsp;<?php echo form::select('is_test', ['-1'=>__('All'),'0'=>__('Production'),'1'=>__('Test')], $is_test)?>
                                    </td>
                                    <?php endif; ?>
                                </tr>

                            </table>
                            <br>

                        </div>



                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Find" />
                            <a class="btn btn-default" href="/enter/counters">Clear</a>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 all_games">

                </div>
            </div>


             <div class="row">
                <div  id='scrollblock' style="overflow-x: scroll; padding-left: 0px; margin-left: 15px;">
                    <div id="head"></div>
                    <div id="left_col"></div>

                    <table id="table" class="datatable"  >
                        <thead>
                        <tr>
                            <th rowspan="3" class="tddate"> Game </th>
                            <th rowspan="3" class="tddate"> Office </th>

                                <th colspan="11"> Normal </th>
                                <th colspan="5" rowspan="2"> FS Cashback </th>
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

                        </tr>

                        </thead>
                        <tbody>
                        <?php foreach($data as $date=>$row):?>
                            <tr brand="<?php echo $row['brand']; ?>">
                                <td class="tddate"><?php echo $row['visible_name'] ?> </td>
                                <td class="tddate"><?php echo $row['office_id']??'-' ?> </td>
                                <td onclick="sortTable(2)" > <?php echo round($row['in']) ?> </td>
                                <td onclick="sortTable(3)"> <?php echo round($row['out']) ?> </td>
                                <td onclick="sortTable(4)" style="<?php echo ($row['win']<0)?'color:red':''; ?>"> <?php echo round($row['win']) ?> </td>
                                <td onclick="sortTable(5)" > <?php echo round($row['count']) ?> </td>
                                <td onclick="sortTable(6)" > <?php echo $row['in']>0? (round($row['out']/$row['in'],2)*100) : 0 ?>% </td>

                                <td onclick="sortTable(7)"> <?php echo round($row['free']) ?> </td>
                                <td onclick="sortTable(8)" > <?php echo round($row['free_count']) ?> </td>
                                <td onclick="sortTable(9)" > <?php echo $row['in']>0? (round($row['free']/$row['in'],2)*100) : 0 ?>% </td>

                                <td onclick="sortTable(10)"> <?php echo round($row['bonus']) ?> </td>
                                <td onclick="sortTable(11)" > <?php echo round($row['bonus_count']) ?> </td>
                                <td onclick="sortTable(12)" > <?php echo $row['in']>0? (round($row['bonus']/$row['in'],2)*100) : 0 ?>% </td>


                                <td onclick="sortTable(13)" > <?php echo round($row['fs_cash_in']) ?> </td>
                                <td onclick="sortTable(14)"> <?php echo round($row['fs_cash_out']) ?> </td>
                                <td onclick="sortTable(15)" style="<?php echo $row['fs_cash_win']<0?'color:red':''; ?>"> <?php echo round($row['fs_cash_win']) ?> </td>
                                <td onclick="sortTable(16)" > <?php echo round($row['fs_cash_count']) ?> </td>
                                <td onclick="sortTable(17)" > <?php echo $row['fs_cash_in']>0? (round($row['fs_cash_out']/$row['fs_cash_in'],2)*100) : 0 ?>% </td>

                                <td onclick="sortTable(18)" > <?php echo round($row['fs_api_in']) ?> </td>
                                <td onclick="sortTable(19)"> <?php echo round($row['fs_api_out']) ?> </td>
                                <td onclick="sortTable(20)" style="<?php echo $row['fs_api_win']<0?'color:red':''; ?>"> <?php echo round($row['fs_api_win']) ?> </td>
                                <td onclick="sortTable(21)" > <?php echo round($row['fs_api_count']) ?> </td>
                                <td onclick="sortTable(22)" > <?php echo $row['fs_api_in']>0? (round($row['fs_api_out']/$row['fs_api_in'],2)*100) : 0 ?>% </td>


                                <td onclick="sortTable(23)" > <?php echo round($row['double_in']) ?> </td>
                                <td onclick="sortTable(24)"> <?php echo round($row['double_out']) ?> </td>
                                <td onclick="sortTable(25)" style="<?php echo $row['double_win']<0?'color:red':''; ?>"> <?php echo round($row['double_win']) ?> </td>
                                <td onclick="sortTable(26)" > <?php echo round($row['double_count']) ?> </td>
                                <td onclick="sortTable(27)" > <?php echo $row['double_in']>0? (round($row['double_out']/$row['double_in'],2)*100) : 0 ?>% </td>
                            </tr>
                        <?php endforeach ?>
                            <tr style="font-weight: bold;">
                                <td class="tddate">Total</td>
                                <td class="tddate">&nbsp;</td>
                                <?php foreach($total as $k=>$v): ?>
                                <td style="<?php echo $v<0?'color:red':''; ?>"><?php echo strpos($k,'rtp')===FALSE?$v:$v.'%'; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                    </div>
            </div>


          </div>
    </div>

</div>

<script>
        $(function(){
                $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});

                /*$('#selBrand').change(function() {
                    var br = $(this).val();
                    if(br!='all') {
                        $('tr[brand]').hide();
                        $('tr[brand='+br+']').show();
                    }
                    else {
                        $('tr[brand]').show();
                    }
                });*/
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