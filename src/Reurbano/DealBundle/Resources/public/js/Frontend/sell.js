$(function(){
    function formatResult(row) {
		return row[1].replace(/(<.+?>)/gi, '');
	}
        function formatItem(row) {
		return row[0] +  row[1] ;
	}
    $("#sell_site").chosen().change(function(){
        $('#sell_cupom').val(null);
        $('#sell_cupomId').val('');
        $("#sellContinue").hide();
        $('.cupom').slideDown('fast', function(){$('#sell_cupom').focus();});
        var hidden = $('#sell_siteId');
        hidden.val($(this).val());
        
        $('#sell_cupom').autocomplete(ajaxPath + "?siteid=" + $(this).val(),{formatResult: formatResult,formatItem: formatItem});
    });
    $("#sell_cupom").keyup(function(){
        $("#sellContinue").hide();
        $('#sell_cupomId').val('');
    });
    $("#sell_cupom").result(function(event, data, formatted) {
         $("#sellContinue").hide();
        var hidden = $('#sell_cupomId');
        hidden.val(data[3]);
        if(hidden.val()!=""){
            $("#sellContinue").show();
        }else{
            $("#sellContinue").hide();
        }
    });
});