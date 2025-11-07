<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4><?php echo $mark ?></h4>
                        <hr>

                        <form method="GET" class="form-horizontal">

                            <div class="form-row">

                                <div class="form-group col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                  id="inputGroup-sizing-sm">Date time range</span>
                                        </div>
                                        <?php echo $time_vidget; ?>
                                    </div>
                                </div>

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">User Id</span>
                                        </div>
                                        <input type="text" name="user_id"
                                               required
                                               value="<?php echo $user_id == -1 ? '' : $user_id; ?>"
                                               class="form-control form-control-sm">
                                    </div>
                                </div>


                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('no_fs', 1, !!$no_fs, ['id' => '_no_fs', 'class' => 'custom-control-input']) ?>
                                        <label class="custom-control-label" for="_no_fs">Exclude FS</label>
                                    </div>
                                </div>

                                <div class="w-100"></div>

								<div class="non-form-control ml-auto">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit"
                                           value="<?php echo __('Поиск') ?>"/>
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-round btn-outline-secondary"
                                       href="/enter/userbet"><?php echo __('Очистить') ?></a>
                                </div>
                            </div>

                            For dates earlier than 1st <?php echo date('F Y', status::instance()->usersMH) ?>, data will
                            be shown for a full month regardless of date.
                        </form>

                        <?php if ($user_id > 0 && Person::$role == 'sa' && in_array(Person::$user_id,[2,16,1007])): ?>
                            <div class="card">

                                <div class="card-body">
                                    <hr>
                                    <form class="form-inline" method="POST" action="/enter/userbet/setrtp">

                                        <div class="form-group mb-1">
                                            <label for="" class="">User Id: <?php echo "{$user->id},<br> Extrenal id: {$user->external_id}" ?> </label>
                                        </div>
                                        <div class="form-group mx-sm-3 mb-2">

                                            <label for="test" class="">&nbsp;&nbsp;RTP&nbsp;&nbsp; </label>
                                            <input name="id" type="hidden" readonly="" class="" id=""
                                                   value="<?php echo $user->id ?>">
                                            <input name="rtp" type="text" class="form-control" id="inputPassword2"
                                                   value="<?php echo $user->rtp ?>">
                                        </div>
                                        <div class="form-group mx-sm-3 mb-2">
                                            <?php echo form::checkbox('test', 1, $user->test == 1, ['id' => 'test', 'class' => "form-control"]) ?>
                                            <label for="test" class="">&nbsp;&nbsp;test </label>

                                        </div>
                                        <button type="submit" class="btn  btn-primary mb-2">Set</button>

                                    </form>

                                </div>
                            </div>

                        <?php endif; ?>

                        <?php if ($user_id <= 0): ?>
                            <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                        <thead>
                                        <th>User ID</th>
                                        <th>Last bet time</th>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $key => $value): ?>
                                            <tr>
                                                <td><?php echo $value['id']; ?></td>
                                                <td><?php echo date('Y-m-d H:i:s', $value['last_bet_time']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>

                            <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                        <thead>
                                        <th>Office</th>
                                        <?php foreach ($headers as $header): ?>
                                            <th><?php echo $header; ?></th>
                                        <?php endforeach ?>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $key => $value): ?>
                                            <tr>
                                                <td> <?php echo $user->office->id . ' ' . $user->office->visible_name ?></td>
                                                <?php foreach ($headers as $key => $header): ?>
                                                    <td>
                                                        <?php if ($key == 'percent'): ?>
                                                            <?php $v = $value['sum_amount'] == 0 ? '-' : round($value['sum_win'] / $value['sum_amount'] * 100); ?>
                                                        <?php elseif ($key == 'created'): ?>
                                                            <?php $v = date('Y-m-d', $value[$key]); ?>
                                                        <?php else: ?>
                                                            <?php $v = $value[$key] ?>
                                                        <?php endif; ?>

                                                        <?php echo (!is_numeric($v) or $key == 'user_id') ? $v : th::number_format($v) ?>
                                                    </td>
                                                <?php endforeach ?>
                                            </tr>
                                        <?php endforeach ?>

                                        <?php if ($total['sum_amount'] > 0): ?>
                                            <tr>
                                                <td><b>Total</b></td>
                                                <td></td>
                                                <td></td>
                                                <td><?php echo th::number_format($total['sum_amount']); ?></td>
                                                <td><?php echo th::number_format($total['sum_win']); ?></td>
                                                <td><?php echo th::number_format($total['win']); ?></td>
                                                <td><?php echo th::number_format($total['count_bets']); ?> </td>
                                                <td><?php echo $total['sum_amount'] == 0 ? '-' : round($total['sum_win'] / $total['sum_amount'] * 100); ?></td>

                                            </tr>


                                        <?php endif ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function () {
        $("#start").datepicker({dateFormat: "yy-mm-dd"});
        $("#end").datepicker({dateFormat: "yy-mm-dd"});
    });


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