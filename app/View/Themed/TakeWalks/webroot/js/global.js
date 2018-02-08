var Cart = function(){
    var self = this;
    var $el;
    var el = {};
    var templates = {};
    var cartItems;
    var _init = function(){
        $el = $('.sidebar.shopping-cart');
        el.itemContainer = $('.cart-item-container', $el);
        el.subTotal = $('.subtotal-price', $el);
        el.itemCount = $('.topnav .topnav-cart .item-count');

        if($el.length === 0) return;

        //load templates
        templates.item = _.template($('.template.template-cart-item').html());

        if(typeof initValues.cart !== 'undefined' && initValues.cart !== null && initValues.cart.length > 0){
            //load cart items
            self.loadCartItems(initValues.cart);
        }



    };

    this.loadCartItems = function(items){
        cartItems = [];
        el.itemContainer.html('');

        items.forEach(function(item){
            var cartItem = new CartItem(self, item);
            cartItems.push(cartItem);
            el.itemContainer.append(cartItem.getEl());
        });

        self.updateTotalPrice();

    };

    this.updateTotalPrice = function(){
        var totalPrice = 0;
        var formattedPrice;

        cartItems.forEach(function(item){
            totalPrice += Number(item.getTotalPrice(initValues.currency.selected));
        });
        if(cartItems.length > 0){
            formattedPrice = window.CurrencyExchange.numberFormatCurrency(totalPrice,'selected',true);
        }else{
            formattedPrice = 'Empty';

        }

        el.subTotal.html(formattedPrice);

        el.itemCount.html(cartItems.length);
    };

    this.getItemIndex = function(item){ return cartItems.indexOf(item) };
    this.removeFromCart = function(item){
        cartItems.splice(self.getItemIndex(item),1);
        self.updateTotalPrice();
    };
    this.getTemplate = function(name){ return templates[name]; };

    this.temp = function(){ return templates; };

    _init();
};


var CartItem = function(parent, itemInfo){
    var self = this;
    var $el;
    var el = {};
    var guestTypes = ['adults','infants','children','students'];

    var _init = function(){
        //format info
        var item = {
            name: itemInfo.name,
            tourUrl: '/' + itemInfo['url_name'],
            guests: [],
            date: moment(itemInfo.datetime).format('ddd, D MMM, YYYY [at] H:mm a')
        };

        //amount of price of each guest type
        guestTypes.forEach(function(guestType){
            if(itemInfo[guestType] > 0){
                // console.log(itemInfo, itemInfo[guestType + '_price_converted_' . initValues.currency.selected);
                item.guests[guestType] = {
                    amount: itemInfo[guestType],
                    price: window.CurrencyExchange.numberFormatCurrency(itemInfo[guestType + '_price_converted_' + initValues.currency.selected], 'selected', true)
                }
            }
        });
        
        $el = $(parent.getTemplate('item')(item));
        el.removeFromCart = $('.remove-from-cart', $el);

        //events
        el.removeFromCart.click(removeFromCart);
    };

    this.getTotalPrice = function(currency){
        return itemInfo['total_price_converted_' + currency];
    };

    const removeFromCart = function(){
        $.get('/pages/remove_from_cart/' + parent.getItemIndex(self));
        parent.removeFromCart(self);
        $el.fadeOut(200);

    };


    this.getEl = function(){ return $el; };
    _init();
};

var Login = function(){
    var self = this;
    var $loginEl;
    var $registerEl;
    var $registerForm;
    var $loginForm;

    var _init = function(){
        $registerEl = $('.login-sidebar.login-sidebar-register');
        $loginEl = $('.login-sidebar.login-sidebar-login');
        $registerForm = $registerEl.find('form');
        $loginForm = $loginEl.find('form');

        //events
        $registerForm.submit(formSubmit);
        $loginForm.submit(formSubmit);

        $registerEl.find('.social-login buttons').click(socialLogin);



    };

    var formSubmit = function(e){
        e.preventDefault();
        var type = $(this).data('form-type');
        var $sidebar = $(this).parents('.sidebar');

        $sidebar.find('.error-message').stop(1,1).hide();

        window.Helper.postFromForm($(this),{
            success: function(result){
                if(result.success){
                    switch(type){
                        case 'login':
                            location.href = '/account';
                            break;
                        case 'register':
                            location.href = '/account';
                            break;
                        case 'register_booked':
                            showError($sidebar,'Email sent!');
                            break;
                    }
                }else{
                    switch(type){
                        case 'login':
                            showError($sidebar, 'Invalid username/password');
                            break;
                        case 'register':
                        case 'register_booked':

                            showError($sidebar,result.errors[0]);
                            break;
                    }
                }
            }
        });

    };

    var showError = function($sidebar, error){
        console.log('showError');
        $sidebar.find('.error-message').html(error).stop(1,1).fadeIn();
    };

    var socialLogin = function(){

    };

    _init();
};


window.CurrencyExchange = {
    exchangeRate: 1,
    selected: null,
    symbol: null,
    exchangeRates: null,
    init: function(){
        if(typeof initValues.currency !== 'undefined'){
            window.CurrencyExchange.exchangeRate = initValues.currency.exchangeRate;
            window.CurrencyExchange.selected = initValues.currency.selected;
            window.CurrencyExchange.symbol = initValues.currency.symbol;
        }
        if(typeof initValues.exchangeRates !== 'undefined'){
            window.CurrencyExchange.exchangeRates = initValues.exchangeRates;
        }
    },
    numberFormatCurrency: function(amount, currency, hasSymbol){
        if(Number(amount) === 0) return 'FREE';
        if(currency === 'selected') currency = initValues.currency.selected;
        switch(currency){
            case 'USD':
            case 'CAD':
            case 'AUD':
                amount = window.CurrencyExchange.numberFormat(amount, 2, '.', ',');
                break;
            default:
                amount = window.CurrencyExchange.numberFormat(amount, 2, ',', '.');
                break;
        }
        return (hasSymbol ? window.CurrencyExchange.getSymbol(currency) : '') + amount
    },
    getSymbol: function(currency){
        if(window.CurrencyExchange.exchangeRates !== null && typeof window.CurrencyExchange.exchangeRates[currency] !== 'undefined'){
            return window.CurrencyExchange.exchangeRates[currency].symbol;
        }
        return window.CurrencyExchange.symbol;
    },
    numberFormat: function(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
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
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }


};


$(function(){
    window.CurrencyExchange.init();
    window.WalksCart = new Cart();
    new Login();
});