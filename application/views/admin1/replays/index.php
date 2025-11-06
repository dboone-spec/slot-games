


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Replays - <?php echo $game ?></h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">User id</span>
                                        </div>
                                        <input class="form-control form-control-sm" name="userId" value="<?php echo $userId ?>" >
                                    </div>
                                </div>

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Start bet Id</span>
                                        </div>
                                        <input class="form-control form-control-sm" name="startId" value="<?php echo $startId ?>">
                                    </div>
                                </div>

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Amount</span>
                                        </div>
                                        <input class="form-control form-control-sm" name="limit" value="<?php echo $limit ?>">
                                    </div>
                                </div>




                                <div class="non-form-control">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/report"><?php echo __('Очистить') ?></a>
                                </div>
                            </div>
                        </form>



                    </div>




                    <table class="table table-striped table-bordered nowrap dataTable supertable-hover">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>User Id </th>
                            <th>Office id</th>
                            <th>Cur</th>
                            <th>info</th>
                            <th>type</th>
                            <th>Bet amount</th>
                            <th>Win</th>
                            <th>Balance before</th>
                            <th>Balance after </th>
                            <th>Lines</th>
                            <th>Game</th>
                            <th>Time</th>
                            <th>Result</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($bets as $bet): ?>
                        <tr>
                            <td>
                                <?php echo $bet->id ?>
                            </td>
                            <td>
                                <?php echo $bet->user_id ?>
                            </td>
                            <td>
                                <?php echo $bet->office_id ?>
                            </td>
                            <td>
                                Currency
                            </td>

                            <td>
                                <?php echo $bet->info ?>
                            </td>
                            <td>
                                <?php echo $bet->type ?>
                            </td>
                            <td>
                                <?php echo $bet->amount ?>
                            </td>
                            <td>
                                <?php echo $bet->win ?>
                            </td>
                            <td>
                                <?php echo $bet->balance-$bet->win+$bet->amount ?>
                            </td>
                            <td>
                                <?php echo $bet->balance ?>
                            </td>
                            <td>
                                <?php echo $bet->come ?>
                            </td>
                            <td>
                                <?php echo $bet->game ?>
                            </td>
                            <td>
                                <?php echo th::date($bet->created) ?>
                            </td>
                            <td>
                                <?php  echo $vidget->render($bet, 'list') ?>
                            </td>
                              </tr>
                        <?php endforeach; ?>


                        </tbody>
                    </table>


<textarea style="height: 500px">
        $video=true;
        if ($video){
            $num=file_exists('<?php echo $guid ?>.posvideo') ? file_get_contents('<?php echo $guid ?>.posvideo') :0;
            $num++;
            <?php $i=1; foreach($poss as $pos): ?>

            if ($num==<?php echo $i ?>) {
                <?php foreach ($pos as $numBar=>$posBar) {
                  echo  '        $this->pos['.$numBar.'] = '.$posBar.';';
                } ?>

                    }
            <?php $i++; endforeach; ?>

            if ($num>=<?php echo $i ?>) {
                exit;
            }
            file_put_contents('<?php echo $guid ?>.posvideo',$num);
            }

</textarea>





                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>








