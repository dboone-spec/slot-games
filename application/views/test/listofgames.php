<style>
    table {
        width: 70%;
        border-collapse: collapse;
    }
    td, th {
        #border: 1px solid #98bf21;
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



<?php foreach ($config as $game=>$c) :?>
<table style="border:none">
    <tr>
        <td rowspan="2" style="width:300px">
            &nbsp;
        </td>
        
        <td >
            <h2>
                <?php echo $games[$game]['visible_name'] ?>
            </h2>
        </td>
        <td rowspan="2">
            
            <?php foreach ($c['images'] as $image):   ?>
                <img style="width:580px" src="/screen/<?php echo $game ?>/<?php echo $image?>" /><br><br>
            <?php endforeach ?>
            
        </td>
    </tr>
    
    <tr>
        <td style="vertical-align: top;">
            <image src="/games/agt/sqthumb/<?php echo $game ?>.png" />
            <br><br><br>
            <?php if (isset($c['barsCount'])): ?>   Reels: <?php echo $c['barsCount'] ?> <br>     <?php endif; ?>
            <?php if (isset($c['heigth'])): ?> Visible elemets on the reel: <?php echo $c['heigth'] ?> <br> <?php endif; ?>
            <?php if (isset($c['linesCh'])): ?> Paylines: <?php echo $c['linesCh'] ?> <br>   <?php endif; ?> 
            RTP: <?php echo $c['rtp'] ?><br>
            <?php if (isset($c['pay'])): ?> Count of differrent symbols: <?php echo count($c['pay']) ?> <br> <?php endif; ?>
            Max win: <?php echo $c['maxWin'] ?>X<br>
            Freegame: <?php echo $c['FG'] ?><br>
            Gamble: Yes<br>
            Jackpot: Yes<br>
            Available FSback: <?php echo $c['FSback']?><br>
            <?php if (isset($c['info']) && (count($c['info'])>0 ) ): ?> Features: <?php echo implode(', ',$c['info']) ?> <br><?php endif; ?>
            Languages: English, German, French, Turkish, Russian
            
        </td>
        
    </tr>

</table>
    <div style="page-break-after: always " > </div>
    
    
<?php endforeach;?>


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