$(function(){
    // the portuguese localization
    $.tools.dateinput.localize("pt_BR",  {
       months:        'janeiro,fevereiro,março,abril,maio,junho,julho,agosto,' +
                            'setembro,outubro,novembro,dezembro',
       shortMonths:   'jan,fev,mar,abr,mai,jun,jul,ago,set,out,nov,dez',
       days:          'domingo,segunda,terça,quarta,quinta,sexta,sábado,domingo',
       shortDays:     'dom,seg,ter,qua,qui,sex,sáb'
    });
    if($('#dealEdit_source_expiresAt').val() == ''){
        var dateInput = new Date;
        var dateValue = new Date(dateInput.getFullYear(), dateInput.getMonth() + 1, dateInput.getDay() + 2);
    }else{
        var dateInput = $('#dealEdit_source_expiresAt').val().split('/');
        var dateValue = new Date(dateInput[2], dateInput[1]-1, dateInput[0]);
    }
    $(".datepicker").dateinput({ 
            lang: 'pt_BR', 
            format: 'dd/mm/yyyy',
            offset: [0, 0],
            value: dateValue
    });
    $("#sellEdit").submit(function (e) { 
        if($('#dealEdit_source_expiresAt').val() == ''){
                alert('Selecione a data de validade da sua oferta.');
                $('#dealEdit_source_expiresAt').focus();
                e.preventDefault();
                return false;
        }
        if($('#dealEdit_price').val() == '' || parseInt($('#dealEdit_price').val()) == 0){
                alert('Digite o valor desejado.');
                $('#dealEdit_price').focus();
                e.preventDefault();
                return false;
        }
    });
});