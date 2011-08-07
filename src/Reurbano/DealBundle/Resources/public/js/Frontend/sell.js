$(function(){
    $('#form_site').autocomplete(ajaxPath);
    $("#form_site").result(function(event, data, formatted) {
        $('.cupom').slideDown();
        var hidden = $('#form_siteId');
        hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
    });
});