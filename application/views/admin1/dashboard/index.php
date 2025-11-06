<script src="/js/jquery.form.js"></script>
<script>
    $(window).ready(function () {

        var optionsCreate = {
            dataType: 'json',
            type: 'post',
            async: false,
            beforeSubmit: function () {
                $('#text_create_office').empty();
            },
            success: function (data) {
                var message_text = '';

                if (data.error) {
                    for (index in data.errors) {
                        message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                    }
                } else {
                    message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';

                    setTimeout(function () {
                        window.location = window.location;
                    }, 3000);
                }

                $('#text_create_office').append(message_text);
            },
            error: function (err) {
                var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
                $('#text_create_office').append(err_message);
            }
        };

        var optionsAdd = {
            dataType: 'json',
            type: 'post',
            async: false,
            beforeSubmit: function () {
                $('#text_office_add_money').empty();
            },
            success: function (data) {
                var message_text = '';

                if (data.error) {
                    for (index in data.errors) {
                        message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                    }
                } else {
                    message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
                }

                $('#text_office_add_money').append(message_text);
            },
            error: function (err) {
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
            beforeSubmit: function () {
                $('#text_office_settings').empty();
            },
            success: function (data) {
                var message_text = '';

                if (data.error) {
                    for (index in data.errors) {
                        message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                    }
                } else {
                    message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
                }
                $('#text_office_settings').append(message_text);
            },
            error: function (err) {
                var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
                $('#text_office_settings').append(err_message);
            }
        };
        $('#office_settings').ajaxForm(optionsSettings);

        var optionsCreateSelect = {
            dataType: 'json',
            type: 'post',
            async: false,
            beforeSubmit: function (data) {
                var link = '<?php echo $dir; ?>/dashboard/createperson/?';

                for (index in data) {
                    if (data[index].name == 'role') {
                        link += 'role=' + data[index].value;
                    }
                }

                for (index in data) {
                    if (data[index].name == 'office_id') {
                        link += '&office_id[]=' + data[index].value;
                    }
                }

                for (index in data) {
                    if (data[index].name == 'comment') {
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
            beforeSubmit: function () {
                $('#text_change_office').empty();
            },
            success: function (data) {
                var message_text = '';

                if (data.error) {
                    for (index in data.errors) {
                        message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
                    }
                } else {
                    message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
                    setTimeout(function () {
                        window.location = window.location;
                    }, 3000);
                }
                $('#text_change_office').append(message_text);
            },
            error: function (err) {
                var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
                $('#text_change_office').append(err_message);
            }
        };
        $('#change_office').ajaxForm(optionsChangeOffice);


        function getSelection(selector) {
            var offices = $(selector).find(':selected').data("offices");
            $("#new_offices option").prop("selected", false);
            $(offices).each(function (i, v) {
                $("#new_offices option[value=" + v + "]").prop("selected", true);
            });
        }

        getSelection("#changed_id");

        $("#changed_id").on("change", function () {
            getSelection(this);
        });

        $("input[name=role]").val(checkOption(['manager', 'rmanager']));
        $("select#roles").on("change", function () {
            checkOption(['manager', 'rmanager']);
            $("input[name=role]").val($(this).val());
        });

        checkSettings($('.not-multiple'));

        $('.not-multiple').change(function () {
            checkSettings($(this));
        });

        function checkSettings($this) {
            var p = [];
            p['v_name'] = $this.find(':selected').data('vname');
            p['encashment_time'] = parseInt($this.find(':selected').data('htime')) || 0;
            p['zone_time'] = parseInt($this.find(':selected').data('rtime')) || 0;
            p['cashback'] = parseFloat($this.find(':selected').data('cash')) || 0;

            $('#office_settings input').each(function () {
                var n = $(this).attr('name');
                if (n == 'v_name' && p[n]) {
                    $(this).attr("readonly", "readonly");
                } else {
                    $(this).removeAttr("readonly");
                }
                $(this).val(p[n]);
            });
        }

        function checkOption(value) {
            /* возвращает значение выбранного option
             * при наличии параметра устанавливает множественный выбор в select office_id
             */
            var v = $("select#roles option:selected").val();
            var $select = '#select_role_office';
            $($select).removeAttr('disabled');
            $($select).show();
            $('[for=select_role_office]').show();

            $($select).removeAttr("multiple");
            value.forEach(function (vv) {
                if (v == vv) {
                    $($select).attr("multiple", "multiple");
                    if (vv == 'rmanager') {
                        $('#select_role_office option:selected').removeAttr('selected');
                        $($select).attr('disabled', 'disabled');
                        $($select).hide();
                        $('[for=select_role_office]').hide();
                    }
                }
            });
            return v;
        }
    });
</script>
<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">

                        <?php if(in_array(person::$role,['client'])): ?>
                            <h4><?php echo __('Операции с ППС') ?></h4>
                            <hr>
                            <div class="form-row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="white-box">
                                        <h2 class="page-title" style="text-align: center;"><?php echo __('Создать ППС') ?></h2>
                                        <form class="form-horizontal form-material" action="<?php echo $dir; ?>/dashboard/create" method="post" id="create_office">
                                            <br/>
                                            <div class="form-groupold">
                                                <label for="add_money_login" class="col-md-12"><?php echo __('Валюта') ?></label>
                                                <div class="col-md-12">
                                                    <select name="currency">
                                                        <?php foreach($currencies as $currency): ?>
                                                            <option <?php if($currency->id == 3): ?> selected <?php endif; ?> value="<?php echo $currency->id ?>"><?php echo __($currency->name) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <br/>
                                            <div class="form-groupold">
                                                <label class="col-md-12"><?php echo __('Название') ?></label>
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control form-control-line" name="v_name" value="" >
                                                </div>
                                            </div>
                                            <?php if(person::$role == 'client'): ?>
                                                <div class="form-groupold">
                                                    <label class="col-md-12"><?php echo __('Тип API') ?></label>
                                                    <div class="col-md-12">
                                                        <select name="apitype">
                                                            <option value="0">Seamless</option>
                                                            <option selected value="1">Balance transfer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-groupold">
                                                    <label class="col-md-12"><?php echo __('Включить API') ?></label>
                                                    <div class="col-md-12">
                                                        <select name="apienable">
                                                            <option value="0"><?php echo __('No') ?></option>
                                                            <option selected value="1"><?php echo __('Yes') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-groupold">
                                                    <label class="col-md-12"><?php echo __('Work mode') ?></label>
                                                    <div class="col-md-12">
                                                        <select name="workmode">
                                                            <option selected value="0"><?php echo __('Default') ?></option>
                                                            <option value="1"><?php echo __('Terminal') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-groupold">
                                                    <label class="col-md-12"><?php echo __('URL взаимодействия') ?></label>
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control form-control-line" name="gameapiurl" >
                                                    </div>
                                                </div>
                                                <div class="form-groupold">
                                                    <label class="col-md-12"><?php echo __('Secret key (not required)') ?> </label>
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control form-control-line" name="secretkey" >
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="form-groupold">
                                                    <label class="col-md-12"><?php echo __('Сумма на счету') ?></label>
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control form-control-line" name="amount" value="<?php echo I18n::$lang == 'en' ? 10000 : 100000; ?>" >
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="form-groupold">
                                                <label class="col-md-12"><?php echo __('Время инкасации') ?></label>
                                                <div class="col-md-12">
                                                    <select name="encashment_time">
                                                        <?php for($i = 0; $i <= 23; $i++): ?>
                                                            <option <?php echo $i == 9 ? 'selected' : ''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-groupold">
                                                <label class="col-md-12"><?php echo __('Часовой пояс') ?></label>
                                                <div class="col-md-12">
                                                    <select style="width:100%" name="zone_time">
                                                        <?php foreach(tz::lst() as $i => $zname): ?>
                                                            <option <?php echo $i == 0 ? 'selected' : ''; ?> value="<?php echo $i; ?>">
                                                                <?php echo $zname; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-groupold">
                                                <label class="col-md-12"><?php echo __('Язык') ?></label>
                                                <div class="col-md-12">
                                                    <select style="width:100%" name="lang">
                                                        <option selected value="">
                                                            Auto
                                                        </option>
                                                        <?php foreach((array) Kohana::$config->load('languages.lang') as $i => $lang): ?>
                                                            <option value="<?php echo $i; ?>">
                                                                <?php echo $lang; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-groupold">
                                                <label class="col-md-12"><?php echo __('Default denomination') ?></label>
                                                <div class="col-md-12">
                                                    <select style="width:100%" name="default_dentab">
                                                        <?php foreach((array) Kohana::$config->load('agt.k_list') as $i => $k): ?>
                                                            <option value="<?php echo $i; ?>">
                                                                <?php echo $k; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <br/>
                                            <div class="form-groupold">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-success"><?php echo __('Создать') ?></button>
                                                </div>
                                            </div>
                                            <div id="text_create_office"></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>