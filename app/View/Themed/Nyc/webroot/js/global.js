Date.prototype.yyyymmdd = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString();
    var dd = this.getDate().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]);
};
Date.prototype.mdy = function() {
    var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    return monthNames[this.getMonth()] + ' ' + this.getDate() + ', ' + this.getFullYear();
}

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

$(document).on('click', function(e) {
    if (!$(e.target).closest('.viewToursNew').length) $('.viewToursNew').removeClass('expanded');
});
$(function() {
    $('.register').click(function() {
        $('.signIn, .modal-signIn').removeClass('expanded');
        $('.cart, .modal-cartItems').removeClass('expanded');
        $('.register, .modal-register').toggleClass('expanded');
    });
    $('.signIn').click(function() {
        $('.register, .modal-register').removeClass('expanded');
        $('.cart, .modal-cartItems').removeClass('expanded');
        $('.signIn, .modal-signIn').toggleClass('expanded');
    });
    $('.cart').click(function() {
        $('.signin, .modal-signIn').removeClass('expanded');
        $('.register, .modal-register').removeClass('expanded');
        $('.cart, .modal-cartItems').toggleClass('expanded');
    });
    $('.modal-cartItems .close').click(function() {
        $(this).next().show();
    });
    $('.modal-cartItems .cancel').click(function() {
        $(this).parent().hide();
    });

    if (enableCongratulationsModal()){
        $('[data-modal-target=congratulationsModal]').addClass('active').fadeIn(100);
        $('html, body').animate({ scrollTop: 0 }, 350);
        modalsGlobal();
    }

});
$(document).on('click', function(e) {
    if (!$(e.target).closest('.signIn').length && !$(e.target).closest('.modal-signIn').length) $('.signIn, .modal-signIn').removeClass('expanded');
    if (!$(e.target).closest('.register').length && !$(e.target).closest('.modal-register').length) $('.register, .modal-register').removeClass('expanded');
    if (!$(e.target).closest('.cart').length && !$(e.target).closest('.modal-cartItems').length) $('.cart, .modal-cartItems').removeClass('expanded');
});
$(function() {
    $('.menu').click(function() {
        $(this).toggleClass('expanded');
        $(this).siblings('.admin, .pages').toggleClass('expanded');
    });
});
$(document).on('click', function(e) {
    if (!$(e.target).closest('.menu').length && !$(e.target).closest('.admin').length && !$(e.target).closest('.pages').length) $('.menu, .admin, .pages').removeClass('expanded');
});
$(document).ready(function($) {
    $('.headerSlider').royalSlider({
        arrowsNav: true,
        loop: true,
        allowCSS3: false,
        keyboardNavEnabled: true,
        controlsInside: false,
        controlNavigation: 'none',
        arrowsNavAutoHide: false,
        navigateByClick: true,
        startSlideId: 0,
        transitionType: 'move',
        transitionSpeed: 400,
        slidesSpacing: 0,
        visibleNearby: {
            enabled: true,
            centerArea: 0.25,
            center: true,
            breakpoint: 1072,
            breakpointCenterArea: 0.4
        }
    });
    
    //currency switch
    $('nav a form select').change(function(){
        $(this).parent().submit();
    });

    function mobileNavPosition() {
        var $hiddenMenu = $('.main-menu');
        var $toggler = $('.reveal-menu');
        var top = $toggler.offset().top + $toggler.outerHeight();
        $hiddenMenu.css('top', top);
    }

    mobileNavPosition();

    function toggleShow() {
        var $toggler = $('[data-hideshow-toggler]');
        var $target = $('[data-hideshow-target]');

        $toggler.click(function(e) {
            var attr = $(this).attr('data-hideshow-toggler');
            var target = $('[data-hideshow-target=' + attr + ']');
            $(this).toggleClass('active');

            target.is(':visible') ? target.hide() : target.css('display', 'flex');
            target.css('top');
            target.toggleClass('active');


            if (attr = 'menu') {
                $('.slider-control').toggleClass('hidden');
            }
        });
    }

    toggleShow();

    $('.currency-dropdown').change(function(event){
        $(this).parents('form').submit();
    });
    
});
$(function() {
    $('.flashMessage a').click(function() {
        $('.flashMessage').hide();
    });
});