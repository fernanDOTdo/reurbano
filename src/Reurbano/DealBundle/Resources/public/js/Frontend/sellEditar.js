$(function(){
    function addVoucher(){
        var qtd = parseInt($("#deal_quantity").val());
        if(qtd < 1 || isNaN(qtd)){
            alert('Digite a quantidade de cupons.');
            $("#deal_quantity").val(1);
            alert();
            return $("#deal_quantity").focus();
        }
        var voucher = '';
        for (i = 0 ; i < qtd ; i++) {
            voucher = voucher + '<label for="deal_voucher'+i+'">Voucher '+(i+1)+'</label><input id="deal_voucher'+i+'" name="deal[voucher'+i+']" value="" type="file" required="required" />';
        }
        $('#sellVoucher').html(voucher);
    }
    addVoucher();
});