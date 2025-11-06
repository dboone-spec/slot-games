<section class="pc-container">
    <div class="pcoded-content">
        <div class="row">
            <!-- [ form-element ] start -->

            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h1 class="">Give FS
							<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/Freespins/<?php echo I18n::$lang; ?>/<?php echo Request::current()->controller().Request::current()->action(); ?>.html">
                                &#x1F517;
                            </a>
						</h1>
                        <hr>
                        <?php if(!empty($errors)): ?>
                        <?php foreach($errors as $err): ?>
                        <div style="color: red;" class="danger">
                            <?php echo $err; ?>
                        </div>
                        <?php endforeach; ?>
                        <hr>
                        <?php else: ?>
                            <div style="color: green;" class="success">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if(empty($user_id)): ?>
                        <div class="row">
                            <form id="mainform" action="/enter/fsapi/give" class="col-md-12" method="GET">
                                <div>
                                    User ID: <input name="user_id" value="<?php echo arr::get($_GET,'user_id'); ?>" required/>
                                </div>
                                <button type="submit" name="go" value="1" class="btn btn-primary">Step 2</button>
                            </form>
                        </div>
                        <?php elseif(empty($errors) || $is_post): ?>
                        <div class="row">
                            <form id="mainform" class="col-md-12" method="POST">
                                <div>
                                    USER ID: <?php echo $user_id; ?>
                                </div>
                                <div>
                                    Game: <?php echo form::select('game',$gamelist,arr::get($_POST,'game',''),['required'=>'required']); ?>
                                </div>
                                <div>
                                    Count: <input name="count" type="number" value="" min="1" step="1" required/>
                                </div>
                                <div>
                                    Full amount: <input name="amount" type="number" value=""  min="0.000000001" step="any" required/>
                                </div>
                                <hr>
                                <button type="submit" name="process_btn" value="1" class="btn btn-primary">Give</button>
                            </form>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($user_id)): ?>
                        <hr />
                        <a href="/enter/fsapi/give" class="btn btn-secondary">Go to list</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
</section>
<style>
    #mainform > div, #paramsbuilder > div {
        margin: 1em auto;
    }
</style>
<script>

</script>