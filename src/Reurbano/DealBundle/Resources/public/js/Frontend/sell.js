var acOptionsImv = {
    dataType: 'json', // this parameter is currently unused
    extraParams: {
        format: 'json' // pass the required context to the Zend Controller
    },
    parse: function(data) {
        var parsed = [];
        for (var i = 0; i < data.length; i++) {
            parsed[parsed.length] = {
                data: data[i],
                value: data[i].titulo,
                result: data[i].titulo
            };
        }
    }
}
$('#form_site').autocomplete(ajaxPath,acOptionsImv).result(function(event, item){
    var site = $(this).val();
    var cupom = $('#form_cupom').val();
});