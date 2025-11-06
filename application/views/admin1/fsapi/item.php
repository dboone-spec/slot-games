<section class="pc-container">
    <div class="pcoded-content">
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">FS API constructor</h1>
                        <hr>
                        <?php if(!empty($errors)): ?>
                        <?php foreach($errors as $err): ?>
                        <div style="color: red;" class="danger">
                            <?php echo $err; ?>
                        </div>
                        <?php endforeach; ?>
                        <hr>
                        <?php else: ?>
                            <?php if(arr::get($_GET,'s') && !arr::get($post,'process_btn')): ?>
                            <div style="color: green;" class="success">
                                Set was created
                            </div>
                            <?php elseif(arr::get($post,'submit_btn')): ?>
                            <div style="color: green;" class="success">
                                Set was saved
                            </div>
                            <?php elseif(arr::get($post,'process_btn') && $set->active): ?>
                            <div style="color: green;" class="success">
                                Set was processed
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="row">
                            <form id="mainform" class="col-md-12" method="POST" onsubmit="return checkConfirm();">
                                <div>
                                    Name of set (Max length 30): <input required name="name" value="<?php echo $set->name; ?>"/>
                                </div>
                                <div>
                                    Visible name of set (Max length 30): <input name="visible_name" value="<?php echo $set->visible_name; ?>"/>
                                    <i>it would be visible at player's interface</i>
                                </div>
                                <div>
                                    Game: <?php echo form::select('game',$gamelist,$set->game,['required'=>'required']); ?>
                                </div>
                                <div>
                                    Count: <input name="fs_count" type="number" value="<?php echo $set->fs_count; ?>" min="1" step="1" required/>
                                </div>
                                <div>
                                    Full amount: <input name="amount" type="number" value="<?php echo $set->amount; ?>"  min="0.1" step="any" required/>
                                </div>
                                <div>
                                    Massive send: <input name="mass" type="checkbox" value="1" <?php echo $set->mass?'checked':''; ?>/>
                                </div>
                                <div <?php echo !$set->active?'style="color: red;"':''; ?>>
                                    Active: <input name="active" type="checkbox" value="1" <?php echo $set->active?'checked':''; ?>/>
                                </div>
                                <div id="paramsbuilder">
                                    <?php echo $form; ?>
                                </div>
                                <hr>
                                <button type="submit" name="submit_btn" value="1" class="btn btn-success">Save</button>
                                <?php if(!$is_new): ?>
                                <hr>
                                <div>
                                    Office: <?php echo form::select('office_id',$officesList,$post['office_id']??'0')?>
                                </div>
                                <div>
                                    Partner User Id (not required): <input name="login" value="<?php echo $post['login']??''; ?>"/>
                                </div>
                                <div>
                                    Expiration time (30 days default): <input name="expirtime" value="<?php echo $post['expirtime']??60*60*24*30; ?>"/>
                                </div>
                                <button onclick="isNeedProcess=true" type="submit" name="process_btn" value="1" class="btn btn-primary">Process</button>
                                <?php endif; ?>
                                <?php if(!empty($autocorrect)): ?>
                                <button type="button" name="autocorrect_btn" value="1" class="btn btn-warning">Correct</button>
                                <script>
                                    var autocorrect_fs=JSON.parse('<?php echo json_encode($autocorrect); ?>');
                                    $('[name=autocorrect_btn]').click(function() {
                                        $('[name=fs_count]').val(autocorrect_fs.cnt);
                                        $('[name=amount]').val(autocorrect_fs.zzz*autocorrect_fs.cnt);
                                        $('[name=game]').val(autocorrect_fs.game);
                                    });
                                </script>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="stacktable">
                                    <tbody>
                                    <?php foreach($last10FSstack as $lastrow): ?>
                                        <tr started="<?php echo $lastrow->status==0?$lastrow->time_to_start:0; ?>" now="<?php echo time(); ?>">
                                            <td><?php echo th::date($lastrow->created); ?></td>
                                            <td><?php echo $lastrow->visible_name; ?></td>
                                            <td><?php echo $lastrow->office_id; ?></td>
                                            <td><?php echo $lastrow->game; ?></td>
                                            <td>
                                                <?php if($lastrow->status==0 && $lastrow->time_to_start>time()): ?>
                                                <span class="countdown"></span>
                                                <a href="/enter/fsapi/cancel/<?php echo $lastrow->id; ?>" class="btn btn-warning"">
                                                    Cancel
                                                </a>
                                                <?php else: ?>
                                                    <?php echo $lastrow->getStatusText(); ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr />
                    <a href="/enter/fsapi" class="btn btn-secondary">Go to list</a>
                    </div>
                </div>
            </div>
        </div>
</section>
<style>
    #mainform > div, #paramsbuilder > div {
        margin: 1em auto;
    }
    .countdown {
        display: flex;
        font-size: 8pt;
    }
</style>
<script src="/theme/admin1/js/plugins/jquery.durationpicker.js"></script>
<script>
    window.onload=function() {
        $('[name=expirtime]').durationpicker({allowZeroTime: true});

        var isNeedProcess=false;

        function countDown() {
            let needRunAgain=false;
            $('#stacktable tr').each(function(i,row) {

                let cdText=$(row).find('.countdown');
                cdText.text('');

                let started=parseInt($(row).attr('started'));

                if(started>0 && (started*1000)>Date.now()) {
                    needRunAgain=true;

                    cdText.text(Math.floor(started-Date.now()/1000)+' seconds to cancel')
                }
                else if(started>0 && (started*1000)<=Date.now()) {
                    $(row).find('td:last').html('new');
                }
            });

            if(needRunAgain) {
                setTimeout(countDown,400);
            }
        }

        countDown();
    }
    function checkConfirm() {

        if($('[name=login]').val().length==0) {
            isNeedProcess=false;
        }

        if(isNeedProcess) {

            isNeedProcess=false;

            let amount = $('[name=amount]').val();
            let office_id = $('[name=office_id]').val();
            return confirm('Are you sure you want to give '+amount+' for login '+$('[name=login]').val()+' at '+office_id+' office?')
        }

        isNeedProcess=false;
        return true;
    }
</script>