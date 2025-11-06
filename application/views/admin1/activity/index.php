<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Activity</h4>
                        <hr>


                        <div class="row">
                            <div class="col-6"><?php echo $avg ?> average count<br>
                                <?php echo th::number_format($avgamount); ?> average bet amount<br/>
                                in last <?php echo $count ?> seconds
                            </div>
                            <div class="col-6">
                                <?php foreach ($avgArr as $owner => $value): ?>
                                    <?php echo round($value, 1) . "<b> $owner</b>" ?> average count<br>
                                <?php endforeach ?>
                            </div>
                        </div>



                    </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered dataTable">
                                <thead>
                                <tr>
                                    <th> Time</th>
                                    <th> Count of bets</th>
                                    <th> AVG bet</th>
                                </tr>


                                </thead>
                                <tbody>
                                <?php foreach ($data as $row): ?>

                                    <tr>
                                        <td><?php echo $row['created'] ?> </td>
                                        <td> <?php echo $row['count'] ?> </td>
                                        <td> <?php echo th::number_format($row['avgbet']) ?> </td>

                                    </tr>

                                <?php endforeach ?>

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
        $('tr:visible').css('background-color', 'rgba(114, 103, 239, 0)');
        $('tr:visible:even').each(function (k, el) {
            $(el).css('background-color', 'rgba(114, 103, 239, 0.03)');
        });
    }

    $(function () {
        $("#time_start").datepicker({dateFormat: "yy-mm-dd"});
        $("#time_end").datepicker({dateFormat: "yy-mm-dd"});
    });

    $('.statrow').hide();
    $('.statrow.total').show();

    redraw();

    $('.statrow').click(function () {
        $('[date=' + $(this).attr('date') + ']').not('.total').toggle();
        redraw();
    });
</script>