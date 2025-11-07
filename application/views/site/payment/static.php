<script>

    $(document).ready(function () {

        var bonus = 0.35;

        function sumbonus() {
            var sum;
            sum = $('#amount-result').val();
            sum *= bonus;
            sum = Math.round(sum * 100, 2) / 100;
            if (sum > 0) {
                $('#labelbonus').show();
                $('#sumbonus').html(sum);
            } else {
                $('#labelbonus').hide();
            }

        }


        $('.bonuscode').hide();

        $('#havebonus50').change(function () {
            if ($(this).prop('checked')) {
                $('#havecode').prop('checked', false);
                $('.bonuscode').hide();
                bonus = 0.35;
                $('#bonustext').html('');
                $('#bonustext').hide();
            } else {
                if (!$('#havecode').prop('checked')) {
                    bonus = 0;
                }
            }
            sumbonus();

        })

        $('#havecode').change(function () {
            if ($(this).prop('checked')) {
                $('#havebonus50').prop('checked', false);
                $('.bonuscode').show();
                bonus = 0;
            } else {
                $('.bonuscode').hide();
                if (!$('#havebonus50').prop('checked')) {
                    bonus = 0;
                }
            }
            sumbonus();

        })


        $('#checkbonus').click(function () {

            $.ajax({
                url: '/payment/bonus/' + $('#bonuscode').val(),
                type: 'POST',
                data: {
                    amount: $('#amount-result').val(),
                },
                success: function (data) {
                    if(data.error == 0 && data.type == 'bezdep') {
                        $('#bonustext').html('Вам на счёт начислен бонус в размере ' + data.bonus + 'руб.');
                        $('#user_balance').text(data.balance);
                    } else if (data.error==0){
                        bonus = data.bonus;
                        var p;
                        p = Math.round(bonus * 100);
                        $('#bonustext').html('Бонус код дает бонус в ' + p + '% от суммы пополнения');
                    } else {
                        if(data.text) {
                            $('#bonustext').html(data.text);
                        } else {
                            $('#bonustext').html('Бонус код неактуальный или уже использован');
                        }
                        bonus = 0;
                    }
                    sumbonus();
                },
                dataType: 'json'
            });


        })

        $('#amount-result').keyup(function () {
            sumbonus();
        });

        sumbonus();

        $('.paysys_select:first').addClass('sys_active');
        $('#paysyscurrent').val($('.paysys:first').attr('href'));

        $('.paysys').click(function () {
            $('.paysys_select').removeClass('sys_active');
            $(this.hash + '_payselect').addClass('sys_active');
            $('#paysyscurrent').val(this.hash);
            return false;
        })

    });

    var options = {
        dataType: 'json',
        type: 'post',
        async: false,
        success: function (data) {
            window.open(data.link, '_blank')
        },
    };
    $('#payment_form').ajaxForm(options);
</script>
<form id="payment_form" method="POST" action="/payment/go">
    <div class="row">
<?php foreach ($systems as $sys): ?>
            <div class="item paysys_select" id="<?php echo "$sys->id" ?>_payselect" style="float:left">
                <a href="#<?php echo "$sys->id" ?>" class="paysys">
                    <img alt="0" src="/games/payment/<?php echo "$sys->image" ?>" class="mCS_img_loaded">
                </a>
            </div>
<?php endforeach; ?>
        <input type="hidden" id="paysyscurrent" name="paysys_current" value="" class="input_amount"/>
        <div style="clear: both"> </div>
    </div>
    <div class="row">
        <div style="padding-top:5px; color:#fff;" class="form-field amount-money">
            <label for="amount-result">Пополнить счет на</label>
            <input type="text" value="500" id="amount-result" class="input_amount" name="amount"> <?php echo auth::user()->currency(); ?>
        </div>
    </div>
    <div class="row">

    </div>
    <div class="row">

        <div style="padding-top:5px;" class="form-field amount-money chbx-wrap">
            <input type="checkbox" id="havecode" class="input_amount" name="havecode">
            <label for="havecode">У меня есть бонус код </label>&nbsp;
            <input type="text" value="" id="bonuscode" class="input_amount bonuscode" name="bonuscode" style="width: 150px; height: 25px">
            <button type="button" id="checkbonus" class="pay_bonus bonuscode" >Применить код</button><br>
            <span id="bonustext" class="go180 bonuscode" ></span>
        </div>
    </div>
    <button type="submit" class="invalid pay_submit" >Оплатить</button>
</form>