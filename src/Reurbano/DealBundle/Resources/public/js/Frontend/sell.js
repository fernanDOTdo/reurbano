$(function(){
    $("#form_site").chosen().change(function(){
        $('#form_cupom').val(null);
        $('.cupom').slideDown('fast', function(){$('#form_cupom').focus();});
        var hidden = $('#form_siteId');
        hidden.val($(this).val());
        
        $('#form_cupom').autocomplete(ajaxPath + "?siteid=" + $(this).val());
    });
    $("#form_cupom").result(function(event, data, formatted) {
        var hidden = $('#form_cupomId');
        hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
    });
});