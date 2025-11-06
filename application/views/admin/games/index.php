<script>
    
$( document ).ready(function() {
    $('.brand').change(function(){
        
        if ( $(this).is(':checked') ){
            $(this).parent().find('input').each(function(){
                $(this).prop('checked', true);
            });
            
        }
        else{
            $(this).parent().find('input').each(function(){
                $(this).prop('checked', false);
            });
        }
        
    });
    
});    
    
</script>


<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Enabled games for office <?php echo $office_id>0 ? Person::user()->officesName($office_id,true) : '';?></h1>

            <div class="row">
                <div class="col-sm-12" >
                    <form method="GET" class="form-horizontal">
                        <div >
                            <table>
                                <tr>
                                    <td style="width:150px">
                                        <label>Office</label>
                                    </td>
                                    
                                </tr>
                                 <tr>
                                    <td>
                                        <?php echo form::select('office_id',$officesList,$office_id)?>
                                    </td>
                                    
                                </tr>
                            </table>
                            <br>

                        </div>

                        


                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="Select" />
                            
                        </div>
                    </form>
                </div>
                
                <?php if ($office_id>0): ?>
                
                    <form method=POST >

                        <div class="col-sm-12" >
                        <?php foreach($brands as $brand=>$name) : ?>
                            <div class="col-md-4">
                                <input type="checkbox" checked="checked" id="<?php echo $brand?>"  class="brand"/> <label for="<?php echo $brand?>"> <?php echo $name?> </label><br>
                                <?php foreach($games as $game) : ?>
                                    <?php if ($game['brand']==$brand): ?>
                                        <input type="checkbox" name="games[]" value="<?php echo $game['id'] ?> "
                                            <?php if ($game['enable']==1): ?>
                                               checked="checked"
                                            <?php endif; ?>

                                               id="<?php echo $game['id'] ?>" /> 
                                        <label for="<?php echo $game['id'] ?>" > <?php echo $game['visible_name'] ?> </label><br>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <input class="btn btn-primary"  type="submit" value="Save for <?php echo Person::user()->officesName($office_id,true);?> " />
                    </form >
                <?php endif; ?>
            </div>


  


          </div>
    </div>

</div>
