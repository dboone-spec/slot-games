<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>

            <div class="row">
                <div class="col-sm-12">
                    <form method="GET" class="form-horizontal">
                        <div class="col-md-2">
                            <label class="col-md-2">С</label>
                            <input type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d',strtotime($time_from)) ?>" >
                            <label class="col-md-2">По</label>
                            <input type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d',strtotime($time_to)) ?>" >
                            <script>
                                $(function () {
                                    $("#time_start").datepicker({dateFormat: "yy-mm-dd"});
                                    $("#time_end").datepicker({dateFormat: "yy-mm-dd"});
                                });
                            </script>
                        </div>
                        <label>Валюта</label>
                        <select name="office_id">
                            <?php foreach($offices as $office_id => $name): ?>
                                <option value="<?php echo $office_id ?>" <?php echo $office_id == $curr_office ? 'selected' : '' ?>><?php echo $name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Провайдер</label>
                        <select name="provider">
                            <?php foreach($providers as $provider => $name): ?>
                                <option value="<?php echo $provider ?>" <?php echo $provider === $curr_provider ? 'selected' : '' ?>><?php echo $name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Тип игры</label>
                        <select name="type">
                            <?php foreach($types as $type => $tname): ?>
                                <option value="<?php echo $type ?>" <?php echo $type === $curr_type ? 'selected' : '' ?>><?php echo $tname ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-group">
                            <?php foreach($search as $s): ?>
                                <label class="col-md-2"><?php echo isset($label[$s]) ? $label[$s] : $s ?>:</label>
                                <div class="col-md-2">
                                    <?php echo $vidgets[$s]->render($search_vars,'search') ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Поиск" />
                            <a class="btn btn-default" href="<?php echo $dir ?>/stats">Очистить</a>
                            <a class="btn btn-default export" onclick='window.open("/admin/stats/" + (window.location.search.length != 0 ? window.location.search + "&export=1" : "?export=1"));' >Экспорт в CSV</a>
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
                    visibility: hidden;
                }
                #left_col {
                    /*visibility: hidden;*/
                    /*background: none;*/
                }
            </style>

            <div class="row">
                <div class="col-sm-12" id='scrollblock' style="overflow: scroll; max-height: 700px; padding-left: 0px; margin-left: 15px;">
                    <div id="head"></div>
                    <div id="left_col"></div>
                    <table class="table <?php echo $dir ?>" >
                        <thead>
                            <tr>
                                <td rowspan="4">Игра</td>
                                <?php $pos = 0; ?>
                                <?php foreach($dbp as $day => $bettypes): ?>
                                    <?php unset($bettypes['bonus']); ?>
                                    <td class="day" data-pos="<?php echo $pos; ?>" colspan="<?php echo count($bettypes) * 4; ?>"><b><?php echo $day ?></b></td>
                                    <?php $pos++; ?>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <?php $pos = 0; ?>
                                <?php foreach($dbp as $day => $bettypes): ?>
                                    <?php unset($bettypes['bonus']); ?>
                                    <?php
                                    $bonusbl = 0;
                                    $freebl  = 0;
                                    
                                    
                                    ?>
                                    <?php foreach($bettypes as $btype => $v): ?>
                                        <?php
                                        if($btype == 'bonus')
                                        {
                                            $bonusbl++;
                                        }
                                        elseif($btype == 'free')
                                        {
                                            $freebl++;
                                        }
                                        ?>
                                    <?php endforeach ?>
                                    <?php if(($bonusbl + $freebl) == 0): ?>
                                        <td class="bf" data-pos="<?php echo $pos; ?>" colspan="<?php echo count($bettypes) * 4; ?>"><?php echo 'Обычные ставки'; ?></td>
                                    <?php else: ?>
                                        <td class="bf" data-pos="<?php echo $pos; ?>" colspan="<?php echo ($bonusbl + $freebl) * 4; ?>"><?php echo 'Бесплатные ставки'; ?></td>
                                        <?php if(count($bettypes) - $bonusbl - $freebl): ?>
                                            <td class="bf" data-pos="<?php echo $pos + 1; ?>" colspan="<?php echo (count($bettypes) - $bonusbl - $freebl) * 4; ?>"><?php echo 'Обычные ставки'; ?></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php $pos += ($bonusbl + $freebl) != 0 ? 2 : 1; ?>
                                <?php endforeach ?>
                            </tr>
                            <tr>
                                <?php $pos = 0; ?>
                                <?php foreach($dbp as $day => $bettypes): ?>
                                    <?php unset($bettypes['bonus']); ?>
                                    <?php foreach($bettypes as $btype => $v): ?>
                                        <td data-pos="<?php echo $pos; ?>" colspan="4"><?php echo $btype; ?></td>
                                        <?php $pos++; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <?php $pos = 0; ?>
                                <?php foreach($dbp as $day => $bettypes): ?>
                                    <?php unset($bettypes['bonus']); ?>
                                    <?php foreach($bettypes as $btype => $v): ?>
                                        <td data-pos="<?php echo $pos; ?>" class="dtype"><?php echo isset($label[$list[5]]) ? $label[$list[5]] : $list[5] ?></td>
                                        <td data-pos="<?php echo $pos + 1; ?>" class="dtype"><?php echo isset($label[$list[6]]) ? $label[$list[6]] : $list[6] ?></td>
                                        <td data-pos="<?php echo $pos + 2; ?>" class="dtype"><?php echo isset($label[$list[7]]) ? $label[$list[7]] : $list[7] ?></td>
                                        <td data-pos="<?php echo $pos + 3; ?>" class="dtype"><?php echo isset($label[$list[8]]) ? $label[$list[8]] : $list[8] ?></td>
                                        <?php $pos += 4; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data as $game => $types): ?>
                                <?php foreach($types as $type => $value): ?>
                                    <tr>
                                        <td><b><?php echo $game; ?></b> - <?php echo $type; ?></td>
                                        <?php foreach($dbp as $day => $bettypes): ?>
                                            <?php unset($bettypes['bonus']); ?>
                                            <?php foreach($bettypes as $btype => $v): ?>
                                                <td><?php echo $value[$day][$btype][0]['amount_in'] ?? '-' ?></td>
                                                <td><?php echo $value[$day][$btype][0]['amount_out'] ?? '-' ?></td>
                                                <td><?php echo $value[$day][$btype][0]['count'] ?? '-' ?></td>
                                                <td><?php echo (isset($value[$day][$btype][0]['amount_in']) AND $value[$day][$btype][0]['amount_in'] > 0) ? round($value[$day][$btype][0]['amount_out'] / $value[$day][$btype][0]['amount_in'] * 100,1) : '-' ?></td>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><b>Итого за период</b></td>
                                <?php foreach($tp as $day => $btypes): ?>
                                    <?php unset($btypes['bonus']); ?>
                                    <?php foreach($btypes as $btype => $v): ?>
                                        <td><b><?php echo $v['sumin'] ?></b></td>
                                        <td><b><?php echo $v['sumout'] ?></b></td>
                                        <td><b><?php echo $v['sumcount'] ?></b></td>
                                        <td><b><?php echo $v['percent'] ?></b></td>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <?php echo $page ?>
                </div>
            </div>
            <?php /*
              <?php if(Person::user()->can_edit($model)): ?>
              <div class="row">
              <div class="col-sm-12">
              <a class="btn btn-success" href="<?php echo $dir ?>/<?php echo $model ?>/item">Создать</a><br>
              </div>
              </div>
              <?php endif; ?>

             */ ?>
        </div>
    </div>
