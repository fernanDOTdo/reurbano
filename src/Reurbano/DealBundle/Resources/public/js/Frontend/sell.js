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
        $('.cupom').slideDown('fast', function(){
            $('#sell_cupom').focus();
        });
        var hidden = $('#sell_siteId');
        hidden.val($(this).val());
        
        $('#sell_cupom').autocomplete(ajaxPath + "?siteid=" + $(this).val(),{
            formatResult: formatResult,
            formatItem: formatItem
        });
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
    $("#deal_quantity").blur(function(){
        var qtd = parseInt($(this).val());
        if(qtd < 1 || isNaN(qtd)){
            alert('Digite a quantidade de cupons.');
            $(this).val(1);
            alert();
            return $(this).focus();
        }
        var voucher = '';
        for (i = 0 ; i < qtd ; i++) {
            voucher = voucher + '<label for="deal_voucher'+i+'">Voucher '+(i+1)+'</label><input id="deal_voucher'+i+'" name="deal[voucher'+i+']" value="" type="file" required="required" />';
        }
        $('#sellVoucher').html(voucher);
    });
    $("#sellDetails").submit(function (e) { 
        var vouchers = $('input:file');
        var verified = new Array();
        $(vouchers).each(function(index) {
            var ext = $(this).val().substr( ($(this).val().lastIndexOf('.') +1) ).toLowerCase();
            var extAllowed = [ 'jpg', 'jpeg', 'gif', 'png', 'pdf' ];
            if($.inArray(ext, extAllowed) == -1){
                alert('Vouchers devem ser na extensão JPG, JPEG, PNG, GIF ou PDF.\nSubstitua o voucher '+$(this).val()+' e tente novamente.');
                e.preventDefault();
                return false;
            }
            if($.inArray($(this).val(), verified) != -1){
                alert('Não é possível enviar o mesmo voucher em 2 campos diferentes. Selecione outro voucher.');
                e.preventDefault();
                return false;
            }else if($(this).val() == ''){
                if(vouchers.length == 1){
                    alert('É preciso enviar 1 voucher.');
                }else{
                    alert('É preciso enviar '+vouchers.length+' vouchers.');
                }
                e.preventDefault();
                return false;
            }else{
                verified[index] = $(this).val();
            }
        });
        return true;
    });

});