<script src='/js/jquery.form.js'></script>
<script>
$(window).ready(function(){
    var optionsBot = {
        dataType: 'json',
        type: 'post',
        async: false,
        beforeSubmit: function() {
            $('#response_text_bot').empty();
        },
        success: function (data) {
            var color = 'green';

            if(data.error) {
                color = 'red';
            } else {

                var newBot = '<tr data-name="' + data.bot['user_name']  + '" class="bot">';

                for(item in data.bot) {
                    newBot += (item=='user_name') ? '<td>' + data.bot[item] + '</td>' : '<td><input name="'+item+'" type="text" value="' + data.bot[item] + '"></td>';
                }
                buttons='<td><button class="btn btn-success save_bot">Сохранить изменения</button></td><td><button class="btn btn-danger delete_bot">Удалить бота</button></td>';
                newBot += buttons;
                newBot += '</tr>';
                $('#bots').append(newBot);

            }

            var message_text = '<div style="text-align: center; color: ' + color + '">' + data.text + '</div>';

            $('#response_text_bot').append(message_text);
        },
        error: function () {
            $('#response_text_bot').append('<div style="text-align: center; color: red">Ошибка при создании бота. Попробуйте позже</div>');
        }
    };

    $('#bot_form').ajaxForm(optionsBot);

    $('table').on("click",".save_bot", function(){
        let parent_tr = $(this).closest('tr');
        let data = {
            'user_name': parent_tr.data('name'),
            'place': parent_tr.find('input[name="place"]').val(),
            'prize': parent_tr.find('input[name="prize"]').val(),
            'count_points': parent_tr.find('input[name="count_points"]').val()
        };

        console.log(data);

        $.ajax({
            url: '<?php echo $dir; ?>/shareprizes/updatebot/<?php echo $share->id ?>',
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSubmit: function() {
                $('#response_text_bot').empty();
            },
            success: function (data) {
                var message_text = '<div style="text-align: center; color: green">' + data.text + '</div>';

                $('#response_text_bot').append(message_text);
            },
            error: function(response) {
                var message_text = '<div style="text-align: center; color: red">Error</div>';

                $('#response_text_bot').append(message_text);
            }
        });
    });
    $('table').on("click",".delete_bot", function(){
        let parent_tr = $(this).closest('tr');
        let data = {
            'user_name': parent_tr.data('name'),
            'place': parent_tr.find('input[name="place"]').val(),
            'prize': parent_tr.find('input[name="prize"]').val(),
            'count_points': parent_tr.find('input[name="count_points"]').val()
        };

        console.log(data);

        $.ajax({
            url: '<?php echo $dir; ?>/shareprizes/deletebot/<?php echo $share->id ?>',
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSubmit: function() {
                $('#response_text_bot').empty();
            },
            success: function (data) {
                var message_text = '<div style="text-align: center; color: green">' + data.text + '</div>';
                $('#response_text_bot').append(message_text);
                $('[data-name='+data.bot['user_name']+']').remove();
            },
            error: function(response) {
                var message_text = '<div style="text-align: center; color: red">Error</div>';

                $('#response_text_bot').append(message_text);
            }
        });
    });
})
</script>
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Призы "<?php echo $share->name; ?>"(<?php echo $share->type; ?>)</h1>

            <?php if($share->calc==0): ?>
                <?php if($share->type=='tournament'): ?>
                    <a href="<?php echo $dir; ?>/shareprizes/item/<?php echo $share->id; ?>?calc_prizes=1" class="btn btn-primary" style="margin: 15px 0;">Рассчитать победителей?</a>
                <?php endif; ?>

                <a href="<?php echo $dir; ?>/shareprizes/item/<?php echo $share->id; ?>?notification=1" class="btn btn-primary" style="margin: 15px 0;">Отправить оповещения</a>
            <?php endif; ?>

            <div class="row" style="overflow-x: scroll;">
                <div class="col-sm-12">
                    <form method="post">
                        <table class="table table-striped" style="text-align: center;">
                            <tr>
                                <?php foreach ($headers_stat as $h): ?>
                                    <td><?php echo $h; ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($stat as $s): ?>
                                <tr>
                                    <?php foreach ($headers_stat as $k => $h): ?>
                                        <?php if(in_array($k,['place','prize']) AND !$share->calc): ?>
                                            <td>
                                                <input type="text" name="user[<?php echo $s['user_id'] ?>][<?php echo $k; ?>]" value="<?php echo $s[$k]??'' ?>" style="text-align: center"/>
                                            </td>
                                        <?php elseif($k=='loss_prize'): ?>
                                            <td>
                                                <input type="checkbox" name="user[<?php echo $s['user_id'] ?>][<?php echo $k; ?>]" value="1" <?php echo $s[$k]==1?'checked':''  ?> style="text-align: center"/>
                                            </td>
                                        <?php elseif(isset($s[$k])): ?>
                                            <td><?php echo $s[$k]; ?></td>
                                        <?php elseif($k=='bonus_code'): ?>
                                            <td>
                                                <select name="user[<?php echo $s['user_id'] ?>][code_id]">
                                                    <?php foreach ($bonus_codes as $code_id => $code_name): ?>
                                                        <option value="<?php echo $code_id ?>" <?php echo $code_id==$s['code_id']?'selected':''; ?>><?php echo $code_name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        <?php else: ?>
                                            <td> - </td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <?php if($share->calc==0): ?>
                            <button class="btn btn-success" type="submit" style="margin: 15px 0;">Сохранить</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <h2>Боты</h2>

            <form action="<?php echo $dir; ?>/shareprizes/addbot/<?php echo $share->id ?>" method="POST" class="form-horizontal" enctype="multipart/form-data" id="bot_form">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table admin-item">
                            <tbody>
                                <tr>
                                    <td>Место</td>
                                    <td><input name='place' /></td>
                                </tr>
                                <tr>
                                    <td>Приз</td>
                                    <td><input name='prize' /></td>
                                </tr>

                                <?php if($share->type=='tournament'): ?>
                                <tr>
                                    <td>Количество очков</td>
                                    <td><input name='count_points' /></td>
                                </tr>
                                <?php endif;?>

                            </tbody>
                        </table>
                        <div id="response_text_bot"></div>
                    </div>
                </div>
                <?php if($share->calc==0): ?>
                    <button class="btn btn-primary" type="submit" style="margin: 15px 0;">Добавить</button>
                <?php endif; ?>
            </form>

            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped" style="text-align: center;" id="bots">
                        <tr>
                            <?php foreach ($headers_bots as $h): ?>
                                <td><?php echo $h; ?></td>
                            <?php endforeach; ?>
                            <td></td>
                        </tr>
                        <?php foreach ($bots as $bot): ?>
                            <tr class="bot" data-name="<?php echo $bot['user_name'] ?>">
                                <?php foreach ($headers_bots as $k => $h): ?>
                                    <?php if($k=='user_name'): ?>
                                        <td><?php echo $bot[$k]; ?></td>
                                    <?php else: ?>
                                        <td><input name="<?php echo $k ?>" type="text" value="<?php echo $bot[$k]; ?>" /></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <td>
                                    <button class="btn btn-success save_bot"><?php echo __('Сохранить изменения') ?></button>
                                </td>
                                <td>
                                    <button class="btn btn-danger delete_bot"><?php echo __('Удалить бота') ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>