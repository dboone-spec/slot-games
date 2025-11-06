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
                                        <label><?php echo __('Month')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Year')?></label>
                                    </td>
                                    <td style="width:150px">
                                        <label><?php echo __('Office')?></label>
                                    </td>
                                    <?php if(Person::$role=='sa'): ?>
                                    <td style="width:150px">
                                        <label><?php echo __('Partner')?></label>
                                    </td>
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
                                        <?php echo form::select('month',[1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12'],$month) ?>
                                    </td>
                                    <td>
                                         <?php echo form::select('year',[2020=>2020],$month) ?>
                                    </td>
                                    <td>
                                        <?php echo form::select('office_id',$officesList,$office_id)?>
                                    </td>
                                    <?php if(Person::$role=='sa'): ?>
                                    <td>
                                         <?php echo form::select('parnerId',$partnersList,$partnerId)?>
                                    </td>
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
 <table>
                                <tr>
                                    <td style="width:160px">
                                        <?php echo form::checkbox('comma',1,$comma,['id'=>'comma'])?> <label for="comma"><?php echo __('Сomma as separator')?></label>
                                    </td>
                                    
                                </tr>
                                
                            </table>
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
                 Offices: <?php echo implode(', ',$offices); ?>
                <div  id='scrollblock' style="overflow-x: scroll; padding-left: 0px; margin-left: 15px;">
                    <div id="head"></div>
                    <div id="left_col"></div>


                        <table class="datatable"  >

                            <tr>
                                <th rowspan="3" class="tddate"> <?php echo __('Game')?> </th>
                                <?php if ($currency): ?>
                                    <th rowspan="3" class="tddate"> <?php echo __('Currency')?> </th>
                                <?php endif; ?>
                            </tr>

                            <tr>
                                    <th colspan="6"> <?php echo __('Normal')?> </th>
                                    <th colspan="6"> <?php echo __('Double')?> </th>
                            </tr>

                            <tr>
                                
                                <th > <?php echo __('In')?> </th>
                                <th> <?php echo __('Out')?> </th>
                                <th> <?php echo __('Win')?> </th>
                                <th > <?php echo __('Count')?> </th>
                                <th > <?php echo __('RTP')?> </th>
                                <th > <?php echo __('Avg bet')?> </th>

                                <th > <?php echo __('In')?> </th>
                                <th > <?php echo __('Out')?> </th>
                                <th > <?php echo __('Win')?> </th>
                                <th > <?php echo __('Count')?> </th>
                                <th > <?php echo __('RTP')?> </th>
                                <th > <?php echo __('Avg bet')?> </th>

                            </tr>


                            <?php  foreach($data as $game=>$gameData):?>
                                <tr >
                                    <td class="tddate"> <?php echo $game  ?> </td>
                                    <?php if ($currency): ?>
                                        <td> <?php echo $currency->name ?> </td>
                                    <?php endif; ?>
                                    
                                            <td> <?php echo $gameData['normal']['in'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['normal']['out'] ?? 0 ?> </td>
                                            <td <?php if( ($gameData['normal']['win']??0 )<0):?>style="color:red"<?php endif; ?>> <?php echo $gameData['normal']['win'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['normal']['count'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['normal']['rtp'] ?? 0 ?>% </td>
                                            <td> <?php echo $gameData['normal']['avgbet'] ?? 0 ?> </td>

                                            <td> <?php echo $gameData['double']['in'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['double']['out'] ?? 0 ?> </td>
                                            <td <?php if(isset($gameData['double']) && $gameData['double']['win']<0):?>style="color:red"<?php endif; ?>> <?php echo $gameData['double']['win'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['double']['count'] ?? 0 ?> </td>
                                            <td> <?php echo $gameData['double']['rtp'] ?? 0 ?>% </td>
                                            <td> <?php echo $gameData['double']['avgbet'] ?? 0 ?> </td>
                                </tr>

                            <?php endforeach ?>

                            <tr>
                                <td class="tddate"> <?php echo __('Total')?> </td>
                                        <?php if ($currency): ?>
                                            <td> <?php echo $currency->name ?> </td>
                                        <?php endif; ?>
                                        <td> <?php echo $total['normal']['in'] ?? 0 ?> </td>
                                        <td> <?php echo $total['normal']['out'] ?? 0 ?> </td>
                                        <td <?php if( ($total['normal']['win'] ?? 0) <0):?>style="color:red"<?php endif; ?>> <?php echo $total['normal']['win'] ?? 0 ?> </td>
                                        <td> <?php echo $total['normal']['count'] ?? 0 ?> </td>
                                        <td> <?php echo $total['normal']['rtp'] ?? 0 ?>% </td>
                                        <td> <?php echo $total['normal']['avgbet'] ?? 0 ?> </td>

                                        <td> <?php echo $total['double']['in'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['out'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['win'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['count'] ?? 0 ?> </td>
                                        <td> <?php echo $total['double']['rtp'] ?? 0 ?>% </td>
                                        <td> <?php echo $total['double']['avgbet'] ?? 0 ?> </td>

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