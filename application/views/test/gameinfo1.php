<style>
    table {
        width: 70%;
        border-collapse: collapse;
    }
    td, th {
        border: 1px solid #98bf21;
        padding: 3px 7px 2px 7px;
    }
    th {
        text-align: left;
        padding: 5px;
        background-color: #A7C942;
        color: #fff;
    }
    .alt td { background-color: #EAF2D3; }

    @media print {
        html, body {
            width: 210mm;
            height: 297mm;
        }
        .pagebreak {
            break-before: page;
        }
    }
</style>

<h1>
Games:
</h1>
<?php foreach ($config as $game=>$c) :?>
    <h2>
<?php echo $games[$game]['visible_name'] ?>
    </h2>
<br>
Reels: <?php echo $c['barsCount'] ?> <br>
Visible elemets on the reel: <?php echo $c['heigth'] ?> <br>
Paylines: <?php echo $c['linesCount'] ?> <br>
Count of differrent symbols: <?php echo count($c['pay']) ?> <br>
Freegame:<?php echo $c['FG'] ?><br>
Paytable: <br>
<table style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <td style="text-align: right">Сombinations of kind</td>
        <?php foreach($c['pay'][0] as $cp=>$val): ?>
            <td rowspan="2"> <?php echo $cp ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td>Symbol's number</td>
    </tr>

    <?php foreach($c['pay'] as $num=>$pay): ?>
        <tr>
            <td> <?php echo $num.' '.$c['mark'][$num] ?>

            </td>
            <?php foreach($pay as $val): ?>
                <td> <?php echo $val ?></td>
            <?php endforeach; ?>


        </tr>
    <?php endforeach; ?>

</table>
All pays are for combinations of kind. All pays are left to right on adjacent reels, on selected lines, beginning with the leftmost reel, except scatters.
Scatter wins are added to the payline wins. Highest payline and/or scatter wins only paid. Line wins are multiplies by the bet value on the winnigs.
Scatter wins are multiplied by the total bet value. Wild symbol substitutes for all symbol except scatters and special symbols mark as 'wild except'.
If the wild symbol has mark 'substitutes for all on the same reel', it substitutes for all symbols on the same reel.

<br>
Paylines configurations:<br>
<?php foreach($c['lines'] as $num=>$lineQ): ?>
<div class="pagebreak lineblock" style="float: left; width: <?php echo count($lineQ)*40; ?>px;margin: 4px;">
    Line <?php echo $num?>:
    <table>
        <?php foreach($lineQ as $line):?>
        <tr>
            <?php foreach($line as $el):?>
                <td <?php if($el==1) : ?> style="background-color:green;" <?php endif; ?>> &nbsp; </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endforeach; ?>
<div style="clear:both"></div>

<br><br>
<?php endforeach;?>

<h1>
Clones:
</h1>
<?php foreach($clones as $game=>$c):?>
    <h2>
    <?php echo $games[$game]['visible_name'] ?><br>
    </h2>
    Paylines: <?php echo $c['linesCount'] ?> <br>
    Clone of <?php echo $games[$c['cloneOf']]['visible_name'] ?><br>
    Paylines configurations:<br>
<?php foreach($c['lines'] as $num=>$lineQ): ?>
<div style="float:left;margin: 4px;" class="pagebreak">
    Line <?php echo $num?>:
    <table class="pagebreak" >
        <?php foreach($lineQ as $line):?>
        <tr>
            <?php foreach($line as $el):?>
                <td <?php if($el==1) : ?> style="background-color:green;" <?php endif; ?>> &nbsp; </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endforeach; ?>
<div style="clear:both">
<br><br>
<?php endforeach; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://files.codepedia.info/files/uploads/iScripts/html2canvas.js"></script>
<script>
    $(document).ready(function() {

        $('.lineblock').each(function() {
            var l = $(this);
            html2canvas(l, {
                onrendered: function(canvas) {
                    var img = $('<img class="imglines pagebreak">');
                    img.attr('href',canvas.toDataURL("image/png"));
                    l.replace(img);
                }
            });
        });

//        // Global variable
//        var element = $("#html-content-holder");
//
//        // Global variable
//        var getCanvas;
//
//        $("#btn-Preview-Image").on('click', function() {
//            html2canvas(element, {
//                onrendered: function(canvas) {
//                    $("#previewImage").append(canvas);
//                    getCanvas = canvas;
//                }
//            });
//        });
//
//        $("#btn-Convert-Html2Image").on('click', function() {
//            var imgageData =
//                getCanvas.toDataURL("image/png");
//
//            var newData = imgageData.replace(
//            /^data:image\/png/, "data:application/octet-stream");
//
//            $("#btn-Convert-Html2Image").attr(
//            "download", "GeeksForGeeks.png").attr(
//            "href", newData);
//        });
    });
</script>