<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ form-element ] start -->


            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4><?php echo __($mark) ?></h4>
                        <hr>
                        <div class="row">

                            <div class="col-md-8" >
                                <?php if(isset($error) && count($error)): ?>
                                    <?php foreach($error as $fieldname=>$errortext): ?>
                                        <div class="">
                                            <div style="color:red">
                                                <?php echo str_replace($model.'.'.$fieldname.'.','',$errortext); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <br />
                                <?php endif; ?>
                                <form method="POST">


                                       <?php foreach($show as $s): ?>
                                                <div class="form-group row">
                                                    <div class="col-sm-3">
                                                         <label for="exampleInputEmail1"><?php echo $label[$s] ?? $s ?></label>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-check">
                                                            <?php echo $vidgets[$s]->render($item, 'item') ?>
                                                        </div>
                                                    </div>

                                                </div>
                                        <?php endforeach ?>




                                    <button type="submit" class="btn  btn-primary">Save</button>
                                </form>
                            </div>










                        </div>











                    </div>
                </div>
            </div>

            <!-- [ form-element ] end -->
        </div>
        <!-- [ Main Content ] end -->

    </div>
</section>

<script>
    $( document ).ready(function() {
        $('input[type=text]').addClass('form-control');
        $('select').addClass('form-control');
        $('.non-form-control input').removeClass('form-control');



    });


</script>