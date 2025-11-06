<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Sessions</h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                                <div class="form-row">

                                    <div class="form-group col-md-2">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm">User id</span>
                                            </div>
                                            <input  class="form-control form-control-sm" id="userId" name="userId" value="<?php echo $userId?>" >
                                        </div>
                                    </div>

                                    <div class="non-form-control">
                                        <input class="btn btn-primary btn-sm btn-round" type="submit" value="<?php echo __('Поиск') ?>" />
                                    </div>
                                    <div>
                                        <a class="btn btn-sm btn-round btn-outline-secondary" href="/enter/sessions"><?php echo __('Очистить') ?></a>
                                    </div>
                                </div>
                        </form>
                        
                </div>

                    
                    <?php if ($userId): ?>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th > Game </th>
                                        <th > TTL </th>
                                        <th > Data </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data as $row):?>

                                        <tr>
                                            <td ><pre><?php echo $row['game'] ?> </pre></td>
                                            <td ><pre><?php echo $row['ttl'] ?> </pre></td>
                                            <td ><pre><?php echo $row['data'] ?> </pre></td>
                                        </tr>

                                    <?php endforeach ?>

                            </table>





                        </div>
                    </div>

                    <?php endif; ?>
                    






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