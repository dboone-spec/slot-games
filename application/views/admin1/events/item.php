<section class="pc-container">
    <div class="pcoded-content">
        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class=""><?php echo $id?'EDIT EVENT':'NEW EVENT'; ?></h1>
                        <hr>
                        <?php if(!empty($errors)): ?>
                            <?php foreach($errors as $err): ?>
                                <div style="color: red;" class="danger">
                                    <?php echo $err; ?>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                        <?php else: ?>
                            <div style="color: green;" class="success">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-sm-6">

                                <a href="/enter/events"  class="btn btn-success">
                                    <i class="fa fa-list"></i>&nbsp;&nbsp;<?php echo __('List') ?>
                                </a>
                                <hr />
                                <form method="POST">
                                    <div class="modal-body text-left">
                                        <div class="form-group">
                                            <label>ID</label>
                                            <input type="text" value="<?php echo $event->id; ?>" disabled class="form-control" id="event_id" aria-describedby="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label>OFFICE ID</label>
                                            <?php if($event->loaded()): ?>
                                            <input type="text" value="<?php echo $offices[$event->office_id]; ?>" disabled class="form-control" id="office_id" aria-describedby="" placeholder="">
                                            <?php else: ?>
                                            <?php echo Form::select('office_id',$offices,$event->office_id,['class'=>'form-control']); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group">
                                            <label>PARTNER (OPTIONAL)</label>
                                            <?php if($event->loaded()): ?>
                                                <input type="text" value="<?php echo $event->partner; ?>" disabled class="form-control" id="partner" name="partner" aria-describedby="" placeholder="">
                                            <?php else: ?>
                                                <?php echo Form::select('partner',$partners,$event->partner,['class'=>'form-control']); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" name="active" value="1" class="custom-control-input input-primary"
                                                       id="customCheckdef4" <?php echo $event->active?'checked':''; ?>>
                                                <label class="custom-control-label" for="customCheckdef4">Active</label>
                                            </div>
                                        </div>
                                        <!-- ID	OFFICE ID	ACTIVE	NEXT TIME	DURATION	AMOUNT	COUNT	WEEK DAY	TIME	GAMES	STARTS	ENDS	TYPE	CREATED -->
                                        <div class="form-group" fortype="progressive,dayweek">
                                            <label>Amount of one spin</label>
                                            <input type="input" min="0" value="<?php echo $event->fs_amount??0; ?>" class="form-control" name="fs_amount" placeholder="">
                                        </div>
                                        <div class="form-group" fortype="dayweek">
                                            <label>Count of spins</label>
                                            <input type="number" min="dayweek" value="<?php echo $event->fs_count??0; ?>" class="form-control" name="fs_count" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">Event type</label>
                                            <?php
                                            $formparams=['class'=>'form-control'];
                                            if($event->loaded()) {
                                                $formparams['disabled']='disabled';
                                            }
                                            ?>
                                            <?php echo Form::select('type',$types,$event->type,$formparams); ?>
                                        </div>
                                        <div class="form-group" fortype="dayweek,promo">
                                            <label class="col-form-label">Day of week</label>
                                            <?php echo Form::select('dow',$daysofweek,$event->dow,['class'=>'form-control']); ?>
                                        </div>
                                        <?php foreach($progressive_map as $day=>$percent): ?>
                                        <div class="form-group" fortype="progressive">
                                            <label class="col-form-label"><?php echo $day+1; ?> day</label>
                                            <input class="form-control" max="100" min="1"
                                                   value="<?php echo $percent; ?>" name="progressive_map[<?php echo $day; ?>]">
                                        </div>
                                        <?php endforeach; ?>
                                        <div class="form-group" fortype="dayweek,promo">
                                            <label class="col-form-label">Time to start <?php echo !$event->loaded()?'(Local time for partner promo)':'(UTC time)'; ?></label>
                                            <input class="form-control" type="time" step="60" value="<?php echo str_pad($event->h>=0?$event->h:0,2,'0',STR_PAD_LEFT).
                                                ':'.str_pad($event->m>=0?$event->m:0,2,'0',STR_PAD_LEFT); ?>" name="time">
                                        </div>
                                        <div class="form-group" fortype="dayweek,promo">
                                            <label class="col-form-label">Duration</label>
                                            <input class="form-control" type="time" step="60" value="<?php echo $event->duration() ?? '01:00'; ?>" name="duration">
                                        </div>
                                        <div class="form-group" fortype="dayweek,promo">
                                            <label class="col-form-label">Time to collect (Interval)</label>
                                            <input class="form-control" type="time" step="60" value="<?php echo $event->time_to_collect() ?? '01:00'; ?>" name="time_to_collect">
                                        </div>
                                        <div class="form-group" fortype="promo">
                                            <label class="col-form-label">MAX PAYOUT (EUR)</label>
                                            <input class="form-control" type="numeric" name="max_payout" value="<?php echo $event->max_payout;?>">
                                        </div>
                                        <!-- 2021-12-31T04:03:20 -->
                                        <div class="form-group">
                                            <label class="col-form-label">Begin <?php echo !$event->loaded()?'(Local time for partner promo)':'(UTC time)'; ?></label>
                                            <input class="form-control" name="starts" type="datetime-local"
                                                   value="<?php echo date('Y-m-d\TH:i:s',$event->starts??mktime(null,null,0)); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label class="col-form-label">End <?php echo !$event->loaded()?'(Local time for partner promo)':'(UTC time)'; ?></label>
                                            <input class="form-control" name="ends" type="datetime-local"
                                                   value="<?php echo date('Y-m-d\TH:i:s',$event->ends??mktime(null,null,0)+Date::DAY); ?>">
                                        </div>
                                        <?php if(count($allgames)): ?>
                                        <div class="form-group">
                                            <label for="games_ids" class="col-form-label">Games</label>
                                            <?php foreach($allgames as $game): ?>
                                            <div class="form-group">
                                            <?php echo $game['visible_name']; ?>
                                            <?php echo Form::checkbox('games_ids['.$game['id'].']',1,in_array($game['id'],$event->games_ids)); ?>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn waves-effect waves-light btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function updateParamsForType() {
        $('[fortype]').hide();
        $('[fortype] input,[fortype] select').prop('disabled',true);

        let type=$('[name=type]').val();

        $('[fortype],[fortype]').each(function() {
            let el=$(this);
            if(el.attr('fortype').indexOf(type)>=0) {
                el.show();
                el.find('select,input').prop('disabled',false);
            }
        });
    }
    $('[name=type]').change(updateParamsForType);

    function selectOfficePartner() {
        if($(this).attr('name')=='partner') {
            $('[name=office_id]').val('-1');
        }
        else {
            $('[name=partner]').val('');
        }
    }

    $('[name=office_id],[name=partner]').change(selectOfficePartner);

    window.onload=updateParamsForType;
</script>