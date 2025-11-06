<div class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ sample-page ] start -->
            <div class="col-lg-4">
                <div class="card user-card user-card-1">
                    <div class="card-body pb-0">
                        <div class="float-right">

                        </div>
                        <div class="media user-about-block align-items-center mt-0 mb-3">
                            <div class="position-relative d-inline-block">
                                <img class="img-radius img-fluid wid-80" src="/theme/admin1/images/user/avatar-2.jpg" alt="User image">
                                <div class="certificated-badge">
                                    <i class="fas fa-certificate text-primary bg-icon"></i>
                                    <i class="fas fa-check front-icon text-white"></i>
                                </div>
                            </div>
                            <div class="media-body ml-3">
                                <h6 class="mb-1"><?php echo person::user()->name ?></h6>
                                <p class="mb-0 text-muted"><?php echo person::user()->role ?></p>
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="f-w-500"><i class="feather icon-mail m-r-10"></i>Email</span>
                            <a href="mailto:<?php echo Person::user()->email?>" class="float-right text-body"><?php echo Person::user()->email?></a>
                        </li>
                        <li class="list-group-item">
                            <span class="f-w-500"><i class="feather icon-phone-call m-r-10"></i>Phone</span>
                            <a href="#" class="float-right text-body"><?php echo Person::user()->phone?></a>
                        </li>
                        <li class="list-group-item">
                            <span class="f-w-500"><i class="feather icon-phone-call m-r-10"></i>Telegram</span>
                            <a href="#" class="float-right text-body"><?php echo  person::user()->tgname ?></a>
                        </li>

                    </ul>
                    <br />
                    <div class="nav flex-column nav-pills list-group list-group-flush list-pills" id="user-set-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link list-group-item list-group-item-action  <?php echo $order=='profile' ? 'active' : '' ?>" id="user-set-profile-tab" data-toggle="pill" href="#user-set-profile" role="tab" aria-controls="user-set-profile" aria-selected="<?php echo $order=='profile' ? 'true' : 'false' ?>">
                            <span class="f-w-500"><i class="feather icon-user m-r-10 h5 "></i>Personal Information</span>
                            <span class="float-right"><i class="feather icon-chevron-right"></i></span>
                        </a>


                        <a class="nav-link list-group-item list-group-item-action  <?php echo $order=='sigin' ? 'active' : '' ?>" id="user-set-sigin-tab" data-toggle="pill" href="#user-set-sigin" role="tab" aria-controls="user-set-sigin" aria-selected="<?php echo $order=='sigin' ? 'true' : 'false' ?>">
                            <span class="f-w-500"><i class="feather icon-settings m-r-10 h5 "></i>Sign In Settings</span>
                            <span class="float-right"><i class="feather icon-chevron-right"></i></span>
                        </a>


                        <a class="nav-link list-group-item list-group-item-action  <?php echo $order=='apitoken' ? 'active' : '' ?>" id="user-set-apitoken-tab" data-toggle="pill" href="#user-set-apitoken" role="tab" aria-controls="user-set-apitoken" aria-selected="<?php echo $order=='apitoken' ? 'true' : 'false' ?>">
                            <span class="f-w-500"><i class="feather icon-zap m-r-10 h5 "></i>API token</span>
                            <span class="float-right"><i class="feather icon-chevron-right"></i></span>
                        </a>

                        <a class="nav-link list-group-item list-group-item-action  <?php echo $order=='password' ? 'active' : '' ?>" id="user-set-passwort-tab" data-toggle="pill" href="#user-set-passwort" role="tab" aria-controls="user-set-passwort" aria-selected="<?php echo $order=='password' ? 'true' : 'false' ?>">
                            <span class="f-w-500"><i class="feather icon-shield m-r-10 h5 "></i>Change Password</span>
                            <span class="float-right"><i class="feather icon-chevron-right"></i></span>
                        </a>

                    </div>
                </div>

            </div>
            <div class="col-lg-8">
                <div class="tab-content" id="user-set-tabContent">


                    <div class="tab-pane fade <?php echo $order=='profile' ? 'show active' : '' ?>  " id="user-set-profile" role="tabpanel" aria-labelledby="user-set-profile-tab">

                         <?php if ($order=='profile' && $action!=''): ?>
                            <div class="alert alert-success" role="alert">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Success!</h5>
                                <p>
                                <?php switch ($action) {
                                            case 'change':
                                                echo 'Settings changed successfully.';
                                            break;

                                            default:
                                            break;
                                            }
                                ?>
                                </p>
                                <hr>

                            </div>
                        <?php endif;?>

                        <div class="card">
                            <div class="card-header">
                                <h5>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-target icon-svg-primary wid-20"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg><span class="p-l-5">Account Information</span>
									<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/Profile/<?php echo I18n::$lang; ?>/personalinfo.html">
                                        &#x1F517;
                                    </a>
                                    <small class="text-muted d-block m-l-25 m-t-5">change your account settings</small>
                                </h5>
                            </div>

                            <form action="<?php echo Request::$current->url() ?>/account" method="POST">
                                <div class="card-body">
                                    <h5 class="mb-4">General</h5>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Login <span class="text-danger"></span></label>
                                                <input type="text" class="form-control" value="<?php echo Person::user()->name?>" disabled="disabled">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Name <span class="text-danger"></span></label>
                                                <input name="visible_name" type="text" class="form-control" value="<?php echo Person::user()->visible_name?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Phone <span class="text-danger"></span></label>
                                                <input name="phone" type="text" class="form-control" value="<?php echo Person::user()->phone?>">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Email <span class="text-danger"></span></label>
                                                <input name="email" type="text" class="form-control" value="<?php echo Person::user()->email?>">
                                            </div>
                                        </div>


                                    </div>

                                </div>
                                <div class="card-footer text-right">
                                    <input type="submit" class="btn btn-success" value="Update Profile" />

                                </div>

                            </form>
                        </div>
                    </div>



                    <div class="tab-pane fade <?php echo $order=='apitoken' ? 'show active' : '' ?>  " id="user-set-apitoken" role="tabpanel" aria-labelledby="user-set-apitoken-tab">

                        <?php if ($order=='apitoken' && $action!=''): ?>
                            <div class="alert alert-success" role="alert">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Success!</h5>
                                <p>
                                    <?php switch ($action) {
                                        case 'change':
                                            echo 'Settings changed successfully.';
                                            break;

                                        default:
                                            break;
                                    }
                                    ?>
                                </p>
                                <hr>

                            </div>
                        <?php endif;?>

                        <div class="card">
                            <div class="alert alert-danger" role="alert" style="display:none" id="tg4dalert">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Alert!</h5>
                                <p id="tg4dalerttext"> </p>
                                <hr>

                            </div>

                            <div class="alert alert-success" role="alert" id="tg4dsucces" style="display:none">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Success!</h5>
                                <p>
                                <p id="tg4dsuccestext"> </p>
                                </p>
                                <hr>

                            </div>



                            <form action="<?php echo Request::$current->url() ?>/apitoken" method="POST">
                                <div class="card-body">
                                    <h5 class="mb-4">API token
										<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/Profile/<?php echo I18n::$lang; ?>/apitoken.html">
                                            &#x1F517;
                                        </a>
									</h5>
                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Current API token<span class="text-danger"></span></label>
                                                <div class="input-group mb-3">
                                                    <input id="apitoken" type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon2"
                                                           value="<?php echo Person::user()->apitoken?>"  disabled="disabled"  >
                                                    <div class="input-group-append" >
                                                        <button id="apitokengen" class="btn  btn-success" type="button">Generate</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                    </div>

                                </div>


                            </form>
                        </div>
                    </div>


                    <div class="tab-pane fade <?php echo $order=='signin' ? 'show active' : '' ?>  " id="user-set-sigin" role="tabpanel" aria-labelledby="user-set-sigin-tab">

                         <?php if ($order=='sigin' && $action!=''): ?>
                            <div class="alert alert-success" role="alert">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Success!</h5>
                                <p>
                                <?php switch ($action) {
                                            case 'change':
                                                echo 'Settings changed successfully.';
                                            break;

                                            default:
                                            break;
                                            }
                                ?>
                                </p>
                                <hr>

                            </div>
                        <?php endif;?>

                        <div class="card">
                            <div class="alert alert-danger" role="alert" style="display:none" id="tg3dalert">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Alert!</h5>
                                <p id="tg3dalerttext"> </p>
                                <hr>

                            </div>

                            <div class="alert alert-success" role="alert" id="tg3dsucces" style="display:none">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Success!</h5>
                                <p>
                                    <p id="tg3dsuccestext"> </p>
                                </p>
                                <hr>

                            </div>



                            <form action="<?php echo Request::$current->url() ?>/sigin" method="POST">
                                <div class="card-body">
                                    <h5 class="mb-4">General
										<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/Profile/<?php echo I18n::$lang; ?>/signsettings.html">
                                            &#x1F517;
                                        </a>
									</h5>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Signin Using <span class="text-danger"></span></label>
                                                <?php if (!(person::user()->tgchatid>0) ): ?>
                                                    <input id="signInUsing" type="text" class="form-control" value="Password" disabled="disabled">
                                                <?php else: ?>
                                                    <input type="text" class="form-control" value="Password + Telegram" disabled="disabled">
                                                <?php endif;?>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Telegram Name <span class="text-danger"></span></label>
                                                <div class="input-group mb-3">
                                                    <input id="tgname" type="text" class="form-control" placeholder="Telegaram username" aria-label="Telegram's username" aria-describedby="basic-addon2"
                                                           value="<?php echo Person::user()->tgname?>"   <?php echo person::user()->tgchatid>0 ? 'disabled="disabled" ':'' ;?>  >
                                                    <div class="input-group-append" <?php echo person::user()->tgchatid>0 ? 'style="display:none" ':'' ;?> >
                                                        <button id="tgnameupdate" class="btn  btn-success" type="button">Update</button>
                                                    </div>

                                                </div>
                                                <div class="mb-3">
                                                    <small class="form-text text-muted">
                                                        <a href="#" data-toggle="modal" data-target=".tgloginmodal">Where to see my login in telegram</a>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>




                                        <div id="tgAllowSpace" class="card-body" <?php if (person::user()->tgname=='' or person::user()->tgchatid>0 ) {
                                            echo 'style="display:none"'  ;
                                        }
                                         ?> >
						<h5 class="card-title">You are not allowed to send you authorization messages.</h5>
						<a href="#" data-toggle="modal" data-target=".tgAllowModal">How to allow to send message?</a>
                                                <p> Allow to send messages and click button. </p>
						<a id="tgIAllow" href="#!" class="btn  btn-primary">I Allow</a>
					</div>


                                    </div>

                                </div>


                            </form>
                        </div>
                    </div>




                    <div class="tab-pane fade <?php echo $order=='password' ? 'show active' : '' ?>" id="user-set-passwort" role="tabpanel" aria-labelledby="user-set-passwort-tab">

                        <?php if ($order=='password' && $alert!=''): ?>
                            <div class="alert alert-danger" role="alert">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Alert!</h5>
                                <p>
                                <?php switch ($alert) {
                                            case 'noeq':
                                                echo 'New password and confirmation password don\'t match.';
                                            break;

                                            case 'no6':
                                                echo 'New password cannot be shorter than 6 characters';
                                            break;

                                            case 'nopass':
                                                echo 'Old password is wrong.';
                                            break;

                                            default:
                                            break;
                                            }
                                ?>
                                </p>
                                <hr>

                            </div>
                        <?php endif;?>


                        <?php if ($order=='password' && $action!=''): ?>
                            <div class="alert alert-success" role="alert">
                                <h5 class="alert-heading"><i class="feather icon-alert-circle mr-2"></i>Success!</h5>
                                <p>
                                <?php switch ($action) {
                                            case 'change':
                                                echo 'Password changed successfully.';
                                            break;

                                            default:
                                            break;
                                            }
                                ?>
                                </p>
                                <hr>

                            </div>
                        <?php endif;?>

                        <div class="card">
                            <div class="card-header">
                                <h5><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock icon-svg-primary wid-20"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
								<span class="p-l-5">Change Password</span>
									<a href="<?php if(defined('LOCAL') && LOCAL): ?>https://static.site-domain.local<?php endif; ?>/wiki/Profile/<?php echo I18n::$lang; ?>/password.html">
                                        &#x1F517;
                                    </a>
								</h5>
                            </div>
                            <form action="<?php echo Request::$current->url() ?>/password" method="POST">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Current Password <span class="text-danger">*</span></label>
                                                    <input name="password" type="password" class="form-control" placeholder="Enter Your current password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>New Password <span class="text-danger">*</span></label>
                                                    <input name="newpassword" type="password" class="form-control" placeholder="Enter New password">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Confirm Password <span class="text-danger">*</span></label>
                                                    <input name="confirmpassword"  type="password" class="form-control" placeholder="Enter your password again">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <input class="btn btn-danger" type="submit" value="Change Password">
                                    </div>
                                </form>
                        </div>
                    </div>

                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>



