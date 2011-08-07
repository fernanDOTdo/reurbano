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
        $(".chzn-select").chosen();
});