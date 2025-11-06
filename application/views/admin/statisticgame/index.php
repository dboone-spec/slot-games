 <style>
                .datatable td,.datatable th {
                    border: 1px solid black;
                    text-align: center;
                    vertical-align: middle;
                    min-width: 70px;
                    padding: 2px 4px;
                }
                .tddate {
                    min-width: 90px !important;
                }
                 .datatable th {
                    background-color: #6d9dff;
                }


                .datatable tr:nth-child(even) {
                    background-color: #dce7fe;
                }



                #head, #left_col{
                    display: grid;
                    position: absolute;
                    grid-row-gap: 0px;
                    grid-column-gap: 0px;
                    background-color: white;
                    border-bottom: 1px solid black !important;
                    border-right: 1px solid black !important;
                }
                #head div, #left_col div{
                    padding: 10px;
                    text-align: center;
                    border-left: 1px solid black !important;
                    border-top: 1px solid black !important;
                }
                .sort{
                    cursor: pointer;
                    position: relative;
                }
                .desc::before{
                    content: '\25bc';
                    position: absolute;
                    bottom: 5px;
                    left: calc(50% - 7px);
                }
                .asc::before{
                    content: '\25b2';
                    position: absolute;
                    bottom: 5px;
                    left: calc(50% - 7px);
                }
                thead {
                    /*visibility: hidden;*/
                }
                #left_col {
                    /*visibility: hidden;*/
                    /*background: none;*/
                }
                .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {padding: 5px;}
            </style>



<?php if (PROJECT==2):   ?>
<script>

$( document ).ready(function() {
    $('#selBrand').change(function(){

        let opt='<option value="all">All</option>';

        if ($(this).val()=='egt' || $(this).val()=='all'){
            <?php foreach( $gameBrand['egt'] as $id=>$name) : ?>
                opt+='<option value="<?php echo $id ?>"><?php echo $name ?></option>'
            <?php endforeach ?>
        }

        if ($(this).val()=='novomatic' || $(this).val()=='all' ){
            <?php foreach( $gameBrand['novomatic'] as $id=>$name) : ?>
                opt+='<option value="<?php echo $id ?>"><?php echo $name ?></option>'
            <?php endforeach ?>
        }

        if ($(this).val()=='igrosoft' || $(this).val()=='all'){
            <?php foreach( $gameBrand['igrosoft'] as $id=>$name) : ?>
                opt+='<option value="<?php echo $id ?>"><?php echo $name ?></option>'
            <?php endforeach ?>
        }

        $('#selGame').empty();
        $('#selGame').append(opt);

    });

});

</script>
<?php endif ?>



