


<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Discount report UTC</h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                                <div class="form-row">

                                    <div class="form-group col-md-3">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Date from (00:00:00)</span>
                                            </div>
                                            <input type="date" class="form-control form-control-sm" id="time_start" name="time_from" value="<?php echo date('Y-m-d',strtotime($time_from)) ?>" >
                                        </div>
                                    </div>


                                    <div class="form-group col-md-3">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Date to (23:59:59)</span>
                                            </div>
                                            <input type="date" class="form-control form-control-sm" id="time_end" name="time_to" value="<?php echo date('Y-m-d',strtotime($time_to)) ?>" >
                                        </div>
                                    </div>


                                    <div class="form-group col-md-3 form-select-1">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                            </div>
                                            <?php echo form::select('office_id',$officesList,$office_id,['class'=>'form-control form-control-sm select2'])?>
                                        </div>
                                    </div>

                                    <?php if (Person::user()->showOwners()): ?>


                                        <div class="form-group col-md-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm">Owner</span>
                                                </div>
                                                <?php echo form::select('owner',$owners,$owner,['class'=>'form-control form-control-sm select2'])?>
                                            </div>
                                        </div>

                                    <?php endif; ?>
                                        <div class="form-group col-md-2">
                                            <div class="custom-control custom-checkbox">
                                                <?php echo form::checkbox('is_test',1,(bool) false,['id'=>'select_all','class'=>'custom-control-input'])?>
                                                <label class="custom-control-label" for="select_all">Select all</label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <div class="custom-control custom-checkbox">
                                                <?php echo form::checkbox('is_test',1,(bool) $is_test,['id'=>'_isTestId','class'=>'custom-control-input'])?>
                                                <label class="custom-control-label" for="_isTestId">Test offices</label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4 form-select-2">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm">Rate at end of</span>
                                                </div>
                                                <?php echo form::select('m',$months,$m,['class'=>"form-control form-control-sm"])?>
                                                <?php echo form::select('y',$years,$y,['class'=>"form-control form-control-sm"])?>
                                            </div>

                                        </div>




                                    <div class="w-100"></div>

                                    <div class="non-form-control ml-auto">
                                        <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                    </div>
                                    <div>
                                        <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/report"><?php echo __('Очистить') ?></a>
                                    </div>
                                    <!--<div>
                                        <a class="btn btn-sm btn-round btn-outline-warning" href="javascript:showChart();"><?php echo __('Chart') ?></a>
                                    </div>-->
                                </div>

                            <hr />
                            <div class="form-row">


                            <?php foreach ($games as $game): ?>

                                <div class="form-group col-md-1">
                                    <div class="custom-control custom-checkbox">
                                        <?php echo form::checkbox('gameOn[]',$game['name'],in_array($game['name'],$gameOn),['id'=>"id{$game['name']}" ,'class'=>'custom-control-input'] )?>
                                        <label class="custom-control-label" for="<?php echo "id{$game['name']}" ?>"><?php echo $game['visible_name'] ?> </label>
                                    </div>
                                </div>


                                <?php endforeach; ?>

                            </div>



                        </form>


                </div>

                                     <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                <thead>

                                    <th class="tddate"> <?php echo __('Date from')?> </th>
                                    <th class="tddate"> <?php echo __('Date to')?> </th>
                                    <th > <?php echo __('Office id')?> </th>
                                     <th > <?php echo __('Office name')?> </th>
                                     <th > <?php echo __('Game')?> </th>
                                    <th > <?php echo __('Currency')?> </th>
                                    <th > <?php echo __('Rate')?> </th>
                                    <th > <?php echo __('Day of rate')?> </th>
                                    <th > <?php echo __('In')?> </th>
                                    <th > <?php echo __('In DS')?> </th>
                                    <th > <?php echo __('Out')?> </th>
                                    <th > <?php echo __('Win')?> </th>



                                </thead>
                                <tbody>

                                    <?php foreach ($data as $row): ?>
                                        <tr>
                                            <td > <?php echo date('d-m-Y',strtotime($time_from)) ?>  </td>
                                            <td > <?php echo date('d-m-Y',strtotime($time_to)) ?> </td>
                                            <td > <?php echo $row['office_id'] ?> </td>
                                            <td > <?php echo $row['visible_name'] ?> </td>
                                            <td > <?php echo $games[$row['game']]['visible_name'] ?> </td>
                                            <td > <?php echo $row['cur_name'] ?> </td>
                                            <td > <?php echo $row['value'] ?> </td>
                                            <td > <?php echo date('d-m-Y',$crdate-60*60*24) ?> </td>
                                            <td > <?php echo th::number_format($row['in'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($row['in_fs'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($row['out'] ?? 0) ?> </td>
                                            <td > <?php echo th::number_format($row['win'] ?? 0) ?> </td>


                                        </tr>
                                    <?php endforeach; ?>




                                </tbody>
                            </table>





                        </div>



                     <div>
                         <a class="btn btn-sm btn btn-primary btn-round" href="<?php echo url::query(['xls'=>'go'])?>"> Export to excel </a>
                     </div>



                    </div>


                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>



<script>
    function showChart() {
        let search = window.location.search;

        if(search.length==0) {
            search='?chart=1';
        }
        else {
            search+='&chart=1';
        }

        window.open(window.location.origin+window.location.pathname+search, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=800,width=1024');
    }

    const selectAll = document.getElementById('select_all');
    const formRow = document.querySelectorAll('.form-row')[1];
    selectAll.onclick = function() {
        formRow.querySelectorAll('[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = !checkbox.checked
        });
    }
</script>



