$(document).ready(function() {
	function attachDropdown() {
		var $currencyDD = $('.currency-dropdown');

		$currencyDD.dropdown({
			customClass: 'header-black-dropdown'
		});

		$('select.condensed').dropdown({
			customClass: 'condensed'
		});

        $currencyDD.on('change', function(){
            $(this).parents('form').submit();
        });

	}

	attachDropdown();

	function attachSlider() {
		$('.testimonial-slider-wrap, .tweets-slider').slick({
			speed: 700,
			prevArrow: '<img src="/theme/nyc2/img/icons/arrow-left.png" class="slick-prev">',
			nextArrow: '<img src="/theme/nyc2/img/icons/arrow-right.png" class="slick-next">'
		});

		//Tour detail slider

		if ($.fn.royalSlider) {
			$('.tour-detail-slider').royalSlider({
                controlNavigation: 'thumbnails',
                autoScaleSlider: true,
                autoScaleSliderWidth: 960,
                autoScaleSliderHeight: 850,
                loop: false,
                imageScaleMode: 'fit-if-smaller',
                navigateByClick: true,
                numImagesToPreload:2,
                arrowsNav:true,
                arrowsNavAutoHide: true,
                arrowsNavHideOnTouch: true,
                keyboardNavEnabled: true,
                fadeinLoadedSlide: true,
                globalCaption: true,
                globalCaptionInside: true,
                thumbs: {
                    appendSpan: true,
                    firstMargin: true,
                    paddingBottom: 4
                }
            });
		}
	}

	attachSlider();

	function mobileNavPosition() {
		var $hiddenMenu = $('.main-menu');
		var $toggler = $('.reveal-menu');
		var top = $toggler.offset().top + $toggler.outerHeight() - 39;
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

	function attachGoogleMap() {
		var map;
		var LatLng = {lat: initValues.latitude*1, lng: initValues.longitude*1};
		var markerIcon = '/theme/nyc2/img/icons/map-marker.png';

		if ( $('#gmap').length ) {
			map = new google.maps.Map(document.getElementById('gmap'), {
				center: LatLng,
				zoom: 12
			});

			var marker = new google.maps.Marker({
			    position: LatLng,
			    map: map,
			    title: ':-)',
			    icon: markerIcon
			});
		}
	}

	attachGoogleMap();

	function scrollToTarget() {
		var $toggler = $('[data-scroll-toggler]');
	    var $target = $('[data-scroll-target]');

	    $toggler.click(function(e) {
	    	var attr = $(this).attr('data-scroll-toggler');
	    	var target = $('[data-scroll-target=' + attr + ']');
	    	
	    	$('html, body').animate({
	    		scrollTop: target.offset().top + 1
	    	}, 400);
	    });
	}

	scrollToTarget();

	function closeFlashMessage(){
		$('.flashMessage .dismiss').click(function(){
			$(this).parent().slideUp();
		})
	}

	closeFlashMessage();

	function realignBookTour() {
		var w = $(window).width();
		var $bookSidebar = $('aside.tour-book');
		var $parent = $('.book-tour-mobile');

		if (w <= 992) {
			$parent.append($bookSidebar);
		}
	}

	realignBookTour();

	//fix tour page hero size to be 4:6
	$(window).resize(function() {
		var width = $('.event-detail-hero').width();
		$('.event-detail-hero').stop(1,1).animate({'height':((width * 4) / 6) + 'px'},100);
	});

	$(window).trigger('resize');

	function firefoxFixes() {
		if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
			$('.tour-detail-content').addClass('ff-fix');
		}
	}

	firefoxFixes();
});