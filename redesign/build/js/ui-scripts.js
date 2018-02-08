$(document).ready(function() {
	function attachDropdown() {
		var $currencyDD = $('.currency-dropdown');

		$currencyDD.dropdown({
			customClass: 'header-black-dropdown'
		});

		$('select.condensed').dropdown({
			customClass: 'condensed'
		});
	}

	attachDropdown();

	function attachSlider() {
		$('.testimonial-slider-wrap, .tweets-slider').slick({
			speed: 700,
			prevArrow: '<img src="img/icons/arrow-left.png" class="slick-prev">',
			nextArrow: '<img src="img/icons/arrow-right.png" class="slick-next">'
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
		var LatLng = {lat: 40.712784, lng: -74.005941};
		var markerIcon = 'img/icons/map-marker.png';

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

	function attachDatepicker() {
		$('.datepick-input').datepicker({
			minDate: 4
		});
	}

	attachDatepicker();

	function homeSlider() {
		var $slide = $('.slide-image');
		var count = $slide.length;
		var $current = $('.slide-image.active');
		var currentIndex = $current.index();

		var $caption = $('.slide-caption');

		function nextSlide() {
			$current = $('.slide-image.active');
			currentIndex == count - 1 ? currentIndex = 0 : currentIndex++;
			$current.removeClass('active');
			$slide.eq(currentIndex).addClass('active');
			$caption.removeClass('active');
			$caption.eq(currentIndex).addClass('active');
		}

		function prevSlide() {
			$current = $('.slide-image.active');
			currentIndex == 0 ? currentIndex = 2 : currentIndex--;
			$current.removeClass('active');
			$slide.eq(currentIndex).addClass('active');
			$caption.removeClass('active');
			$caption.eq(currentIndex).addClass('active');
		}

		var autoRotate = setInterval(nextSlide, 5000);

		function clickNext() {
			nextSlide();
			clearInterval(autoRotate);
			autoRotate = setInterval(nextSlide, 5000);
		}

		function clickPrev() {
			prevSlide();
			clearInterval(autoRotate);
			autoRotate = setInterval(nextSlide, 5000);
		}

		$(document).on('click', '.slider-control.right', clickNext);
		$(document).on('click', '.slider-control.left', clickPrev);

		// $('.home-page .hero').on('touchstart', handleTouchStart, false);        
		// $('.home-page .hero').on('touchmove', handleTouchMove, false);

		var xDown = null;                                                        
		var yDown = null;                                                        

		function handleTouchStart(evt) {                                         
		    xDown = evt.touches[0].clientX;                                      
		    yDown = evt.touches[0].clientY;                                      
		};                                                

		function handleTouchMove(evt) {
		    if ( ! xDown || ! yDown ) {
		        return;
		    }

		    var xUp = evt.touches[0].clientX;                                    
		    var yUp = evt.touches[0].clientY;

		    var xDiff = xDown - xUp;
		    var yDiff = yDown - yUp;

		    if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {
		        if ( xDiff > 0 ) {
		            clickPrev();
		        } else {
		           	clickNext();
		        }                       
		    }

		    /* reset values */
		    xDown = null;
		    yDown = null;                                             
		};
	}

	homeSlider();

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
});