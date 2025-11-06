<section class="pc-container">
    <div class="pcoded-content">
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">FS API constructor
							<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/Freespins/<?php echo I18n::$lang; ?>/<?php echo Request::current()->controller(); ?>.html">
                                &#x1F517;
                            </a>
						</h1>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-success" href="<?php echo $dir ?>/fsapi/item"><?php echo __('Создать') ?></a>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="table-responsive col-md-6">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Game
                                        </th>
                                        <th>
                                            Amount
                                        </th>
                                        <th>
                                            FS COUNT
                                        </th>
                                        <th>
                                            Active
                                        </th>
                                    </thead>
                                    <tbody>
                                        <?php foreach($sets as $set): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo $dir ?>/fsapi/item/<?php echo $set->id; ?>" style="display: block;" class="tabledata">
                                                <?php echo $set->name; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo $dir ?>/fsapi/item/<?php echo $set->id; ?>" style="display: block;" class="tabledata">
                                                <?php echo $gamelist[$set->game]??''; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo $dir ?>/fsapi/item/<?php echo $set->id; ?>" style="display: block;" class="tabledata">
                                                <?php echo $set->amount; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo $dir ?>/fsapi/item/<?php echo $set->id; ?>" style="display: block;" class="tabledata">
                                                <?php echo $set->fs_count; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo $dir ?>/fsapi/item/<?php echo $set->id; ?>" style="display: block;" class="tabledata">
                                                <?php echo ($set->active>0)?'yes':'no'; ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>