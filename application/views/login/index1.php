<div class="auth-wrapper auth-v3" style="background: none;">
    <div class="auth-content">
        <div class="card">
            <div class="row align-items-stretch text-center">
                <div class="col-md-6 img-card-side">
                    <img style="margin-top: 27%;margin-left: 27%;margin-bottom: 27%;" src="/games/agt/images/games/megahot100/icons/icon0.png" alt="" class="img-fluid">
                    <div class="img-card-side-content">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body" style="">
                        <div class="text-left">
                            <h4 class="mb-3 f-w-600">Welcome </h4>
                            <p class="text-muted mb-4">Welcome back, Please login <br>into a account</p>
                        </div>
                        <form method="post" id="auth_form" style="min-height: 70%" action="/login/login">
                            <div style="display:none" class="alert alert-danger" role="alert" id="error">

                            </div>
                            <div style="display:none" class="alert alert-success" role="alert" id="message">

                            </div>
                            <div class="">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i data-feather="user"></i></span>
                                    </div>
                                    <input type="text" name="login" class="form-control" placeholder="Login">
                                </div>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i data-feather="lock"></i></span>
                                    </div>
                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                </div>

                                <div class="input-group mb-4" style="display:none" id="telegram" >
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i data-feather="user"></i></span>
                                    </div>
                                    <input type="text" value="" name="telegram" class="form-control" placeholder="Telegram username"  />
                                </div>


                                <div class="input-group mb-4" style="display:none" id="code">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i data-feather="lock"></i></span>
                                    </div>
                                    <input type="text" value="" name="code" class="form-control"  placeholder="1234"  />
                                </div>





                            </div>
<!--                            <div class="text-left">
                                <a href="/htmlpages/telegramhelp.html" target="_blank"/>Telegram help </a>
                            </div>-->
                            <div class="text-right">
                                <button class="btn btn-primary mt-2">Sign in</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    $(function () {

        var options = {
            beforeSubmit: function () {
                //$('#error').show();
                //$('#error').html('Wait');
            },

            dataType: 'json',
            success: function (data) {

                $('#error').html('');
                $('#error').hide();
                $('#message').hide();


                if (data.refresh == '1') {
                    $('#message').html('Login');
                    $('#message').show();
                    window.location.replace('/terminal');
                    return;
                }
                
                
                
                if (data.needcode){
                    $('#code').show();
                }
                
                $('#error').show();
                $('#error').html(data.error);

            }
        };

        $('#auth_form').ajaxForm(options);
    });
</script>