


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h4><?php echo __($mark) ?>
						<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/<?php echo $bigcurrent.'/'.I18n::$lang; ?>/<?php echo Request::current()->controller(); ?>.html">
                            &#x1F517;
                        </a>
						</h4>
                        <hr>
                        <form method="POST" class="form-horizontal">

                                <div class="form-row">
                                    <?php foreach($search as $s): ?>
                                    <div class="form-group col-md-<?php echo in_array($s,['id','user_id',''])?'1':'2'; ?>">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><?php echo $label[$s] ?? $s ?></span>
                                            </div>
                                            <?php echo $vidgets[$s]->render($search_vars,'search') ?>
<!--                                            <input type="text" class="form-control" aria-label="<?php echo $label[$s] ?? $s ?>" aria-describedby="inputGroup-sizing-sm">-->
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <div class="w-100"></div>

                                    <div class="non-form-control ml-auto">
                                        <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                    </div>
                                    <div>
                                        <a class="btn btn-sm btn-round btn-outline-secondary" href="<?php echo $dir ?>/<?php echo $model ?>"><?php echo __('Очистить') ?></a>
                                    </div>
                                </div>
                        </form>


			<?php if($canCreate): ?>

					<a class="btn btn-success" href="<?php echo $dir ?>/<?php echo $model ?>/item"><?php echo __('Создать') ?></a><br>

                        <?php endif; ?>
                </div>


                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered nowrap dataTable supertable-hover">
                                <thead>
                                    <tr>
                                        <?php foreach($list as $l): ?>
                                            <?php if (!in_array($l,$notSortable)): ?>
                                                <?php $sortas = $currentUrl['sortas']??'asc'; ?>

                                                        <th class="<?php echo ( ($currentUrl['sortby']??'created' )==$l) ? 'sorting_'.$sortas :'sorting'?> superSort<?php echo ( ($currentUrl['sortby']??'created' )==$l) ? $sortas :''?>">
                                                            <a href="/<?php echo Request::current()->uri().'?'.http_build_query(array_merge($currentUrl,['sortby'=>$l,'sortas'=>$sortas=='asc'?'desc':'asc'])); ?> ">
                                                                <?php echo $label[$l] ?? $l ?>
                                                            </a>

                                                        </th>

                                            <?php else: ?>
                                                <th >
                                                    <?php echo $label[$l] ?? $l ?>
                                                </th>
                                            <?php endif ?>

                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data as $c): ?>
                                        <tr>
                                            <?php foreach($list as $l): ?>
                                                <td>
                                                    <?php if($canItem):?>

                                                        <a style="display: block;" class="tabledata" href="<?php echo $dir ?>/<?php echo $model . '/item/' . $c->id ?>">
                                                            <?php echo $vidgets[$l]->render($c, 'list') ?>&nbsp;
                                                        </a>
                                                    <?php else: ?>
                                                        <?php echo $vidgets[$l]->render($c, 'list') ?>
                                                    <?php endif ?>
                                                </td>
                                            <?php endforeach ?>


                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>



                            <?php echo $page ?>

                        </div>
                    </div>


                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>




<script>
    $( document ).ready(function() {
        $('input').addClass('form-control');
        $('input').addClass('form-control-sm');
        $('select').addClass('form-control');
        $('select').addClass('form-control-sm');
        $('.non-form-control input').removeClass('form-control');
        $('.non-form-control input').removeClass('form-control-sm');
        $('.non-form-control').removeClass('form-control');
        $('.non-form-control').removeClass('form-control-sm');


    });


</script>

<style>
    .dataTable a {
        color: #000;
    }
    .dataTable td{
        vertical-align: middle;
    }
</style>