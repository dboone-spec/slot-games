<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Акции (не клиенты)</h1>
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal" action="<?php echo $dir ?>/shareunreg/import" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="col-md-2">
                                <input type="file" name="file" id="file" />
                                <input type="submit" name="submit" />
                            </div>
                        </div>
                    </form>
                    <div class="">Ждет отправки: <?php echo $not_sent_count; ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped" style="text-align: center">
                        <tr>
                            <?php foreach($headers as $h): ?>
                                <td><?php echo $h; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach($u_unreg as $u): ?>
                            <tr>
                                <?php foreach($headers as $k => $h): ?>
                                    <td><?php echo $u[$k]??'-'; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>