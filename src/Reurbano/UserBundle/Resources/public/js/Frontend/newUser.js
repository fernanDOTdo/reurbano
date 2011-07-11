$('#Userform_email').blur(function(e) {
    
    var email = $(this).val();
    if(email!=""){
        $.post(ajaxPath,{email:email}, function(data) {
            if(data==0){
                alert(emailExiste);
                $('#Userform_email').val('');
            }
    
        })
    }
})