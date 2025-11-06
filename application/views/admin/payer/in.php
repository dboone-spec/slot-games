<style>
    .with_preloader {
        margin: 0 auto;
        min-height: 366px;
        background: #171f28;
        background-image: url(/assets/img/spinner.gif);
        background-repeat: no-repeat;
        background-position: center;
    }

</style>

<script>
    /*
     * закрытие страницы с платежной системой
     * после успешного выполнения платежа
     */
    window.payment_id_current;
    window.win_opened;

    if (!window.payment_interval_check) {
        window.payment_interval_check = setInterval(function () {
            if (!window.payment_id_current) {
                return;
            }

            $.ajax({
                url: '<?php echo $dir; ?>/payer/check/' + window.payment_id_current,
                type: 'post',
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.status == 30) {
                        typeof window.win_opened != 'undefined' && window.win_opened.close();
                        window.location = '<?php echo $dir; ?>';
                    }
                }
            });
        }, 3000);
    }
</script>

<script>
    function check_iframe() { 
        setTimeout(function () {
            if ($('#iFrameResizer0').length) {
                $('#iFrameResizer0').attr('scrolling', 'yes');
            }
        }, 1000);
    }

    $(window).on('load', function() {
        $('#select_paysys').change(function () {
            var currency_id = $('#select_paysys option:selected').val();
            var src = '<?php echo $dir; ?>/payer/iframe/' + currency_id;
            $('iframe').attr('src', src);
        });
    });
</script>
<select style="padding: 20px; width: 250px" id="select_paysys">
    <?php foreach (person::user()->balances->find_all() as $balance): ?>
        <option value="<?php echo $balance->currency_id ?>" <?php echo $balance->currency_id==1?'selected':'' ?>><?php echo $balance->currency->code ?></option>
    <?php endforeach; ?>
</select>
<iframe onload="check_iframe()" scrolling="yes" src="<?php echo $dir; ?>/payer/iframe/1" style="width: 100%; height: 800px; overflow-x: scroll;"></iframe>
