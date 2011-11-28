$(function(){
    $("#contact_quantity").blur(function(){
        var qtd = parseInt($(this).val());
        if(qtd < 1 || isNaN(qtd)){
            alert('Digite a quantidade de cupons.');
            $(this).val(1);
            alert();
            return $(this).focus();
        }
        var voucher = '';
        for (i = 0 ; i < qtd ; i++) {
            voucher = voucher + '<label for="contact_voucher'+i+'">Voucher '+(i+1)+'</label><input id="contact_voucher'+i+'" name="contact[voucher'+i+']" value="" type="file" required="required" />';
        }
        $('#contactVoucher').html(voucher);
    });
});