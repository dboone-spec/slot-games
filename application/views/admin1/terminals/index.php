<div class="pc-container">

    <div class="pcoded-content">
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-xl-6 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Terminal dashboard') ?></h2>
                        <form class="form-horizontal form-material" method="post" id="amount_opts">
                            <div class="row">
                                <h3 class="col-md-12"><?php echo __('Office balance') ?></h3>
                                <div class="col-md-12">
                                    <h4 id="office_amount"><?php echo person::user()->my_office->amount; ?></h4>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <label class="col-md-12"><?php echo __('Terminal balance') ?></label>
                                <div class="col-md-12">
                                    <b id="comment"></b>
                                    <h4 id="current_balance">Choose terminal</h4>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <label for="add_money_login" class="col-md-12"><?php echo __('Terminal ID') ?></label>
                                <div class="col-md-12">
                                    <b id="comment"></b>
                                    <input type="text" class="form-control form-control-line" name="login" id="add_money_login">
                                </div>
                            </div>

                            <div class="row">
                                <label class="col-md-12"><?php echo __('Сумма') ?></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" id="amount_input" name="amount" value="0" >
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php foreach([10,50,100,200,500,1000] as $price): ?>
                                        <button type="button" value="<?php echo $price; ?>" class="enter_amount_input btn btn-primary"><?php echo $price; ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" value="pay" onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/amountpay';" class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<?php echo __('Пополнить') ?>
                                    </button>
                                    <div class="float-right">
                                        <button type="submit" value="withdraw" onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/amountwithdraw';" class="btn pull-right btn-danger">
                                            <i class="fa fa-minus-circle"></i>&nbsp;&nbsp;<?php echo __('Списать') ?>
                                        </button>
                                        <button type="submit" value="withdraw" onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/amountwithdraw?m=all';
                                                " class="btn pull-right btn-danger">
                                                    <i class="fa fa-times"></i>&nbsp;&nbsp;<?php echo __('Списать все') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="response_text_opts"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Terminals') ?></h2>
                        <div class="table-responsive">
                            <div id="report-table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="button-select" class="table table-striped table-bordered nowrap dataTable" style="cursor: pointer" role="grid" aria-describedby="report-table_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="" tabindex="0" aria-controls="report-table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Icon: activate to sort column descending" style="">ID</th>
                                                    <th class="" tabindex="0" aria-controls="report-table" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" style="width: 100px;">Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($terminals as $terminal): ?>
                                                    <tr role="row" class="odd" terminal_id="<?php echo $terminal->id; ?>" comment="<?php echo $terminal->comment; ?>">
                                                        <td>
                                                            <?php echo $terminal->id; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $terminal->visible_name; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
</div>
<script src="/js/jquery.form.js"></script>
<script>

                                            var operation;
                                            var optionsPayOrW = {
                                                dataType: 'json',
                                                type: 'post',
                                                async: false,
                                                cache: false,
                                                beforeSubmit: function () {
                                                    $('#response_text_opts').empty();

                                                    $('button[value=' + operation + ']').prop('disabled', true);
                                                    setTimeout(function () {
                                                        $('button[value=' + operation + ']').prop('disabled', false);
                                                    }, 1000);
                                                },
                                                success: function (data) {
                                                    var message_text = '';

                                                    if (data.error) {
                                                        for (index in data.errors) {
                                                            message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                                                        }
                                                    } else {
                                                        var result;
                                                        var $val = parseFloat($('#amount_opts input[name=amount]').val());
                                                        var $before = parseFloat($('#offamount').text());
                                                        if (operation == "pay") {
                                                            result = $before - $val;
                                                        } else {
                                                            result = $before + $val;
                                                        }

                                                        $('#offamount').text(result.toFixed(2));
                                                        $('#amount_opts input[name=amount]').val(0);
                                                        if ($('[terminal_id=' + ($('#add_money_login').val()) + ']')) {
                                                            $('[terminal_id=' + ($('#add_money_login').val()) + '] td').eq(3).text(data.newamount);
                                                            $('[terminal_id=' + ($('#add_money_login').val()) + ']').attr('comment', data.comment);
                                                            $('[terminal_id=' + ($('#add_money_login').val()) + ']').click();
                                                        }

                                                        message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
                                                    }

                                                    $('#response_text_opts').append(message_text);
                                                },
                                                error: function (err) {
                                                    var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
                                                    $('#response_text_opts').append(err_message);
                                                }
                                            };

                                            $('#amount_opts').ajaxForm(optionsPayOrW);

                                            $('button[type=submit]').click(function () {
                                                operation = $(this).val();
                                            });
                                            $('#button-select').on("click", "tr", function () {

                                                var $v = $(this).attr('terminal_id');

                                                $.ajax({
                                                    url: '/enter/dashboard/officebalance',
                                                    success: function(d) {
                                                        $('#office_amount').text(d);
                                                    },
                                                    error: function() {
                                                        $('#office_amount').text('Error');
                                                    }
                                                });

                                                $.ajax({
                                                    url: '/enter/dashboard/userbalance',
                                                    data: {
                                                        user_id: $v
                                                    },
                                                    success: function(d) {
                                                        $('#current_balance').text(d);
                                                    },
                                                    error: function() {
                                                        $('#current_balance').text('Error');
                                                    }
                                                });

                                                $("#amount_opts input[name=login]").val($v);
                                                $("#amount_opts input[name=amount]").val('');
                                                $("#amount_opts input[name=amount]").focus();

                                                $('#add_money_login').attr('data-original-title', 'Comment: ' + $('[terminal_id=' + $v + ']').attr('comment'));
//                                                $('#add_money_login').tooltip("show");

                                            });

                                            $('#add_money_login')
                                                    .tooltip({
                                                        title: '',
                                                        trigger: 'manual',
                                                        container: '#amount_opts',
                                                        placement: 'right'
                                                    })
                                                    .tooltip('hide');

                                            $('.enter_amount_input').click(function () {
                                                $('#amount_input').val($(this).val());
                                            });

                                            $('#button-select').DataTable({
                                                dom: 'Bfrtip',
                                                select: {
                                                    style: 'single',
                                                    selector: 'td:not(:last-child)'
                                                },
                                                pageLength: 20
//                                                paging: {
//
//                                                },
//                                                paging: false,
//                                                bFilter: false,
//                                                bSort: false,
//                                                bInfo: false
                                            });
</script>