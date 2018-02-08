function formatNumber(nStr, thousandsSeparator, decimal) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = decimal + (x.length > 1 ? x[1] : '00');
    x2 = x2.length < 3 ? x2 + '0' : x2;
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + thousandsSeparator + '$2');
    }
    return x1 + x2;
}

var Payment = {
    exchangeRates: null, //all of the exchange rates
    exchangeRate: null, //selected exchange rate
    currency: null,
    defaultCurrency: 'EUR',
    totalPrice : 0,
    donations: {},
    isDonatationPage: false,
    translations: {
        ticketTypes: {
            adults: 'Adultos',
            children: 'Niños',
            infants: 'Infantes',
            students: 'Estudiantes',
        },
        months: {
            January: 'enero',
            February: 'febrero',
            March: 'marzo',
            April: 'abril',
            May: 'mayo',
            June: 'junio',
            July: 'julio',
            August: 'agosto',
            September: 'septiembre',
            October: 'octubre',
            November: 'noviembre',
            December: 'diciembre'

        }
    },
    el: {
        sideBar : null,
        currencyPicker: null,
        form: null
    },
    templates: {
        cartTour: null,
        cartDonation: null
    },
    init: function(){

        this.isDonatationPage = $('script.cartTour').length == 0;

        this.el.sideBar = $('aside');
        this.el.currencyPicker = $('#currencyPicker');
        this.el.form = $('#travellerInfo');

        this.exchangeRates = initValues.exchangeRates;
        //set the default exchange rate
        this.currency = this.el.currencyPicker.find('.selected').html();
        Payment.exchangeRate = Payment.exchangeRates[Payment.currency];

        if(!this.isDonatationPage){
            this.templates.cartTour = _.template($('script.cartTour').html());
            this.templates.cartDonation = _.template($('script.cartDonation').html());
        }

        this.enableCurrencySwitcher();
        this.enableDonations();
        if(!this.isDonatationPage) {
            this.loadCart();
        }else {
            //$('#content .donation_amount').change(Payment.updateDonation);
        }

    },
    enableCurrencySwitcher:function(){
        this.el.currencyPicker.on('click','.currency',function(){
            $(this).siblings('.selected').removeClass('selected');
            $(this).addClass('selected');
            if($(this).attr('data-currency') != Payment.currency){
                Payment.currency = $(this).attr('data-currency');
                //set the default exchange rate
                Payment.exchangeRate = Payment.exchangeRates[Payment.currency];
                if(!Payment.isDonatationPage) {
                    Payment.loadCart();
                }else{
                    Payment.updateDonation();
                }
            }
        });
    },
    enableDonations: function(){
        $('#charities').on('change','select',function() {
            Payment.donations[$(this).attr('data-charity-id')] = {
                price: $(this).val(),
                name : $(this).attr('data-charity-name')
            };

            Payment.loadCart();
        });
    },
    loadCart: function(){

        //put the selected currency into the form
        Payment.el.form.find('input[name="currency"]').val(Payment.currency);

        //reset total price
        Payment.totalPrice = 0;

        //delete old tours
        Payment.el.sideBar.find('article').remove();

        //create new tours
        var position = 0;
        initValues.cart.forEach(function(tour){
            tour.position = position++;

            //translate tickets types
            for(var englishWord in Payment.translations.ticketTypes){
                if(typeof tour.tickets[englishWord] !== 'undefined'){
                    tour.tickets[Payment.translations.ticketTypes[englishWord]] = tour.tickets[englishWord];
                    delete tour.tickets[englishWord];
                }
            }

            //translate month in date
            for(var englishMonth in Payment.translations.months){
                tour.dateTime = tour.dateTime.replace(englishMonth, Payment.translations.months[englishMonth]);
                console.log(englishMonth, Payment.translations.months[englishMonth]);
            }
            console.log(tour);

            var cartItemDiv = Payment.templates.cartTour(Payment.formatTourPrices(tour));
            if(position == 1){
                Payment.el.currencyPicker.after(cartItemDiv);
            }else{
                Payment.el.sideBar.find('article:last').after(cartItemDiv);
            }

        });

        //create the donations if any
        for(var charityId in Payment.donations){
            var donation = Payment.donations[charityId];
            if(donation.price > 0){

                var donationAmount = Payment.convertPrice(donation.price);
                Payment.totalPrice += Number(donationAmount);
                donation.formattedPrice = Payment.formatPrice(donationAmount);

                donation.formattedEuroPrice = formatNumber(donation.price,',','.');

                Payment.el.sideBar.find('article:last').after(Payment.templates.cartDonation(donation));
            }
        }

        if (initValues.promo_discount_fixed_total != null){
            var subtotal = Number(Payment.totalPrice);
            var discountFixed = Number(initValues.promo_discount_fixed_total[Payment.currency]);
            Payment.totalPrice = subtotal - discountFixed;
            if (Payment.totalPrice < 0){
                Payment.el.sideBar.find('.subtotalPrice, .discountFixed').parent().parent().hide();
                Payment.totalPrice = 0;
            } else {
                Payment.el.sideBar.find('.subtotalPrice, .discountFixed').parent().parent().show();
                Payment.el.sideBar.find('.subtotalPrice').html(Payment.formatPrice(subtotal.toFixed(2)));
                Payment.el.sideBar.find('.discountFixed').html(Payment.formatPrice(discountFixed.toFixed(2)));
            }
        }

        //update total price
        Payment.el.sideBar.find('.fullPrice').html(Payment.formatPrice(Number(Payment.totalPrice).toFixed(2)));
    },
    formatTourPrices: function(tour){

        var subtotal = 0;

        //format ticket prices
        for(var ticketType in tour.tickets){
            var ticketTypePrice = tour.tickets[ticketType][Payment.currency];
            tour.tickets[ticketType].formattedPrice = ticketTypePrice > 0 ? Payment.formatPrice(ticketTypePrice) : 'Gratis';
            subtotal += Number(ticketTypePrice);
        }

        //format subtotal price
        tour.formattedTotalPrice = subtotal > 0 ? Payment.formatPrice(subtotal.toFixed(2)) : 'Gratis';

        tour.originalPrice = tour.formattedTotalPrice;

        var discountPercent = 0;

        //if there is a discount then apply it
        if(tour.promo_type != ""){
            if(tour.promo_discount_fixed > 0 || tour.promo_discount_fixed_by_event > 0){
                var promo_discount_fixed = Number(tour.discountFixed[Payment.currency]);
                subtotal = subtotal - promo_discount_fixed;
                tour.discountAmount = Payment.formatPrice((promo_discount_fixed).toFixed(2));
                if (subtotal < 0){
                    subtotal = 0;
                }
            } else if(tour.discount > 0){
                discountPercent = tour.discount / 100;
                tour.discountAmount = Payment.formatPrice((subtotal * discountPercent).toFixed(2));
                subtotal *= 1 - discountPercent;
            }
        }

        tour.priceDiscount = subtotal > 0 ? Payment.formatPrice(subtotal.toFixed(2)) : 'Free';

        Payment.totalPrice = Payment.totalPrice + Number(subtotal.toFixed(2));

        return tour;
    },
    convertPrice: function(price){
        if(Payment.currency != Payment.defaultCurrency){
            price *= Payment.exchangeRates[Payment.defaultCurrency].exchange;
            if(Payment.currency != 'USD'){
                price /= Payment.exchangeRate.exchange;
            }
        }
        return Number(price).toFixed(2);
    },
    formatPrice: function(price){
        return Payment.exchangeRate.symbol + formatNumber(price, Payment.exchangeRate.separator, Payment.exchangeRate.decimal);
    },
    //only for the donation page.
    updateDonation: function(){
        var totalPrice = Payment.convertPrice($('#content .donation_amount option:selected').attr('value'));
        Payment.el.sideBar.find('#total_price').html(
            Payment.formatPrice(totalPrice)
        );
    },
    onSubmitPromoCode : function(event,promo_form,payment_form){
        //event.preventDefault();
        //'#travellerInfo'
        $.each($(payment_form).find('input'),function(a,inp){
            if (inp.name != ''){
                var input = document.createElement("input");
                input.name = inp.name;
                input.type = 'hidden';
                input.value = inp.value;
                promo_form.appendChild(input);
            }
        });
        return true;
    }
};

