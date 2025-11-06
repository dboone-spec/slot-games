<script>
    $(window).ready(function () {

        $('#primary-header-modal').find('.close,.closebtn').click(function() {
            $('#primary-header-modal').hide();
        });

        $('#create_rfid').on('click', function () {
            $('#primary-header-modal').show();
        });


        $('#create_rfid_user').on('click', function () {

            if ($('[name=rfid]').val() == '0' || $('[name=rfid]').val() == '') {
                return alert('CAN NOT READ CARD NUMBER');
            }
            send_create();
        });

        $('#create_user').on('click', function () {
            $('[name=rfid]').val('-1');
            send_create();
        });

        function send_create() {
            $.ajax({
                method: 'post',
                url: '<?php echo $dir ?>/dashboard/createuser?print=1',
                dataType: 'json',
                cache: false,
                data: {
                    comment: $('#comment_input').val(),
                    rfid: $('[name=rfid]').val()
                },
                async: false,
                success: function (data) {
                    if ($('[name=rfid]').val() == '-1') {
                        var w = window.open('', '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
                        var html = data.code;
                        $(w.document.body).html(html);
                        w.print();
                    }
                    if (data.login != null) {
                        $('#users').prepend("<li balance='0' comment='"+data.comment+"' value='" + data.login + "'>" + data.login + " [0] <i>"+data.comment+"</i></li>");
                        $('#add_money_login').val(data.login);
                        $('#rfid_message').text('Player with login '+data.login+' created successfully');
                        $('[value=' + ($('#add_money_login').val()) + ']').click();
                    }

                    if (data.errors.length) {
                        alert(data.errors);
                    }
                },
            });
        }

        var operation;//тип операции - списание/пополнение
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
                    if($('[value=' + ($('#add_money_login').val()) + ']')) {
                        var ht = $('#add_money_login').val() + " ["+data.newamount+"] <i>"+$('[value=' + ($('#add_money_login').val()) + ']').attr('comment')+"</i>";
                        $('[value=' + ($('#add_money_login').val()) + ']').html(ht);
                        $('[value=' + ($('#add_money_login').val()) + ']').attr('balance',data.newamount);
                        $('[value=' + ($('#add_money_login').val()) + ']').click();
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
        $('#users,#terminals').on("click", "li", function () {
            $('#users li,#terminals li').removeClass('active');
            $(this).addClass('active');

            $('#comment').empty();
            if ($(this).attr('num')) {
                $('#comment').text($(this).attr('num'));
            }
            $('#comment').text();

            var $v = $(this).val();
            $("#amount_opts input[name=login]").val($v);
            $("#amount_opts input[name=amount]").val('');
            $("#amount_opts input[name=amount]").focus();

            $('#add_money_login').attr('data-original-title','<?php echo __('Баланс'); ?>' + ': ' + $('[value=' + $v + ']').attr('balance') + '; Comment: ' + $('[value=' + $v + ']').attr('comment'));
            $('#add_money_login').tooltip("show");

        });

        $('#add_money_login')
        .tooltip({
            title: '',
            trigger: 'manual',
            container: '#amount_opts',
            placement: 'right'
        })
        .tooltip('hide');
    });
</script>
<style>
    #users li.active, #terminals li.active {
        background-image: linear-gradient(#707cd2,#707cd2),linear-gradient(rgba(120,130,140,.13),rgba(120,130,140,.13));
        color: #fff;
    }
</style>


<!-- Primary Header Modal -->
<div id="primary-header-modal" class="modal" tabindex="-1" role="dialog"
    aria-labelledby="primary-header-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-primary">
                <h4 class="modal-title" id="primary-header-modalLabel" style="float: left;">Scan RFID card
                </h4>
                <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p id="rfid_message">Please, place a card on RFID reader</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light closebtn"
                    data-dismiss="modal">Close</button>
                <button type="button" id="create_rfid_user" class="btn btn-primary ">Create user with card</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="container-fluid">
    <!--    <div class="row bg-title">
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                <h4 class="page-title"><?php // echo $info_row    ?></h4>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <a class="btn btn-danger" href="<?php echo $dir ?>/select/endsession"><?php // echo __('Завершить смену')    ?></a>
            </div>
        </div>-->
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo __('Операции с аккаунтами') ?></h4>
        </div>
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <?php if(!count($terminals)): ?>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="white-box">
                            <h2 class="page-title" style="text-align: center;"><?php echo __('Для создания нового игрока нажмите на кнопку ниже') ?></h2>
                            <label>
                                Comment:
                                <input name="comment" type="text" id="comment_input" autocomplete="off" />
                            </label>
                            <input type="hidden" name="rfid" />
                            <button class="btn btn-success" id="create_user"><?php echo __('Создать') ?></button>
                            <button class="btn btn-success" id="create_rfid"><?php echo __('Создать') ?> RFID</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="white-box">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Операции с балансом игрока') ?></h2>
                        <form class="form-horizontal form-material" method="post" id="amount_opts">
                            <div class="form-group">
                                <label for="add_money_login" class="col-md-12"><?php echo __('Логин игрока') ?></label>
                                <div class="col-md-12">
                                    <b id="comment"></b>
                                    <input type="text" class="form-control form-control-line" name="login" id="add_money_login">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12"><?php echo __('Сумма') ?></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" id="amount_input" name="amount" value="0" >
                                </div>
                            </div>

                            <div class="form-group">
                                <?php foreach([10,50,100,200,500,1000] as $price): ?>
                                    <div class="col-sm-1">
                                        <button type="button" value="<?php echo $price; ?>" class="enter_amount_input btn btn-default"><?php echo $price; ?></button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">
                                    <button type="submit" value="pay" onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/amountpay';" class="btn btn-success"><?php echo __('Пополнить') ?></button>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" value="withdraw" onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/amountwithdraw';" class="btn pull-right btn-danger"><?php echo __('Списать') ?></button>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" value="withdraw" onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/amountwithdraw?m=all';
                                            " class="btn pull-right btn-danger"><?php echo __('Списать все') ?></button>
                                </div>
                            </div>

                            <div id="response_text_opts"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
<?php if(count($terminals)): ?>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="white-box">
                            <style>
                                .white-box ul li {
                                    list-style-type: none;
                                    margin: 5px 0 ;
                                    padding: 5px;
                                }
                                .white-box ul li:nth-child(odd) {
                                    background: #EEE;
                                }
                                .white-box ul li:hover {
                                    cursor:pointer;
                                }
                            </style>
                            <h2 class="page-title" style="text-align: center;"><?php echo __('Список Терминалов') ?></h2>
                            <style>
                                #terminals {
                                    display: table;
                                    width: 100%;
                                }
                                #terminals li{
                                    display: table-row;
                                }
                                #terminals li:first-child {

                                }
                                #terminals li span{
                                    display: table-cell;
                                    padding: 5px;
                                }
                            </style>
                            <ul id="terminals">
                                <li>
                                    <span><?php echo __('Номер') ?></span>
                                    <span><?php echo __('ID Терминала') ?></span>
                                    <span><?php echo __('Название') ?></span>
                                    <span><?php echo __('Баланс') ?></span>
                                </li>
    <?php foreach($terminals as $terminal): ?>
                                    <li num="<?php echo str_pad($terminal->msrc,2,'0',STR_PAD_LEFT) ?>" terminal_id="<?php echo $terminal->id; ?>" style="<?php echo $terminal->blocked ? 'color:red' : ''; ?>" value="<?php echo $terminal->id ?>">
                                        <span><?php echo str_pad($terminal->msrc,2,'0',STR_PAD_LEFT) ?></span>
                                        <span><?php echo $terminal->id ?></span>
                                        <span><?php echo $terminal->visible_name; ?></span>
                                        <span class="user_amount"><?php echo $terminal->amount(); ?></span>
                                    </li>
    <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
