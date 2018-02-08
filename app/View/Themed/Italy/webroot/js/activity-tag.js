ActivityTag = {
    UpcomingTours : {
        // var tomorrow = new Date(); tomorrow.setDate(today.getDate()+1);
        selectedDate : null,
        minDate : null,
        currentDate : null,
        nextDate : null,
        previousDate : null,
        previousButton : null,
        listUpcomingTours : null,
        init : function() {
            this.minDate = new Date();
            this.minDate.setDate(this.minDate.getDate()+1);
            this.currentDate = new Date();
            this.currentDate.setDate(this.currentDate.getDate()+1);
            this.selectedDate = $('#choose_date');
            this.previousButton = $('#btn_previous_date');
            this.listUpcomingTours = $('#list_upcoming_tours');
            this.selectedDate.datepicker({
                defaultDate: this.currentDate,
                minDate: 1,
                dateFormat: 'M d, yy',
                onSelect: function(date) {
                    date = moment(date, 'MMMM D, YYYY').format('YYYY-MM-DD') + ' 00:00:00';
                    var choosenDate = new Date(date);
                    ActivityTag.UpcomingTours.currentDate.setTime(choosenDate.getTime());
                    ActivityTag.UpcomingTours.refreshDate();
                }

            }).datepicker("setDate", this.currentDate);
            $('#ui-datepicker-div').hide();

            this.previousButton.click(function(event){
                event.preventDefault();
                if(ActivityTag.UpcomingTours.setPreviousDate()) {
                    ActivityTag.UpcomingTours.refreshDate();
                }
            });

            $('#btn_next_date').click(function(event){
                event.preventDefault();
                ActivityTag.UpcomingTours.setNextDate();
                ActivityTag.UpcomingTours.refreshDate();
            });

            this.refreshDate();
        },
        setPreviousDate : function() {
            this.previousButton.css('background-color', '');
            if (this.minDate.getTime() >= this.currentDate.getTime()){
                this.previousButton.css('background-color', 'lightgrey');
                return false;
            }
            this.currentDate.setDate(this.currentDate.getDate()-1);
            this.selectedDate.datepicker("setDate", this.currentDate);
            return true;
        },
        setNextDate : function() {
            this.previousButton.css('background-color', '');
            this.currentDate.setDate(this.currentDate.getDate()+1);
            this.selectedDate.datepicker("setDate", this.currentDate);
        },
        refreshDate : function() {
            this.listUpcomingTours.html('<tr><td colspan="5" style="text-align: center"><img src="/img/loading.gif"></td></tr>');
            var date = this.selectedDate.val();
            date = moment(date, 'MMMM D, YYYY').format('YYYY-MM-DD');
            var data = {'dateSelected' : date, eventIds: eventIds};
            $('body').css({cursor:'wait'});
            $.ajax({
                url: '/activity-tag-tour-upcoming',
                method: 'post',
                data: data
            }).done(function(resp) {
                $('body').css({cursor:'default'});
                ActivityTag.UpcomingTours.loadUpcomingTours(resp);
            });


        },
        loadUpcomingTours : function(data) {
            var rows = '<tr><td colspan="5" style="text-align: center">The choosen date did not match any tours.  Please choose another date.</td></tr>';
            if ( data.length > 0) {
                rows = '';
                var tours = [];
                var tour = null;
                for(var i = 0; i < data.length; i++ ) {
                    tour = data[i];
                    var count = (tour.times_group !== undefined) ? tour.times_group.length : 0;
                    for(var j = 0; j < count; j++ ) {
                        tours.push({
                            time_pretty:    tour.times_group[j],
                            time:           moment( tour.times_group[j] , 'h:mm a').valueOf(),
                            name_listing:   tour.name_listing,
                            duration:       tour.duration,
                            adults_price:   tour.adults_price,
                            more_info:      tour.more_info
                        });
                    }
                }
                tours.sort(function(a, b) { return a.time > b.time; });

                for(var k = 0; k < tours.length; k++ ) {
                    tour = tours[k];
                    rows += '<tr>';
                    rows += '<td>'+tour.time_pretty+'</td>';
                    rows += '<td>'+tour.name_listing+'</td>';
                    rows += '<td class="right">'+tour.duration+'</td>';
                    rows += '<td class="right">'+tour.adults_price+'</td>';
                    rows += '<td><a href="'+tour.more_info+'" class="red-btn"><i class="fa fa-eye"></i> VIEW TOUR</a></td>';
                    rows += '</tr>';
                }
            }
            this.listUpcomingTours.empty();
            this.listUpcomingTours.append(rows);
        }

    }
};

$(document).ready(function() {
  function initReviewsSlider() {
      var $sl = $('.tour-img');
      var $main = $('.tag-page-title-slider');

      if ($.fn.slick) {
          $sl.slick({
              nextArrow: '<i class="fa fa-chevron-right slick-next"></i>',
              prevArrow: '<i class="fa fa-chevron-left slick-prev"></i>'
          });

          // $main.slick({
          //   dots: true,
          //   infinite: true,
          //   autoplay: true
          // });
      }
  }

  initReviewsSlider();

  var tabToggle = function() {
    var w = $(window).width();
    var $toggler = $('[data-tab-toggler]');
    var $target = $('[data-tab-target]');

    $toggler.click(function(e) {
      e.stopPropagation();
      var attr = $(this).attr('data-tab-toggler');
      var target = $('[data-tab-target=' + attr + ']');
      $toggler.removeClass('active');
      $target.removeClass('active');

      $(this).toggleClass('active');
      target.toggleClass('active');
    });
  };

  tabToggle();

  var revealFaq = function() {
    var $toggler = $('.tag-question-title');

    $toggler.click(function(e) {
      $(this).closest('.tag-question').toggleClass('active');
      $(this).closest('.tag-question').find('.tag-question-answer').slideToggle();
    });
  };

  revealFaq();

  function scrollToTarget() {
    var $toggler = $('[data-scroll-toggler]');
    var $target = $('[data-scroll-target]');

    $toggler.click(function(e) {
      e.preventDefault();
      var attr = $(this).attr('data-scroll-toggler');
      var target = $('[data-scroll-target=' + attr + ']');
      
      $('html, body').animate({
        scrollTop: target.offset().top
      }, 400);
    });
  }

  // scrollToTarget();

    ActivityTag.UpcomingTours.init();
});