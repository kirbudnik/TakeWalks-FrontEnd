<!-- tracking code -->
<?php
//calculate total price
$totalPrice = 0;
if($transaction['success']) {
    foreach ($transaction['confirmCart'] as $n => $item) {
        $item_price = isset($item['promo_local']) ? $item['promo_local'] : $item['total_price'];
        $totalPrice += $item['charged_amount'];
    }
}
?>

<?php if($ecTheme == 'Italy'): //make sure it's ONLY for Italy, WrapperGA only works for italy?>

<script src="/js/wrapper_ga.js"></script>
<script type="text/javascript">
    var wrapperParams = {};
    wrapperParams.analyticsAccount = '<?php echo $analyticsAccount ?>';
    wrapperParams.actualCurrency = '<?php echo ExchangeRate::getCurrency() ?>';
    wrapperParams.initValues = initValues;
    wrapperParams.cakeDebugLevel = 0;// <?php //echo Configure::read('debug') ?>;
    $(document).ready(function(){
        WrapperGA.init(wrapperParams);
    });
</script>


<?php else: ?>
<script type="text/javascript">

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '<?php echo $analyticsAccount ?>', 'auto');
    ga('require', 'ec');
    ga('set', '&cu', '<?php echo ExchangeRate::getCurrency() ?>');

    <?php if($transaction['success']): ?>
        //*************************************************************
        //The Enhanced Ecommerce plug-in should not be used alongside the Ecommerce (ecommerce.js) plug-in for the same property.
        //*************************************************************
        <?php
            foreach($transaction['confirmCart'] as $n => $item) :
            $item_price = isset($item['promo_local']) ? $item['promo_local'] : $item['total_price'];
        ?>
        ga('ec:addProduct', {
            'id': '<?php echo $item['sku'] ?>',                     // SKU
            'name': '<?php echo $item['name'] ?>',                  // Product name. Required
            'category': 'Tour',                                     // Category or variation
            'price': '<?php echo $item['charged_amount'] ?>',    // Unit price
            'quantity': '1'                                          // Quantity
        });
        <?php endforeach ?>
        ga('ec:setAction', 'purchase', {
            'id': '<?php echo $transaction['booking_id'] ?>',   // Transaction ID = Booking ID
            'revenue': '<?php echo $totalPrice  ?>', // Grand Total
            'affiliation' : 'Walks LLC',
            'shipping': '0',                                    // Shipping
            'tax': '0'                                          // Tax
        });

        ga('send', 'pageview');     // Send transaction data with initial pageview.
    
    <?php elseif(isset($ecViewProductList)): ?>
        //Viewing your products in a product list.
        // add addImpression might be called for every item in the product list
        <?php 
        $i = 0;
        $listname = "Search Results ".(!is_null($ecViewProductList['list'])) ? $ecViewProductList['list'] : "";
        $events = (!is_null($ecViewProductList['events'])) ? $ecViewProductList['events'] : $featured;
        foreach($events as $event) : 
            $i++;
            ?>
        ga('ec:addImpression', {
          'id': '<?php echo $event['Event']['id'] ?>',
          'name': '<?php echo $event['Event']['name_short'] ?>',
          'list': '<?php echo $listname; ?>',
          'position': <?php echo $i; ?> 
        });
        <?php endforeach ?>
        ga('send', 'pageview');
        
        // Called when a link to a product is clicked.
        function ecOnProductClick(event,e){
            event.preventDefault();
            ga('ec:addProduct', {
                'id': e.dataset.id,
                'name': e.dataset.name,
                'category': 'Tour',
                'position': e.dataset.position
              });
            ga('ec:setAction', 'click', {list: '<?php echo $listname; ?>'});

            // Send click with an event, then send user to product page.
            ga('send', 'event', 'UX', 'click', '<?php echo $listname; ?>', {
                hitCallback: function() {
                  document.location = e.dataset.href;
                }
            });
        }
        
    <?php elseif(isset($ecViewProductDetailPage)): ?>
    //Viewing product detail page.
        ga('ec:addProduct', {
          'id': '<?php echo $ecViewProductDetailPage['id'] ?>',
          'name': '<?php echo $ecViewProductDetailPage['name_short'] ?>'
        });
        ga('ec:setAction', 'detail');
        ga('send', 'pageview');            

        // Called when a product is added to a shopping cart.
        function ecAddToCart(e) {
            var guests = document.getElementById('ec_quantity');
            var price = document.getElementById('ec_price');
            if (guests && price){
                ga('ec:addProduct', {
                  'id': '<?php echo $ecViewProductDetailPage['id'] ?>',
                  'name': '<?php echo $ecViewProductDetailPage['name_short'] ?>',
                  'category': 'Tour',
                  'price': price.value,
                  'quantity': guests.value
                });
                ga('ec:setAction', 'add');
                ga('send', 'event', 'UX', 'click', 'add to cart');     // Send data using an event.
            }
            return true;
        }
        
    <?php elseif(isset($ecViewPaymentPage)): ?>
        // Called when the user begins the checkout process.
        function ecCheckout() {
        <?php foreach($initValues['cart'] as $item) : ?>
            ga('ec:addProduct', {
                'id': <?php echo $item['event_id'] ?>,
                'name': '<?php echo $item['name'] ?>',
                'category': 'Tour',
                'price': '<?php echo $item['totalPrice'] ?>',
                'quantity': '1'
            });
        <?php endforeach ?>
        
            return true;
        }
        // A value of 1 indicates this action is first checkout step.
        ga('ec:setAction','checkout', {'step': 1 });
        ga('send', 'pageview');   // Pageview for payment.html

        // Called when a product is removed from shopping cart.
        function ecRemoveFromCart(e) {
            $('body').css( 'cursor', 'wait');
            ga('ec:addProduct', {
                'id': e.dataset.id,
                'name': e.dataset.name,
                'category': 'Tour',
                'position': e.dataset.position,
                'price': e.dataset.price
            });
            ga('ec:setAction', 'remove');
            ga('send', 'event', 'UX', 'click', 'remove from cart', {
                hitCallback: function() {
                    //action to remove from cart
                    document.location = e.dataset.href;
                }
            });     // Send data using an event.
        }
    <?php else : ?>
        ga('send', 'pageview');
    <?php endif ?>



</script>
<?php endif ?>