</div>
<script>
    $(window).on("load", function () {

        recalc();

        var parent = $('.table tbody tr').parent();
        var rows = $('.table tbody tr');// Строки

        $('#left_col').on("click", '.first', function () {
            var n = $(this).attr('pos');//Номер выбранного столбца в шапке
            updown(this, 1, n);
        });

        $('#head').on("click", 'div.sort', function () {
            var num = $(this).attr('pos');//Номер выбранного столбца в шапке
            num++; //Номер соответствующего столбца в таблице
            updown(this, 0, num);
        });

        function updown($this, $up, $num) {
            if ($($this).hasClass('desc')) {
                $($this).removeClass('desc');
                $($this).addClass('asc');
                sorting($up, $num);
            } else if ($($this).hasClass('asc')) {
                $($this).removeClass('asc');
                $($this).addClass('desc');
                sorting(!$up, $num);
            } else {
                $('.sort').removeClass('desc');
                $('.sort').removeClass('asc');
                $($this).addClass('desc');
                sorting(!$up, $num);
            }
        }
        function sorting(ascDesc = 1, num) {
            rows.sort(function (a, b) {
                a_val = parseFloat($(a).find('td').eq(num).text());//
                b_val = parseFloat($(b).find('td').eq(num).text());//Значение сравниваемой ячейки
                a_name = $(a).find('td:first-child').text();//
                b_name = $(b).find('td:first-child').text();//Текст первой ячейки в строке
                an = isNaN(a_val) ? -1 : a_val;
                bn = isNaN(b_val) ? -1 : b_val;
                if (an == bn) {
                    if (ascDesc) {//По алфавиту 
                        if (a_name < b_name)
                            return -1;
                        if (a_name > b_name)
                            return 1;
                        return 0;
                    } else { //По алфавиту обратно
                        if (a_name > b_name)
                            return -1;
                        if (a_name < b_name)
                            return 1;
                        return 0;
                    }
                }
                if (ascDesc) {
                    return bn - an; //По убыванию
                } else {
                    return an - bn;
                }
            });
            rows.detach().appendTo(parent);

            var leftcol = $('#left_col div').not('.first');//Ячейки первого столбца
            $(rows).each(function (index, val) {
                ind = index + 2;
                t = $(val).find('td:first-child').text();//Текст ячейки в таблице
                $(leftcol).each(function () {
                    d = $(this).text();//Текст div ячейки
                    if (d == t) {
                        $(this).css({'grid-area': ind + ' / 1 / auto / auto'});
                    }
                });
            });
        }
    });
    $(window).resize(function () {
        setTimeout(recalc, 500);
    });
    function recalc() {
        $('#head').empty();
        $('#left_col').empty();
        var $thead = $('thead'), $tbody = $('tbody');
        var first = $thead.find('tr:first-child td:first-child');
        var firstdiv = $('<div />',
                {
                    pos: '0',
                    class: 'first sort',
                    style: 'position:fixed; \n\
                            top: ' + first.offset().top + 'px; \n\
                            background: white;\n\
                            border-bottom: 1px solid black; \n\
                            border-right: 1px solid black; \n\
                            height:' + parseInt(first.outerHeight() + 1) + 'px;\n\
                            width:' + parseInt(first.outerWidth() + 1) + 'px;\n\
                            grid-row: 1; \n\
                            grid-column:1;'
                }
        );
        firstdiv.html(first.html());
        $('#left_col').append(firstdiv);
        $('#left_col').css({
            top: first.outerHeight()
        });

        var footnum = $tbody.find('tr td:first-child').length;//Число строк в таблице без шапки и футера
        $tbody.find('tr td:first-child').each(function (index) {
            var row = index + 2;
            var $newdiv = $('<div />',
                    {
                        style: 'height:' + $(this).outerHeight() + 'px ;width:' + $(this).outerWidth() + 'px;grid-row:' + row + '; grid-column:1;'
                    }
            );
            $newdiv.html($(this).html());
            $('#left_col').append($newdiv);

            if (index == (footnum - 1)) {
                var foot = $('tfoot').find('tr td:first-child');
                var place = footnum + 2;//Номер строки для div ячейки футера
                var $footdiv = $('<div />',
                        {
                            style: 'height:' + $(foot).outerHeight() + 'px ;width:' + $(foot).outerWidth() + 'px;grid-row:' + place + '; grid-column:1;'
                        }
                );
                $footdiv.html($(foot).html());
                $('#left_col').append($footdiv);
            }
        });

        var col_count = 0;
        $thead.find('tr').each(function (index, item) {
            $(item).find('td').each(function () {
                if ($(this).hasClass('dtype'))
                    col_count++;
            });
        });

        $thead.find('tr').each(function (index, item) {
            var curr_pos = 2;//Позиция текущей ячейки в строках
            $(item).find('td').each(function () {
                var colspan = $(this).attr('colspan');//Аттрибут colspan элемента
                var pos = parseInt($(this).attr('data-pos'));//Позиция - индекс ячейки в строке (не считая первой колонки)
                var grow = '';// grid-row
                var gcol = '';// grid-column
                var addcl = '';//Класс для ячеек, по столбцам которых нужно сортировать таблицу
                var addpos = '';//Индекс ячейки для сортировки
                var cl = $(this).attr('class');
                if ($(this).attr('rowspan')) {//Для самой первой ячейки 
                    gcol = '1'; //Номер столбца в шапке
                    grow = '1/5';//Номер строки в шапке
                } else if (cl == 'day') {//Для ячейки с датой
                    gcol = curr_pos + '/' + parseInt(parseInt(curr_pos) + parseInt(colspan));
                    grow = '1';
                    curr_pos += parseInt(colspan);
                } else if (cl == 'bf') {//Для ячейки с датой
                    gcol = curr_pos + '/' + parseInt(parseInt(curr_pos) + parseInt(colspan));
                    grow = '2';
                    curr_pos += parseInt(colspan);
                } else if (colspan == 4) {//Для ячеек с типом ставок
                    gcol = (2 + colspan * pos) + '/' + (2 + colspan * (pos + 1));
                    grow = '3';
                } else {
                    grow = '4';
                    addpos = pos;
                    addcl = 'sort';//Добавляем класс для сортировки
                }
                var $newdiv = $('<div />',
                        {
                            pos: addpos,
                            class: addcl,
                            style: 'height:' + $(this).outerHeight() + 'px ;width:' + $(this).outerWidth() + 'px;grid-row:' + grow + '; grid-column:' + gcol + ';'
                        }
                );
                $newdiv.html($(this).html());
                $('#head').append($newdiv);
            });
        });

        var scrollblock = $('#scrollblock');//Блок с таблицей
        scrollblock.scroll(function () {
            var stoffset = $('table.table').offset();
            $('#left_col').css({
                left: $(this).scrollLeft()
            });
            $('.first').css({
                left: stoffset.left + $(this).scrollLeft()
            });
            $('#head').css({
                top: $(this).scrollTop()
            });
        });
        $(window).scroll(function () {
            var wsoffset = $('#scrollblock').offset();
            $('.first').css({
                top: wsoffset.top - $(this).scrollTop(),
            });
        });
    }
</script>