var Compare = function(){
    var self = this,
        $el,
        searchDate,
        slug,
        tourPicker,
        templates = {};


    var _init = function(){
        $el = $('#compare');
        slug = $el.data('slug');

        //load templates
        templates.tour = _.template($('.template.template-compare-upcoming-tour').html());

        searchDate = new CompareSearchDate(self, $('#upcomingTours',$el));
        tourPicker = new CompareTourPicker(self, $('.tour-list-items', $el));

        initHighlights();
    };

    var initHighlights = function(){
        //make highlights work
        $('.highlight-tabs .vertical-tab').click(function(){
            $('.highlight-tabs').find('.active').removeClass('active');
            $(this).addClass('active');

            $('.highlight-tabs .right').hide().eq($(this).index()).show();
        })

        //click first tab
        $('.highlight-tabs .vertical-tab:eq(0)').trigger('click');
    };

    this.getSlug = function(){ return slug; };
    this.getTemplate = function(templateName){ return templates[templateName]; };
    _init();
};

var CompareSearchDate = function(parent, $el){
    var self = this;
    var $datePickerInput;
    var $upcomingTours;
    var $header;
    var $noTours;
    var $loading;
    var request = null;
    var _init = function(){
        $datePickerInput = $el.find('input.has-datepick');
        $upcomingTours = $el.find('.upcoming-tours-items');
        $header = $el.find('.upcoming-tours-headings');
        $noTours = $el.find('.upcoming-tour-not-found');
        $loading = $el.find('.upcoming-tour-loading');

        //events
        $datePickerInput.datepick('option', 'onSelect', getDateTours);
        $el.find('.change-date').click(changeDate);

        //if user goes back to this page
        getDateTours();
    };

    var getDateTours = function(){
        var date = $datePickerInput.val() === '' ? '0' : $datePickerInput.val();
        $upcomingTours.html('');
        $header.hide();
        $noTours.hide();
        $loading.show();
        if(request) request.abort();
        request = $.getJSON('/ajax_upcoming_tours/' + parent.getSlug() + '/' + encodeURIComponent(date.replace(/\//g,'-')),function(tours){
            $loading.hide();
            if(tours.length){
                $header.show();
                $datePickerInput.val(tours[0].date);
            }else{
                $noTours.show();
            }
            tours
                .sort(function(a,b){
                    return moment(a.time, 'h:mm a ').isAfter(moment(b.time, 'h:mm a '))
                })
                .forEach(function(tour){
                $upcomingTours.append(parent.getTemplate('tour')(tour));
            });
        });

    };

    var changeDate = function(){
        var date = $datePickerInput.val() === '' ? '0' : $datePickerInput.val();
        if(date === '0') return false;

        var direction = $(this).hasClass('next-date') ? 1 : -1;

        $datePickerInput.val(moment(date).add(direction, 'days').format('MM/DD/YYYY'));
        getDateTours();
    };

    _init();
};
//<div class="comparing-tour">Early Access: Sistine Chapel and Vatican <i class="icon icon-close"></i></div>
var CompareTourPicker = function(parent, $el){
    var self = this;
    var $compareBar;
    var selectedTours = {};

    var _init = function(){
        $compareBar = $('.comparison-bar');

        //events
        $el.find('.compare-cb input[type=checkbox]').click(toggleTour);
        $compareBar.on('click','.icon-close', function(){ removeTour($(this).parents('.comparing-tour').attr('data-tour-id')); });
        $compareBar.find('.btn.btn-compare-tours').click(function(){ compareTours(); });
    };

    var getTourEl = function(id,name){
        return $('<div>',{'class':'comparing-tour','data-tour-id': id})
            .append(name)
            .append($('<i>',{'class':'icon icon-close'}));
    };

    var toggleTour = function(){
        var $tour = $(this).parents('.tour-box');
        var tourId = $tour.data('tour-id');

        if($(this).prop('checked')){
            selectedTours[tourId] = $tour.find('.tour-title')[0].dataset.titleShort;
            $compareBar.find('.btn-compare-tours').before(getTourEl(tourId,selectedTours[tourId]));
        }else{
            removeTour(tourId);
        }

        if(Object.keys(selectedTours).length >= 3) {
            $el.find('.compare-cb input[type=checkbox]:not(:checked)').prop('disabled', true);
        }

        toggleBar();

    };

    var removeTour = function(tourId){
        if(typeof selectedTours[tourId] !== 'undefined'){
            delete selectedTours[tourId];
            $compareBar.find('[data-tour-id=' + tourId + ']').remove();
            $el.find('[data-tour-id=' + tourId + '] .compare-cb input[type=checkbox]').prop('checked',false);
        }
        $el.find('.compare-cb input[type=checkbox]:not(:checked)').prop('disabled',false);
        toggleBar();
    };

    var toggleBar = function(){

        $compareBar.toggleClass('active', Object.keys(selectedTours).length > 0);
    };


    var compareTours = function(){
        if(Object.keys(selectedTours).length > 0){
            window.location = '/' + parent.getSlug() + '/' + Object.keys(selectedTours).join('/');
        }
    };

    _init();
};



$(function(){ new Compare(); });