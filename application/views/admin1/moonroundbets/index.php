<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>ToTheMoon round bets</h4>
                        <hr>
                        <?php if(!empty($round_id)): ?>
                        <h3>Round ID: <?php echo $round_id; ?></h3>
                        <?php endif; ?>
                        <form method="get">
                        <input type="text" name="round_id" />
                        <button type="submit" value="go">GO</button>
                        </form>
                </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <form method="POST">
                            <table class="table supertable-hover table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th > BET ID </th>
                                        <th > CALCED?</th>
                                    </tr>


                                </thead>
                                <tbody>
                                    <?php foreach($made_bets as $bet):?>

                                        <tr>
                                            <td ><?php echo $bet['id']; ?> </td>
                                            <td> 
                                                <?php if(isset($missed_bets[$bet['id']])): ?>
                                                    <button type="submit"
                                                        name="bet_to_dispatch[<?php echo $bet['office_id']; ?>][<?php echo $bet['user_id']; ?>][]"
                                                        value="<?php echo $bet['id']; ?>">SEND ONE</button>
                                                <?php else: ?>
                                                    OK
                                                <?php endif; ?>
                                            </td>

                                        </tr>

                                    <?php endforeach ?>
                                    <?php if(!empty($missed_bets)): ?>
                                        <tr>
                                            <td>
                                            </td>
                                            <td>
                                            <?php foreach($missed_bets as $mbet): ?>
                                                <input type="hidden"
                                                   name="bets_to_dispatch[<?php echo $mbet['office_id']; ?>][<?php echo $mbet['user_id']; ?>][]"
                                                   value="<?php echo $mbet['id']; ?>" />
                                            <?php endforeach ?>
                                            <button type="submit">SEND ALL</button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                            </table>


                            </form>


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