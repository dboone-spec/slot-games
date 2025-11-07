<html>
<head>


    <style>
        table, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 7px;
            width: 100%;
        }

        td img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }

        body {
            max-width: 75mm;
            width: calc(100% - 2px);
        }

        /*@media print {*/
        /*    html, body {*/
        /*        width: 75mm;*/
        /*}*/

    </style>

</head>

<body>

<?php if (count($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <?php echo $error; ?>
    <?php endforeach; ?>
<?php else: ?>

    <table>
        <tr>
            <td>
                <table style="border: 0px solid white; ">
                    <tr style="border: 0px solid white;">
                        <td style="border: 0px solid white;">
                            <?php if(Person::user()->office_id==1049 || Person::user()->office_id==1148 || Person::user()->office_id==1457): ?>
                                <img src="/theme/admin1/logo/anybet.svg" >
                            <?php else: ?>
                                <img src="/theme/admin1/logo/Logo_light.png" style="width:100px" >
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <br>

                <table style="border: 0px solid white;">
                    <tr style="border: 0px solid white;">
                        <td style="border: 0px solid white;text-align: center;">Cash back tomorrow if you lose.</td>
                    </tr>
                </table>


                <table>
                    <tr style="border: 0px solid white;">
                        <td style="border: 0px solid white;"> Bet shop</td>
                        <td style="border: 0px solid white;"> <?php echo $user->office->visible_name ?> </td>
                    </tr>
                    <tr style="border: 0px solid white;">
                        <td style="border: 0px solid white;"> Cashier</td>
                        <td style="border: 0px solid white;"> <?php echo $fio ?>  </td>
                    </tr>
                    <tr style="border: 0px solid white;">
                        <td style="border: 0px solid white;"> User</td>
                        <td style="border: 0px solid white;"> <?php echo $login ?>  </td>
                    </tr>
                    <tr style="border: 0px solid white;">

                        <td style="border: 0px solid white;"> Password</td>
                        <td style="border: 0px solid white;"> <?php echo $password ?>  </td>

                    </tr>
                    <tr style="border: 0px solid white;">
                        <td style="border: 0px solid white;"> Coupon Date</td>
                        <td style="border: 0px solid white;">  <?php echo th::date($user->created) ?>  </td>
                    </tr>

                </table>

            </td>

            <td style="width: auto;padding:0;">
                <?php if ($user->office->tg_cashusers == 0) : ?>

                    <table style="border: 0px solid white;width: auto;padding:0;">
                        <tr style="border: 0px solid white;">
                            <td style="border: 0px solid white; text-align: center;width: auto;padding:2px;">
                                <span class="barcode"></span>
                            </td>
                            <style>
                                .barcode {
                                    background: url("/enter/cashusers/barcode/<?php echo $user->barcode ?>?rotated=1");
                                    width: 48px;
                                    -webkit-print-color-adjust: exact;
                                    background-size: 78px auto;
                                    display: block;
                                    background-repeat: no-repeat;
                                    height: 284px;
                                }
                            </style>
                        </tr>
                    </table>

                <?php endif; ?>
            </td>
        </tr>
    </table>
<?php if ($print): ?>
    <script>
        window.onload = function () {
            this.print();
        }
    </script>
<?php endif; ?>
<?php endif; ?>

</body>
</html>