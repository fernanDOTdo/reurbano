$(function(){
    // Todos os alertas são exibidos após o carregamento da página
    $('.alert').slideDown('slow');
    $('#topNL').slideDown('slow');
    // Ao clicar no "fechar" do alerta, remove o mesmo do DOM
    $(".closeAlert").click(function (e) {
        $(this).parent().parent().parent().slideUp('slow', function(){
            $(this).remove();
        });
        e.preventDefault();
        return false;
    });
    $("#closeNL").click(function (e) {
        $(this).parent().parent().parent().slideUp('slow', function(){
            $(this).remove();
            $.cookie('hideNL', '1', {
                expires: 1, 
                path: '/'
            });
        });
        e.preventDefault();
        return false;
    });
    // Todos os inputs do tipo text tem um class "idleField" por padrão e ganham um "focusField" quando o elemento ganha foco
    $('input[type="text"]').not('.focusField').addClass("idleField")
    .focus(function() {
        $(this).removeClass("idleField").addClass("focusField");
        if (this.value == this.defaultValue){
            this.value = '';
        }
        if(this.value != this.defaultValue){
            this.select();
        }
    })
    .blur(function() {
        if ($.trim(this.value) == ''){
            this.value = (this.defaultValue ? this.defaultValue : '');
            $(this).removeClass("focusField").addClass("idleField");
        }
    });
    // Todos os selects com class "chzn-select" são ransformados em "chosen" (plugin jquery)
    $("select.chzn-select").chosen();
    // Facebook (só funciona se tem um div com id "fb-root" na página
    var e = document.createElement('script');
    e.async = true;
    e.src = document.location.protocol + '//connect.facebook.net/pt_BR/all.js';
    $('#fb-root').append(e);
    $(".fbLoginBtn").click(function (e) {
        fblogin();
        e.preventDefault();
        return false;
    });
});