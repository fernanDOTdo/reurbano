$(function(){
        $('.alert').slideDown('slow');
        $('#topNL').slideDown('slow');
        $(".closeAlert").click(function (e) {
            $(this).parent().parent().parent().slideUp('slow', function(){$(this).remove();});
            e.preventDefault();
        return false;
        });
        $("#closeNL").click(function (e) {
            $(this).parent().parent().parent().slideUp('slow', function(){
                $(this).remove();
                $.cookie('hideNL', '1', { expires: 1 });
            });
            e.preventDefault();
        return false;
        });
});