$(document).ready(function(){

    // Toggle promo code form
    $('.promo label').click(function(){
        $(this).parent().toggleClass('expanded');
    });

    /* Autofill city/state when zipcode is entered
     $('.zip input').keyup(function() {
     var el = $(this);
     if (el.val().length >= 5) {
     $.ajax({
     url: "http://zip.elevenbasetwo.com",
     cache: false,
     dataType: "json",
     type: "GET",
     data: "zip=" + el.val(),
     success: function(result, success) {
     $(".city input").val(result.city);
     $(".state input").val(result.state);
     }
     });
     }
     });*/

    // Copy name from traveller information section to credit card info
    $('.fname input, .lname input').blur(function(){
        $('.ccName input[name=ccFirstName]').val($('.fname input').val()).trigger('blur');
        $('.ccName input[name=ccLastName]').val($('.lname input').val()).trigger('blur')
    });

    // Validate credit card type
    $('input[name="cc"]').keyup(function(){
        if (!checkCreditCard($(this).val())) {
            $(this).attr('class','');
        } else {
            $(this).attr('class',cardType);
        }
    });

    $('form#travellerInfo').validate({
        errorPlacement: $.noop,
        ignore: null,
        submitHandler: function(form) {

            if(!$('input#conditions', form).prop('checked')) {
                $('span#conditionError', form).html('<br />You must accept the Booking Conditions');
                return false;
            }

            //make sure email and confirm email are the same
            if($('input[name="email"]').val() != $('input[name="confirm_email"]').val()){
                $('span#conditionError', form).html('<br />Your email does not match the confirm email');
                return false;
            }


            $(form).find('[type=submit]').attr('disabled','disabled');
            form.submit();

        },
        highlight: function (element, errorClass, validClass) {
            var elem = $(element);
            if (elem.hasClass("select2-offscreen")) {
                $("#s2id_" + elem.attr("id") + " ul").addClass(errorClass);
            } else {
                elem.addClass(errorClass);
            }
        },

        unhighlight: function (element, errorClass, validClass) {
            var elem = $(element);
            if (elem.hasClass("select2-offscreen")) {
                $("#s2id_" + elem.attr("id") + " ul").removeClass(errorClass);
            } else {
                elem.removeClass(errorClass);
            }
        }
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

    $('#add_promo').on('click', function(e) {
        $('#promo_code').slideDown();
    });

    // Disable enter key for form submit
    $('form').keypress(function(e){
        if (e.which == 13) { // Enter key = keycode 13
            return false;
        }
    });

    //Flash message close button
    $('.wrapNotes .add_to_cart .close, .flashMessage a img').click(function(){
        $(this).parent().parent().animate({
            //slide up
            height: 0
        },400,function(){
            //then disappear
            $(this).css('display','none');
        });
    });

    //add a search field to the country select drop down
    $('#fci').select2({
        width: 'resolve'
    }).change(function(){
        var countryCodeAVS = [
            'US', // =>  'United States',
            'CA', // => 'Canada',
            'GB', // => 'Great Britain',
            'UK'  // => 'United Kingdom',
        ];
        var value = $(this).select2("val");
        var stateDropdown = $('.state_dropdown');
        var stateText = $('#fcg').removeAttr('required');
        var stateLabel = $('label[for="fcg"]');
        stateText.removeAttr('required');
        $('#s2id_state_dropdown_us,#s2id_state_dropdown_ca,#s2id_state_dropdown_gb').hide();
        stateText.val('');

        if(countryCodeAVS.indexOf(value) != -1){
            stateLabel.html('');
            value = value.toLowerCase();
            value = (value == 'uk') ? 'gb' : value;
            stateText.hide().attr({name: 'state_text'});
            if (value == 'us' || value == 'ca' || value == 'gb'){
                stateDropdown.select2("val","__");
                $('#state_dropdown_'+value).attr('name', 'state');
                $('#s2id_state_dropdown_'+value).show();
            } else {
                stateLabel.html('State Code');
                stateDropdown.select2("val","").attr('name', 'state_dropdown');
                stateText.show().attr({required: 'required', name: 'state', maxlength : 2});
            }
        } else {
            stateLabel.html('Estado / Provincia');
            stateDropdown.select2("val","").attr('name', 'state_dropdown');
            stateText.show().attr({required: 'required', name: 'state', maxlength : 20});
        }

    });

    function charitiesFix() {
        var w = $(window).width();

        if (w < 760) {
            $('#charities').css('width', w - 45 + 'px');
            $('.cart-a article .select2-container').css('width', w - 45 + 'px');
        }

        else {
            return false
        }
    }

    charitiesFix();
    $('#apply_promo_form').submit(function(event){Payment.onSubmitPromoCode(event, this, '#travellerInfo')});

    Payment.init();
});
