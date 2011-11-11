$(function(){
    $.tools.dateinput.localize("pt_BR",  {
       months:        'janeiro,fevereiro,março,abril,maio,junho,julho,agosto,' +
                            'setembro,outubro,novembro,dezembro',
       shortMonths:   'jan,fev,mar,abr,mai,jun,jul,ago,set,out,nov,dez',
       days:          'domingo,segunda,terça,quarta,quinta,sexta,sábado,domingo',
       shortDays:     'dom,seg,ter,qua,qui,sex,sáb'
    });
    $(".datepicker").dateinput({ 
            lang: 'pt_BR', 
            format: 'dd/mm/yyyy',
            offset: [0, 0]
    });
})