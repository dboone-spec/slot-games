<script src="/theme/admin1/js/plugins/apexcharts.min.js"></script>
<?php echo '<pre>'; var_dump($data); exit; ?>
<script>
    var labels = [];
    var series = [];
    <?php foreach($data as $kb=>$bdate): ?>
        labels.push("<?php echo $kb; ?>");
    <?php endforeach; ?>
    <?php foreach($betstats['data'] as $bsk=>$bsv): ?>
        series.push({
            name: '<?php echo $bsk; ?>',
            type: 'line',
            data: [<?php echo implode(',',$bsv); ?>]
        });
    <?php endforeach; ?>
</script>