<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>

            <div class="row">
                <div class="col-sm-12">
                    <form method="GET" class="form-horizontal">
                        <div class="col-md-2">
                            <label class="col-md-2">С</label>
                            <input type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d', $time_from) ?>" >
                            <label class="col-md-2">По</label>
                            <input type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d', $time_to) ?>" >
                            <script>
                                $(function () {
                                    $("#time_start").datepicker({dateFormat: "yy-mm-dd"});
                                    $("#time_end").datepicker({dateFormat: "yy-mm-dd"});
                                });
                            </script>
                        </div>
                        <label>ППС</label>
                        <select name="office_id">
                            <?php foreach ($offices as $office_id => $name): ?>
                                <option value="<?php echo $office_id ?>" <?php echo $office_id == $curr_office ? 'selected' : '' ?>><?php echo $office_id.' - '.$name ?></option>
                            <?php endforeach; ?>
                        </select>
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
                            <a class="btn btn-default" href="<?php echo $dir ?>/statsall">Очистить</a>
                            <a class="btn btn-default export" onclick='window.open("<?php echo $dir; ?>/statsall/" + (window.location.search.length != 0 ? window.location.search + "&export=1" : "?export=1"));' >Экспорт в CSV</a>
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
                                <td rowspan="2">Дата</td>
                            </tr>
                            <tr>
                                <?php $i=1; ?>
                                <?php foreach($labels as $label): ?>
                                    <td class="sortby" onclick="sortTable(<?php echo $i; ?>)"><?php echo $label; ?></td>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </tr>
				<tr>
                                <td><b>Итого</b></td>
                                <?php foreach($labels as $label): ?>
                                    <td><?php echo arr::get($data['Итого'],$label,0); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $date => $row): ?>
                                <?php if ($date == 'Итого') continue; ?>
                                <tr>
                                    <td><?php echo date('Y-m-d',$date); ?></td>
                                    <?php foreach($labels as $label): ?>
                                    <td><?php echo arr::get($row,$label,0); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><b>Итого</b></td>
                                <?php foreach($labels as $label): ?>
                                    <td><?php echo arr::get($data['Итого'],$label,0); ?></td>
                                <?php endforeach; ?>
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
var aVal = a.value;
var bVal = b.value;
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