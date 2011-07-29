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

$('#Userformedit_email').blur(function(e) {
    
    var email = $(this).val();
    var id = $("#Userformedit_id").val();
    if(email!=""){
        $.post(ajaxPath2,{email:email,id:id}, function(data) {
            if(data==0){
                alert(emailExiste);
                $('#Userform_email').val('');
            }
    
        })
    }
})