<style>
    html,body,iframe {margin:0;padding:0;border:0;}
</style>
<script>
    window.onmessage=function(event) {
        if (event.data=='closeGame' || event.data=='close') {
            window.parent.postMessage('closeGame','*');
        }
    }
</script>
<?php echo $content; ?>