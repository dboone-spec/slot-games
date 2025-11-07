<script>
    <?php foreach ($flash as $message): ?>
        $(window).load(function() {
            $('#popup_static').show();
            $('#popup_static').load('<?php echo $message->text; ?>');
            $('.popup_hide').hide();
        });
    <?php endforeach; ?>
</script>