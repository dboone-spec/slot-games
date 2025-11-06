<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>
            <div class="row">
                <div class="col-sm-12">
                    <form method="GET" class="form-horizontal">
                        <div class="form-group" style="display: flex;">
                            <?php foreach($search as $s): ?>
                                <label class="col-md-2"><?php echo isset($label[$s]) ? $label[$s] : $s ?>:</label>
                                <div class="col-md-4">
                                    <?php echo $vidgets[$s]->render($search_vars,'search') ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Поиск" />
                            <a class="btn btn-default" href="<?php echo $dir ?>/officegame">Очистить</a>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-danger" type="submit" name="flash_off" value="Выключить флеш" />
                            <input class="btn btn-success" type="submit" name="flash_on" value="Включить флеш" />
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped <?php echo $dir ?>" >
                        <tr>
                            <?php $q = Request::current()->query(); ?>
                            <?php foreach($list as $l): ?>
                                <td>
                                    <?php $sortas = $q['sortas'] ?? 'asc'; ?>
                                    <a <?php if(isset($q['sortby']) && $q['sortby'] == $l): ?>class="<?php echo $q['sortas']; ?>"<?php endif; ?> href="/<?php echo Request::current()->uri() . '?' . http_build_query(array_merge($q,['sortby' => $l,'sortas' => $sortas == 'asc' ? 'desc' : 'asc'])); ?> ">
                                        <?php echo isset($label[$l]) ? $label[$l] : $l ?>&nbsp;<?php if(isset($q['sortby']) && $q['sortby'] == $l): ?><?php echo $q['sortas'] == 'asc' ? '&dArr;' : '&uArr;'; ?><?php endif; ?>
                                    </a>
                                </td>
                            <?php endforeach ?>
                        </tr>
                        <?php foreach($data as $c): ?>
                            <tr>
                                <?php foreach($list as $l): ?>
                                    <td>
                                        <?php echo $vidgets[$l]->render($c,'list') ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?php echo $page ?>
                </div>
            </div>
            <?php if(Person::user()->can_edit($model)): ?>
            <div class="row">
                    <div class="col-sm-12">
                            <a class="btn btn-success" href="<?php echo $dir ?>/officegame/item"><?php echo __('Создать') ?></a><br>
                    </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
