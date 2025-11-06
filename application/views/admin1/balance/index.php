



<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
        <div class="col-md-12">
                <div class="card">
                   
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            
                            <h4>Balance report</h4>
                            <hr>
                            <table class="table supertable-hover table-bordered tableEvenOdd">
                                <thead>
                                    
                                   <tr>
                                        <th class="tddate"> <?php echo __('Date')?> </th>
                                        <th class="tddate"> <?php echo __('Who')?> </th>
                                        <th > <?php echo __('Object')?> </th>
                                        <th > <?php echo __('Amount')?> </th>
                                    </tr>


                                 
                                </thead>
                                <tbody>
                            
                                    <?php foreach($data as $row):?>
                                        <tr >
                                            <td > <?php echo date('H:m:i  d-m-Y',$row['created']) ?> </td>
                                            <td > <?php echo $row['who'] ?> </td>
                                            <td > <?php echo $row['object'] ?> </td>
                                            <td > <?php echo $row['amount'] ?> </td>
                                        </tr >
                                    <?php endforeach ?>            
                                    
                                   
                        
                 
                                </tbody>
                            </table>
                            
                    
                            
                          

                        </div>
                    </div>     
                    
                    
                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>




<script>
    $( document ).ready(function() {
        $('input[type=text]').addClass('form-control');
        $('input[type=text]').addClass('form-control-sm');
        $('select').addClass('form-control');
        $('select').addClass('form-control-sm');
        $('.non-form-control input').removeClass('form-control');
        $('.non-form-control input').removeClass('form-control-sm');
        
        
    });
    
    
</script>






















