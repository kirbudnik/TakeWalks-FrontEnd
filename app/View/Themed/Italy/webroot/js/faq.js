$(function(){
    //accordion
    $('.accordion .title').click(function() {
        //check if already selected
        var isSelected = $(this).parent().hasClass('selected');

        //deselect all
        $('.accordion.selected')
            .removeClass('selected')
            .find('.fa') //change minus to plus
            .removeClass('fa-minus')
            .addClass('fa-plus');


        //if not selected then select
        if (isSelected == false) {
            $(this).parent()
                .addClass('selected')
                .find('.fa') //change plus to minus
                .removeClass('fa-plus')
                .addClass('fa-minus');
        }


    });


});