<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo __('Game statistic')?></h1>

            <div class="row">
                <div class="col-sm-12" >
                    <form method="GET" class="form-horizontal">
                        <div >
                            <table>
                                <tr>
                                    <td style="width:150px">
                                        <label><?php echo __('Date from')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Date to')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Office')?></label>
                                    </td>
                                    <?php if(Person::$role=='sa'): ?>
                                    <td style="width:150px">
                                        <label><?php echo __('Test offices')?></label>
                                    </td>
                                    <?php endif; ?>
                                    <td >
                                        <label><?php echo __('Game')?></label>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>
                                        <input type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d',strtotime($time_from)) ?>" >
                                    </td>
                                    <td>
                                        <input type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d',strtotime($time_to)) ?>" >
                                    </td>
                                    <td>
                                        <?php echo form::select('office_id',$officesList,$office_id)?>
                                    </td>
                                    <?php if(Person::$role=='sa'): ?>
                                    <td>
                                        &nbsp;&nbsp;&nbsp;<?php echo form::checkbox('is_test',1,$is_test)?>
                                    </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php echo __('Brand')?>:<?php echo form::select('brand',$brandList,$brand,['id'=>'selBrand'])?>
                                        <?php echo __('Game')?>:<?php echo form::select('game',$gamesList,$game,['id'=>'selGame'])?>
                                    </td>
                                </tr>
                            </table>
                            <br>

                        </div>



                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="<?php echo __('Find')?>" />
                            <a class="btn btn-default" href="/enter/statisticgame"><?php echo __('Clear')?></a>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 all_games">

                </div>
            </div>


             <div class="row">
                <div  id='scrollblock' style="overflow-x: scroll; padding-left: 0px; margin-left: 15px;">
                    <div id="head"></div>
                    <div id="left_col"></div>


                        <table class="datatable"  >

                            <tr>
                                <th rowspan="3" class="tddate"> <?php echo __('Date')?> </th>
                                <th colspan="20"> <?php echo $games[$game]['name'] ?? ucfirst($brand) ?></th>
                            </tr>

                            <tr>
                                    <th colspan="5"> <?php echo __('Normal')?> </th>
                                    <th colspan="5"> <?php echo __('FS Cashback')?> </th>
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
                                <th > <?php echo __('Out')?> </th>
                                <th > <?php echo __('Win')?> </th>
                                <th > <?php echo __('Count')?> </th>
                                <th > <?php echo __('RTP')?> </th>

                            </tr>


                            <?php foreach($data as $date=>$games):?>
                                <?php foreach($games as $game=>$gameData):?>
                                <tr class="statrow <?php echo $game; ?>" date="<?php echo $date; ?>">
                                    <td class="tddate"> <?php echo $game=='total'?$date:$game ?> </td>
                                            <td> <?php echo $gameData['normal']['in'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['normal']['out'] ?? 0 ?> </td>
                                            <td <?php if($gameData['normal']['win']??0<0):?>style="color:red"<?php endif; ?>> <?php echo $gameData['normal']['win'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['normal']['count'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['normal']['rtp'] ?? 0 ?>% </td>

                                            <td> <?php echo $gameData['norcfs']['in'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['norcfs']['out'] ?? 0 ?> </td>
                                            <td <?php if($gameData['norcfs']['win']??0<0):?>style="color:red"<?php endif; ?>> <?php echo $gameData['norcfs']['win'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['norcfs']['count'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['norcfs']['rtp'] ?? 0 ?>% </td>

                                            <td> <?php echo $gameData['norafs']['in'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['norafs']['out'] ?? 0 ?> </td>
                                            <td <?php if($gameData['norafs']['win']??0<0):?>style="color:red"<?php endif; ?>> <?php echo $gameData['norafs']['win'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['norafs']['count'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['norafs']['rtp'] ?? 0 ?>% </td>

                                            <td> <?php echo $gameData['double']['in'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['double']['out'] ?? 0 ?> </td>
                                            <td <?php if(isset($gameData['double']) && $gameData['double']['win']<0):?>style="color:red"<?php endif; ?>> <?php echo $gameData['double']['win'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['double']['count'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['double']['rtp'] ?? 0 ?>% </td>
                                </tr>
                                <?php endforeach ?>
                            <?php endforeach ?>

                            <tr>
                                <td class="tddate"> <?php echo __('Total')?> </td>

                                        <td> <?php echo $total['normal']['in'] ?? 0 ?> </td>
                                        <td> <?php echo $total['normal']['out'] ?? 0 ?> </td>
                                        <td <?php if($total['normal']['win']??0<0):?>style="color:red"<?php endif; ?>> <?php echo $total['normal']['win'] ?? 0 ?> </td>
                                        <td> <?php echo $total['normal']['count'] ?? 0 ?> </td>
                                        <td> <?php echo $total['normal']['rtp'] ?? 0 ?>% </td>

                                        <td> <?php echo $total['norcfs']['in'] ?? 0 ?> </td>
                                        <td> <?php echo $total['norcfs']['out'] ?? 0 ?> </td>
                                        <td <?php if($total['norcfs']['win']??0<0):?>style="color:red"<?php endif; ?>> <?php echo $total['norcfs']['win'] ?? 0 ?> </td>
                                        <td> <?php echo $total['norcfs']['count'] ?? 0 ?> </td>
                                        <td> <?php echo $total['norcfs']['rtp'] ?? 0 ?>% </td>

                                        <td> <?php echo $total['norafs']['in'] ?? 0 ?> </td>
                                        <td> <?php echo $total['norafs']['out'] ?? 0 ?> </td>
                                        <td <?php if($total['norafs']['win']??0<0):?>style="color:red"<?php endif; ?>> <?php echo $total['norafs']['win'] ?? 0 ?> </td>
                                        <td> <?php echo $total['norafs']['count'] ?? 0 ?> </td>
                                        <td> <?php echo $total['norafs']['rtp'] ?? 0 ?>% </td>

                                        <td> <?php echo $total['double']['in'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['out'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['win'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['count'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['rtp'] ?? 0 ?>% </td>

                              </tr>



                            <tbody>
                            </tbody>
                        </table>

                    </div>
            </div>


          </div>
    </div>

</div>
<style>
    .statrow {
        font-style: italic;
    }
    .statrow.total {
        font-style: normal;
    }
</style>
<script>
        $(function(){
                $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});
        });

        $('.statrow').hide();
        $('.statrow.total').show();

        $('.statrow').click(function() {
            $('[date='+$(this).attr('date')+']').not('.total').toggle();
        });
</script>