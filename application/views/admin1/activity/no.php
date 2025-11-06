<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Activity</h4>
                        <hr>
                </div>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            No entries found
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