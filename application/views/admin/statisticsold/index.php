<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>

            <div class="row">
                <div class="col-sm-12" >
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
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Поиск" />
                            <a class="btn btn-default" href="<?php echo $dir ?>/<?php echo $model ?>">Очистить</a>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 all_games">
                            
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
                    <table class="table" >
                        <thead class="first">
                            <tr>
                                <td rowspan="5">Дата</td>
                            </tr>
                            <tr id="head_1"></tr>
                            <tr id="head_2"></tr>
                            <tr id="head_3"></tr>
                            <tr id="head_4"></tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(window).on('load', function() {
        let data = <?php echo $data ?>;
        let types = <?php echo $types ?>;
        let games = <?php echo $games ?>;
        let dates = <?php echo $dates ?>;
        let labels = <?php echo $labels ?>;
        
        let body = $('table tbody');
        
        for(i in dates) {
            let row = '';
            
            row += '<tr class="dates" id="' + dates[i] + '">';
            row += '<td>' + dates[i] + '</td>';
            row += '</tr>';
            
            body.append(row);
        }
        
        let div_games = $('.all_games');
        
        for(i in games) {
            let game_div = '<div style="display:inline-block; padding: 3px">';
            
            game_div += '<label for="' + games[i] + '">' + games[i] + '</label>';
            game_div += '<input type="checkbox" class="change_game" value="1" style="margin: 3px" id="' + games[i] + '" />';
            game_div += '</div>';
            
            div_games.append(game_div);
        }
        
        $('.change_game').change(function() {
            let game_name = $(this).attr('id');
            
            if($(this).prop('checked') == false) {
                $('.table_' + game_name).remove();
                
                recalc();
                
                return true;
            }
            
            let head_1 = $('#head_1');
            let head_2 = $('#head_2');
            let head_3 = $('#head_3');
            let head_4 = $('#head_4');
            
            let body_dates = $('.dates');
            
            let colspan = 0;
            
            for(type in types[game_name]) {
                let colspan2 = 0;
                
                for(bettype in types[game_name][type]) {
                    let colspan3 = 0;
                    
                    for(i in types[game_name][type][bettype]) {
                        
                        if((bettype=='bonus' || bettype.indexOf('free') + 1) && types[game_name][type][bettype][i] == 'amount_in') {
                            continue;
                        }
                        
                        colspan += 1;
                        colspan2 += 1;
                        colspan3 += 1;
                        
                        head_4.append('<td data-row="4" colspan="1" class="table_' + game_name + '">' + labels[types[game_name][type][bettype][i]] + '</td>');
                        
                        body_dates.each(function(index, item){
                            let name = types[game_name][type][bettype][i];
                            $(item).append('<td class="table_' + game_name + ' ' + $(item).attr('id') + ' ' + bettype +  ' ' + name + '"> 0 </td>');
                        });
                    }
                    
                    head_3.append('<td data-row="3" class="table_' + game_name + '" colspan="' + colspan3 + '">' + labels[bettype] + '</td>');
                }
                
                head_2.append('<td data-row="2" class="table_' + game_name + '" colspan="' + colspan2 + '">' + labels[type] + '</td>');
            }
            
            head_1.append('<td data-row="1" class="table_' + game_name + '" colspan="' + colspan + '">' + game_name + '</td>');
            
            for(date in data[game_name]) {
                for(type in data[game_name][date]) {
                    for(bettype in data[game_name][date][type]) {
                        for(i in data[game_name][date][type][bettype]) {
                            let value = data[game_name][date][type][bettype][i];
                            $('.table_'+game_name + '.' + date + '.' + bettype + '.' + i).text(value);
                        }
                    }
                }
            }
            
            
            function row_colspan(index, item){
                let curr_value = current_lenght + +$(item).attr('colspan');
                                     
                $(item).data('colspan', current_lenght + '/' + curr_value);
                current_lenght = curr_value;
            }
            
            let headers = [1,2,3,4];
            let current_lenght = 2;
            
            headers.forEach(function(item){
                let head = $('#head_' + item).find('td');
                
                head.each(row_colspan);
                
                current_lenght = 2;
            })
            
            recalc();
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
                            top: ' + $('#head').offset().top + 'px; \n\
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
                    grow = '1/5' ;//Номер строки в шапке
                } else {                
                    gcol = $(this).data('colspan');
                    let row = $(this).data('row');
                    row = row?row:1;
                    grow = row ;//Номер строки в шапке
                }
                
                curr_pos += 1;
                
                var $newdiv = $('<div />',
                        {
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
              
    });
</script>