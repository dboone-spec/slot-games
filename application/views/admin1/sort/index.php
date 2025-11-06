<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Games order</h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                            <div class="form-row">


                                <div class="form-group col-md-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Office</span>
                                        </div>
                                        <?php echo form::select('office_id', $officesList, $office->id, ['class' => 'form-control form-control-sm']) ?>
                                    </div>
                                </div>


                                <div class="non-form-control">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit"
                                           value="<?php echo __('Submit') ?>"/>
                                </div>

                            </div>
                        </form>


                    </div>

                    <div class="card-body table-border-style">
                        <div class="row">
                            <?php foreach ($office->sort('name') as $game => $sort) : ?>
                                <div class="col-4 col-md-3 col-xl-2">
                                    <img class="img-thumbnail rounded" src="https://content.site-domain.com/games/agt/sqthumb/<?php echo $game ?>.png?v=3" />
                                </div>
                            <?php endforeach; ?>
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

        if (search.length == 0) {
            search = '?chart=1';
        } else {
            search += '&chart=1';
        }

        window.open(window.location.origin + window.location.pathname + search, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=800,width=1024');
    }
</script>



