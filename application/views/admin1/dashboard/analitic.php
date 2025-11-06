<script>
$(window).ready(function() {

    var optionsCreate = {
        dataType: 'json',
        type: 'post',
        async: false,
        beforeSubmit: function() {
            $('#text_create_office').empty();
        },
        success: function (data) {
            var message_text = '';

            if(data.error) {
                for(index in data.errors) {
                    message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                }
            } else {
                message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';

                setTimeout(function(){
                   window.location = window.location;
                },3000);
            }

            $('#text_create_office').append(message_text);
        },
        error: function(err) {
            var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
            $('#text_create_office').append(err_message);
        }
    };

    var optionsAdd = {
        dataType: 'json',
        type: 'post',
        async: false,
        beforeSubmit: function() {
            $('#text_office_add_money').empty();
        },
        success: function (data) {
            var message_text = '';

            if(data.error) {
                for(index in data.errors) {
                    message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                }
            } else {
                message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
            }

            $('#text_office_add_money').append(message_text);
        },
        error: function(err) {
            var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
            $('#text_office_add_money').append(err_message);
        }
    };

    $('#create_office').ajaxForm(optionsCreate);
    $('#office_add_money').ajaxForm(optionsAdd);

    var optionsSettings = {
        dataType: 'json',
        type: 'post',
        async: false,
        beforeSubmit: function() {
            $('#text_office_settings').empty();
        },
        success: function (data) {
            var message_text = '';

            if(data.error) {
                for(index in data.errors) {
                    message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                }
            } else {
                message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
            }
            $('#text_office_settings').append(message_text);
        },
        error: function(err) {
            var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
            $('#text_office_settings').append(err_message);
        }
    };
    $('#office_settings').ajaxForm(optionsSettings);

    var optionsCreateSelect = {
        dataType: 'json',
        type: 'post',
        async: false,
        beforeSubmit: function(data) {
            var link = '<?php echo $dir; ?>/dashboard/createperson/?';

            for(index in data) {
                if(data[index].name == 'role') {
                    link += 'role=' + data[index].value;
                }
            }

            for(index in data) {
                if(data[index].name == 'office_id') {
                    link += '&office_id[]=' + data[index].value;
                }
            }
            
            for(index in data) {
                if(data[index].name == 'comment') {
                    link += '&comment=' + data[index].value;
                }
            }
            
            link += '&print=1';

            window.open(link, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');

            return false;
        },
    };
    $('#create_office_select').ajaxForm(optionsCreateSelect);
    
    
    var optionsChangeOffice = {
        dataType: 'json',
        type: 'post',
        async: false,
        beforeSubmit: function() {
            $('#text_change_office').empty();
        },
        success: function (data) {
            var message_text = '';

            if(data.error) {
                for(index in data.errors) {
                    message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                }
            } else {
                message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
                setTimeout(function(){
                   window.location = window.location;
                },3000);
            }
            $('#text_change_office').append(message_text);
        },
        error: function(err) {
            var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
            $('#text_change_office').append(err_message);
        }
    };
    $('#change_office').ajaxForm(optionsChangeOffice);


    function getSelection(selector){
        var offices = $(selector).find(':selected').data("offices");
        $("#new_offices option").prop("selected", false);
        $(offices).each(function(i,v){
            $("#new_offices option[value="+v+"]").prop("selected", true);
        });
    }
    
    getSelection("#changed_id");
    
    $("#changed_id").on("change", function(){
        getSelection(this);
    });

    $("input[name=role]").val(checkOption(['manager','rmanager']));
    $("select#roles").on("change", function(){
        checkOption(['manager','rmanager']);
        $("input[name=role]").val($(this).val());
    });

    checkSettings($('.not-multiple'));

    $('.not-multiple').change(function(){
        checkSettings($(this));
    });

    function checkSettings($this){
        var p = [];
        p['v_name'] = $this.find(':selected').data('vname');
        p['encashment_time'] = parseInt($this.find(':selected').data('htime')) || 0;
        p['zone_time'] = parseInt($this.find(':selected').data('rtime')) || 0;
        p['cashback'] = parseFloat($this.find(':selected').data('cash')) || 0;

        $('#office_settings input').each(function(){
            var n = $(this).attr('name');
            if(n=='v_name' && p[n]){
                $(this).attr("readonly","readonly");
            }else{
                $(this).removeAttr("readonly");
            }
            $(this).val(p[n]);
        });
    }

    function checkOption(value){
        /* возвращает значение выбранного option
         * при наличии параметра устанавливает множественный выбор в select office_id
         */
        var v = $("select#roles option:selected").val();
        var $select ='#select_role_office';
        $($select).removeAttr('disabled');
        $($select).show();
        $('[for=select_role_office]').show();

        $($select).removeAttr("multiple");
        value.forEach(function(vv) {
            if(v==vv){
                $($select).attr("multiple", "multiple");
                if(vv=='rmanager') {
                    $('#select_role_office option:selected').removeAttr('selected');
                    $($select).attr('disabled','disabled');
                    $($select).hide();
                    $('[for=select_role_office]').hide();
                }
            }
        });
        return v;
    }
});
</script>
<div class="container-fluid">
    <?php if(in_array(person::$role,['analitic'])): ?>
        <div class="row bg-title">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title"><?php echo __('Операции с ППС') ?></h4>
            </div>
        </div>
        <div class="row">
            <?php if(count($person_offices)): ?>
                <div class="col-md-6 col-xs-12">
                    <div class="white-box">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Пополнение баланса ППС') ?></h2>
                        <form class="form-horizontal form-material" action="<?php echo $dir; ?>/dashboard/addmoney" method="post" id="office_add_money">
                            <div class="form-group">
                                <label class="col-md-12"><?php echo __('Выберите ППС') ?></label>
                                <div class="col-md-12">
                                    <select name="office_id" class="not-multiple">
                                        <?php foreach ($person_offices as $office_id => $office): ?>
                                            <option value="<?php echo $office_id ?>">
                                                <?php echo $office_id . ' - ' . $office['amount'] . ' '.$office['code'] ?>
                                                <?php if($office['visible_name']): ?>
                                                <?php echo '['.$office['visible_name'].']'; ?>
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12"><?php echo __('Сумма пополнения') ?></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" name="amount" value="0" >
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button class="btn btn-success"><?php echo __('Пополнить') ?></button>
                                </div>
                            </div>

                            <div id="text_office_add_money"></div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-6 col-xs-6">
                    <div class="white-box">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Операции с балансом персонала') ?></h2>
                        <form class="form-horizontal form-material" method="post" id="amount_opts">
                            <div class="form-group">
                                <label for="add_money_login" class="col-md-12"><?php echo __('Логин персонала') ?></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" name="login" id="add_money_login">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12"><?php echo __('Сумма') ?></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" name="amount" value="0" >
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">
                                    <button type="submit" value="pay" onclick="javascript: form.action='<?php echo $dir ?>/dashboard/personpay';" class="btn btn-success"><?php echo __('Пополнить') ?></button>
                                </div>
                                <div class="col-sm-5">
                                    <button type="submit" value="withdraw" onclick="javascript: form.action='<?php echo $dir ?>/dashboard/personwithdraw';" class="btn pull-right btn-danger"><?php echo __('Списать') ?></button>
                                </div>
                            </div>

                            <div id="response_text_opts"></div>
                        </form>
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
                                    <input type="text" class="form-control form-control-line" name="login" id="add_money_login">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-12"><?php echo __('Сумма') ?></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" name="amount" value="0" >
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">
                                    <button type="submit" value="pay" onclick="javascript: form.action='<?php echo $dir ?>/dashboard/amountpay';" class="btn btn-success"><?php echo __('Пополнить') ?></button>
                                </div>
                                <div class="col-sm-5">
                                    <button type="submit" value="withdraw" onclick="javascript: form.action='<?php echo $dir ?>/dashboard/amountwithdraw';" class="btn pull-right btn-danger"><?php echo __('Списать') ?></button>
                                </div>
                            </div>

                            <div id="response_text_opts"></div>
                        </form>
                    </div>
                </div>
            </div>
</div>
<script>
    var operation;//тип операции - списание/пополнение
    var optionsPayOrW = {
        dataType: 'json',
        type: 'post',
        async: false,
        beforeSubmit: function() {
            $('#response_text_opts').empty();
        },
        success: function (data) {
            var message_text = '';

            if(data.error) {
                for(index in data.errors) {
                    message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                }
            } else {
                var result;
                var $val = parseFloat($('#amount_opts input[name=amount]').val());
                var $before = parseFloat($('#offamount').text());
                if(operation=="pay"){
                    result = $before-$val;
                }else{
                    result = $before+$val;
                }

                $('button[value='+operation+']').prop('disabled', true);
                setTimeout(function(){
                    $('button[value='+operation+']').prop('disabled', false);

                }, 6000);

                $('#offamount').text(result.toFixed(2));
                $('#amount_opts input[name=amount]').val(0);

                message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
            }

            $('#response_text_opts').append(message_text);
        },
        error: function(err) {
            var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
            $('#response_text_opts').append(err_message);
        }
    };

    $('#amount_opts').ajaxForm(optionsPayOrW);

    $('button[type=submit]').click(function(){
        operation = $(this).val();
    });
</script>
<!-- /.container-fluid -->