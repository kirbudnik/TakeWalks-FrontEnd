//Get Base URL
if (!location.origin)
    location.origin = location.protocol + "//" + location.host;

function enableCongratulationsModal (){
    return ($('#congratulationsModal').val() !== undefined);
}


function modalsGlobal() {
    var target = '[data-modal-target]';
    var toggler = $('[data-modal-toggler]');
    var close = $('[data-modal-close]');

    toggler.click(function() {
        var modalTarget = $(this).attr('data-modal-toggler');
        $('[data-modal-target='+modalTarget+']').addClass('active').fadeIn(300);
        $('html, body').animate({
            scrollTop: 0
        }, 350);
    });

    close.click(function(event) {
        closeModalGlobal();
    });

    function closeModalGlobal() {
        $(target).removeClass('active').fadeOut(300);
    }
}

$(function(){
    //newsletter script
    $('#italy-newsletter').submit(function(){

        $.post(location.origin + '/mail/subscribe', $(this).serialize() ,function(data){
            var response = $.parseJSON(data);
            console.log('response.status',response.status);
            if(response.status == 'error'){
                $('#italy-newsletter .error-message').html(response.message).stop(1,1).fadeIn(300);
            }else{
                //success
                $('#italy-newsletter').html(
                    $('<div />',{'class':'subscribed'}).html('Usted ha sido suscrito!')
                );
            }
        });

        return false;
    });

    if (enableCongratulationsModal()){
        $('[data-modal-target=congratulationsModal]').addClass('active').fadeIn(100);
        $('html, body').animate({ scrollTop: 0 }, 350);
        modalsGlobal();
    }

});

$(document).ready(function() {
    function newTestimonialSlider() {
        var $slider = $('#new-testimonial-slider');
        var $taSlider = $('#home-tripadvisor-slider');

        $slider.slick({
            arrows: false,
            dots: true,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 10000
        });

        $taSlider.slick({
            arrows: false,
            dots: true,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 10000
        });
    }

    newTestimonialSlider();
});
