// perform JavaScript after the document is scriptable.
$(function() {
	// setup ul.tabs to work as tabs for each div directly under div.panes
	$("ul.tabs").tabs("div.panes > div");
        $('.voucher').click(function(){
            alert("Você não pode alterar a oferta pois ela está em processo de pagamento.");
        });
});