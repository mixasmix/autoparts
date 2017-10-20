$('.user_data_input').focusout(function(){
    var value= $(this).val();
    var paramname=$(this).attr('name');
    $.ajax({
        type:"POST",
        url:'/user/setting',
        data:'action=edituserinfo&lk_param='+paramname+'&value='+value,
        
    });
});

$('#confirm_password').focusout(function(){
    $('#confirm_password').popover('hide');
    if($('#new_password').val()!=$('#confirm_password').val()){
        $('#confirm_password').popover({content:$('span.pass_not_ident').text()});
        $('#confirm_password').popover('show');
    }else{
        $('div.newpass_btn').removeAttr('disabled');
        $('div.newpass_btn').click(function(){
          
            var new_pass=$('#new_password').val();
            var conf_pass=$('#confirm_password').val();
            $.ajax({
                type: "POST",
                url: '/user/setting',
                data: 'action=replase_password&new_pass=' + new_pass + '&conf_pass=' + conf_pass,
                success: function (msg) {
                    console.log(msg);
                    if(msg==true){
                        $('div.newpass_btn').hide();
                        $('.pass_confirm').removeClass('hide');
                    }else{
                        $('div.newpass_btn').hide();
                        $('.pass_error').removeClass('hide');
                    }
                }
            });
        });
    }
});