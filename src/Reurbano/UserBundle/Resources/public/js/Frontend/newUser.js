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
});
$('#Userform_cpf').blur(function(e) {
    var cpf = $(this).val();
    if(cpf!=""){
        $.post(ajaxCPF,{cpf:cpf}, function(data) {
            if(data==0){
                alert("Já existe um cadastro com este CPF. Digite outro.");
                $('#Userform_cpf').val('');
            }
    
        })
    }
});
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
});
jQuery(function($){
    $('.cpfMask').mask("999.999.999-99", {placeholder: " "});
});