$(document).ready(function(){
    $("#deal_source").change(function(){
        var idSource = $(this).val();
        $.post( ajaxPath, {id: idSource}, function(data) {
            if(data.success == true){
                $("#deal_label").val(data.title);
                $("#deal_price").val(data.price);
                $('#deal_price').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
                });
            }
        },'json');
    });
    $('#deal_price').priceFormat({
	prefix: '',
	centsSeparator: ',',
        thousandsSeparator: '.'
    }); 
});