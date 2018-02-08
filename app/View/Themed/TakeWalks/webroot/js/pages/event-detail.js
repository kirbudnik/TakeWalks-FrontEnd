var EventDetail = function(){
    var self = this;
    var $el = $('.tour-detail-content');
    var el = {};
    var tourPicker;
    var reviewModal;
    var _init = function(){

        //children
        tourPicker = new EventDetailTourPicker(self);

        reviewModal = new EventDetailReviewModal(self, $('.modal-overlay.reviews'));
    };

    this.formatPrice = function(price){
        if(Number(price) === 0) return 'FREE';

        return price;
    };

    this.getEl = function(){ return $el; };
    _init();
};

var EventDetailTourPicker = function(parent){
    var self = this;
    var $el = $('.right-book', parent.getEl());
    var pickerOrder = ['datePicker','timePicker','paxPicker'];
    var selectedPicker = null;
    var el = {
        form: $('form.book', $el),

        heading: $('.sidebar-subheading', $el),

        priceContainer: $('.sidebar-heading', $el),

        adultPrice: $('.price-value',$el),
        originalPrice: $('.original-price',$el),

        datePicker: $('.date-picker-container', $el),
        timePicker: $('.time-picker-container', $el),
        paxPicker: $('.pax-picker-container', $el),

        selectedDateInput: $('input.selected-date', $el),

        timePickerSelect: $('.time-picker-container select', $el),
        timePickerSelected: $('.time-picker-container .selected-time',$el),

        adultsPaxInput: $('.pax-picker-container [name=adults]',$el),
        infantsPaxInput: $('.pax-picker-container [name=infants]',$el),
        childrenPaxInput: $('.pax-picker-container [name=children]',$el),
        studentsPaxInput: $('.pax-picker-container [name=students]',$el),

        adultsPaxPrice: $('.pax-picker-container .adults-container .guest-price',$el),
        infantsPaxPrice: $('.pax-picker-container .infants-container .guest-price',$el),
        childrenPaxPrice: $('.pax-picker-container .children-container .guest-price',$el),
        studentsPaxPrice: $('.pax-picker-container .students-container .guest-price',$el),

        totalContainer: $('.pax-picker-container .total', $el),
        errorMessage: $('.error-message', $el),
        bookNowButton: $('.book-now-container .btn-book-now', $el),

        totalGuestCount: $('.pax-picker-container .guest-amount',$el),
        totalPrice: $('.pax-picker-container .total-price',$el),

        btnAddToWishlist: $('#addToWishlist')

    };
    var calendar;
    this.selectedDate = null;
    this.selectedTime = null;
    this.prices = null;

    var _init = function(){

        //events
        el.timePickerSelect.change(timePickerSelected);
        $('select',el.paxPicker).change(updateTotalPrice);
        el.heading.click(headingSelect);
        el.selectedDateInput.click(headingSelect);
        el.form.submit(addItemToCart);
        //init select2
        $('.time-select',$el).select2({
            minimumResultsForSearch: 200 //disables search
        });

        //children
        calendar = new EventDetailCalendar(self);


        //show calendar
        self.goToStep('datePicker');

        //fill in adult price
        el.adultPrice.html(convertFormatPrice(initValues.group_prices.adults, true));
        if(initValues.group_prices.discount !== false){
            el.priceContainer.addClass('discounted');
            el.originalPrice.html(convertFormatPrice(initValues.group_prices.discount, true));
        }

        el.btnAddToWishlist.click(addItemToWishlist);

    };

    var addItemToWishlist = function(event){
        event.preventDefault();
        el.btnAddToWishlist.attr('disabled', 'disabled');
        var data = {
            event_id : $('[name=events_id]').val()
        };
        $('body').css({cursor:'wait'});
        $.ajax({ data: data, url: '/user/wishlist/add', method: 'post' }
        ).done(function(response) {
            $('body').css({cursor:'default'});
            el.btnAddToWishlist.html('ADDED TO WISHLIST!').removeClass('purple').addClass('grey');
        }).fail(function(response){
            $('body').css({cursor:'default'});
            window.location.reload();
        });

    };

    var addItemToCart = function(e){
        if(window.Helper.isMobile()) {
            return true;
        }
        e.preventDefault();
        el.bookNowButton.attr('disabled','disabled');
        el.bookNowButton.toggleClass('purple', false);
        el.errorMessage.hide();
        //ajax add item
        window.Helper.postFromForm($(this),{
            'success': function(results){
                results = typeof results.response === 'undefined' ? $.parseJSON(results) : results;

                if(results.response === 'error'){
                    el.errorMessage.html(results.message).stop().slideDown(200);
                    el.bookNowButton.attr('disabled',false);
                    el.bookNowButton.toggleClass('purple', true);
                }else{
                    window.WalksCart.loadCartItems(results.cart);

                    //open cart
                    $('.topnav .topnav-cart').trigger('click');

                    //scroll to top
                    var body = $("html, body");
                    body.stop().animate({scrollTop:0}, 100, 'swing');

                    self.resetPaxPicker();

                }
            }
        });


    };

    this.resetPaxPicker = function(){
        //close pax picker
        self.goToStep('datePicker');

        el.paxPicker.find('select').val(0).eq(0).trigger('change');
    };

    this.goToStep = function(step){
        var statuses = [false,false,false];

        //if going back from pax picker then reset it
        if(selectedPicker === 'paxPicker'){
            el.paxPicker.find('input[type=number]').val(0);
            updateTotalPrice();
        }

        switch(step){
            case 'datePicker':
                statuses = ['active',false,false];

                // el.datePicker.find('.sidebar-circle').css('background','#af3756');
                // el.datePicker.find('.sidebar-content').slideDown();
                // el.datePicker.find('.sidebar-selected-value').css({
                //     opacity: 0,
                //     position: 'absolute',
                //     transform: 'translateY(-5000px)',
                //     height: '0'
                // });

                break;
            case 'timePicker':
                //reset time picker
                self.updateTimePicker();
                // el.datePicker.find('.sidebar-circle').css('background','#adb6bc');
                // el.datePicker.find('.sidebar-content').slideUp();
                // el.datePicker.find('.sidebar-selected-value').css({
                //     opacity: 1,
                //     position: 'static',
                //     transform: 'translateY(0)',
                //     height: 'auto'
                // });
                statuses = ['completed','active',false];
                break;
            case 'paxPicker':
                statuses = ['completed','completed','active'];
                break;
        }
        selectedPicker = step;

        self.changeContainerStatus(el.datePicker, statuses[0]);
        self.changeContainerStatus(el.timePicker, statuses[1]);
        self.changeContainerStatus(el.paxPicker, statuses[2]);
    };

    this.changeContainerStatus = function($container, status){
        $container.removeClass('active completed');
        $container.addClass(status);

    };

    var timePickerSelected = function(){

        self.selectedTime = self.selectedDate[moment('2001-01-01 ' + $(this).val()).format('HH:mm:00')];
        self.prices = self.selectedTime.prices;

        // @TODO
        $('.selected-time').val($(this).val()).trigger('change.select2');
        self.updatePaxPicker();
        self.goToStep('paxPicker');
    };

    var convertPrice = function(price){
        price *= window.CurrencyExchange.exchangeRate;
        return Math.round(price * 100) / 100;

    };

    var convertFormatPrice = function(price, convert){
        if(convert){
            price = convertPrice(price);
        }

        return window.CurrencyExchange.numberFormatCurrency(price, 'selected',true);
    };

    this.updateTimePicker = function(){
        el.timePickerSelect.html('').append($('<option>'));

        Object.keys(self.selectedDate).forEach(function(time){
            time = moment('2001-01-01 ' + time);


            el.timePickerSelect.append($('<option>',{value: time.format('HH:mm:ss')}).html(time.format('h:mm a')));
        });
    };

    this.updatePaxPicker = function(){
        el.adultsPaxPrice.html(convertFormatPrice(self.prices.adults, true));
        el.infantsPaxPrice.html(convertFormatPrice(self.prices.infants, true));
        el.childrenPaxPrice.html(convertFormatPrice(self.prices.children, true));
        el.studentsPaxPrice.html(convertFormatPrice(self.prices.students, true));
    };

    var updateTotalPrice = function(){
        var totalPrice = 0;
        totalPrice += convertPrice(self.prices.adults) * el.adultsPaxInput.val();
        totalPrice += convertPrice(self.prices.infants) * el.infantsPaxInput.val();
        totalPrice += convertPrice(self.prices.children) * el.childrenPaxInput.val();
        totalPrice += convertPrice(self.prices.students) * el.studentsPaxInput.val();

        var totalGuestCount = Number(el.adultsPaxInput.val());
        totalGuestCount += Number(el.infantsPaxInput.val());
        totalGuestCount += Number(el.childrenPaxInput.val());
        totalGuestCount += Number(el.studentsPaxInput.val());

        el.totalGuestCount.html(totalGuestCount);
        el.totalPrice.html(convertFormatPrice(totalPrice, false));

        //show total price and book now if price if user selected guests
        el.totalContainer.css('display', totalPrice > 0 ? 'inline-block' : 'none');
        el.bookNowButton.toggleClass('purple', totalPrice > 0);
        if(totalPrice > 0) {
            el.bookNowButton.attr('disabled',false);
        }else{
            el.bookNowButton.attr('disabled','disabled');
        }

    };

    var headingSelect = function(){
        //only allow user to go back
        var clickedHeading = $(this).parents('.right-sidebar-item').attr('data-section');
        if(pickerOrder.indexOf(clickedHeading) < pickerOrder.indexOf(selectedPicker)){

            self.goToStep(clickedHeading);
        }



    };

    this.getEl = function(){ return $el; };
    _init();
};

