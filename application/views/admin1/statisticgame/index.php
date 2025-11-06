


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Game statistic</h4>
                        <hr>

                        <form method="GET" class="form-horizontal">

                                <div class="form-row">

                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Date from</span>
                                            </div>
                                            <input type="date" id="time_start" name="time_from" class="form-control form-control-sm" value="<?php echo date('Y-m-d',strtotime($time_from)) ?>" >
                                        </div>
                                    </div>


                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Date to</span>
                                            </div>
                                            <input type="date" id="time_end" name="time_to" class="form-control form-control-sm" value="<?php echo date('Y-m-d',strtotime($time_to)) ?>" >
                                        </div>
                                    </div>




                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                            </div>
                                            <?php echo form::select('office_id',$officesList,$office_id,['class'=>'form-control form-control-sm select2'])?>
                                        </div>
                                    </div>

                                    <?php if(Person::$role=='sa' || in_array(Person::$user_id,[1149,1214])): ?>

                                        <div class="form-group col-md-2">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm">Owner</span>
                                                </div>
                                                <?php echo form::select('owner',$owners,$owner,['class'=>'form-control form-control-sm select2'])?>
                                            </div>
                                        </div>

                                    <?php endif; ?>




                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Game</span>
                                            </div>
                                            <?php echo form::select('game',$gamesList,$game,['id'=>'selGame','class'=>'form-control form-control-sm'])?>
                                        </div>
                                    </div>

                                    <div class="w-100"></div>

                                    <div class="form-group col-md-1">
                                        <div class="custom-control custom-checkbox">
                                            <?php echo form::checkbox('convert',1,(bool) $convert,['id'=>'_converId','class'=>'custom-control-input'])?>
                                            <label class="custom-control-label" for="_converId">Convert to EUR</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-1">
                                        <div class="custom-control custom-checkbox">
                                            <?php echo form::checkbox('is_test',1,(bool) $is_test,['id'=>'_isTestId','class'=>'custom-control-input'])?>
                                            <label class="custom-control-label" for="_isTestId">Test offices</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-1">
                                        <div class="custom-control custom-checkbox">
                                            <?php echo form::checkbox('group',1,(bool) $group,['id'=>'_group','class'=>'custom-control-input'])?>
                                            <label class="custom-control-label" for="_group">Group by month</label>
                                        </div>
                                    </div>


                                    <div class="w-100"></div>

                                    <div class="non-form-control ml-auto">
                                        <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                    </div>
                                    <div>
                                        <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/statisticgame"><?php echo __('Очистить') ?></a>
                                    </div>
                                </div>
                        </form>




                </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th rowspan="3" > <?php echo __('Date')?> </th>
                                        <th colspan="25"> <?php echo $games[$game]['name'] ?? ' ' ?></th>
                                    </tr>

                                    <tr>
                                            <th colspan="5"> <?php echo __('Normal')?> </th>
                                            <th colspan="5"> <?php echo __('DS')?> </th>
                                            <th colspan="5"> <?php echo __('LS')?> </th>
                                            <th colspan="5"> <?php echo __('FS API')?> </th>
                                            <th colspan="5"> <?php echo __('Double')?> </th>
                                    </tr>

                                    <tr>

                                        <th > <?php echo __('In')?> </th>
                                        <th> <?php echo __('Out')?> </th>
                                        <th> <?php echo __('Win')?> </th>
                                        <th > <?php echo __('Count')?> </th>
                                        <th > <?php echo __('RTP')?> </th>

                                        <th > <?php echo __('In')?> </th>
                                        <th> <?php echo __('Out')?> </th>
                                        <th> <?php echo __('Win')?> </th>
                                        <th > <?php echo __('Count')?> </th>
                                        <th > <?php echo __('RTP')?> </th>

                                        <th > <?php echo __('In')?> </th>
                                        <th> <?php echo __('Out')?> </th>
                                        <th> <?php echo __('Win')?> </th>
                                        <th > <?php echo __('Count')?> </th>
                                        <th > <?php echo __('RTP')?> </th>

                                        <th > <?php echo __('In')?> </th>
                                        <th> <?php echo __('Out')?> </th>
                                        <th> <?php echo __('Win')?> </th>
                                        <th > <?php echo __('Count')?> </th>
                                        <th > <?php echo __('RTP')?> </th>

                                        <th > <?php echo __('In')?> </th>
                                        <th > <?php echo __('Out')?> </th>
                                        <th > <?php echo __('Win')?> </th>
                                        <th > <?php echo __('Count')?> </th>
                                        <th > <?php echo __('RTP')?> </th>

                                    </tr>

                                </thead>
                                <tbody>
                                    <?php foreach($data as $date=>$games):?>
                                        <?php foreach($games as $game=>$gameData):?>
                                        <tr class="statrow <?php echo $game; ?>" date="<?php echo $date; ?>">
                                            <td class="tddate"> <?php echo $game=='total'?$date:$game ?> </td>
                                                    <td> <?php echo th::number_format($gameData['normal']['in'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['normal']['out'] ?? 0) ?> </td>
                                            <td <?php if(($gameData['normal']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($gameData['normal']['win'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['normal']['count'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['normal']['rtp'] ?? 0) ?>% </td>

                                            <td> <?php echo th::number_format($gameData['norcfs']['in'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norcfs']['out'] ?? 0) ?> </td>
                                            <td <?php if(($gameData['norcfs']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($gameData['norcfs']['win'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norcfs']['count'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norcfs']['rtp'] ?? 0) ?>% </td>

                                            <td> <?php echo th::number_format($gameData['norlfs']['in'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norlfs']['out'] ?? 0) ?> </td>
                                            <td <?php if(($gameData['norlfs']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($gameData['norlfs']['win'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norlfs']['count'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norlfs']['rtp'] ?? 0) ?>% </td>

                                            <td> <?php echo th::number_format($gameData['norafs']['in'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norafs']['out'] ?? 0) ?> </td>
                                            <td <?php if(($gameData['norafs']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($gameData['norafs']['win'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norafs']['count'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['norafs']['rtp'] ?? 0) ?>% </td>

                                            <td> <?php echo th::number_format($gameData['double']['in'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['double']['out'] ?? 0) ?> </td>
                                            <td <?php if(isset($gameData['double']) && $gameData['double']['win']<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($gameData['double']['win'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['double']['count'] ?? 0) ?> </td>
                                            <td> <?php echo th::number_format($gameData['double']['rtp'] ?? 0) ?>% </td>
                                        </tr>
                                        <?php endforeach ?>
                                    <?php endforeach ?>

                                    <tr>
                                        <td class="tddate"> <?php echo __('Total')?> </td>

                                                <td> <?php echo th::number_format($total['normal']['in'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['normal']['out'] ?? 0) ?> </td>
                                                <td <?php if(($total['normal']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['normal']['win'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['normal']['count'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['normal']['rtp'] ?? 0) ?>% </td>

                                                <td> <?php echo th::number_format($total['norcfs']['in'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norcfs']['out'] ?? 0) ?> </td>
                                                <td <?php if(($total['norcfs']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['norcfs']['win'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norcfs']['count'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norcfs']['rtp'] ?? 0) ?>% </td>

                                                <td> <?php echo th::number_format($total['norlfs']['in'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norlfs']['out'] ?? 0) ?> </td>
                                                <td <?php if(($total['norcfs']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['norlfs']['win'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norlfs']['count'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norlfs']['rtp'] ?? 0) ?>% </td>

                                                <td> <?php echo th::number_format($total['norafs']['in'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norafs']['out'] ?? 0) ?> </td>
                                                <td <?php if(($total['norafs']['win']??0)<0):?>style="color:red"<?php endif; ?>> <?php echo th::number_format($total['norafs']['win'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norafs']['count'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['norafs']['rtp'] ?? 0) ?>% </td>

                                                <td> <?php echo th::number_format($total['double']['in'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['double']['out'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['double']['win'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['double']['count'] ?? 0) ?> </td>
                                                <td> <?php echo th::number_format($total['double']['rtp'] ?? 0) ?>% </td>

                                      </tr>
                                </tbody>
                            </table>





                        </div>
                    </div>







                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>






















<style>
    .statrow:not(.total) {
        font-style: italic;
    }
</style>


<script>
        function redraw() {
            $('tr:visible').css('background-color','rgba(114, 103, 239, 0)');
            $('tr:visible:even').each(function(k,el) {
                $(el).css('background-color','rgba(114, 103, 239, 0.03)');
            });
        }

        $(function(){
                $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});
        });

        $('.statrow').hide();
        $('.statrow.total').show();

        redraw();

        $('.statrow').click(function() {
            $('[date='+$(this).attr('date')+']').not('.total').toggle();
            redraw();
        });
</script>