<div class="pc-container">

    <div class="pcoded-content">
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-xl-6 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Users dashboard') ?></h2>
                        <form class="form-horizontal form-material" method="post" id="amount_opts">
<!--                            <div class="row">-->
<!--                                <h3 class="col-md-12">--><?php //echo __('Office balance') ?><!--</h3>-->
<!--                                <div class="col-md-12">-->
<!--                                    <h4 id="office_amount">--><?php //echo person::user()->my_office->amount; ?><!--</h4>-->
<!--                                </div>-->
<!--                            </div>-->
                            <hr/>
                            <div class="row">
                                <label class="col-md-12"><?php echo __('User\'s balance') ?></label>
                                <div class="col-md-12">
                                    <b id="comment"></b>
                                    <h4 id="current_balance">Choose user</h4>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <label for="add_money_login" class="col-md-12"><?php echo __('User ID') ?></label>
                                <div class="col-md-12">
                                    <b id="comment"></b>
                                    <input type="text" class="form-control form-control-line" name="login" id="add_money_login">
                                </div>
                            </div>
                            
                            <?php if(person::user()->my_office->tg_cashusers==1): ?>
                                 <div class="row">
                                    <label for="add_money_login" class="col-md-12"><?php echo __('Telegram name') ?></label>
                                    <div class="col-md-12">
                                        <input  id="sentTgValue"  type="text" class="form-control form-control-line" name="tgname" id="add_money_login">
                                        <button id="sendTg" type="button" value="Send" class="enter_amount_input btn btn-primary">Activate</button>
                                        <button id="sendTgInfo" type="button" value="Send" class="enter_amount_input btn btn-primary">Send account info</button>
                                        
                                    </div>
                                </div>
                            
                            <?php endif; ?>
                            

                            <div class="row">
                                <label class="col-md-12"><?php echo __('Сумма') ?></label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" id="amount_input" name="amount" value="0" >
                                </div>
                            </div>
                            <style>
                                #usepassword_input ~ #passwordblock {
                                    display: none;
                                }
                                #usepassword_input:checked ~ #passwordblock {
                                    display: block;
                                }
                            </style>
                            <div id="password" class="row" style="margin-top: 15px; display:none">
                                <label style="margin-left: 15px;" for="usepassword_input" class="">
                                    <?php echo __('Use password') ?>
                                </label>
                                <input type="checkbox" class="" style="
                                display: flex;
                                margin-left: 10px;
                                margin-top: 4px;
                                height: 12px;"
                                       id="usepassword_input" name="usepassword"  >
                                <br />
                                <div id="passwordblock">
                                    <label class="col-md-12"><?php echo __('Password') ?></label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control form-control-line" id="password_input" name="password"  >
                                    </div>
                                </div>
                            </div>

                            <div  class="row" style="display:none">
                                <label class="col-md-12"> 11</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control form-control-line" id="codeInput" name="code"  >
                                </div>
                            </div>


                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php foreach([10,50,100,200,500,1000] as $price): ?>
                                        <button type="button" value="<?php echo $price; ?>" class="enter_amount_input btn btn-primary"><?php echo $price; ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" value="pay" onclick="javascript: form.action = '<?php echo $dir ?>/cashusers/amountpay';" class="btn btn-success">
                                        <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<?php echo __('Пополнить') ?>
                                    </button>
                                    
                                    <div class="float-right" id="withdraw-form">
                                        <button id="withdraw"  value="withdraw" class="btn pull-right btn-danger">
                                            <i class="fa fa-minus-circle"></i>&nbsp;&nbsp;<?php echo __('Списать') ?>
                                        </button>
                                        <button id="withdrawAll"  value="withdraw"  class="btn pull-right btn-danger">
                                                    <i class="fa fa-times"></i>&nbsp;&nbsp;<?php echo __('Списать все') ?>
                                        </button>
                                    </div>
                                    
                                    <div class="float-right" id="confirm-form">
                                        <div class="float-right">
                                            <input type="text" id="wcode" class="form-control"/>
                                            <button id="confirm-code-withdraw" value="Confirm" class="btn pull-right btn-success">
                                                <i class="fa fa-minus-circle"></i>&nbsp;&nbsp;<?php echo __('Confirm') ?>
                                            </button>
                                            <button id="cancel-code-withdraw" value="Cancel" class="btn pull-right btn-primary">
                                                <i class="fa fa-minus-circle"></i>&nbsp;&nbsp;<?php echo __('Cancel') ?>
                                            </button>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>    

                            <div id="response_text_opts"></div>
                    
                    
                    
                    
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="page-title" style="text-align: center;"><?php echo __('Users') ?></h2>
                        <div class="table-responsive">
                            <div id="report-table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">

                                    <div class="input-group mb-3">
                                        <input id="searchInput" type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button id="searchButton" class="btn btn-outline-secondary" type="button">Search</button>
                                        </div>
                                    </div>




                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="button-select" class="table table-striped table-bordered nowrap" style="cursor: pointer" role="grid" aria-describedby="report-table_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="" tabindex="0" aria-controls="report-table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Icon: activate to sort column descending" style="">ID</th>
                                                    <?php if(person::user()->my_office->tg_cashusers==1): ?>
                                                            <th class="" tabindex="0" aria-controls="report-table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Icon: activate to sort column descending" style="">TG name</th>
                                                         <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($terminals as $terminal):  ?>
                                           
                                                
                                                    <tr role="row" class="odd" terminal_id="<?php echo $terminal->id; ?>" >
                                                        <td>
                                                            <?php echo $terminal->id; ?>
                                                        </td>
                                                        
                                                        <?php if(person::user()->my_office->tg_cashusers==1): ?>
                                                            <td>
                                                                <?php echo $terminal->tg_name; ?>
                                                            </td>
                                                         <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                                    
                                                    
                                            </tbody>
                                        </table>
                                        
                                        <button type="submit" value="pay" id="newUser" class="btn btn-success">
                                                <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<?php echo __('New User') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
