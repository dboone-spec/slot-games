<script>

    $(document).ready(function () {
        $('.brand').change(function () {

            if ($(this).is(':checked')) {
                $(this).parent().find('input').each(function () {
                    $(this).prop('checked', true);
                });

            } else {
                $(this).parent().find('input').each(function () {
                    $(this).prop('checked', false);
                });
            }

        });

    });

</script>


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ form-element ] start -->


            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">

                        <?php if ($office_id > 0): ?>
                            <h5 class="mt-5">Enabled games for
                                office <?php echo Person::user()->officesName($office_id, true); ?></h5>
                            <hr>
                        <?php endif; ?>

                        <?php if ($game_id > 0): ?>
                            <h5 class="mt-5"><?php echo $fullgameslist[$game_id]; ?> enabled at offices:</h5>
                            <hr>
                        <?php endif; ?>

                        <form class="form-inline">
                            <?php if ($game_id == -1): ?>
                                <div class="form-group mb-2">
                                    <label for="staticEmail2">Choose office</label>

                                </div>
                                <div class="form-group mx-sm-3 mb-2">

                                    <?php echo form::select('office_id', $officesList, $office_id, ['class' => 'select2']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($office_id == -1): ?>
                                <div class="form-group mb-2">
                                    <label for="staticEmail3">Or game</label>

                                </div>

                                <div class="form-group mx-sm-3 mb-2">
                                    <?php echo form::select('game_id', [-1 => 'Select game'] + $fullgameslist, $game_id) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group mx-sm-3 mb-2">
                                <button type="submit" class="btn mx-sm-3 btn-primary mb-2">Select</button>
                                <?php if ($game_id > 0 || $office_id > 0): ?>
                                    <a href="" class="btn mx-sm-3 btn-secondary mb-2" style="color: #fff;">Reload</a>
                                    <a class="btn mx-sm-3 btn-danger mb-2" href="/enter/games" style="color: #fff;">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <form method="POST">

                                    <?php if ($office_id > 0): ?>
                                        <?php foreach ($brands as $brand => $name) : ?>

                                            <?php foreach ($allgames as $game) : ?>

                                                <?php if ($game['brand'] == $brand): ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-10">
                                                            <label
                                                                for="<?php echo $game['id'] ?>"> <?php echo $game['visible_name'] ?> </label>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-check">
                                                                <input type="checkbox" name="g[]"
                                                                       value="<?php echo $game['id'] ?>"
                                                                    <?php if (isset($games[$game['id']]) && $games[$game['id']]['enable'] == 1): ?>
                                                                        checked="checked"
                                                                        default_checked="1"
                                                                    <?php endif; ?>

                                                                       id="<?php echo $game['id'] ?>"/>
                                                            </div>
                                                        </div>

                                                    </div>

                                                <?php endif; ?>

                                            <?php endforeach; ?>

                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if ($game_id > 0): ?>
                                        <?php foreach ($officesList as $o_id => $o_name): ?>
                                            <?php if ($o_id <= 0) {
                                                continue;
                                            } ?>
                                            <div class="form-group row">
                                                <div class="col-sm-10">
                                                    <label for="<?php echo $o_id ?>"> <?php echo $o_name ?> </label>
                                                </div>
                                                <div class="col-sm-1">
                                                    <div class="form-check">
                                                        <input
                                                            <?php if (isset($owner_offices[$o_id])): ?>data-owner="<?php echo $owner_offices[$o_id]; ?>" <?php endif; ?>
                                                            type="checkbox" name="g[]" value="<?php echo $o_id ?>"
                                                            <?php if (isset($games[$o_id]) && $games[$o_id]['enable'] == 1): ?>
                                                                checked="checked"
                                                                default_checked="1"
                                                            <?php endif; ?>

                                                            id="<?php echo $o_id ?>"/>
                                                    </div>
                                                </div>

                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if ($office_id > 0): ?>
                                        <input class="btn btn-primary" type="submit"
                                               value="Save for <?php echo Person::user()->officesName($office_id, true); ?> "/>
                                    <?php elseif ($game_id > 0): ?>
                                        <input class="btn btn-primary" type="submit"
                                               value="Save for <?php echo $fullgameslist[$game_id] ?> for all offices"/>
                                    <?php else: ?>
                                    <?php endif; ?>
                                </form>
                            </div>
                            <?php if ($game_id > 0 || $office_id > 0): ?>
                            <div class="col-md-6">
                                <div class="row">
                                    <button class="btn btn-dark check_office_list" need_check="1">Check all</button>
                                    <button class="btn btn-warning check_office_list">Uncheck all</button>
                                </div>
                                <?php if (Person::$role == 'sa' && $game_id > 0): ?>
                                    <?php foreach ($owners_list as $ow_id => $owner): ?>
                                        <div class="row mt-1">
                                            <button class="btn btn-dark check_office_list" need_check="1"
                                                    owner="<?php echo $ow_id; ?>">Check all
                                                of <?php echo $owner; ?></button>
                                            <button class="btn btn-warning check_office_list"
                                                    owner="<?php echo $ow_id; ?>">Uncheck all
                                                of <?php echo $owner; ?></button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>


                    </div>
                </div>
            </div>

            <!-- [ form-element ] end -->
        </div>
        <!-- [ Main Content ] end -->

    </div>
</section>
<style>
    .state_changed {
        box-shadow: 1px 1px 10px red;
    }
    .form-group.row:nth-child(odd) {
        background: rgba(150,150,150,0.1);
    }
    .form-group.row {
        padding-top: 0.6rem;
    }
    .form-group {
        margin:0;
    }
</style>
<script>
    $(document).ready(function () {
        $('input[type=text]').addClass('form-control');

        $('select').addClass('form-control');

        $('.non-form-control input').removeClass('form-control');

        $('input:checkbox').change(function () {
            $(this).removeClass('state_changed');

            let default_checked = !!$(this).attr('default_checked');
            if (this.checked != default_checked) {
                $(this).addClass('state_changed');
            }
        });

        $('.check_office_list').click(function () {
            let owner_id = $(this).attr('owner');
            let need_check = !!$(this).attr('need_check');

            if (!owner_id) {
                $('input:checkbox').prop('checked', need_check).change();
                return;
            }

            $('[data-owner=' + owner_id + ']').prop('checked', need_check).change();
        });

    });


</script>


