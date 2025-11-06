<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo __('Список пользователей') ?></h4>
        </div>
    </div>
    <div class="row">
        <?php if(in_array("rmanager",$roles)): ?>
                <div class="col-md-6 col-xs-6">
                    <div class="white-box">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Рег. менеджеры') ?></h2>
                        <div class="form-group">
                            <?php foreach($rmanagers as $office_id => $values): ?>
                                <?php echo __('ППС') . ' - ' . $office_id ?>
                                <ul>
                                    <?php foreach($values as $v): ?>
                                        <li><a href="/admin/persons/item/<?php echo $v ?>"><?php echo $v ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
        <?php endif; ?>
        <?php if(in_array("manager",$roles)): ?>
                <div class="col-md-<?php echo in_array("rmanager",$roles)?6:12; ?> col-xs-<?php echo in_array("rmanager",$roles)?6:12; ?>">
                    <div class="white-box">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Менеджеры') ?></h2>
                        <div class="form-group">
                            <?php foreach($managers as $office_id => $values): ?>
                                <?php echo __('ППС') . ' - ' . $office_id ?>
                                <ul>
                                    <?php foreach($values as $v): ?>
                                        <li><a href="/admin/persons/item/<?php echo $v ?>"><?php echo $v ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
        <?php endif; ?>
    </div>
    <?php if(in_array("administrator",$roles)): ?>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="white-box">
                    <h2 class="page-title" style="text-align: center;"><?php echo __('Администраторы') ?></h2>
                    <div class="form-group">
                        <?php foreach($admins as $office_id => $values): ?>
                            <?php echo __('ППС') . ' - ' . $office_id ?>
                            <ul>
                                <?php foreach($values as $v): ?>
                                    <li><a href="<?php echo $dir; ?>/persons/item/<?php echo $v ?>"><?php echo $v ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
    <?php endif; ?>
    <?php if(in_array("administrator",$roles)): ?>
            <div class="col-md-6 col-xs-12">
                <div class="white-box">
                    <h2 class="page-title" style="text-align: center;"><?php echo __('Кассиры') ?></h2>
                    <div class="form-group">
                        <div class="form-group">
                            <?php foreach($kassirs as $office_id => $values): ?>
                                <?php echo __('ППС') . ' - ' . $office_id ?>
                                <ul>
                                    <?php foreach($values as $v): ?>
                                        <li><a href="<?php echo $dir; ?>/persons/item/<?php echo $v ?>"><?php echo $v ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- /.container-fluid -->