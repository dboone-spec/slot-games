<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Призы акции/турниры</h1>

            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped" style="text-align: center">
                        <tr>
                            <?php foreach ($headers as $h): ?>
                                <td><?php echo $h; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($shares as $s): ?>
                            <tr class="<?php echo time()>$s->time_to?'share_end':'share_process'; ?>" onclick="window.location = '<?php echo $dir; ?>/shareprizes/item/<?php echo $s->id; ?>';" style="cursor: pointer">
                                <?php foreach ($headers as $k => $h): ?>
                                    <?php if(isset($s->$k)): ?>
                                        <?php if(in_array($k,['time_to'])): ?>
                                            <td><?php echo date('Y-m-d',$s->$k); ?></td>
                                        <?php else: ?>
                                            <td><?php echo $s->$k; ?></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<style>
    .share_end td {
        color: red;
    }
    .share_process td {
        color: green;
    }
</style>