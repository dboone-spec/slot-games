

<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ form-element ] start -->


            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="mt-5">Games limits</h5>

                        <form class="form-inline" method="GET">
                            <div class="form-group mb-2">
                                <label for="staticEmail2" >Office</label>

                            </div>
                            <div class="form-group mx-sm-3 mb-2">
                                <?php echo form::select('office_id',$officesList,$office->id??'-1',['class' => 'select2']); ?>
                            </div>
                            <button type="submit" class="btn  btn-primary mb-2">Search</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach($settings as $name=>$set): ?>
                            <?php echo $name; ?>: <?php echo $set; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class = "table-responsive">
                            <table class = "table table-striped table-bordered nowrap dataTable supertable-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <?php foreach(['Game','Default bet','Game Lines','Min Bet Per Line','Max Bet Per Line','Max Multiplier','Min Total','Max Total','Max Exposure'] as $column): ?>
                                    <th class = "sorting superSort" style="position:sticky;top:0;">
                                        <?php echo $column; ?>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1; foreach($data as $row) : ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <?php foreach($row as $d): ?>
                                        <?php if(is_array($d)): ?>
                                        <td>
                                        <?php foreach($d as $l=>$blist): ?>
                                            <?php echo $l; ?>=>[<?php echo $blist; ?>]<br />
                                        <?php endforeach; ?>
                                        </td>
                                        <?php else: ?>
                                        <td>
                                            <?php echo $d; ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
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

        function tableFixHead(tableEl,sec) {
            let scroll=0;
            if($(sec).offset().top<$(window).scrollTop()) {
                scroll=($(window).scrollTop()-$(sec).offset().top+$('.pc-header').height());
            }
            $(sec).find('thead th').each(function(i,th) {
                $(th).css('transform','translateY('+scroll+'px)')
            });
        }

        $(window).scroll(function() {
            $('table').each(tableFixHead)
        });


    })
</script>


