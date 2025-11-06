

<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ form-element ] start -->


            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="mt-5">Game sessions</h5>

                        <form class="form-inline" method="POST">
                            <div class="form-group mb-2">
                                <label for="staticEmail2" >Office ID</label>

                            </div>
                            <div class="form-group mx-sm-3 mb-2">

                                <?php echo form::select('office_id',$officesList,$office_id,['class'=>'form-control form-control-sm select2'])?>
                            </div>
                            <button type="submit" class="btn  btn-primary mb-2">Search</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php

                        function pretty_print($json_data)
                        {

                            $space = 0;
                            $flag  = false;

                            echo "<pre>";

                            for($counter = 0; $counter < strlen($json_data); $counter++)
                            {

                                if($json_data[$counter] == '}' || ($json_data[$counter] == ']' && $json_data[$counter-1] == ']'))
                                {
                                    $space--;
                                    echo "\n";
                                    echo str_repeat(' ',($space * 2));
                                }

                                if($json_data[$counter] == '"' && ($json_data[$counter - 1] == ',' ||
                                        $json_data[$counter - 2] == ','))
                                {
                                    echo "\n";
                                    echo str_repeat(' ',($space * 2));
                                }
                                if($json_data[$counter] == '"' && !$flag)
                                {
                                    if($json_data[$counter - 1] == ':' || ($json_data[$counter - 2] == ':' && $json_data[$counter - 1]!='{')) {
                                        echo '<span style="color:blue;font-weight:bold">';
                                    }
                                    else {
                                        echo '<span style="color:red;">';
                                    }
                                }

                                echo $json_data[$counter];

                                if($json_data[$counter] == '"' && $flag) {
                                    echo '</span>';
                                }
                                if($json_data[$counter] == '"') {
                                    $flag = !$flag;
                                }
                                if($json_data[$counter] == '{' || ($json_data[$counter] == '[' && $json_data[$counter+1] == '['))
                                {
                                    $space++;
                                    echo "\n";
                                    echo str_repeat(' ',($space * 2));
                                }
                                if($json_data[$counter] == ',' && $json_data[$counter-1] == ']' && $json_data[$counter+1] == '[') {
                                    echo "\n";
                                    echo str_repeat(' ',($space * 2));
                                }
                                if(isset($json_data[$counter+1]) && $json_data[$counter+1] == '[') {
                                    echo '<span style="color:green;font-weight:bold">';
                                }
                                if($json_data[$counter] == ']') {
                                    echo '</span>';
                                }
                            }
                            echo "</pre>";
                        }
                        ?>

                        <div class = "table-responsive">
                            <table class = "table table-striped table-bordered nowrap dataTable supertable-hover">
                                <thead>
                                    <tr>

                                        <th class = "sorting superSort">
                                            Game

                                        </th>



                                        <th class = "sorting superSort">
                                            data
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($sessions as $key) :
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo str_replace('-'.$office_id,'',$key); ?>
                                            </td>
                                            <td>
                                                <?php echo pretty_print(dbredis::instance()->get($key)); ?>
                                            </td>
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



    });


</script>


