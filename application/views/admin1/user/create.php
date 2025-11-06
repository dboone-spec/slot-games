<html>
<head>


    <style>
        table, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 7px;
            width: 100%;
        }
        td img{
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 95%;
        }
        body {
            min-width: 232px;
            width: 95%;
        }
        @media print {
            html, body {

        }

    </style>

</head>

<body>


<?php if(count($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <?php echo $error; ?>
    <?php endforeach; ?>
<?php else: ?>


    <table style="border: 0px solid white; ">
    <tr  style="border: 0px solid white;">
        <td  style="border: 0px solid white;">
            <?php if(Person::user()->office_id==1049): ?>
                <img src="/theme/admin1/logo/anybet.svg" >
            <?php else: ?>
                <img src="/theme/admin1/logo/Logo_light.png" style="width:100px" >
            <?php endif; ?>
        </td>
    </tr>
    </table>
    <br>
    <table style="border: 0px solid white;">
        <tr  style="border: 0px solid white;">
            <td  style="border: 0px solid white;text-align: center;">Cash back tomorrow if you lose.</td>
        </tr>
    </table>


    <table>
        <tr style="border: 0px solid white;">
            <td style="border: 0px solid white;"> Bet shop </td>
            <td style="border: 0px solid white;"> <?php echo $user->office->visible_name ?> </td>
        </tr>
        <tr style="border: 0px solid white;">
            <td style="border: 0px solid white;"> Cashier</td>
            <td style="border: 0px solid white;"> <?php echo $fio ?>  </td>
        </tr>
        <tr  style="border: 0px solid white;">
            <td  style="border: 0px solid white;"> User </td>
            <td  style="border: 0px solid white;"> <?php echo $login ?>  </td>
        </tr>
        <tr  style="border: 0px solid white;">

            <td  style="border: 0px solid white;"> Password </td>
            <td  style="border: 0px solid white;"> <?php echo $password ?>  </td>

        </tr>
        <tr  style="border: 0px solid white;">
            <td  style="border: 0px solid white;">  Coupon Date  </td>
            <td  style="border: 0px solid white;">  <?php  echo th::date($user->created,'d.m.y H:i:s',$user->office->zone_time) ?>  </td>
        </tr>

    </table>


    <br>
    <?php if($user->office->tg_cashusers==0 ) : ?>

        <table style="border: 0px solid white; ">
            <tr  style="border: 0px solid white;">
                <td  style="border: 0px solid white; text-align: center;">
                    <img src="/enter/cashusers/barcode/<?php echo $user->barcode ?>" >
                </td>
            </tr>
        </table>

    <?php endif; ?>



    <?php if($print): ?>
        <script>
            window.onload = function () {
                this.print();
            }
        </script>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>