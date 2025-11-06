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
    <?php if(in_array(person::$role,['rmanager','agent','client'])): ?>
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo __('Операции с ППС') ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="white-box">
                <h2 class="page-title" style="text-align: center;"><?php echo __('Создать ППС') ?></h2>
                <form class="form-horizontal form-material" action="<?php echo $dir; ?>/dashboard/create" method="post" id="create_office">
                    <br/>
                    <div class="form-group">
                        <label for="add_money_login" class="col-md-12"><?php echo __('Валюта') ?></label>
                        <div class="col-md-12">
                            <select name="currency">
                                <?php foreach ($currencies as $currency): ?>
                                    <option <?php if($currency->id==3): ?> selected <?php endif; ?> value="<?php echo $currency->id ?>"><?php echo __($currency->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <br/>
                    <div class="form-group">
                        <label class="col-md-12"><?php echo __('Название') ?></label>
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-line" name="v_name" value="" >
                        </div>
                    </div>
                    <?php if(person::$role=='client'): ?>
                    <div class="form-group">
                        <label class="col-md-12"><?php echo __('Тип API') ?></label>
                        <div class="col-md-12">
                            <select name="apitype">
                                    <option value="0">Seamless</option>
                                    <option selected value="1">Balance transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12"><?php echo __('Включить API') ?></label>
                        <div class="col-md-12">
                            <select name="apienable">
                                    <option value="0">No</option>
                                    <option selected value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12"><?php echo __('URL взаимодействия') ?></label>
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-line" name="gameapiurl" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12">Secret key (not required)</label>
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-line" name="secretkey" >
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label class="col-md-12"><?php echo __('Сумма на счету') ?></label>
                        <div class="col-md-12">
                            <input type="text" class="form-control form-control-line" name="amount" value="<?php echo I18n::$lang=='en'?10000:100000; ?>" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12"><?php echo __('Время инкасации') ?></label>
                        <div class="col-md-12">
                            <select name="encashment_time">
                                <?php for($i=0;$i<=23;$i++): ?>
                                <option <?php echo $i==9?'selected':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="col-md-12"><?php echo __('Часовой пояс') ?></label>
                        <div class="col-md-12">
                            <select style="width:100%" name="zone_time">
                                <?php foreach(tz::lst() as $i=>$zname): ?>
                                <option <?php echo $i==0?'selected':''; ?> value="<?php echo $i; ?>">
                                    <?php echo $zname; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
		    <div class="form-group">
                        <label class="col-md-12"><?php echo __('Язык') ?></label>
                        <div class="col-md-12">
                            <select style="width:100%" name="lang">
                                <option selected value="">
                                    Auto
                                </option>
                                <?php foreach((array) Kohana::$config->load('languages.lang') as $i=>$lang): ?>
                                <option value="<?php echo $i; ?>">
                                    <?php echo $lang; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
		<div class="form-group">
                        <label class="col-md-12"><?php echo __('Default denomination') ?></label>
                        <div class="col-md-12">
                            <select style="width:100%" name="default_dentab">
                                <?php foreach((array) Kohana::$config->load('agt.k_list') as $i=>$k): ?>
                                <option value="<?php echo $i; ?>">
                                    <?php echo $k; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <button class="btn btn-success"><?php echo __('Создать') ?></button>
                        </div>
                    </div>
                    <div id="text_create_office"></div>
                </form>
            </div>
        </div>

        <?php if(false && count($person_offices) && person::$role!='gameman'): ?>
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
                                            <?php if(arr::get($office,'visible_name')): ?>
                                            <?php echo '['.$office['visible_name'].']'; ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12"><?php echo __('Сумма пополнения/списания') ?></label>
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-line" name="amount" value="0" >
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/addmoney';" class="btn btn-success"><?php echo __('Пополнить') ?></button>
                                <?php if (Person::$role=='rmanager' || Person::$role=='analitic'): ?>
                                <button onclick="javascript: form.action = '<?php echo $dir ?>/dashboard/removemoney';" class="btn btn-danger"><?php echo __('Списать') ?></button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div id="text_office_add_money"></div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php if(false && count($person_offices)): ?>
            <div class="col-md-6 col-xs-12">
                <div class="white-box">
                    <h2 class="page-title" style="text-align: center;"><?php echo __('Настройки ППС') ?></h2>
                    <form class="form-horizontal form-material" action="<?php echo $dir; ?>/dashboard/settings" method="post" id="office_settings">
                        <div class="form-group">
                            <label class="col-md-6"><?php echo __('Выберите ППС') ?></label>
                            <div class="col-md-6">
                                <select name="office_id" class="not-multiple">
                                    <?php foreach ($person_offices as $office_id => $office): ?>
                                        <option data-vname="<?php echo $office['visible_name'];?>" data-htime="<?php echo $office['encashment_time'];?>" data-rtime="<?php echo $office['zone_time'];?>" data-cash="<?php echo $office['cashback'];?>" value="<?php echo $office_id ?>">
                                            <?php echo $office_id . ' - ' . $office['amount'] . ' '.$office['code'] ?>
                                            <?php if(arr::get($office,'visible_name')): ?>
                                            <?php echo '['.$office['visible_name'].']'; ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-6"><?php echo __('Кэшбэк') ?></label>
                            <div class="col-md-6">
                                <input type="number" step="0.01" class="form-control form-control-line" name="cashback" value="" >
                            </div>
                            <label class="col-md-6"><?php echo __('Время расчетного периода') ?></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-line" name="encashment_time" value="" >
                            </div>
                            <label class="col-md-6"><?php echo __('Часовой пояс') ?></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-line" name="zone_time" value="" >
                            </div>
                            <label class="col-md-6"><?php echo __('Название ППС') ?></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-line" name="v_name" value="" >
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success"><?php echo __('Сохранить') ?></button>
                            </div>
                        </div>

                        <div id="text_office_settings"></div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php endif; ?>
    <?php if(false && (count($person_offices) && person::$role!='gameman') || person::$role=='gameman'): ?>
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo __('Операции с пользователями') ?></h4>
        </div>
    </div>
    <?php endif; ?>
    <div class="row">
        <?php if(false && (count($person_offices) && person::$role!='gameman') || person::$role=='gameman'): ?>
            <div class="col-md-6 col-xs-12">
                <div class="white-box">
                    <h2 class="page-title" style="text-align: center;"><?php echo __('Создать нового пользователя') ?></h2>
                    <form class="form-horizontal form-material" action="<?php echo $dir; ?>/dashboard/createperson" method="get" id="create_office_select">
                        <div class="form-group">
                            <label class="col-md-8"><?php echo __('Выберите роль') ?></label>
                            <div class="col-md-8">
                                <select id="roles">
                                    <?php if(Person::$role=='gameman' && !count($person_offices)): ?>
                                        <option value="<?php echo $roles[array_search('client',$roles)]; ?>"><?php echo __('Рег. менеджер'); ?></option>
                                    <?php else: ?>
                                        <?php foreach($roles as $role_name=>$role):?>
                                        <option value="<?php echo $role; ?>"><?php echo $role_name; ?></option>
                                        <?php endforeach;?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php if(count($person_offices)): ?>
                            <label for="select_role_office" class="col-md-8"><?php echo count($person_offices)>1?__('Выберите ППС'):__('ППС') ?></label>
                            <div class="col-md-8">
                                <select id="select_role_office" name="office_id">
                                    <?php foreach ($person_offices as $office_id => $office): ?>
                                        <option value="<?php echo $office_id ?>">
                                            <?php echo $office_id . ' - ' . $office['code'] ?>
                                            <?php if(arr::get($office,'visible_name')): ?>
                                            <?php echo '['.$office['visible_name'].']'; ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <?php if(Person::$role=='agent'): ?>
                                <label class="col-md-8"><?php echo __('Комментарий') ?></label>
                                <div class="col-md-8">
                                    <input type="text" name="comment" />
                                </div>
                            <?php endif; ?>
                            <input name="role" hidden value="" /> <!--hidden-->
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success"><?php echo __('Создать') ?></button>
                            </div>
                        </div>
                        <div id="create_select"></div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if(in_array(person::$role,['agent'])): ?>
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo __('Привязка к ППС') ?></h4>
        </div>
    </div>
    <div class="row">
        <?php if(count($person_offices)): ?>
            <div class="col-md-6 col-xs-12">
                <div class="white-box">
                    <h2 class="page-title" style="text-align: center;"><?php echo __('Привязанные к ') ?><?php echo __($role_to_change) ?><?php echo __('ам') ?><?php echo __(' ППС') ?></h2>
                    <form class="form-horizontal form-material" action="<?php echo $dir; ?>/dashboard/changeoffice" method="get" id="change_office">
                        <div class="form-group">
                            <label class="col-md-6"><?php echo __('ID') ?></label>
                            <label class="col-md-6"><?php echo __('ППС') ?></label>
                            <div class="col-md-6">
                                <?php if(count($persons)):?>
                                    <select class=" col-md-12" id="changed_id" name="changed_id" >
                                        <?php foreach ($persons as $person_id =>$person): ?>
                                                <option value="<?php echo $person_id; ?>" data-offices='<?php echo $person['offices']; ?>'><?php echo $person_id; ?> <?php echo $person['visible_name']? "- ".$person['visible_name']:""; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else:?>
                                    <?php echo __('Нет созданных ') ?><?php echo __($role_to_change) ?><?php echo __('ов') ?>
                                <?php endif;?>
                            </div>
                            <div class="col-md-6">
                                <select class="change_office col-md-12" id="new_offices" style="height: 200px;" name="new_offices[]" multiple="multiple">
                                    <?php foreach ($person_offices as $office_id => $office): ?>
                                        <option value="<?php echo $office_id ?>">
                                            <?php echo $office_id . ' - ' .($office['amount']??"0")." ". $office['code'] ?>
                                            <?php if($office['visible_name']): ?>
                                            <?php echo '['.$office['visible_name'].']'; ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success"><?php echo __('Сохранить') ?></button>
                            </div>
                        </div>
                        <div id="text_change_office"></div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<!-- /.container-fluid -->