<?php else: ?>
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="white-box">
                            <style>
                                .white-box ul li {
                                    list-style-type: none;
                                    margin: 5px 0 ;
                                    padding: 5px;
                                }
                                .white-box ul li:nth-child(odd) {
                                    background: #EEE;
                                }
                                .white-box ul li:hover {
                                    cursor:pointer;
                                }
                            </style>
                            <h2 class="page-title" style="text-align: center;"><?php echo __('Список игроков') ?></h2>
                            <ul id="users">
                                    <?php foreach($users as $user): ?>
                                    <li balance="<?php echo $user->amount(); ?>" comment="<?php echo $user->comment; ?>" value="<?php echo $user->id ?>"><?php echo $user->id ?> [<?php echo $user->amount(); ?>]
                                        <?php if(!empty($user->comment)): ?>
                                            <i><?php echo $user->comment; ?></i>
                                    <?php endif; ?>
                                    </li>
    <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
<?php endif; ?>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
<script>
    $('.enter_amount_input').click(function () {
        $('#amount_input').val($(this).val());
    });

    function rfid_listen() {

        $.ajax({
            url: "http://localhost:8686",
            jsonp: "callback",
            dataType: "jsonp",
            cache: "false",
            success: function (response) {

                if ($('[name=rfid]').val() != response.code) {
                    $('[name=rfid]').val(response.code);
                    $('#add_money_login').val('');
                    $('#add_money_login').tooltip("hide");
                    $('#rfid_message').text('Please, place a card on RFID reader');
                    if (response.code != '0') {
                        $('#rfid_message').text('Card scan success. You can create new player now');
                        $.ajax({
                            url: 'dashboard/rfid',
                            cache: false,
                            data: {
                                rfid: response.code
                            },
                            success: function (b) {
                                if (b) {
                                    $('#add_money_login').val(b);
                                    $('#rfid_message').text('Player with this card already created');
                                    listenIfLoginChanged();
                                }
                            }
                        });
                    }
                }

            },
            error: function (jqXHR, exception, err) {
                $('[name=rfid]').val('');
            }
        });

        setTimeout(rfid_listen, 2000);
    }

    rfid_listen();

    function terminals_balance() {
        $.ajax({
            url: '/api/v1/terminals.php?r=' + Math.random(),
            dataType: 'json',
            success: function (data) {
                data.forEach(function (d) {
                    $('[terminal_id=' + d['id'] + '] .user_amount').text(d['amount']);
                });
                setTimeout(terminals_balance, 3000);
            }
        });
    }

    false && terminals_balance();

    var current_login_input;
    function listenIfLoginChanged() {
        var new_login_input = $('#add_money_login').val();

        setTimeout(listenIfLoginChanged, 200);

        if (new_login_input.length < 6) {
            return;
        }

        if (new_login_input == current_login_input) {
            return;
        }

        current_login_input = new_login_input;

        $.ajax({
            url: 'dashboard/userbalance',
            cache: false,
            data: {
                user_id: new_login_input
            },
            success: function (b) {
                $('[value=' + ($('#add_money_login').val()) + ']').click();
            }
        });
    };

    listenIfLoginChanged();
</script>
<style>
    #amount_opts .tooltip {
        left: 200px !important;
    }
</style>