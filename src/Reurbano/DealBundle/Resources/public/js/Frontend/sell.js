$(function(){
    $("#sell_site").chosen().change(function(){
        $('#sell_cupom').val(null);
        $('.cupom').slideDown('fast', function(){$('#sell_cupom').focus();});
        var hidden = $('#sell_siteId');
        hidden.val($(this).val());
        
        $('#sell_cupom').autocomplete(ajaxPath + "?siteid=" + $(this).val());
    });
    $("#sell_cupom").result(function(event, data, formatted) {
        var hidden = $('#sell_cupomId');
        hidden.val(data[1]);
    });
});