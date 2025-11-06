<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Prohibited areas</h4>
                        <hr>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">

                        <?php if (person::$role == 'sa'): ?>

                            <form method="POST" >
                                <?php if ($updated): ?>
                                <span style="color:red" >Updated</span><br>
                                <?php endif; ?>
                                <textarea name="text" cols="100" rows="15"><?php echo $news->text ?></textarea><br>
                                <button class="btn btn-success" type="submit" >Save</button>
                            </form>

                        <?php else: ?>
                            <?php echo $news->text ?>
                        <?php endif; ?>


                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>