<!-- tg login modal -->

<div class="modal fade tgloginmodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title h4" id="myLargeModalLabel">Telegram login</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                                 You can find telegram username in telegram application on you phone.
                                                        Enter your name into input "Telegram username"
                                <div class="row">
                                    <img class="col-sm-4" width="33%" src="/theme/admin1/images/tg/name1.png" />
                                    <img class="col-sm-4" width="33%" src="/theme/admin1/images/tg/name2.png" />
                                    <img class="col-sm-4" width="33%" src="/theme/admin1/images/tg/name3.png" />
                                </div>
                        </div>
                </div>
        </div>
</div>

<!-- tg allow modal -->

<div class="modal fade tgAllowModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title h4" id="myLargeModalLabel">Telegram login</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                                 You must give permission to the bot  to send you messages.<br>
                                1) Find the bot "AgtMegaWinAppBot"<br>
                                2) Send a message "/start" or press the button <br>
                                3) If you can't get a secret code, send any message to the bot and try again.<br>

                                <div class="row">
                                    <img class="col-sm-4" width="33%" src="/theme/admin1/images/tg/findbot.png" />
                                    <img class="col-sm-4" width="33%" src="/theme/admin1/images/tg/start.png" />
                                    <img class="col-sm-4" width="33%" src="/theme/admin1/images/tg/startsend.png" />
                                </div>
                        </div>
                </div>
        </div>
