<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>
            <div class="row">
                <div class="col-sm-12">
                    <form method="GET" class="form-horizontal">
                        <div class="form-group">
                            <?php foreach($search as $s): ?>
                                <label class="col-md-2"><?php echo isset($label[$s]) ? $label[$s] : $s ?>:</label>
                                <div class="col-md-2">
                                    <?php echo $vidgets[$s]->render($search_vars,'search') ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="<?php echo __('Поиск') ?>" />
                            <a class="btn btn-default" href="<?php echo $dir ?>/<?php echo $model ?>"><?php echo __('Очистить') ?></a>
                        </div>
                        <div class="form-group" style="float:right">
                            <a class="btn btn-primary" href="<?php echo $dir ?>/<?php echo $model ?>/clear/all">Сбросить все</a>
                            <a class="btn btn-danger" href="<?php echo $dir ?>/<?php echo $model ?>/clear/our">Сбросить наши</a>
                            <a class="btn btn-success" href="<?php echo $dir ?>/<?php echo $model ?>/clear/imperium">Сбросить империум</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="overflow: scroll; max-height: 55vh;">
                    <table id="table" class="table <?php echo $dir ?>" >
                        <thead>
                        <style>
                            thead td{
                                border: 1px solid black;
                                vertical-align:middle; text-align:center;
                            }
                        </style>

                        <?php $q = Request::current()->query(); ?>
                        <tr>
                            <?php foreach($list as $k => $h): ?>
                                <?php $i = 0; ?>
                                <?php $r = 2; ?>
                                <?php if(is_array($h)): ?>
                                    <?php $r = 1; ?>
                                    <?php foreach($h as $h1): ?>
                                                <?php $i++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <td rowspan="<?php echo $r; ?>" colspan="<?php echo $i; ?>">
                                    <?php $sortas = $q['sortas'] ?? 'asc'; ?>
                                    <?php if(in_array($k,['provider','game','type'])): ?>
                                        <a <?php if(isset($q['sortby']) && $q['sortby'] == $k): ?>class="<?php echo $q['sortas']; ?>"<?php endif; ?>
                                            href="/<?php echo Request::current()->uri() . '?' . http_build_query(array_merge($q,['sortby' => $k,'sortas' => $sortas == 'asc' ? 'desc' : 'asc'])); ?> ">
                                            <?php echo isset($label[$k]) ? $label[$k] : $k ?>&nbsp;<?php if(isset($q['sortby']) && $q['sortby'] == $k): ?>
                                                <?php echo $q['sortas'] == 'asc' ? '&dArr;' : '&uArr;'; ?><?php endif; ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo isset($label[$k]) ? $label[$k] : $k ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php $col = 2; ?>
                            <?php foreach($list as $k => $h): ?>
                                <?php if(is_array($h)): ?>
                                    <?php foreach($h as $h2): ?>
                                        <?php if(in_array($h2,['percent_bonus','percent_free','percent_double','percent_normal'])): ?>
                                            <td onclick="sortTable(<?php echo $col; ?>)" style="vertical-align:middle; text-align:center;">
                                                <a><?php echo isset($label[$h2]) ? $label[$h2] : $h2 ?></a>
                                            </td>
                                        <?php else: ?>
                                            <td style="vertical-align:middle; text-align:center;">
                                                <?php $sortas = $q['sortas'] ?? 'asc'; ?>
                                                <a <?php if(isset($q['sortby']) && $q['sortby'] == $h2): ?>class="<?php echo $q['sortas']; ?>"<?php endif; ?>
                                                    href="/<?php echo Request::current()->uri() . '?' . http_build_query(array_merge($q,['sortby' => $h2,'sortas' => $sortas == 'asc' ? 'desc' : 'asc'])); ?> ">
                                                    <?php echo isset($label[$h2]) ? $label[$h2] : $h2 ?>&nbsp;<?php if(isset($q['sortby']) && $q['sortby'] == $h2): ?>
                                                        <?php echo $q['sortas'] == 'asc' ? '&dArr;' : '&uArr;'; ?><?php endif; ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <?php $col++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>


                        </thead>
                        <tbody>
                            <?php foreach($data as $c): ?>
                                <tr>
                                    <?php foreach($list as $l): ?>
                                        <?php if(is_array($l)): ?>
                                            <?php foreach($l as $ll): ?>
                                                <?php if(is_array($ll)): ?>
                                                    <?php foreach($ll as $lll): ?>
                                                        <td style="vertical-align:middle; text-align:center;">
                                                            <?php echo $vidgets[$lll]->render($c,'list') ?>
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <td style="vertical-align:middle; text-align:center;">
                                                        <?php echo $vidgets[$ll]->render($c,'list') ?>
                                                    </td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <td style="vertical-align:middle; text-align:center;">
                                                <?php echo $vidgets[$l]->render($c,'list') ?>
                                            </td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                </tr>
                            <?php endforeach ?>
                        </tbody>
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
<script>
    $(document).ready(function () {
        $('.calcable[name=sum_in]').each(function () {
            if (parseFloat($(this).text()) == 0) {
                $(this).parent().parent().parent().find('a').css('color', 'green');
            }
        });
    });
</script>

<script type="text/javascript">
    var sortedOn = 0;
    function sortTable(sortOn) {
        var table = document.getElementById('table');
        var tbody = table.getElementsByTagName('tbody')[0];
        var rows = tbody.getElementsByTagName('tr');
        var rowArray = new Array();
        for (var i = 0, length = rows.length; i < length; i++) {
            rowArray[i] = new Object;
            rowArray[i].oldIndex = i;
            rowArray[i].value = rows[i].getElementsByTagName('td')[sortOn].firstChild.nodeValue;
        }

        if (sortOn == sortedOn) {
            rowArray.reverse();
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
        console.log(rowArray);
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
        return (aVal == bVal ? 0 : (aVal > bVal ? 1 : -1));
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