</div>
<script src="/js/jquery.form.js"></script>
<script>
    
var useTG=<?php echo person::user()->my_office->tg_cashusers>0 ? 'true' : 'false'; ?>;

var operation;
var optionsPayOrW = {
    dataType: 'json',
    type: 'post',
    async: false,
    cache: false,
    beforeSubmit: function () {
        $('#response_text_opts').empty();

        $('button[value=' + operation + ']').prop('disabled', true);
        setTimeout(function () {
            $('button[value=' + operation + ']').prop('disabled', false);
        }, 1000);
    },
    success: function (data) {
        var message_text = '';

        if (data.error) {
            for (index in data.errors) {
                message_text += '<div style="text-align: center; color: red">' + data.errors[index] + '</div>';
            }
        } else {
            var result;
            var $val = parseFloat($('#amount_opts input[name=amount]').val());
            var $before = parseFloat($('#offamount').text());
            if (operation == "pay") {
                result = $before - $val;
            } else {
                result = $before + $val;
            }

            $('#offamount').text(result.toFixed(2));
            $('#amount_opts input[name=amount]').val(0);
            if ($('[terminal_id=' + ($('#add_money_login').val()) + ']')) {
                $('[terminal_id=' + ($('#add_money_login').val()) + '] td').eq(3).text(data.newamount);
                $('[terminal_id=' + ($('#add_money_login').val()) + ']').attr('comment', data.comment);
                $('[terminal_id=' + ($('#add_money_login').val()) + ']').click();
            }

            message_text += '<div style="text-align: center; color: green">' + data.text + '</div>';
        }

        $('#response_text_opts').append(message_text);
    },
    error: function (err) {
        var err_message = '<div style="text-align: center; color: red">' + err.responseText + '</div>';
        $('#response_text_opts').append(err_message);
    }
};

$('#amount_opts').ajaxForm(optionsPayOrW);

$('button[type=submit]').click(function () {
    operation = $(this).val();
});

