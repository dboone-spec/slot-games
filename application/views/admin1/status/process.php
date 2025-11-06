

<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ form-element ] start -->


            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="mt-5">Work processes</h5>
                    </div>
                    <div class="card-body">

                        <div class = "table-responsive">
                            <table class = "table table-striped table-bordered nowrap dataTable supertable-hover">
                                <thead>
                                    <tr>

                                        <th class = "sorting superSort">
                                            Process

                                        </th>



                                        <th class = "sorting superSort">
                                            Status
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($processes): ?>
                                    <?php foreach($processes as $key=>$val) : ?>
                                        <tr>
                                            <td>
                                                <?php echo str_replace('__process_lock__','',$key); ?>
                                            </td>
                                            <td>
                                                <?php echo $val; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                        <?php endif; ?>
                                </tbody>
                            </table>




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
    $(document).ready(function () {
        $('input[type=text]').addClass('form-control');

        $('select').addClass('form-control');

        $('.non-form-control input').removeClass('form-control');



    });


</script>