var EventDetailCalendar = function(parent){
    var self = this;
    var $el = $('.date-picker-container', parent.getEl());

    var el = {
        datePicker: $('.datepick', $el),
        selectedDate: $('.sidebar-selected-value .selected-date', $el)
    };
    var whitelistDates = [];
    var _init = function(){

        loadDatePicker();
    };

    var loadDatePicker = function(){
        //get all tour dates
        whitelistDates = Object.keys(initValues.dates_group);

        //get first date
        var firstGroupDate = initValues.first_group;
        firstGroupDate = typeof firstGroupDate.year !== 'undefined' ? new Date(firstGroupDate.year, firstGroupDate.month - 1, 1) : new Date();

        //get last date
        if(whitelistDates.length > 0){
            var lastGroupDate = whitelistDates[whitelistDates.length - 1].split('-');
            lastGroupDate = new Date(lastGroupDate[0],lastGroupDate[1]-1,lastGroupDate[2]);
        }else{
            lastGroupDate = new Date();
        }

        el.datePicker.datepick({
            changeMonth: false,
            prevText: '<i class="icon icon-arrow_left"></i>',
            nextText: '<i class="icon icon-arrow_left"></i>',
            showOtherMonths: true,
            selectOtherMonths: true,
            dayNamesMin: ['Su', 'M', 'Tu', 'W', 'Th', 'F', 'Sa'],
            minDate: firstGroupDate,
            maxDate: lastGroupDate,
            onDate: whitelistDays,
            onSelect: dateSelect,
            useMouseWheel: false
        });

    };

    var whitelistDays = function(date, inMonth){
        if(whitelistDates.indexOf(formatDate(date)) === -1){
            return {
                selectable: false,
                dateClass: 'disabled-date'
            };
        }
        return {};
    };

    var dateSelect = function(date){
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        //date is selected dates. we only allow one selection
        date = date[0];

        el.selectedDate.val(moment(date).format('D MMMM YYYY'));
        parent.selectedDate = initValues.dates_group[formatDate(date)];
        parent.updateTimePicker();
        parent.goToStep('timePicker');
        parent.getEl().find('input[name=date]').val(moment(date).format('YYYY-MM-DD'));

    };


    var formatDate = function(date){
        return moment(date).format('YYYY-MM-DD')
    };

    this.getEl = function(){ return $el; };
    _init();
};

var EventDetailReviewModal = function(parent, $el){
    var self = this;
    var page = 1;
    var $reviewContainer = $el.find('.reviews-container');
    var gettingNextPage = false;
    var containerHeight = null;

    var _init = function(){
        parent.getEl().find('[data-modal-toggler=reviews]').click(modalOpen);
        $reviewContainer.scroll(containerScroll);
    };

    var containerScroll = function(e){
        if(!gettingNextPage && containerHeight - $reviewContainer.scrollTop() - $reviewContainer.height() < 200){
            getNextPage();
        }
    };

    var modalOpen = function(){
        if(page === 1){
            getNextPage();
        }
    };

    var getNextPage = function(){
        gettingNextPage = true;
        $.get('/eventrewiewshtml?e=' + initValues.eventId + '&p=' + (page++), function(results){
            $reviewContainer.append(results);
            gettingNextPage = false;
            containerHeight = $reviewContainer[0].scrollHeight;
        });
    };

    _init();
};



$(function(){ new EventDetail(); });
