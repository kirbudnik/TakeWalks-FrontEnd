function applyDatePicker(selector) {
    var $sel = $(selector);
    $sel.datepick({
        changeMonth: false,
        prevText: '<i class="icon icon-arrow_left"></i>',
        nextText: '<i class="icon icon-arrow_left"></i>',
        showOtherMonths: true,
        selectOtherMonths: true,
        dayNamesMin: ['Su', 'M', 'Tu', 'W', 'Th', 'F', 'Sa'],
        startDate: '0d',
        minDate: 0,
        useMouseWheel: false,
        onSelect: function() {
            $(this).closest('.input-icon').addClass('valid').removeClass('invalid');
            $(this).closest('.input-icon').find('.icon-attention').remove();
            $(this).closest('.input-icon').append('<i class="icon icon-checkmark_circled"></i>');
            $(this).closest('.item').removeClass('active').addClass('completed');
            $('[data-section="selectTime"]').addClass('active');
        }
    });
}

$(document).ready(function() {
  if ($.fn.select2) {
    $('.single-select').select2({
      minimumResultsForSearch: -1
    });
  }

  $(window).scroll(function() {
    var top = $(window).scrollTop();
    var $tofix = $('.city-tours-header, .city-nav-tabs:not(.my-account-tabs)');
    var w = $(window).width();

    if (w >= 1024) {
      if ($tofix.length) {
        if (top > $tofix.offset().top && !$tofix.hasClass('compact')) {
          $tofix.addClass('compact')
        }

        if (top < 140) {
          $tofix.removeClass('compact');
          top < $tofix.offset().top;
        }
      }
    }
  });



  function signUp() {

    var signup = $('.signup-box');

    var signupButton = signup.find('#signup-button');

    signupButton.click(function() {

    $('.signup-box .error-message').hide() ;

    var signupName =  $('#signup-name').val();
    var signupEmail =  $('#signup-email').val();


$('body').css({cursor:'wait'});

$.ajax({ data: { 'signupName': signupName, 'signupEmail': signupEmail }, url: '/user/signup', method: 'post' }
                ).done(function(response) {

                    if (response.success) {

                    $('body').css({cursor:'default'});
                    $('.signup-box .error-message').html('Sign up Successful!').stop(1,1).fadeIn(300);


                  } else {

                    console.log('Sign up failed');
                    $('body').css({cursor:'default'});
                    $('.signup-box .error-message').html('Sign up failed').stop(1,1).fadeIn(300);

                  }

                }).fail(function(response){
                    
                });

    }); // end click
  
    
  }

  signUp();



  function signUpFooter() {

    console.log('signUpFooter');

    var signup = $('.footer-subscribe');

    var signupButton = signup.find('#footer-signup-button');

    signupButton.click(function() {

      console.log('signupButton clicked');

    $('.signup-box .error-message').hide() ;

    var signupEmail =  $('#footer-signup-email').val();

     console.log('signupEmail '+signupEmail);

      //$('body').css({cursor:'wait'});

      $.ajax({ data: { 'signupEmail': signupEmail }, url: '/user/signup', method: 'post' }
                      ).done(function(response) {

                          if (response.success) {

                         // $('body').css({cursor:'default'});

                         // $('.signup-box .error-message').html('Sign up Successful!').stop(1,1).fadeIn(300);
                         signupButton.attr('value', 'Thanks!');

                        } else {

                          console.log('Sign up failed');

                         // $('body').css({cursor:'default'});

                        //  $('.signup-box .error-message').html('Sign up failed').stop(1,1).fadeIn(300);
                        signupButton.attr('value', 'Failed!');


                        }

                      }).fail(function(response){
                          
                      });

          }); // end click
  
    
  }

  signUpFooter();


  function scrollSpy() {
    var lastId,
        topMenu = $('.city-tour-tabs, .city-nav-tabs:not(.my-account-tabs)'),
        topMenuHeight = topMenu.outerHeight()+15,
        // All list items
        menuItems = topMenu.find("a"),
        // Anchors corresponding to menu items
        scrollItems = menuItems.map(function(){
          var item = $($(this).attr("href"));
          if (item.length) { return item; }
        });

    // Bind to scroll
    $(window).scroll(function() {
      // Get container scroll position
      var fromTop = $(this).scrollTop()+topMenuHeight;

      // Get id of current scroll item
      var cur = scrollItems.map(function() {
        if ($(this).offset().top < fromTop)
          return this;
      });
      // Get the id of the current element
      cur = cur[cur.length-1];
      var id = cur && cur.length ? cur[0].id : "";

      if (lastId !== id) {
          lastId = id;
          // Set/remove active class
          menuItems.removeClass('active').filter("[href='#"+id+"']").addClass("active");
      }
    });
  }

  scrollSpy();

  function scrollToItem() {
    var $a = $('.city-tour-tabs .tab-item');
    $a.click(function() {
      var id = $(this).attr('href');
      var headerHeight;
      headerHeight = 0;

      if (!$('.city-tours-header').hasClass('compact')) {
        headerHeight = $('.city-tours-header').outerHeight();
      } else {
        headerHeight = 0;
      }

      var top;

      if ($(window).width() >= 1024) {
        top = $(id).offset().top - headerHeight;
      } else {
        top = $(id).offset().top + 60;
      }

      if (id == '#mostPopular') {
        top = 0;

        if ($(window).scrollTop() + 200 < $(id).offset().top + $(id).outerHeight()) {
          return false
        }
      }

      $('html, body').animate({
        scrollTop: top - 60
      }, 400)
    });

    $('[data-scroll-toggler]').click(function() {
      var attr = $(this).attr('data-scroll-toggler');

      if (!$(this).hasClass('city-tour-tab')) {
        $('html, body').animate({
          scrollTop: $('[data-scroll-target=' + attr + ']').offset().top
        }, 400)
      } else {
        $('html, body').animate({
          scrollTop: $('[data-scroll-target=' + attr + ']').offset().top - 50
        }, 400)
      }
    });
  }

  scrollToItem();


  function tourDetailSlider() {
    $('#tourImageSlider').lightSlider({
      gallery: true,
      item: 1,
      loop:true,
      slideMargin: 0,
      thumbMargin: 10,
      thumbItem: 4
    });

      applyDatePicker('.foo-datepick');

    // var $sel = $('.foo-datepick');
    // $sel.datepick({
    //   changeMonth: false,
    //   prevText: '<i class="icon icon-arrow_left"></i>',
    //   nextText: '<i class="icon icon-arrow_left"></i>',
    //   showOtherMonths: true,
    //   selectOtherMonths: true,
    //   dayNamesMin: ['Su', 'M', 'Tu', 'W', 'Th', 'F', 'Sa'],
    //   startDate: '0d',
    //   minDate: 0,
    //   useMouseWheel: false,
    //   onSelect: function() {
    //     $(this).closest('.input-icon').addClass('valid').removeClass('invalid');
    //     $(this).closest('.input-icon').find('.icon-attention').remove();
    //     $(this).closest('.input-icon').append('<i class="icon icon-checkmark_circled"></i>');
    //     $(this).closest('.item').removeClass('active').addClass('completed');
    //     $('[data-section="selectTime"]').addClass('active');
    //   }
    // });
  }

  tourDetailSlider();

  function toggleModal(e) {
    var $toggler = $('[data-modal-toggler]');
    var $target = $('[data-modal-target]');
    var $close = $('[data-modal-close]');

    $toggler.click(function(e) {
      e.stopPropagation();
      e.preventDefault();
      var attr = $(this).attr('data-modal-toggler');
      var target = $('[data-modal-target=' + attr + ']');
      $('.modal-overlay').addClass('active');
      target.show();
      target.css('top');
      target.addClass('active');
      $('body').addClass('no-scroll');
    });

    $close.click(function() {
      $('.modal-overlay').removeClass('active');
      $target.removeClass('active');

      setTimeout(function() {
        $target.hide();
      }, 200);
        $('body').removeClass('no-scroll');
    });
  }

toggleModal();

  function toggleFaq() {
    $('.faq-question-title').click(function() {
      var $tar = $(this).parent().find('.faq-question-content');
      $tar.toggleClass('active');
      $(this).parent().toggleClass('active');
    });
  }

  toggleFaq();

  function toggleAskQuestion() {
    $(window).scroll(function() {
      if ($('.chat-bubble').length) {
        if ($(window).scrollTop() +  890 >= $('.footer-copyright').offset().top) {
          $('.chat-bubble').addClass('bubble-hidden');
        } else if ($(window).scrollTop() + 890 < ($('.footer-copyright').offset().top) && $('.chat-bubble').hasClass('bubble-hidden')) {
          $('.chat-bubble').removeClass('bubble-hidden');
        }
      }
    });
  }

  toggleAskQuestion();

  function handleBtnTogglers() {
    var $tgl = $('.btn-toggler');

    $tgl.click(function() {
      $(this).closest('.btn-togglers').find('.btn-toggler').removeClass('active');
      $(this).toggleClass('active');
    });
  }

  handleBtnTogglers();


  function toggleContent() {
    var $toggler = $('[data-toggle-toggler]');
    var $target = $('[data-toggle-target]');

    $toggler.click(function(e) {
      e.preventDefault();
      var attr = $(this).attr('data-toggle-toggler');
      var target = $('[data-toggle-target=' + attr + ']');
      $(this).parent().find('[data-toggle-toggler]').removeClass('active');
      $(this).addClass('active');

      $target.hide();
      $target.removeClass('active');
      target.show();
      target.css('top');
      target.addClass('active');

      if (attr == 'requestAcc' || attr == 'resetPwd') {
        $('.account-status-hide-onclick').hide();
      }
    });
  }

  toggleContent();

    function fooStaticRevealCompareBar() {

        var hideActiveSidebar = function () {
            var $sidebar = $('.sidebar.active');
            $sidebar.removeClass('active').hide();
        }

        $('.topnav-cart').click(function () {
            hideActiveSidebar();
            $('.sidebar.shopping-cart').show().addClass('active');
        });

        $('.top-nav-login, .contact-log-in').click(function () {
            hideActiveSidebar();
            $('.sidebar.login-sidebar-login').show().addClass('active');
            if($(this).hasClass('open-register')){
                $('.sidebar.login-sidebar-login #btnSignUp').trigger('click');
            }
        });

        $('.top-nav-register').click(function () {
            hideActiveSidebar();
            $('.sidebar.login-sidebar-register').show().addClass('active');
        });

        $('.top-nav-forgot-password').click(function () {
            $('html, body').animate({scrollTop: '0px'});
            hideActiveSidebar();
            $('.sidebar.login-sidebar-forgot-password').show().addClass('active');
        });

        $('.close-cart').click(hideActiveSidebar);
    }

  fooStaticRevealCompareBar();

  function fooInputValidate() {
    var placeholderTxt;

    $('.foo-validate input:not(.valid, .invalid)').one('focus', function() {
      placeholderTxt = $(this).closest('.input-icon').find('.placeholder').text();
    });

    $('.foo-validate input').keyup(function() {
    //   var len = $(this).val().length;
    //   var $wrap = $(this).closest('.input-icon');
    //
    //   if (len > 4 && len <= 12) {
    //     $wrap
    //     .addClass('valid')
    //     .removeClass('invalid')
    //     .find('.icon-attention').remove();
    //     $wrap.find('.placeholder').text(placeholderTxt);
    //
    //     if ($wrap.find('.icon-checkmark_circled').length == 0) {
    //       $wrap.append('<i class="icon icon-checkmark_circled"></i>')
    //     }
    //   } else {
    //     $wrap
    //     .addClass('invalid')
    //     .removeClass('valid')
    //     .find('.icon-checkmark_circled').remove();
    //
    //     $wrap.find('.placeholder').text('Please Enter a Valid Phone Number');
    //
    //     if ($wrap.find('.icon-attention').length == 0) {
    //       $wrap.append('<i class="icon icon-attention"></i>')
    //     }
    //   }
    });

      $('body').on('blur', '.foo-validate input',function(){
        //do not highlight read only inputs
        if($(this).is('[readonly]')) return;

        $(this).parents('.input-div').toggleClass('valid', $(this).val() !== '');
      });

    $('.promo-code').click(function(event) {
      event.preventDefault();
      $(this).parents('.sidebar-payment-item').find('form').parent().toggleClass('active');
      $(this).toggleClass('active');
    });
  }

  fooInputValidate();

  function fooRandomUpcomingContent() {
    var rand = Math.random() >= 0.5;
    var $no = $('.account-no-content');
    var $up = $('div.upcoming-tours');

    if (rand) {
      $no.show();
    } else {
      $up.show();
    }
  }

  fooRandomUpcomingContent();

  function toggleDropdown() {
    var $toggler = $('[data-dropdown-toggler]');
    var $target = $('[data-dropdown-target]');

    $toggler.click(function(e) {
      e.stopPropagation();
      e.preventDefault();
      var attr = $(this).attr('data-dropdown-toggler');
      var target = $('[data-dropdown-target=' + attr + ']');
      var $togglers = $('[data-dropdown-toggler=' + attr + ']');
      var left = $(this).offset().left + 5;
      var top = $(this).offset().top + $(this).outerHeight() + 13;
      var width = $(this).outerWidth();
      var w = $(window).width();

      if (attr == 'accountMenu' && w > 640) {
        return false
      }

      target.toggleClass('active');
      target.css({
        'left': left,
        'top': top,
        'width': width
      });

      $togglers.toggleClass('active');
    });

    $target.click(function(e) {
      e.stopPropagation();
      $target.removeClass('active');
    });

    $('html').click(function() {
      if ($('[data-dropdown-target].active').length > 0) {
        $target.removeClass('active');
        $toggler.removeClass('active');
      }
    });
  }

  toggleDropdown();

  function mobileMenu() {
    var $toggler = $('.mobile-menu-btn');

    $toggler.click(function(e) {
      var target = $('.topnav-nav');

      if (!target.is(':visible')) {
        $(this).addClass('active');
        target.show();
        target.css('top');
        target.addClass('active');
      } else {
        $(this).removeClass('active');
        target.removeClass('active');
        setTimeout(function() {
          target.hide();
        }, 200);
      }
    });
  }

  mobileMenu();

  function fooPromoCode() {
    var $wrap = $('.promocode-input,.iata-input');
    var $btn = $wrap.find('.btn');
    var $input = $wrap.find('input');

    $btn.click(function(e) {

      var $wrap = $(this).parents('.promocode-input,.iata-input');
      var $input = $wrap.find('input');
      if (!$input.val()) {
        $wrap.addClass('invalid').removeClass('valid');
        $wrap.find('.input-icon').addClass('invalid');
        if (!$wrap.find('.icon-attention').length) {
          $wrap.find('.input-icon').append('<i class="icon icon-attention"></i>');
        }
        $wrap.find('.icon-checkmark').remove();
        $(this).closest('.sidebar-promocode').find('.error-msg').show();
      } else {
        $wrap.removeClass('invalid').addClass('valid');
        $wrap.find('.input-icon').removeClass('invalid').addClass('valid');
        if (!$wrap.find('.icon-circle-close').length) {
          $wrap.find('.input-icon').append('<i class="icon icon-circle-close clear-input"></i>');
        }
        $wrap.find('.icon-attention').remove();
        $(this).text('Applied');
        $(this).append('<i class="icon icon-checkmark"></i>');
        $(this).closest('.sidebar-promocode').find('.error-msg').hide();
      }
    });

    $(document).on('click', '.clear-input', function() {
      $(this).parent().find('input').val('');
      $wrap.removeClass('valid');
      $wrap.find('.input-icon').removeClass('valid');
      $btn.text('Apply');
      $(this).remove();
    });

    $input.keyup(function() {
      $btn.addClass('green');
    });

    $input.blur(function() {
      if (!$(this).val().length) {
        $btn.removeClass('green');
      }
    });
  }

  fooPromoCode();

  function fooModifyTour() {
    var $input = $('.modify-tour-wrap input');
    $input.click(function() {
      $('[data-section="selectDate"]').addClass('active').removeClass('completed');
    });

    if ($(window).width() >= 1024) {
      var $wrap = $('.modify-tour-wrap');
      $wrap.css('min-height', $wrap.outerHeight());
    }

    $('.datepick-input').datepick({
        changeMonth: false,
        prevText: '<i class="icon icon-arrow_left"></i>',
        nextText: '<i class="icon icon-arrow_left"></i>',
        showOtherMonths: true,
        selectOtherMonths: true,
        dayNamesMin: ['Su', 'M', 'Tu', 'W', 'Th', 'F', 'Sa'],
        startDate: '0d',
        minDate: 0,
        useMouseWheel: false,
        onSelect: function() {
            $(this).closest('.item').removeClass('active').addClass('completed');
            $('[data-section="selectTime"]').addClass('active');
        }
    });
  }

  fooModifyTour();

  function capitalizeHomeTabHeadings() {
    var $headings = $('.plan-trip-wrap .section-heading');

    $headings.each(function() {
      var capitalized, arr;
      arr = $(this).text().split(' ');
      for (var i = 0; i < arr.length; i++) {
        arr[i] = arr[i].toLowerCase();
        arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
      }
      $(this).text(arr.join(' '));
    });
  }

  capitalizeHomeTabHeadings();

  function attachRipple() {
    Waves.attach('.btn', ['waves-button', 'waves-float']);
    Waves.attach('.topnav-dropdown a', ['waves-float']);
    Waves.attach('.guide-tag', ['waves-float']);
    Waves.init();
  }

  attachRipple();

  function backToTop() {
    if ($('.back-to-top').length) {
      $(window).scroll(function() {
        if ($(window).scrollTop() >= 400) {
          $('.back-to-top').addClass('active');
        } else {
          $('.back-to-top').removeClass('active');
        }
      });
    }

    $('.back-to-top').click(function() {
      if ($('html').hasClass('eventDetail')) {
        console.log('has')
        $('html, body').animate({
          scrollTop: $('.right-book').offset().top
        }, 400)
      } else {
        $('html, body').animate({
          scrollTop: 0
        }, 400)
      }
    });
  }

  backToTop();

  (function($) {
    var controller = new slidebars();
    controller.init();

    var $menu = $('.topnav .topnav-nav');
    var $offcanvas = $('#offcanvas');
    var w;

    $(window).resize(function() {
      w = $(window).width();

      if (w >= 1024) {
        $menu.css('display', 'flex');
      }
    });

    $('.mobile-menu-btn').on('click', function(event) {
      w = $(window).width();

      if (w <= 1024) {
        if (!$offcanvas.find('.topnav-nav').length) {
          $offcanvas.html($menu.clone());
        }

        $offcanvas.find('.topnav-nav').addClass('active');
        $menu.hide();
      }

      event.stopPropagation();
      event.preventDefault();
      controller.toggle('id-1');
      $('[canvas="container"]').toggleClass('disable-scroll');
      $('[canvas="container"]').addClass('open');
      $('html, body').toggleClass('slidebar-open');
      $('[off-canvas]').toggleClass('open');

      if ($('[canvas="container"]').hasClass('open')) {
        setTimeout(function() {
          $('[canvas="container"]').removeClass('open');
        }, 300);
      }
    });

    $(document).on('click', '.js-close-any', function(event) {
        if (controller.getActiveSlidebar()) {
            controller.close();
            $('[canvas="container"]').toggleClass('disable-scroll');
            $('[canvas="container"]').addClass('open');
            $('html, body').toggleClass('slidebar-open');
            $('[off-canvas]').toggleClass('open');

            if ($('[canvas="container"]').hasClass('open')) {
              setTimeout(function() {
                $('[canvas="container"]').removeClass('open');
              }, 300);
            }
        }
    });

    $(controller.events).on('opening', function(event) {
        $('[canvas]').addClass('js-close-any');
        $('[off-canvas] .topnav-dropdown a').addClass('js-close-any');
    });

    $(controller.events).on('closing', function(event) {
        $('[canvas]').removeClass('js-close-any');
        $('[off-canvas] .topnav-dropdown a').addClass('js-close-any');
    });

  })(jQuery);

  $('.topnav .currency-select-item .topnav-dropdown a, .footer-select .topnav-dropdown a ').click(function(){
    $('footer select.currency-select')
        .val($(this).data('currency'))
        .parents('form')
        .submit();
  });

  $('input[readonly]').focus(function() {
    $(this).blur();
  });

  function cancellationOtherReasons() {
    $('[name="cancellation-reason"]').on('change', function() {
      $('#other-reasons').addClass('disabled');
    });

    $('#reasonOther').on('change', function() {
      $('#other-reasons').removeClass('disabled');
      $('#other-reasons').focus();
    });
  }

  cancellationOtherReasons();

  function closeSignupSidebar() {
    $('.close-signup-sidebar').click(function() {
      $(this).closest('.sidebar').addClass('hidden');
      window.location.pathname = '/login';
    });
  }

  closeSignupSidebar();

  function revealMobileFooterCurrencyPicker() {
    var $toggle = $('.footer-select .currency-select-text');

    $toggle.click(function(e) {
      e.stopPropagation();
      $(this).parent().find('.topnav-dropdown').toggleClass('active');
    });

    $('.topnav-dropdown a').click(function(e) {
      e.stopPropagation();
    });

    $('body').click(function() {
      if ($('.footer-select .topnav-dropdown').hasClass('active')) {
        $('.footer-select .topnav-dropdown').removeClass('active');
      }
    });
  }

  revealMobileFooterCurrencyPicker();

  function resizeTourDetailMap() {
    var w = $(window).width();

    if (w <= 640) {
      $('.upcoming-detail-description .map iframe').css('width', w);
    }
  }

  resizeTourDetailMap();
});
