<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>

            <div class="row">
                <div class="col-sm-12">
                    <form method="GET" class="form-horizontal">
                        <div class="col-md-2">
                            <label class="col-md-2">С</label>
                            <input type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d', strtotime($time_from)) ?>" >
                            <label class="col-md-2">По</label>
                            <input type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d', strtotime($time_to)) ?>" >
                            <script>
                                $(function () {
                                    $("#time_start").datepicker({dateFormat: "yy-mm-dd"});
                                    $("#time_end").datepicker({dateFormat: "yy-mm-dd"});
                                });
                            </script>
                        </div>
                        <label><?php echo OFFLINE?'ППС':'Валюта'; ?></label>
                        <select name="office_id">
                            <?php foreach ($offices as $office_id => $name): ?>
                                <option value="<?php echo $office_id ?>" <?php echo $office_id == $curr_office ? 'selected' : '' ?>><?php echo $name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Провайдер</label>
                        <select name="provider">
                            <?php foreach ($providers as $provider => $name): ?>
                                <option value="<?php echo $provider ?>" <?php echo $provider === $curr_provider ? 'selected' : '' ?>><?php echo $name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Бренд</label>
                        <select name="brand">
                            <?php foreach ($brands as $brand => $name): ?>
                                <option value="<?php echo $brand ?>" <?php echo $brand === $curr_brand ? 'selected' : '' ?>><?php echo $name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Показывать выкл. игры?</label>
                        <input type="checkbox" name="gameshow" <?php echo $gameshow?'checked':''; ?> />
                        <div class="form-group">
                            <?php foreach ($search as $s): ?>
                                <label class="col-md-2"><?php echo isset($label[$s]) ? $label[$s] : $s ?>:</label>
                                <div class="col-md-2">
                                    <?php echo $vidgets[$s]->render($search_vars, 'search') ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Поиск" />
                            <a class="btn btn-default" href="<?php echo $dir ?>/stats">Очистить</a>
                            <a class="btn btn-default export" onclick='window.open("<?php echo $dir; ?>/stats/" + (window.location.search.length != 0 ? window.location.search + "&export=1" : "?export=1"));' >Экспорт в CSV</a>
                        </div>
                    </form>
                </div>
            </div>
            <style>
                td {
                    border: 1px solid black;
                    text-align: center;
                    vertical-align: middle;
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
                <div class="col-sm-12" id='scrollblock' style="overflow: scroll; max-height: 700px; padding-left: 0px; margin-left: 15px;">
                    <div id="head"></div>
                    <div id="left_col"></div>
                    <table id="stats_table" class="table <?php echo $dir ?>" >
                        <thead>
                            <tr>
                                <td rowspan="3">Игра</td>
                                <?php if(person::$role!='gameman'): ?>
                                <td rowspan="3">Провайдер</td>
                                <?php endif; ?>
                                <td rowspan="3">Бренд</td>
                            </tr>
                            <tr>
                                <?php foreach (['normal', 'free', 'double'] as $btype): ?>
                                    <td colspan="<?php echo $btype == 'free' ? 3 : 5; ?>"><?php echo $btype; ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <?php $i=person::$role!='gameman'?3:2; ?>
                                <?php foreach (['normal', 'free', 'double'] as $btype): ?>
                                    <?php if ($btype != 'free'): ?>
                                        <td class="sortby" onclick="sortTable(<?php echo $i; ?>)"><?php echo isset($label[$list[5]]) ? $label[$list[5]] : $list[5] ?></td>
                                        <?php $i++; ?>
                                    <?php endif; ?>
                                    <td class="sortby" onclick="sortTable(<?php echo $i; ?>)"><?php echo isset($label[$list[6]]) ? $label[$list[6]] : $list[6] ?></td>
                                    <?php $i++; ?>
                                    <?php if ($btype != 'free'): ?>
                                        <td class="sortby" onclick="sortTable(<?php echo $i; ?>)">win</td>
                                        <?php $i++; ?>
                                    <?php endif; ?>
                                    <td class="sortby" onclick="sortTable(<?php echo $i; ?>)"><?php echo isset($label[$list[7]]) ? $label[$list[7]] : $list[7] ?></td>
                                    <?php $i++; ?>
                                    <td class="sortby" onclick="sortTable(<?php echo $i; ?>)"><?php echo isset($label[$list[8]]) ? $label[$list[8]] : $list[8] ?></td>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </tr>
				<tr>
                                <td colspan="<?php echo person::$role!='gameman'?3:2; ?>"><b>Итого</b></td>
                                <?php if(!empty($data['Итого'])): ?>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_in'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_out'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_win'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_count'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_in'] > 0 ? (100 * $data['Итого']['normal_out'] / $data['Итого']['normal_in']) : '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['fg_out'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['fg_count'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_in'] > 0 ? (100 * $data['Итого']['fg_out'] / $data['Итого']['normal_in']) : '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_in'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_out'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_win'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_count'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_in'] > 0 ? (100 * $data['Итого']['double_out'] / $data['Итого']['double_in']) : '0') ?></b></td>
                                <?php else: ?>
                                    <td colspan="13"></td>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $game => $row): ?>
                                <?php if ($game == 'Итого') continue; ?>
                                <tr>
                                    <td><b><?php echo $row['game']; ?></b></td>
                                    <?php if(person::$role!='gameman'): ?>
                                    <td><?php echo $row['provider'] ?></td>
                                    <?php endif; ?>
                                    <td><?php echo $row['brand'] ?></td>
                                    <td><?php echo th::number_format($row['normal_in'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['normal_out'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['normal_win'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['normal_count'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['normal_in'] > 0 ? (100 * $row['normal_out'] / $row['normal_in']) : '0') ?></td>
                                    <td><?php echo th::number_format($row['fg_out'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['fg_count'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['normal_in'] > 0 ? (100 * $row['fg_out'] / $row['normal_in']) : '0') ?></td>
                                    <td><?php echo th::number_format($row['double_in'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['double_out'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['double_win'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['double_count'] ?? '0') ?></td>
                                    <td><?php echo th::number_format($row['double_in'] > 0 ? (100 * $row['double_out'] / $row['double_in']) : '0') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="<?php echo person::$role!='gameman'?3:2; ?>"><b>Итого</b></td>
                                <?php if(!empty($data['Итого'])): ?>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_in'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_out'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_win'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_count'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_in'] > 0 ? (100 * $data['Итого']['normal_out'] / $data['Итого']['normal_in']) : '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['fg_out'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['fg_count'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['normal_in'] > 0 ? (100 * $data['Итого']['fg_out'] / $data['Итого']['normal_in']) : '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_in'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_out'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_win'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_count'] ?? '0') ?></b></td>
                                    <td><b><?php echo th::number_format($data['Итого']['double_in'] > 0 ? (100 * $data['Итого']['double_out'] / $data['Итого']['double_in']) : '0') ?></b></td>
                                <?php else: ?>
                                    <td colspan="13"></td>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var sortedOn = 0;
function sortTable(sortOn) {
var table = document.getElementById('stats_table');
var tbody = table.getElementsByTagName('tbody')[0];
var rows = tbody.getElementsByTagName('tr');
var rowArray = new Array();
for (var i=0, length=rows.length; i<length; i++) {
rowArray[i] = new Object;
rowArray[i].oldIndex = i;
rowArray[i].value = rows[i].getElementsByTagName('td')[sortOn].firstChild.nodeValue;
}
if (sortOn == sortedOn) { rowArray.reverse(); }
else {
sortedOn = sortOn;
/*
Decide which function to use from the three:RowCompareNumbers,
RowCompareDollars or RowCompare (default).
For first column, I needed numeric comparison.
*/
if (sortedOn == 0) {
rowArray.sort(RowCompareNumbers);
}
else {
rowArray.sort(RowCompare);
}
}
var newTbody = document.createElement('tbody');
for (var i=0, length=rowArray.length ; i<length; i++) {
newTbody.appendChild(rows[rowArray[i].oldIndex].cloneNode(true));
}
table.replaceChild(newTbody, tbody);
}
function RowCompare(a, b) {
var aVal = parseFloat(a.value.replace(/ /g,''));
var bVal = parseFloat(b.value.replace(/ /g,''));
console.log(aVal);
return (aVal == bVal ? 0 : (aVal > bVal ? 1 : -1));
}
// Compare number
function RowCompareNumbers(a, b) {
var aVal = parseInt( a.value);
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