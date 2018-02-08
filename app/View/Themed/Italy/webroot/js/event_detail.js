function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
        .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
                .toFixed(prec);
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
        .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
        .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
            .join('0');
    }
    return s.join(dec);
}

function formatCurrency(amount, currency) {

    //overwriting currency but leaving the parameter because some code might still use it
    currency = initValues.currency.selected;
    if(amount*1 == 0) {
        return 'FREE';
    }

    amount = amount * initValues.currency.exchangeRate;

    // Determine euro or dollars based on exchangepair, default to euro

    return initValues.currency.symbol + (currency == 'USD' ? number_format(amount, 2, '.', ',') :  number_format(amount, 2, ',', '.'));
}
console.log(initValues);
Tour = {
    eventId : initValues['eventId'],

    BookingPanel : {
        selected : {
            date : null,
            time : null
        },

        types : ['adults', 'students', 'children', 'infants'],

        group_prices : initValues['group_prices'],

        private_prices : initValues['private_prices'],
        private_base_price : initValues['private_base_price'],

        first_group : initValues['first_group'],
        dates_group : initValues['dates_group'],
        sellout_group : initValues['sellout_group'],

        first_private : initValues['first_private'],
        dates_private : initValues['dates_private'],
        sellout_private : initValues['sellout_private'],

        loaded : initValues['loaded'],
        actualTotalPrice : 0,
        actualTotalPriceItem : false,

        reset : function(type) {
            Tour.BookingPanel.setSection('date');
            Tour.BookingPanel.$el.find('.submit').slideUp();

            Tour.BookingPanel.setSummaryText('date', '');
            Tour.BookingPanel.setSummaryText('time', '');
            Tour.BookingPanel.setSummaryText('people', '');


            Tour.BookingPanel.Calendar.$el.datepicker('setDate', null);

            //try to go to the month of the next available tour
            var dates;
            if(type == 'private'){
                dates = Object.keys(Tour.BookingPanel.dates_private);
            }else{
                dates = Object.keys(Tour.BookingPanel.dates_group);
            }
            if(dates.length){
                var dateParts = dates[0].split('-');
                Tour.BookingPanel.Calendar.$el.datepicker('setDate', new Date(dateParts[0],dateParts[1]-1,dateParts[2]));
            }

            Tour.BookingPanel.selected.date = null;
            Tour.BookingPanel.selected.time = null;
            Tour.BookingPanel.$el.get(0).reset();
        },

        init : function() {
            this.$el = $('.book');
            this.TimePicker.init(this);
            this.Calendar.init(this);
            this.PaxPicker.init(this);

            // Collapsible booking form
            $('.time', this.$el).hide();
            $('.people', this.$el).hide();
            $('.submit', this.$el).hide();
            $('.date, .time, .people', this.$el).prev().on('click', this._clickSection);

            this.reset('group');

            // Read more links
            $('.more').click(function(){
                $(this).siblings().slideDown('fast').removeClass('hidden');
            });

            // Show base price for private groups
            $('aside .base-price').html(formatCurrency(Tour.BookingPanel.private_base_price));

            //disable "Add to cart" button on click
            //prevent multiple submittions
            $('aside form').submit(function(){
                $('button.green.button.large').prop('disabled',1);
            });


        },
        setSummaryText : function(section, text) {
            this.$el.find('.' + section).prev().find('span').html(text);
        },
        setSection : function(section) {
            this.$el.find('.date, .time, .people').not('.' + section).slideUp();
            this.$el.find('.' + section).slideDown();
        },
        showSection : function(section) {
            this.$el.find('.' + section).slideDown();
        },
        hideSection : function(section){
            this.$el.find('.' + section).slideUp();
        },
        showPrice : function(guests, price) {
            var guestText = guests > 1 ? 'guests' : 'guest';
            var summary = guests + ' ' + guestText + ' for <span>' + formatCurrency(price, 'euro') + '</span>';
            var priceDiscount = '';
            if ($('[name=show_discount]').val() == "1"){
                priceDiscount = number_format( ( price * (1 - initValues.discountPercentRelatedTour/100) ), 2);
                summary = guests + ' ' + guestText + ' for <span style="text-decoration: line-through;font-size: 0.8em;font-style: italic;">' + formatCurrency(price, 'euro') + '</span> <span>' + formatCurrency(priceDiscount, 'euro') + '</span> ';
            }

            this.$el.find('.submit p.summary').html(summary);
        },
        _clickSection : function() {
            var prevSummary = $(this).parent().prev().find('.summary span');
            if(prevSummary.length && !prevSummary.html()) {
                return;
            }
            $('.date, .time, .people').slideUp();
            $(this).next().slideDown();
        },

        loadMonth : function(year, month) {
            if($.inArray(year + '-' + month, Tour.BookingPanel.loaded) > -1) {
                return;
            }

            $.ajax({
                url: '/events/getPrivateStages/' + Tour.eventId + '/' + year + '/' + month,
                success: function(data) {
                    console.log(data);
                    $.extend(Tour.BookingPanel.dates_private, data.stages);

                },
                dataType: 'json',
                async: false
            });

            $.ajax({
                url: '/events/getGroupStages/' + Tour.eventId + '/' + year + '/' + month,
                success: function(data) {
                    console.log(data);
                    $.extend(Tour.BookingPanel.dates_group, data.stages);

                    Tour.BookingPanel.loaded.push(data.year_month);

                },
                dataType: 'json',
                async: false
            });

        },

        PaxPicker : {
            init : function(parent) {
                this.parent = parent;
                this.$el = this.parent.$el.find('.people');
                this.$el.find('select').change({parent : this}, this._changePeople);
            },
            _changePeople : function(e) {
                var guests = 0;
                var non_infant = 0;
                var type = $('input[name=type]').val();
                var price = type == 'group' ? 0 : parseFloat(Tour.BookingPanel.private_base_price);
                var exempt = type == 'group' ? 0 : 2;

                var prices;
                if(type == 'group') {
                    var date = Tour.BookingPanel.selected.date;
                    var time = Tour.BookingPanel.selected.time;

                    prices = Tour.BookingPanel.dates_group[date][time].prices;
                }
                else {
                    prices = Tour.BookingPanel.private_prices;
                }

                $.each(['adults', 'students', 'children', 'infants'], function(i, val) {
                    var select = Tour.BookingPanel.PaxPicker.$el.find('select[name='+val+']');
                    var g = parseInt(select.val());
                    guests += g;
                    for(var p = 0; p < g; p++) {
                        if(exempt > 0) {
                            exempt--;
                            continue;
                        }
                        if(prices[val]) {
                            price += parseFloat(prices[val]);
                        }
                    }

                    if(val != 'infants') {
                        non_infant += g;
                    }
                });

                Tour.BookingPanel.actualTotalPrice = price;

                if(!non_infant) {
                    Tour.BookingPanel.PaxPicker.parent.hideSection('submit');
                    return;
                }

                Tour.BookingPanel.PaxPicker.parent.setSummaryText('people', guests + ' Guest' + (guests != 1 ? 's' : ''));
                Tour.BookingPanel.PaxPicker.parent.showSection('submit');
                Tour.BookingPanel.PaxPicker.parent.showPrice(guests, price);
                document.getElementById('ec_quantity').value = guests;
                document.getElementById('ec_price').value = price;
            },
            render : function() {
                var date = Tour.BookingPanel.selected.date;
                var time = Tour.BookingPanel.selected.time;
                var show_discount = $('input[name=show_discount]').val();

                var type = $('input[name=type]').val();
                if(type == 'group') {
                    var prices = Tour.BookingPanel.dates_group[date][time].prices;
                }else{
                    //private
                    var prices = Tour.BookingPanel.private_prices
                }
                    $.each(['adults', 'students', 'children', 'infants'], function(i, val) {
                        var priceRender = (show_discount) ? number_format(prices[val] * (1 - initValues.discountPercentRelatedTour/100), 2) :  number_format(prices[val], 2) ;
                        var paxPrice = (type == 'group') ? priceRender : prices[val];
                        $('span.person-price.'+val).html(formatCurrency(paxPrice, 'EUR'));
                    });


                this.$el.find('select').select2('val', 0)
            }
        },


        TimePicker : {
            init : function(parent) {
                this.parent = parent;
                this.$el = this.parent.$el.find('select[name="time"]');
                this.render();

                this.$el.on('change', {parent : this}, this._selectTime);
            },
            render : function() {
                var type = $('input[name=type]').val();
                var options;
                if(type == 'group') {
                    options = Tour.BookingPanel.dates_group[Tour.BookingPanel.selected.date];
                }
                else {
                    options = Tour.BookingPanel.dates_private[Tour.BookingPanel.selected.date];
                }
                if(typeof options === 'undefined') return;

                html = '<option></option>';
                $.each(options, function(i, opt) {
                    html += '<option value="' + opt.time + '">' + opt.pretty_time + '</option>';
                });

                this.$el.html(html);
                $('select[name=time]').select2('val', '');
            },
            _selectTime : function(e) {
                Tour.BookingPanel.selected.time = $(this).val();

                Tour.BookingPanel.PaxPicker.render();

                Tour.BookingPanel.setSummaryText('time', e.data.parent.$el.find(':selected').text());
                Tour.BookingPanel.setSection('people');
            }
        },

        Calendar : {
            init : function(parent) {
                this.parent = parent;

                // Group tours
                this.$el = this.parent.$el.find('.calendar');

                //Attempt to get the first date
                var firstDate = null;

                for(var tour in Tour.BookingPanel.dates_group){
                    firstDate = tour.split('-');
                    break;
                }

                //if private tour starts before group tour then set that month as the min month
                for(var tour in Tour.BookingPanel.dates_private){
                    if(firstDate != null){
                        tour = tour.split('-');
                        var firstGroupDate = new Date(firstDate[0],Number(firstDate[1])-1,firstDate[2]);
                        var firstPrivateDate = new Date(tour[0],Number(tour[1])-1,tour[2]);
                        if(firstPrivateDate.getTime() < firstGroupDate.getTime()){
                            firstDate = tour;
                        }
                    }else{
                        firstDate = tour;
                    }
                    break;

                }

                this.$el.datepicker({
                    minDate : firstDate != null ? new Date(firstDate[0],Number(firstDate[1])-1,firstDate[2]): new Date(),
                    beforeShowDay: this.paxDays,
                    onSelect: this.selectDate,
                    onChangeMonthYear: this.parent.loadMonth,
                    dateFormat: $.datepicker.ATOM
                });
            },
            isOnSale : function(datestr) {
                var timeslots = Tour.BookingPanel.dates_group[datestr];

                $.each(timeslots, function() {
                    var prices = this.prices;

                    if(prices['adults'] && parseFloat(prices['adults']) != parseFloat(Tour.BookingPanel.group_prices['adults'])) {
                        return true;
                    }
                });

                return false;
            },
            paxDays : function(date) {
                var datestr = $.datepicker.formatDate($.datepicker.ATOM, date);
                var type = $('input[name=type]').val();

                var classes = "available";

                if(type == 'group') {
                    if(typeof Tour.BookingPanel.dates_group[datestr] === 'undefined') {
                        return [false];
                    }

                    if(typeof Tour.BookingPanel.sellout_group[datestr] !== 'undefined') {
                        classes += " sellout"
                    }

                    if(Tour.BookingPanel.Calendar.isOnSale(datestr)) {
                        classes += " sale"
                    }

                }
                else {
                    if(typeof Tour.BookingPanel.dates_private[datestr] === 'undefined') {
                        return [false];
                    }

                    if(typeof Tour.BookingPanel.sellout_private[datestr] !== 'undefined') {
                        classes += " sellout"
                    }
                }

                return [true, classes]
            },
            selectDate : function(date) {
                // Slide time section down
                Tour.BookingPanel.selected.date = date;
                Tour.BookingPanel.$el.find('input[name=date]').val(date);

                Tour.BookingPanel.setSummaryText('date', date);
                Tour.BookingPanel.TimePicker.render();
                Tour.BookingPanel.setSection('time');
            }
        },
        ajaxBooking : function(successCallback){
            var s = $('#booking_form').serialize();
            $.post( "/add_to_cart",s, successCallback)
            .fail(function() {
                document.location.reload()
            })
            .always(function() {
            });

        }
    },

    ModalCheckout :{
        init : function(){
            $('[data-modal-close]').click(this.onCloseModal);
            $("#modal-checkout, #modify-cart").click(function(event){
                $("#booking_form").submit();
                //document.location.href = '/payment';
            });
            $("#btn_book_now").click(this.bookingCheckout);
            //Escape button close shadow box
            $(document).keyup(function(e) {
                if (e.keyCode == 27) $('[data-modal-close]').trigger('click');
            });
        },

        bookingCheckout : function(event){
            event.preventDefault();
            var type = $('input[name=type]').val();
            if (!enableCongratulationsModal()){
                if (type != 'group' || window.innerWidth <= 800){// || window.innerHeight <= 600){
                    $("#booking_form").submit();
                } else {
                    Tour.ModalCheckout.updateModal();
                }
            } else {
                $("#booking_form").submit();
            }
        },

        updateSubtotalModal : function (excludeId){
            var priceSubtotal = Tour.BookingPanel.actualTotalPrice;
            var row_subtotal = formatCurrency(priceSubtotal, 'euro');
            if ($('[name=show_discount]').val() == "1"){
                priceSubtotal = number_format( ( Tour.BookingPanel.actualTotalPrice * (1 - initValues.discountPercentRelatedTour/100) ), 2);
                row_subtotal = '<span style="text-decoration: line-through;">'+formatCurrency(Tour.BookingPanel.actualTotalPrice, "euro")+'</span><br>';
                row_subtotal += '<span style="margin-left: 0px; ">'+formatCurrency(priceSubtotal, "euro")+'</span>';
                $('#row_subtotal').parent().parent().find('.summary-space').hide();
            }
            if (!Tour.BookingPanel.actualTotalPriceItem){
                priceSubtotal = 0;
            }
            $('#row_subtotal').html(row_subtotal);
            var priceTotalGuest = parseFloat(priceSubtotal);
            if(initValues.cart != null && initValues.cart != undefined){
                $.each(initValues.cart, function(j, rt){
                    if (excludeId != rt['event_id'] ) {
                        priceTotalGuest += parseFloat(rt['total_price'] - rt['discount_bundle_tour']);
                    }
                });
            }
            priceTotalGuest = (priceTotalGuest > 0) ? formatCurrency(priceTotalGuest, 'euro') : initValues.currency.symbol + '0';
            $('#cart_total').html(priceTotalGuest);
        },

        updateModal : function (){
            var guestSelected = {};
            var totalGuests = 0;
            Tour.BookingPanel.actualTotalPriceItem = true;
            $.each(['adults', 'students', 'children', 'infants'], function(i, val) {
                var select = Tour.BookingPanel.PaxPicker.$el.find('select[name='+val+']');
                var g = parseInt(select.val());
                if (g > 0){
                    guestSelected[val] = g;
                    totalGuests += g;
                }
            });

            var datetime = moment(Tour.BookingPanel.selected.date+' '+Tour.BookingPanel.selected.time, 'YYYY-MM-DD HH:mm:ss');
            $('#row_date').html(datetime.format('MMMM D, YYYY'));
            $('#row_time').html(datetime.format('hh:mmA'));
            $('#row_guests').html(totalGuests);

            Tour.ModalCheckout.updateSubtotalModal();

            $('#actual_event').show();

            var countrelatedToursModal = 0;
            $('.modal-header.second, .tour-suggestions').show();
            $.each(initValues.relatedToursModal, function(j, rt){
                var priceTotalGuest = 0;
                var priceTotalGuestLocal = 0;
                var priceFor = 'Price for';
                var eventId = rt['event_id'];
                if($('[data-eventid='+eventId+']').length > 0){
                    $.each(guestSelected, function(k, val) {
                        priceFor += " " + val + " " + k + ", ";
                        priceTotalGuest += parseFloat(rt[k+'_price_converted_'+initValues.currency.selected]) * val;
                        priceTotalGuestLocal += parseFloat(rt[k+'_price']) * val;
                    });
                    var discount = number_format(1 - (initValues.discountPercentRelatedTour / 100), 2);
                    var priceTotalGuestDiscount = (priceTotalGuest * discount);
                    var priceTotalGuestLocalDiscount = (priceTotalGuestLocal * discount);
                    var diffPriceDiscount = initValues.currency.symbol + number_format(priceTotalGuest - priceTotalGuestDiscount, 2, ',', '.');
                    priceFor = priceFor.substring(0, priceFor.length - 2);
                    $('[data-eventid='+eventId+'] [data-modalfield=price_for]').html(priceFor);
                    $('[data-eventid='+eventId+'] [data-modalfield=price_total_discount]').html(initValues.currency.symbol + number_format(priceTotalGuestDiscount, 2, ',', '.'));
                    $('[data-eventid='+eventId+'] [name=modal_event_price]').val(number_format(priceTotalGuestLocalDiscount, 2));
                    $('[data-eventid='+eventId+'] [data-modalfield=diff_total_discount]').html('Book now and save ' + diffPriceDiscount);

                    var html = '<option value="-1">CHOOSE DATE</option>';
                    var countOption = 0;
                    $('[data-eventid='+eventId+']').show();

                    //options for dates before
                    var datesBefore = [];
                    var datesAfter = [];
                    var amountAvailable = 6;
                    for (var m = 0; m < 30; m++ ){
                        var dateBefore = initValues.relatedToursModal[j].dates_group[moment(Tour.BookingPanel.selected.date).subtract(m, 'days').format('YYYY-MM-DD')];
                        var dateAfter = initValues.relatedToursModal[j].dates_group[moment(Tour.BookingPanel.selected.date).add(m, 'days').format('YYYY-MM-DD')];
                        if (dateBefore != undefined && datesBefore.length <= amountAvailable){
                            $.each(dateBefore, function(i, d){
                                if (totalGuests < parseInt(d.pax_remaining)){
                                    datesBefore.unshift(dateBefore);
                                    return false;
                                }
                            });
                        }
                        if (dateAfter != undefined && datesAfter.length <= amountAvailable && m != 0){
                            $.each(dateAfter, function(i, d){
                                if (totalGuests < parseInt(d.pax_remaining)){
                                    datesAfter.push(dateAfter);
                                    return false;
                                }
                            });

                        }
                        if (datesBefore.length == amountAvailable &&  datesAfter.length == amountAvailable){
                            break;
                        }
                    }
                    $.each(datesBefore, function(i, d){
                        $.each(d, function(k, t) {
                            countOption++;
                            countrelatedToursModal++;
                            html +='<option value="' + t.date + ' ' + t.time + '">' +  moment(t.date + ' ' + t.time, 'YYYY-MM-DD HH:mm:ss').format('ddd MMM D, h:mmA') + '</option>';
                        });
                    });
                    $.each(datesAfter, function(i, d){
                        $.each(d, function(k, t) {
                            countOption++;
                            countrelatedToursModal++;
                            html +='<option value="' + t.date + ' ' + t.time + '">' +  moment(t.date + ' ' + t.time, 'YYYY-MM-DD HH:mm:ss').format('ddd MMM D, h:mmA') + '</option>';
                        });
                    });
                    $('[data-eventid='+eventId+'] select').html(html);
                    $('[data-eventid='+eventId+'] select').select2('val', '-1');
                    if (countOption == 0){
                        $('[data-eventid='+eventId+']').hide();
                    }
                }
            });
            if (countrelatedToursModal == 0){
                //$('.modal-header.second, .tour-suggestions').hide();
                $("#booking_form").submit();
            } else {
                $('[data-modal-target=cartModal]').addClass('active').fadeIn(100);
                $('html, body').animate({ scrollTop: 0 }, 350);
            }


        },
        onCloseModal : function(event, e){
            event.preventDefault();
            if (!enableCongratulationsModal()){
                Tour.BookingPanel.ajaxBooking(function(data){
                    if (data.response == 'ok'){
                        //initValues.cart = data.cart;
                        //$('#cart_count').html(data.cart.length);
                        WrapperGA.ecAddToCartBookingForm();
                    } else if (data.response == 'error'){
                        //alert(data.message);
                    }
                });
            }
        },
        bookingModalOption : function(event, e){
            event.preventDefault();
            var href = "";
            var type = $('input[name=type]').val();
            var p = $(event.target.parentElement);
            var date_modal = p.parent().find('select').val();
            var addBundleTour = true;
            if($(e).attr('href') != undefined){
                href = $(e).attr('href');
                addBundleTour = false;
            }
            if (date_modal == "-1" && href == ""){
                alert("Please choose a date!");
                return false;
            }

            WrapperGA.ecTrackClickUI(event, e, false);

            p.find('[name=modal_redirect]').val( href );
            p.find('[name=modal_adults]').val( $('[name=adults]').val() );
            p.find('[name=modal_seniors]').val( $('[name=seniors]').val() );
            p.find('[name=modal_students]').val( $('[name=students]').val() );
            p.find('[name=modal_children]').val( $('[name=children]').val() );
            p.find('[name=modal_infants]').val( $('[name=infants]').val() );
            p.find('[name=modal_type]').val(type);
            p.find('[name=modal_date_time]').val(date_modal);
            p.find('[name=date]').val(Tour.BookingPanel.selected.date);
            p.find('[name=time]').val(Tour.BookingPanel.selected.time);
            //measure GA
            WrapperGA.ecAddToCartBundleModal(event.target.parentElement, addBundleTour);
            p.find('form').submit();

        }
    },


    Slider : {
        init : function() {
            // If video is embedded instead of royalSlider, return
            if ($('.royalSlider').size() == 0) return;

            // Header tours slider
            $('.heroSlider').royalSlider({
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
};

$(document).ready(function($) {
    Tour.BookingPanel.init();
    Tour.ModalCheckout.init();
    Tour.Slider.init();

    // Group/private tabs
    $('form .tabs a').click(function(){
        $('form .tabs a').removeClass('selected');
        var type = $(this).attr('class');
        $(this).addClass('selected');

        $('input[name=type]').val(type);
        Tour.BookingPanel.reset(type);
        $('input[name=type]').val(type);

        if(type == 'private') {
            var prices = Tour.BookingPanel['private_prices'];
            $('span#type_price').each(function() {
                $(this).html(prices[$(this).next().next().attr('name')]);
            });
            $('fieldset.people .private-details').show();
        }else{
            $('fieldset.people .private-details').hide();
        }


        $('.' + type + '_price, #breadcrumbs .rating-a').css('display', 'block');

    });
    $('form .tabs a.selected').trigger('click');

    $('[data-target]').on('click', function(e) {
        e.stopPropagation();

        var target = $('#tab-target-'+$(this).attr('data-target'));
        $('.tab-target').not(target).addClass('hidden');
        $(target).removeClass('hidden');
    });

    $('#show_more_reviews').on('click', function(e) {
        e.stopPropagation();

        $('#tab-target-reviews').find('.hidden').removeClass('hidden');
        $(this).addClass('hidden');
    });


    // Notes lightbox
    $('.showNotes').click(function(){
        $('.wrapNotes').addClass('expanded');
    });
    $('.wrapNotes').click(function(){
        $('.wrapNotes').removeClass('expanded');
    });
    $('.wrapNotes').children().click(function(e){
        e.stopPropagation();
    });
    $('.wrapNotes .close').click(function(){
        $('.wrapNotes').removeClass('expanded');
    });

    function showMoreInfo() {
        var a = $('.module-a .information-content');
        var b = $('.module-a .link-a');

        b.click(function(event) {
            $(this).closest('.module-a').find(a).slideToggle(200);
            event.preventDefault();
        });
    }

    showMoreInfo();

    function tabsActiveState() {
        var a = $( '.tabs-list li a' );

        a.click(function() {
            a.removeClass('active');
            $(this).addClass('active');
        });
    }

    tabsActiveState();

    if(location.hash == '#reviews') {

        $('#reviews-tab-button').trigger('click');

    }

    if($('#reviews_list li').length > 0){
        $('#reviews-tab-button').show();
    }

    //social media buttons
    $('.social-media a').click(function(e){
        e.preventDefault();
        window.open($(this).attr('href'), 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        return false;
    });


    //positive steps fancy box
    $(".positive-steps .video").fancybox();

    //new reviews slider

    function initReviewsSlider() {
        var $sl = $('.review-slider');

        if ($.fn.slick) {
            $sl.slick({
                nextArrow: '<i class="fa fa-chevron-right slick-next"></i>',
                prevArrow: '<i class="fa fa-chevron-left slick-prev"></i>'
            });
        }
    }

    initReviewsSlider();

    function eventNavLinks() {
        var $link = $('.event-nav-links a, .book-tour-anchor');
        var NAV_HEIGHT = 88;
        var GAP = 15;
        var TOTAL_GAP = NAV_HEIGHT + GAP;
        $link.click(function(){
            $('html, body').animate({
                scrollTop: $( $.attr(this, 'href') ).offset().top - TOTAL_GAP
            }, 500);
            return false;
        });
    }

    eventNavLinks();

    function showVideo() {
        var w = $(window).width();
    	var h = $('.pl-wrap').height();
    	var pl = $('.player');
    	var iframe = document.getElementById('banner-video');
    	var player = $f(iframe);

    	pl.css('top', h/2);

    	//FULL SCREEN
    	var c = 1;
    	$('.play-button').click(function() {
    		c++;

    		$(this).toggleClass('fa-close');
    		$('.extra-expand').toggleClass('expanded');

    		//odd -> Full Screen
    		if(c%2==0){

    			pl.animate({width:w}, 800, function(){
    				$(this).animate({height:h+200}, 800);
    				pl.addClass('active');
    			});
    			player.api("play");
                $('body, html').animate({
                    scrollTop: ($( ".event-detail-hero" ).position().top - $('#top-fixed').height())
                }, 500);
    		}

    		//even -> Exit Full Screen
    		if (c%2!=0){

    			pl.animate({height:'400'}, 800, function(){
    				$(this).animate({width:'850'}, 800);
    				pl.removeClass('active');
    			});
    			player.api("pause");
    		}
    	});

    	//PLAY BTN
    	$('.play-button').click(function(){
    		$(this).toggleClass('fa-pause');
    	});
    }

    showVideo();

    function showHiddenReviews() {
        var h = $(window).height();
        var $reviews = $('.hidden-reviews');
        var $toggle = $('.show-hidden-reviews-btn');
        var $close = $('.close-hidden-reviews');

        $toggle.click(function() {
            $reviews.show();
            $reviews.css('top');
            $reviews.addClass('active');
            $reviews.animate({
                scrollTop: 0
            }, 0);
            $('body').addClass('no-scroll');
            $('html').addClass('no-scroll');

        });

        $close.click(function() {
            $reviews.removeClass('active');
            window.setTimeout(function() {
                $reviews.hide();
                $('body').removeClass('no-scroll');
                $('html').removeClass('no-scroll');
            }, 300);
        });
    }

    showHiddenReviews();

    function modals() {
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
            if (!enableCongratulationsModal()){
                WrapperGA.ecTrackClickUI(event, this, true);
            }
            closeModal();
        });

        function closeModal() {
            $(target).removeClass('active').fadeOut(300);
        }
    }

    modals();
    
    function expanColapseRewiew(e){
//        $(':not(.slick-active) > .review-body').css({'height': '100px'});
//        $('.slick-active > .review-body').css({'height': '100%'});
    }
//    $('.slick-next, .slick-prev, .review, .review-body, .review-slider-wrapper')
    $('body')
            .mousemove(expanColapseRewiew)
            .mouseover(expanColapseRewiew)
            .mousedown(expanColapseRewiew)
            .mouseup(expanColapseRewiew);
});

$(window).scroll(function () {
    function highlightScrollspy() {
        var $key, $highlights, $description, $sites, $reviews, scrTop;
        var scrTop = $(window).scrollTop();
        var eventLinks = $('.event-nav-links a');

        var NAV_HEIGHT = 88;
        var GAP = 15;
        var TOTAL_GAP = NAV_HEIGHT + GAP;

        $key = $('#key-details');
        $highlights = $('#highlights');
        $description = $('#description');
        $sites = $('#sites-visited');
        $reviews = $('#reviews');
        var keyId = $key.attr('id');

        if ($key.length == 1) {
            if (scrTop >= $key.offset().top - TOTAL_GAP - 5) {
                eventLinks.removeClass('active');
                $('a[href="#'+$key.attr('id')+'"]').addClass('active');
            }

            if (scrTop >= $highlights.offset().top - TOTAL_GAP - 5) {
                eventLinks.removeClass('active');
                $('a[href="#'+$highlights.attr('id')+'"]').addClass('active');
            }

            if (scrTop >= $description.offset().top - TOTAL_GAP - 5) {
                eventLinks.removeClass('active');
                $('a[href="#'+$description.attr('id')+'"]').addClass('active');
            }

            if (scrTop >= $sites.offset().top - TOTAL_GAP - 5) {
                eventLinks.removeClass('active');
                $('a[href="#'+$sites.attr('id')+'"]').addClass('active');
            }

            if (scrTop >= $reviews.offset().top - TOTAL_GAP - 5) {
                eventLinks.removeClass('active');
                $('a[href="#'+$reviews.attr('id')+'"]').addClass('active');
            }
        }

        else {
            return false
        }
    }

    highlightScrollspy();
});
