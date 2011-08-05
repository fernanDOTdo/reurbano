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
var emails = [
	{ name: "Peter Pan", to: "peter@pan.de" },
	{ name: "Molly", to: "molly@yahoo.com" },
	{ name: "Forneria Marconi", to: "live@japan.jp" },
	{ name: "Master <em>Sync</em>", to: "205bw@samsung.com" },
	{ name: "Dr. <strong>Tech</strong> de Log", to: "g15@logitech.com" },
	{ name: "Don Corleone", to: "don@vegas.com" },
	{ name: "Mc Chick", to: "info@donalds.org" },
	{ name: "Donnie Darko", to: "dd@timeshift.info" },
	{ name: "Quake The Net", to: "webmaster@quakenet.org" },
	{ name: "Dr. Write", to: "write@writable.com" }
];
/*$("#suggest13").autocomplete(emails, {
		minChars: 0,
		width: 310,
		matchContains: "word",
		autoFill: false,
		formatItem: function(row, i, max) {
			return i + "/" + max + ": \"" + row.name + "\" [" + row.to + "]";
		},
		formatMatch: function(row, i, max) {
			return row.name + " " + row.to;
		},
		formatResult: function(row) {
			return row.to;
		}
	});*/
var months = [
        { "titulo": "Peixe Urbano" , id: "peixe"},
        { "titulo": "Uoshitu", id: "uoshi"},
        { "titulo": "Craudomira", id: "craudo"},
        { "titulo": "Felizberta", id: "feliz"}
];
$("#form_site").autocomplete(ajaxPath, {
                dataType: 'json', // this parameter is currently unused
                extraParams: {
                    format: 'json' // pass the required context to the Zend Controller
                },
		minChars: 0,
		width: 310,
		matchContains: "word",
		autoFill: false,
		formatItem: function(row, i, max) {
			return i + "/" + max + ": \"" + row.titulo;
		},
		formatMatch: function(row, i, max) {
			return row.titulo;
		},
		formatResult: function(row) {
			return row.titulo;
		}
	});
/*$('#form_site').autocomplete(ajaxPath,acOptionsImv).result(function(event, item){
    var site = $(this).val();
    var cupom = $('#form_cupom').val();
});*/