function selectUser (userId) {

     var $v = userId ;

    $('#codeInput').val('');
    $.ajax({
        url: '/enter/cashusers/officebalance',
        success: function(d) {
            $('#office_amount').text(d);
        },
        error: function() {
            $('#office_amount').text('Error');
        }
    });

    $.ajax({
        url: '/enter/cashusers/userbalance',
        dataType: "json",
        data: {
            user_id: $v
        },
        success: function(d) {
            $('#current_balance').text(d.amount);
            $('#password_input').val('');
            $('#sentTgValue').val(d.tg_name);
            if (d.active){
                $('#sentTgValue').attr('disabled','disabled');
                $('#sendTg').hide();
                $('#sendTgInfo').show();
            }
            else{
                $('#sentTgValue').removeAttr('disabled');
                $('#sendTg').show();
                $('#sendTgInfo').hide();
            }


            console.log('start');
            var needRemove=true;
            $( '#button-select tr' ).each(function( index ) {

                if ($(this).attr('terminal_id')==$v ){
                    $(this).remove();
                    needRemove=false;
                }
            });
            if (needRemove){
                $( '#button-select :last' ).remove();
            }
            $('.green-color').removeClass('green-color');
            let s='<tr role="row" class="green-color odd" terminal_id="'+$v+'"><td>'+$v+'</td></tr>';
            $( '#button-select' ).prepend(s);


        },
        error: function() {
            $('#current_balance').text('Error');
        }
    });

    $("#amount_opts input[name=login]").val($v);
    $("#amount_opts input[name=amount]").val('');
    $("#amount_opts input[name=amount]").focus();

    $('#add_money_login').attr('data-original-title', 'Comment: ' + $('[terminal_id=' + $v + ']').attr('comment'));
//                                                $('#add_money_login').tooltip("show");

};


$('#button-select').on("click", "tr", function () {

    selectUser($(this).attr('terminal_id') );

});

$('#add_money_login')
        .tooltip({
            title: '',
            trigger: 'manual',
            container: '#amount_opts',
            placement: 'right'
        })
        .tooltip('hide');

$('.enter_amount_input').click(function () {
    $('#amount_input').val($(this).val());
});
/*
$('#button-select').DataTable({
    dom: 'Bfrtip',
    "searching": false,

//                                                paging: {
//
//                                                },
   "paging": false
//                                                bFilter: false,
//                                                bSort: false,
//                                                bInfo: false
});
                                            
     */
                                            
        
        
$('#sendTg').hide();
$('#sendTgInfo').hide();
$('#confirm-form').hide();
                                   
                                   
                                            
$('#newUser').on("click",function () {

    $.ajax({
        url: '/enter/cashusers/createuser',
        dataType: "json",
        success: function(d) {
            $('#button-select tbody').prepend(' <tr role="row" class="odd" terminal_id="'+d.login+'"><td>'+d.login+'</td></tr>')

            if (!useTG){

                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write(d.code);
                newWin.document.close();
            }
        },
        error: function() {
            alert('Error');
        }
    });



});


$('#sendTg').on("click",function () {

    $.ajax({
        url: '/enter/cashusers/sendcode',
        dataType: "json",
        type: "POST",
        data: {userid:$('#add_money_login').val() ,login:$('#sentTgValue').val() },
        success: function(d) {
            
            if (d.error==1){
                var err_message = '<div style="text-align: center; color: red">' + d.text + '</div>';
                $('#response_text_opts').empty();
                $('#response_text_opts').append(err_message);
                return null;
            }
            
            var message = '<div style="text-align: center; color: green">' + d.text + '</div>';
            $('#response_text_opts').empty();
            $('#response_text_opts').append(message);
            $('#sentTgValue').attr('disabled','disabled');
            
            $('#sendTg').hide();
            $('#sendTgInfo').show();

            
            
        },
        error: function() {
            alert('Error');
        }
    });



});



$('#sendTgInfo').on("click",function () {

    $.ajax({
        url: '/enter/cashusers/sendinfo',
        dataType: "json",
        type: "POST",
        data: {userid:$('#add_money_login').val() },
        success: function(d) {
            
            if (d.error==1){
                var err_message = '<div style="text-align: center; color: red">' + d.text + '</div>';
                $('#response_text_opts').empty();
                $('#response_text_opts').append(err_message);
                return null;
            }
            
            var message = '<div style="text-align: center; color: green">' + d.text + '</div>';
            $('#response_text_opts').empty();
            $('#response_text_opts').append(message);
            
            
            
        },
        error: function() {
            alert('Error');
        }
    });



});
   
   
//onclick="javascript: form.action = '<?php echo $dir ?>/cashusers/amountwithdraw';" 
//onclick="javascript: form.action = '<?php echo $dir ?>/cashusers/amountwithdraw?m=all';
        
        
        
