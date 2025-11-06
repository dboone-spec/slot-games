<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Restart moon</h4>
                        <hr>
                    </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <?php foreach($moonGames as $id=>$app): ?>
                            <form action="<?php echo Request::$current->url() ?>/index/<?php echo $id; ?>" method="POST">
                                <div class="card-footer">
                                    <input type="submit" class="btn btn-success" value="Restart <?php echo $app; ?>" />
                                </div>
                            </form>
                            <?php endforeach; ?>
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