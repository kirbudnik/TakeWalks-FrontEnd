(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
//*************************************************************
//The Enhanced Ecommerce plug-in should not be used alongside the Ecommerce (ecommerce.js) plug-in for the same property.
//*************************************************************


var WrapperGA = {
    analyticsAccount: null,
    affiliation: 'Walks LLC',
    initValues: null,
    cart: null,
    transaction: null,
    productList: null,
    currencyActual: null,
    currencyLocal: null,
    listname: null,
    productDetail: null,
    cakeDebugLevel: true,
    init: function(params){
        this.analyticsAccount = params.analyticsAccount;
        this.initValues = params.initValues;
        this.cakeDebugLevel = params.cakeDebugLevel;
        this.currencyActual = params.actualCurrency;
        this.currencyLocal = (this.initValues.theme == 'nyc') ? 'USD' : 'EUR';
        this.cart = this.initValues.cart;
        this.productList = this.initValues.product_list;
        this.productDetail = this.initValues.product_detail;
        this.transaction = this.initValues.transaction;
        //make sure it's not in debug mode
        if (this.cakeDebugLevel == 0){
            ga('create', this.analyticsAccount, 'auto');
            ga('require', 'ec');
            ga('set', '&cu', this.currencyLocal);
        }
        this.ecOnLoadMeasure(this.initValues.measure);
    },
   ecOnLoadMeasure: function(measure){
       switch(measure) {
           case "transaction_success":
               this.ecTransactionSuccess();
               break;
           case "view_product_list":
               this.ecViewProductList();
               break;
           case "view_product_detail":
               this.ecViewProductDetailPage();
               break;
           case "view_payment_page":
               this.ecViewPaymentPage();
               break;
           default :
               ga('send', 'pageview');
       }

    },
    ecTransactionSuccess: function(){
        if (this.transaction.success != 0){
            var confirmCart = this.initValues.transaction.confirmCart;
            var totalPrice = 0;
            var totalLocalPrice = 0;
            //same currency from server side
            ga('set', '&cu', this.currencyActual);
            $.each(confirmCart, function(a,item){
                totalPrice += parseFloat(item.charged_amount);
                totalLocalPrice += parseFloat(item.charged_local_amount);
                ga('ec:addProduct', {
                    'id': item.sku,                 // SKU
                    'name': item.name,              // Product name. Required
                    'category': 'Tour',             // Category or variation
                    'price': item.charged_amount,   // Unit price
                    'quantity': '1'                 // Quantity
                });
            });
            ga('ec:setAction', 'purchase', {
                'id': WrapperGA.transaction.booking_id,       // Transaction ID = Booking ID
                'revenue': totalPrice,              // Grand Total
                'affiliation' : WrapperGA.affiliation,
                'shipping': '0',                    // Shipping
                'tax': '0'                          // Tax
            });

            ga('send', 'pageview');     // Send transaction data with initial pageview.

            //send total local revenue to optimizely
            window.optimizely = window.optimizely || [];
            window.optimizely.push(['trackEvent', 'totalRevenue', {'revenue': totalLocalPrice * 100}]);

        }
    },
    ecViewProductList: function(){
        var events = this.productList.events;
        if (events.length > 0 ){
            WrapperGA.listname = "Search Results " + ((this.productList.list != null) ? this.productList.list : "");
            $.each(events, function(i,event){
                ga('ec:addImpression', {
                    'id': event.event_id,
                    'name': event.event_name_short,
                    'list': WrapperGA.listname,
                    'position': i
                });
            });
            ga('send', 'pageview');
        }
    },
    /**
     * Track product click on listing page
     * @param event
     * @param e
     * @returns {boolean}
     */
    ecOnProductClick: function(event,e){
        event.preventDefault();
        if(WrapperGA.cakeDebugLevel != 0) {
            document.location = e.dataset.href;
            return true;
        }
        ga('ec:addProduct', {
            'id': e.dataset.sku,
            'name': e.dataset.name,
            'category': 'Tour',
            'position': e.dataset.position
        });
        ga('ec:setAction', 'click', {list: WrapperGA.listname});

        // Send click with an event, then send user to product page.
        ga('send', 'event', 'UX', 'click', WrapperGA.listname, {
            hitCallback: function() {
                document.location = e.dataset.href;
            }
        });
        return !ga.loaded;
    },
    /**
     * Track any click on UI
     * @param event
     * @param e
     * @param defaultEvent
     * @returns {boolean}
     */
    ecTrackClickUI: function(event,e, defaultEvent){
        var description = (e.dataset.description) ? e.dataset.description : 'no description';
        var price = (e.dataset.price != undefined) ? e.dataset.price : '';
        if(WrapperGA.cakeDebugLevel != 0 && e.dataset.href != undefined) {
            document.location = e.dataset.href;
            return true;
        }
        if(!defaultEvent) {
            event.preventDefault();
        }

        if(e.dataset.sku && e.dataset.name) {
            var bundleImpresion = (this.productDetail) ? this.productDetail.name_short : '';
            var bundlePosition = 'none';
            switch (e.dataset.position) {
                case '1':
                    bundlePosition = 'left';
                    break;
                case '2':
                    bundlePosition = 'center';
                    break;
                case '3':
                    bundlePosition = 'right';
                    break;
            }
            ga('ec:addImpression', {
                'id': e.dataset.sku,
                'name': e.dataset.name,
                'list': 'Bundled modal ' + bundleImpresion,
                'position': e.dataset.position,
                'dimension1': bundlePosition
            });

            ga('ec:addProduct', {
                'id': e.dataset.sku,
                'name': e.dataset.name,
                'category': 'Tour',
                'position': e.dataset.position,
                'price': price
            });
        }
        ga('ec:setAction', 'click');
        ga('send', 'event', 'UX', 'click', description, {
            hitCallback: function() {
                if(!defaultEvent && e.dataset.href != undefined) {
                    document.location = e.dataset.href;
                }
            }
        });
    },
    ecViewProductDetailPage: function(){
        if (this.productDetail != null){
            ga('ec:addProduct', {
                'id': this.productDetail.sku,
                'name': this.productDetail.name_short
            });
            ga('ec:setAction', 'detail');
            ga('send', 'pageview');
        }
    },
    ecAddItemToCart: function(item){
        ga('ec:addProduct', {
            'id': item.sku,
            'name': item.name_short,
            'category': 'Tour',
            'price': item.price,
            'quantity': item.guests
        });
        ga('ec:setAction', 'add');
        ga('send', 'event', 'UX', 'click', 'add to cart');
    },
    ecAddToCartBundleModal: function(form, addBundleTour){
        //bundled tour
        if(addBundleTour){
            form = $(form);
            var guests = document.getElementById('ec_quantity');
            var item = {};
            item.guests = guests.value;
            item.price = form.find('[name=modal_event_price]').val();
            item.id = form.find('[name=modal_event_id]').val();
            item.sku = form.find('[name=modal_event_sku]').val();
            item.name_short = form.find('[name=modal_event_name]').val();
            this.ecAddItemToCart(item);
        }
        //main tour
        this.ecAddToCartBookingForm();
    },
    ecAddToCartBookingForm: function(){
        var guests = document.getElementById('ec_quantity');
        var price = document.getElementById('ec_price');
        var item = {};
        item.guests = guests.value;
        item.price = price.value;
        item.id = this.productDetail.id;
        item.sku = this.productDetail.sku;
        item.name_short = this.productDetail.name_short;
        this.ecAddItemToCart(item);
        return true;
    },
    ecCheckout: function(){
        if(this.cart != undefined && this.cart != null && this.cart.length > 0){
            $.each(this.cart, function(i,item){
                var totalPrice = item.totalPrice * ( 1 - (item.discount_bundle_tour_percent/100) );
                totalPrice = WrapperGA.number_format( ( totalPrice * WrapperGA.initValues.currency.exchangeRate) , 2 );
                ga('ec:addProduct', {
                    'id': item.sku,
                    'name': item.name,
                    'category': 'Tour',
                    'price': totalPrice,
                    'quantity': '1'
                });
            });
            return true;
        } else {
            return false;
        }
    },
    ecViewPaymentPage: function(){
        // A value of 1 indicates this action is first checkout step.
        ga('ec:setAction','checkout', {'step': 1 });
        ga('send', 'pageview');   // Pageview for payment.html
    },
    ecRemoveFromCart: function(e){
        ga('ec:addProduct', {
            'id': e.dataset.sku,
            'name': e.dataset.name,
            'category': 'Tour',
            'position': e.dataset.position,
            'price': e.dataset.price
        });
        ga('ec:setAction', 'remove');
        ga('send', 'event', 'UX', 'click', 'remove from cart', {
            hitCallback: function() {}
        });
    },
    number_format: function(number, decimals, dec_point, thousands_sep) {
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


};

