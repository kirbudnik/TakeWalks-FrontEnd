var Payment = function(){
    var self = this;
    var $el;
    var el = {};
    var templates = {};
    var cartItems = [];
    var currency;
    var formPayment;
    var _init = function(){
        $el = $('main.payment');
        el.itemContainer = $('.payment-item-container', $el);
        el.total = $('.price', $el);
        el.currencyTogglers = $('.currency-togglers .buttons', $el);
        el.country = $('[name=country]', $el);
        el.state_select = $('#state_select', $el);
        el.state_text = $('#state_text', $el);
        el.iataContainer = $('.sidebar-payment-item.sidebar-iata');

        currency = window.CurrencyExchange.selected;

        if(typeof initValues.cart === 'undefined' || initValues.cart === null || initValues.cart.length === 0){
            formPayment = new PaymentForm(self, $('#formPayment', $el));
            return false;
        }

        //load templates
        templates.item = _.template($('.template.template-payment-item').html());
        templates.guestRow = _.template($('.template.template-payment-item-guest-row').html());
        templates.discountRow = _.template($('.template.template-payment-item-discount-row').html());

        //load cart items
        initValues.cart.forEach(function(item){
            var cartItem = new PaymentItem(self, item);
            cartItems.push(cartItem);
            el.itemContainer.append(cartItem.getEl());
        });

        formPayment = new PaymentForm(self, $('#formPayment', $el));

        self.updateTotalPrice();

        //events
        el.currencyTogglers.on('click','.currency-toggler', function(){ setCurrency($(this).attr('data-currency')); });
        el.country.on('change', function(event){
            if($(this).val() !== 'US') {
                el.state_select.parent().hide();
                el.state_select.val('').removeAttr('name').removeAttr('required');

                el.state_text.parent().show();
                el.state_text.attr('name', 'state').attr('required', 'required');
            } else {
                var txtVal = el.state_text.val();
                el.state_text.parent().hide();
                el.state_text.val('').removeAttr('name').removeAttr('required');

                el.state_select.val(txtVal);
                el.state_select.parent().show();
                el.state_select.attr('name', 'state').attr('required', 'required');
                el.state_select.trigger('change');
            }
        });
        el.country.trigger('change');

        el.iataContainer.find('form').submit(addIataNumber);
        el.iataContainer.on('click','.clear-input',clearIataNumber);
        // testFill();



        //select selected currency
        setCurrency(window.CurrencyExchange.selected);
    };

    var setCurrency = function(newCurrency){
        currency = newCurrency;
        el.currencyTogglers.find('.active').removeClass('active');
        el.currencyTogglers.find('[data-currency=' + currency + ']').addClass('active');
        cartItems.forEach(function(item){
            item.setCurrency(currency);
        });

        self.updateTotalPrice(currency);
        $el.find('form input[name=currency]').val(currency);
    };

    this.updateTotalPrice = function(currency){
        var totalPrice = 0;
        cartItems.forEach(function(item){
            totalPrice += Number(item.getTotalPrice());
        });
        el.total.html(window.CurrencyExchange.numberFormatCurrency(totalPrice, currency,true));
    };

    this.getCurrency = function(){ return currency; };
    this.getItemIndex = function(item){ return cartItems.indexOf(item) };
    this.removeFromCart = function(item){
        cartItems.splice(self.getItemIndex(item),1);
        self.updateTotalPrice();
    };
    this.getTemplate = function(name){ return templates[name]; };
    var testFill = function(){
        var $form = $('form');
        var dummyData = {
            ccFirstName: 'test',
            ccLastName: 'tester',
            email: 'dev@vimbly.com',
            phone_number: '212-212-2112',
            ccNo: 4112344112344113,
            ccExpires: '08/20',
            ccCCV: '123',
            zip: 12345,
            state: 'NY',
            street_address: '123 fake street',
            city: 'manhattan',
            country: 'US'
        };

        // for(var key in dummyData){
        //     $form.find('[name=' + key + ']').val(dummyData[key]);
        // }

    };

    this.isCartEmpty = function(){ return cartItems.length === 0 } ;
    this.checkCartEmpty = function(){
        formPayment.checkCartEmpty();
    };

    var addIataNumber = function(e){
        e.preventDefault();
        $el.find('[name=iata]').val(el.iataContainer.find('input[name=iata-input]').val());
    };
    var clearIataNumber = function(){
        $el.find('[name=iata]').val('');
    };

    _init();
};


