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
    isGiftCardPage: false,
    giftCardAmounts: [25, 50, 100, 200, 300, 500],
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
        this.isGiftCardPage = $('.gift-card-cart-item').length > 0;

        this.el.sideBar = $('aside');
        this.el.currencyPicker = $('#currencyPicker');
        this.el.form = $('#travellerInfo');

        this.exchangeRates = initValues.exchangeRates;
        //set the default exchange rate
        this.currency = this.el.currencyPicker.find('.selected').html();
        if(this.currency === undefined) this.currency = 'USD';
        Payment.exchangeRate = Payment.exchangeRates[Payment.currency];

        if(!this.isDonatationPage){
            this.templates.cartTour = _.template($('script.cartTour').html());
            this.templates.cartDonation = _.template($('script.cartDonation').html());
        }

        this.enableCurrencySwitcher();
        this.enableDonations();
        if(this.isGiftCardPage){ this.enableGiftCards(); } //gift card page: /gift_cards
        if(!this.isDonatationPage) {
            this.loadCart();
        }else {
            //$('#content .donation_amount').change(Payment.updateDonation);
        }

        this.reCaptcha.init();
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
                if(Payment.isGiftCardPage){ Payment.giftCardCurrencyUpdate(); }
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
        if (initValues.cart !== undefined) {
            initValues.cart.forEach(function (tour) {
                tour.position = position++;
                var cartItemDiv = Payment.templates.cartTour(Payment.formatTourPrices(tour));
                if (position == 1) {
                    Payment.el.currencyPicker.after(cartItemDiv);
                } else {
                    Payment.el.sideBar.find('article:last').after(cartItemDiv);
                }

            });
        }
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

        var subtotal = Number(tour.basePrice[Payment.currency]);

        //format ticket prices
        for(var ticketType in tour.tickets){
            var ticketTypePrice = tour.tickets[ticketType][Payment.currency];
            tour.tickets[ticketType].formattedPrice = ticketTypePrice > 0 ? Payment.formatPrice(ticketTypePrice) : 'Free';
            subtotal += Number(ticketTypePrice);
        }

        //format subtotal price
        tour.formattedTotalPrice = subtotal > 0 ? Payment.formatPrice(subtotal.toFixed(2)) : 'Free';

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
    enableGiftCards: function(){
        Payment.giftCardCurrencyUpdate();
        $('.gift-card-cart-item select').change(Payment.giftCardUpdateTotal);
    },
    giftCardCurrencyUpdate: function(){
        var currency = initValues.exchangeRates[$('#currencyPicker .selected').data('currency')];
        var $select = $('.gift-card-cart-item select').html('');
        $select.select2("destroy");
        Payment.giftCardAmounts.forEach(function(amount){
            $select.append(
                $('<option />',{value:amount}).html(Payment.formatPrice(amount))
            );
        });
        $select.select2();
        $('input[name=currency]').val($('#currencyPicker .selected').data('currency'));
        Payment.giftCardUpdateTotal();
    },
    giftCardUpdateTotal: function(){
        $('.fullPrice').html(Payment.formatPrice($('.gift-card-cart-item select').val()));
        $('input[name=price]').val($('.gift-card-cart-item select').val());
    },
    reCaptcha: {
        formCaptcha: null,
        init: function() {
            this.formCaptcha = $('form#travellerInfo');
            this.formCaptcha.find('[name=giftcard]').val("walks");
        },
        validate: function() {
            console.log('grecaptcha');
            grecaptcha.execute();
        },
        validForm: function(token) {
            console.log('valid form');
            Payment.reCaptcha.formCaptcha.find('[name=giftcard]').remove();
            Payment.reCaptcha.formCaptcha.attr('disabled','disabled');
            Payment.reCaptcha.formCaptcha.submit();
        }
    }
};

function reCaptchaCallback(token) {
    if(token !== undefined) {
        Payment.reCaptcha.validForm(token);
    }
}

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


            var giftcard = $(form).find('[name=giftcard]').val();
            if (giftcard !== undefined ) {
                Payment.reCaptcha.validate();
            } else {
                $(form).find('[type=submit]').attr('disabled','disabled');
                form.submit();
            }
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
        var stateText = $('#fcg');
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
            stateLabel.html('State / County');
            stateDropdown.select2("val","").attr('name', 'state_dropdown');
            stateText.show().attr({required: 'required', name: 'state', maxlength : 20});
        }

    });
    
    Payment.init();

});



