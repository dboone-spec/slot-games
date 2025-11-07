<script>
    function banlisten() {
        $.ajax({
            url: '/o/<?php echo auth::user()->office_id; ?>.json',
            success: function(d) {
                if(d=='1') {
                    <?php if(auth::user()->parent_acc()->chrome_ext_id): ?>
                          
                    <?php endif; ?>
                    window.location.reload();
                }
                setTimeout(banlisten,2000);
            },
            error: function() {
                setTimeout(banlisten,2000);
            }
        })
    }
    
    $(document).ready(function() {
        banlisten();
    });
</script>