/* ::: Home - Everybody's talking about us
------------------------------------------ */

// Rotating reviews - press
window.setInterval(function(){
    $('.talking .press li:first-child').hide('fast');
    $('.talking .press li:first-child').clone().appendTo('.talking .press');
    setTimeout(function () {
        $('.talking .press li:first-child').remove();
        $('.talking .press li').show();
    }, 1000);
}, 5000); // Rotate every 5 seconds

// Rotating reviews - individual
window.setInterval(function(){
    $('.talking .individuals li:first-child').hide('fast');
    $('.talking .individuals li:first-child').clone().appendTo('.talking .individuals');
    setTimeout(function () {
        $('.talking .individuals li:first-child').remove();
        $('.talking .individuals li').show();
    }, 1000);
}, 7000); // Rotate every 7 seconds

// Rotating logos
window.setInterval(function(){
    $('.talking .logos li:first-child').hide('fast');
    $('.talking .logos li:first-child').clone().appendTo('.talking .logos');
    setTimeout(function () {
        $('.talking .logos li:first-child').remove();
        $('.talking .logos li').show();
    }, 1000);
}, 3000); // Rotate every 3 seconds

// Responsive hero sizing
$(function(){
    $('.hero').height($('.heroContent').outerHeight()+'px');
    $(window).resize(function(){
        $('.hero').height($('.heroContent').outerHeight()+'px');
    });
});

// Disable video on small screens
$(function(){
    if($(window).innerWidth() < 848){ // 53rem
        $('.hero video').remove();
    }
    $(window).resize(function(){
        if($(window).innerWidth() < 848){ // 53rem
            $('.hero video').remove();
        }
    });

});