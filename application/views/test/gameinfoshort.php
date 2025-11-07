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
<?php $i=1; foreach ($config as $game=>$c) :?>
        <h2>
    <?php echo $i.') '.$games[$game]['visible_name'] ?>
        </h2>





    <div style="clear:both"></div>

    <br>
<?php $i++; endforeach;?>

<h1>
Clones:
</h1>
<?php $i=1; foreach($clones as $game=>$c):?>
    <h2>
    <?php echo $i.') '.$games[$game]['visible_name'] ?> clone of <?php echo $games[$c['cloneOf']]['visible_name'] ?><br><br>
    </h2>
   


<div style="clear:both">
<br>
<?php $i++; endforeach; ?>
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