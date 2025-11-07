<div style="font-size: 40pt; display: block; width: 100%;">
    <section class="agreement vekselstep active">
        <br/>
        <br/>
        <br/>
        <div class="agreement__text rectangle" style="text-align: center;">
            <?php echo __('Обратитесь к менеджеру зала'); ?><br>
            <b>ID: <?php echo arr::get($_GET,'terminal_id','?'); ?></b>
        </div>
        <br/>
        <br/>
        <br/>
        <button onclick="javascript:location.reload()" class="agreement__confirm game-button" style="font-size: 36pt; margin: 0 auto; display: block;">OK</button>
    </section>
</div>