function withdraw(amount,id,password,code){
    
    let urlAdd='';
    if (amount=='all'){
        urlAdd='?m=all';
        amount=0;
    }
      
    
      $.ajax({
        url: '/enter/cashusers/amountwithdraw'+urlAdd,
        dataType: "json",
        type: "POST",
        data: {login:id,amount:amount, password: password, code: code },
        success: function(d) {
            
            if (d.error==1){
                var err_message = '<div style="text-align: center; color: red">' + d.errors[0] + '</div>';
                $('#response_text_opts').empty();
                $('#response_text_opts').append(err_message);
                return null;
            }
            
            var message = '<div style="text-align: center; color: green">' + d.text + '</div>';
            $('#response_text_opts').empty();
            $('#response_text_opts').append(message);
            
            if (useTG){
                $('#wcode').val('');
                $('#withdraw-form').hide();
                $('#confirm-form').show();
            }
            else{
                $('#current_balance').text(d.newamount);
                $('#password_input').val('');
            }

            $('#codeInput').val('');
            
            
        },
        error: function() {
            alert('Error');
        }
    });   
    
}
        
$('#withdraw').on("click",function () { 
   
   let amount=$('#amount_input').val();
   let userId=$('#add_money_login').val();
   let password=$('#password_input').val();
   let code=$('#codeInput').val();

   withdraw(amount,userId,password,code);
   return false;
   
});


$('#withdrawAll').on("click",function () {

    let password=$('#password_input').val();
    let code=$('#codeInput').val();

   let userId=$('#add_money_login').val();
   withdraw('all',userId,password,code);
   return false;
   
});
    
    
$('#cancel-code-withdraw').on("click",function () { 
   
    $('#withdraw-form').show();
    $('#confirm-form').hide();
    $('#wcode').val('');
    $('#response_text_opts').empty();
    return false;
   
});


$('#confirm-code-withdraw').on("click",function () { 
   
   
    $.ajax({
        url: '/enter/cashusers/withdrawcode',
        dataType: "json",
        type: "POST",
        data: {login:$('#add_money_login').val() ,wcode:$('#wcode').val() },
        success: function(d) {
            
            if (d.error==1){
                var err_message = '<div style="text-align: center; color: red">' + d.errors[0] + '</div>';
                $('#response_text_opts').empty();
                $('#response_text_opts').append(err_message);
                return null;
            }
            
            var message = '<div style="text-align: center; color: green">' + d.text + '</div>';
            $('#response_text_opts').empty();
            $('#response_text_opts').append(message);
            
            
            
            $('#withdraw-form').show();
            $('#confirm-form').hide();
            $('#current_balance').text(d.newamount);
            $('#wcode').val('');
            
            
        },
        error: function() {
            alert('Error');
        }
    });      
   
    return false;
   
});


var externalInput = '';

document.addEventListener('keypress', function(e){

    let s = new Date();
    let time = s.getTime()
    var startInputTime = time;

    if(e.keyCode == 13)
    {
        console.log('enter');

        if((time-startInputTime)<=1000)
        {

            if(externalInput.substr(0,2)=='00')//ввод сканера штрихкодов - клиент
            {



                let h=externalInput
                h=h.substr(2,8);
                h=+h;

                selectUser( h );
                $('#codeInput').val(externalInput);
                $('#searchInput').val('');
                e.stopPropagation();
                e.preventDefault();
            }
        }

        externalInput = '';
        return false;
    }

    else if(e.which >= 48 && e.which <= 57 || externalInput.length == 0 || (externalInput.length == 1 && e.which == 32))
    {
        if((time-startInputTime)>1000)
        {
            externalInput = '';
            startInputTime = time;
        }
        externalInput += String.fromCharCode(e.which)
    }

}, true);


document.forms[0].onkeypress = function (a) {

    a = a || window.event;

    if (a.keyCode == 13 || a.which == 13)
        a.preventDefault ? a.preventDefault() : a.returnValue = false

};

$('#searchButton').bind('click',function(){

    var val=$('#searchInput').val();
    selectUser(val);
return false;
})



   
</script>


<style>

    .green-color{
        background: #a7ffa7 !important;
    }

</style>