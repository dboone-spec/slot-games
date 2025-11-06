<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>ToTheMoon ban</h4>
                        <hr>
                        <form method="POST">
                            <label>
                                UserID
                            <input type="text" name="user_id" />
                            </label>
                            <label>
                                Percent
                            <input type="text" name="percent" />
                            </label>
                            <button type="submit" value="go">BAN TOTHEMOON USER</button>
                        </form>
                </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <form method="POST">
                            <table class="table supertable-hover table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th>UserId</th>
                                        <th>Value</th>
                                        <th>Banned</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($banned_users as $user_id=>$banned): ?>
                                    <tr>
                                        <td><?php echo $user_id; ?></td>
                                        <td><?php echo $banned['val']*100; ?>%</td>
                                        <td><?php echo date('Y-m-d H:i:s',$banned['time']); ?></td>
                                        <td><a class="btn btn-danger" href="/enter/moonban/delete/<?php echo $user_id; ?>">Delete ban</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
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