</div>





<script>
        $(function(){


                $("#tgnameupdate").click(function(){
                    $('#tg3dsucces').hide();
                    $('#tg3dalert').hide();
                    $.post( '<?php echo Request::$current->url() ?>/tgname', { tgname: $('#tgname').val() },function(data){


                        if (data.error==0){
                            $('#tg3dsuccestext').html(data.message);
                            $('#tg3dsucces').show();
                            $('#tgAllowSpace').show();

                        };

                        if (data.error==1){
                            $('#tg3dalerttext').html(data.message);
                            $('#tg3dalert').show();
                        };


                    }, 'JSON' );


                });

            $("#apitokengen").click(function(){
                $('#tg4dsucces').hide();
                $('#tg4dalert').hide();
                $.post( '<?php echo Request::$current->url() ?>/apitoken', {  },function(data){


                    if (data.error==0){
                        $('#tg4dsuccestext').html(data.message);
                        $('#tg4dsucces').show();

                        $('#apitoken').val(data.apitoken);
                    };

                    if (data.error==1){
                        $('#tg4dalerttext').html(data.message);
                        $('#tg4dalert').show();
                    };


                }, 'JSON' );


            });


                $("#tgIAllow").click(function(){
                    $('#tg3dsucces').hide();
                    $('#tg3dalert').hide();
                    $.post( '<?php echo Request::$current->url() ?>/tgid', { tgname: $('#tgname').val() },function(data){


                        if (data.error==0){
                            $('#tg3dsuccestext').html(data.message);
                            $('#tg3dsucces').show();
                            $('#tgAllowSpace').hide();

                            $('#tgnameupdate').hide();
                            $('#tgname').attr('disabled','disabled');
                            $('#signInUsing').val('Password + Telegram');


                        };

                        if (data.error==1){
                            $('#tg3dalerttext').html(data.message);
                            $('#tg3dalert').show();
                        };


                    }, 'JSON' );


                });












        });


</script>