var PaymentItem = function(parent, tour){
    var self = this;
    var $el;
    var el = {};
    var guestTypes = ['adults','infants','children','students'];
    var subtotal = 0;
    var _init = function(){
        //format info
        var item = {
            name: tour.name,
            date: tour.formattedDate,
            discount: null,
            slug: tour.url_name,
        };

        $el = $(parent.getTemplate('item')(item));
        el.removeFromCart = $('.remove-from-cart', $el);
        el.summary = $('.checkout-summary', $el);
        el.priceRows = $('.price-rows', el.summary);
        el.subTotal = $('.subtotal-row span', el.summary);

        //events
        el.removeFromCart.click(removeFromCart);

    };

    this.setCurrency = function(currency){
        //reset
        el.priceRows.html('');
        subtotal = Number(tour.basePrice[currency]);
        var discountAmount = 0;

        //amount of price of each guest type
        guestTypes.forEach(function(guestType){

            if(typeof tour.tickets[guestType] === 'undefined')return;

            if(tour.tickets[guestType].amount > 0){
                var price = tour.tickets[guestType][currency];
                subtotal += Number(price);

                el.priceRows.append(parent.getTemplate('guestRow')({
                    count: tour.tickets[guestType].amount,
                    guest: guestType,
                    price: window.CurrencyExchange.numberFormatCurrency(price, currency,true)
                }))


            }
        });

        //calculate discount
        var discountPercent = 0;
        //first discount for bundle tour
        //todo no bundled tours here
        if(tour.discount_bundle_tour_percent > 0){
            discountPercent = tour.discount_bundle_tour_percent / 100;
            tour.discountBundleTour = (subtotal * discountPercent).toFixed(2);
            subtotal *= 1 - discountPercent;
        }

        //if there is a discount then apply it
        if(tour.promo_type !== ""){
            if(tour.promo_discount_fixed > 0 || tour.promo_discount_fixed_by_event > 0){
                var promo_discount_fixed = Number(tour.discountFixed[currency]);
                subtotal -= promo_discount_fixed;
                discountAmount = (promo_discount_fixed).toFixed(2);
                subtotal = Math.max(0, subtotal);
            } else if(tour.discount > 0){
                discountPercent = tour.discount / 100;
                discountAmount = (subtotal * discountPercent).toFixed(2);
                subtotal *= 1 - discountPercent;
            }
        }

        if(discountAmount > 0){
            el.priceRows.append(parent.getTemplate('discountRow')({
                percent: tour.discount > 0 ? tour.discount + '%' : '',
                amount: window.CurrencyExchange.numberFormatCurrency(discountAmount, currency, true)
            }));
        }


        el.subTotal.html(window.CurrencyExchange.numberFormatCurrency(subtotal, currency, true));

    };

    this.getTotalPrice = function(){
        return subtotal;
    };

    var removeFromCart = function(){
        $.get('/pages/remove_from_cart/' + parent.getItemIndex(self),function(){
            parent.checkCartEmpty();
        });
        parent.removeFromCart(self);
        $el.fadeOut(200);

    };


    this.getEl = function(){ return $el; };
    _init();
};

var PaymentForm = function(parent, $el){
    var self = this;
    var $errorMessage;
    var $submitButton;

    var _init = function() {
        $errorMessage = $el.find('.error-message');
        $submitButton = $el.find('.center-btn .primary');
        //events
        $el.submit(formSubmit);
        self.checkCartEmpty();

    };

    this.checkCartEmpty = function(){
        if(parent.isCartEmpty()){
            $submitButton.removeClass('green');
            $submitButton.click(function(e){
                e.preventDefault();
                showError('Your cart is empty.');
            });
        }
    };

    var formSubmit = function(){
        $errorMessage.hide();
        $el.find('[name=ccFirstName]').val( getVal('first_name'));
        $el.find('[name=ccLastName]').val( getVal('last_name'));

        // if()

        //check credit card number
        var ccNum = getVal('ccNo');
        if(ccNum.length !== 16 || (ccNum.length === 16 && !luhnCheck(ccNum) )){
            showError('Please check your credit card number');
            return false;
        }
        if(!$('#ccb_01').prop('checked')){
            showError('You must accept the terms and conditions');
            return false;
        }

        $('.complete_booking').prop('disabled', true);
        return true;
    };

    var showError = function(message){
        $errorMessage.html(message).fadeIn(200);
    };

    var getVal = function(name){
        return $el.find('[name=' + name + ']').val();
    };

    var luhnCheck = function(val) {
        var sum = 0;
        for (var i = 0; i < val.length; i++) {
            var intVal = parseInt(val.substr(i, 1));
            if (i % 2 == 0) {
                intVal *= 2;
                if (intVal > 9) {
                    intVal = 1 + (intVal % 10);
                }
            }
            sum += intVal;
        }
        return (sum % 10) == 0;
    };


    _init();
};

$(function(){ new Payment(); });