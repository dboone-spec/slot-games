

<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ form-element ] start -->


            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="mt-5">Game sessions</h5>

                        <form class="form-inline" method="GET">
                            <div class="form-group mb-2">
                                <label for="staticEmail2" >UserID</label>

                            </div>
                            <div class="form-group mx-sm-3 mb-2">

                                <?php echo form::input('user_id',$user_id) ?>
                            </div>
                            <div class="form-group mb-2">
                                <label for="staticEmail3" >Show deleted?</label>

                            </div>
                            <div class="form-group mx-sm-3 mb-2">

                                <?php echo form::checkbox('show_del',1,$show_del) ?>
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
                                        $counter>1 && $json_data[$counter - 2] == ','))
                                {
                                    echo "\n";
                                    echo str_repeat(' ',($space * 2));
                                }
                                if($json_data[$counter] == '"' && !$flag)
                                {
                                    if($json_data[$counter - 1] == ':' || ($counter>1 && $json_data[$counter - 2] == ':' && $json_data[$counter - 1]!='{')) {
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



                                        <th class = "sorting superSort" colspan="2">
                                            data
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($sessions as $key) : ?>
                                        <?php if(!$show_del && strpos($key, 'del')) continue; ?>
                                        <tr>
                                            <td>
                                                <?php echo $game=str_replace($user_id . '-agt-','',$key); ?>
                                            </td>
                                            <td>
                                                <?php echo pretty_print(dbredis::instance()->get($key)); ?>
                                            </td>
                                            <td><a onclick="return confirm('Are you sure?')" class="btn btn-danger" href="/enter/gamesessions/delete/<?php echo $user_id; ?>?game=<?php echo $game; ?>">delete</a></td>
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


