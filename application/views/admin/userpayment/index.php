<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>
            <div class="row">
                <div class="col-sm-12">
                    <form method="GET" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-2">ID пользователя</label>
                            <div class="col-md-2">
                                <input type="text" id="user_id" name="user_id" value="<?php echo $user_id; ?>" >
                            </div>
                            <label class="col-md-2">Период:</label>
                            <div>    
                                <b>С </b><input type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d',$time_from) ?>" > 
                                <b>ПО </b><input type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d',$time_to) ?>" >
                            </div>
                            <div class="col-md-2">    
                                <script>
                                    $(function () {
                                        $("#time_start").datepicker({dateFormat: "yy-mm-dd"});
                                        $("#time_end").datepicker({dateFormat: "yy-mm-dd"});
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Поиск" />
                            <a class="btn btn-default" href="<?php echo $dir ?>/userpayment">Очистить</a>
                            <a class="btn btn-default export" onclick='window.open("<?php echo $dir ?>/userpayment/" + (window.location.search.length != 0 ? window.location.search + "&export=1" : "?export=1"));' >Экспорт в CSV</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="sort" class="table table-striped <?php // echo $dir            ?>" >
                        <thead>
                        <tr>
                            <?php foreach($headers as $header): ?>
                                <td><?php echo $header; ?></td>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data as $key => $value): ?>
                            <tr>
                                <?php foreach($headers as $key => $header): ?>
                                    <td>
                                        <?php echo th::number_format($value[$key]) ?>
                                    </td>
                                <?php endforeach ?>
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
<style>
    #sort thead tr td{
        cursor: pointer;
        position: relative;
    }
    .desc::before{
        content: '\25bc';
        position: absolute;
        left: -10px;
    }
    .asc::before{
        content: '\25b2';
        position: absolute;
        left: -10px;
    }
</style>
<script>
    $(window).on("load", function () {
        $('#sort thead tr').on("click", "td", function () {
            updown(this, $(this).index(), 1);
        });
        var parent = $('#sort tbody');
        var rows = $('#sort tbody tr');
        
        function updown($this, $num, $down) {
            if ($($this).hasClass('desc')) {
                $($this).removeClass('desc');
                $($this).addClass('asc');
                sorting($num, !$down);
            } else if ($($this).hasClass('asc')) {
                $($this).removeClass('asc');
                $($this).addClass('desc');
                sorting($num, $down);
            } else {
                $('#sort thead tr td').removeClass('desc');
                $('#sort thead tr td').removeClass('asc');
                $($this).addClass('desc');
                sorting($num, $down);
            }
        }

        function sorting(num, desc = 1) {
            rows.sort(function (a, b) {
                a_val = parseFloat($(a).find('td').eq(num).text());//
                b_val = parseFloat($(b).find('td').eq(num).text());//Значение сравниваемой ячейки
                a_name = $(a).find('td:first-child').text();//
                b_name = $(b).find('td:first-child').text();//Текст первой ячейки в строке
                an = isNaN(a_val) ? -1 : a_val;
                bn = isNaN(b_val) ? -1 : b_val;
                if (an == bn) {
                    if (desc) {//По первому столбцу убывание
                        if (a_name > b_name)
                            return -1;
                        if (a_name < b_name)
                            return 1;
                        return 0;
                    } else { //По первому столбцу возрастание
                        if (a_name < b_name)
                            return -1;
                        if (a_name > b_name)
                            return 1;
                        return 0;
                    }
                }
                if (desc) {
                    return bn - an; //По убыванию
                } else {
                    return an - bn;
                }
            });
            rows.detach().appendTo(parent);
        }
    });
</script>
