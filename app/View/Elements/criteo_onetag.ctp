<!-- tracking code criteo onetag -->
<?php if($ecTheme == 'Italy'): //make sure it's ONLY for Italy
    // TODO: Set account number by currency
    // The “account:” variable is what will reference your partner IDs. There are separate partner IDs for the separate
    // countries on the campaign. The preconfigured ID is your US partner ID, for AU it is 32401, for CA it is 32400,
    // for IT it is 32399 and for UK it is 32398. You can use the logic that is in place to determine the users country
    // and currency to populate the partner id in the tags:

    $criteoAccountNumber = [
        'AUD' => 32401, // for AU
        'CAD' => 32400, // for CA
        'EUR' => 32399, // for IT
        'GBP' => 32398, // for UK
        'USD' => 32397  // for US
    ];
    $accountNumber = $criteoAccountNumber[ExchangeRate::getCurrency()];

    ?>

    <script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
    <script type="text/javascript">
        window.criteo_q = window.criteo_q || [];
        var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
        var accountNumber = <?php echo $accountNumber; ?>;

        <?php if($transaction['success']):
            $customerEmail = $travellerInfo['email'];
            $hashEmail = trim(strtolower($customerEmail));
            $hashEmail = mb_convert_encoding($hashEmail, "UTF-8", "ISO-8859-1");
            $hashEmail = md5($hashEmail);

            $orderId = $booking_number;
            $transactionItem = "";
            foreach($confirmCart as $item) {
                $price = $item['total_price'];
                $quantity = 0;
                $quantity += (isset($item['adults'])) ? $item['adults'] : 0;
                $quantity += (isset($item['seniors'])) ? $item['seniors'] : 0;
                $quantity += (isset($item['students'])) ? $item['students'] : 0;
                $quantity += (isset($item['children'])) ? $item['children'] : 0;
                $quantity += (isset($item['infants'])) ? $item['infants'] : 0;
                $price = ($quantity > 0) ? $price / $quantity : 0;
                $transactionItem .= '{ id : "'.$item['url_name'].'", price : '.$price.', quantity: '.$quantity.' },';
            }
            $transactionItem = rtrim($transactionItem, ",");
        ?>
        // Track Confirm page
        window.criteo_q.push(
            { event: "setAccount", account: accountNumber },
            { event: "setSiteType", type: deviceType },
            { event: "setHashedEmail", email: ["<?php echo $hashEmail; ?>"] },
            { event: "trackTransaction", id : "<?php echo $orderId; ?>", item : [ <?php echo $transactionItem ?> ]}
        );

        <?php elseif(isset($ecViewHomePage)): ?>
        // Track home page
        window.criteo_q.push(
            { event: "setAccount", account: accountNumber },
            { event: "setSiteType", type: deviceType },
            { event: "setHashedEmail", email: [""] },
            { event: "viewHome"}
        );
        <?php elseif(isset($ecViewProductList)):
            $itemListCriteo = "";
            $listname = "Search Results ".(!is_null($ecViewProductList['list'])) ? $ecViewProductList['list'] : "";
            $events = $ecViewProductList['events'];
            foreach($events as $event) {
                $itemListCriteo .= '"'.$event['event_url_name'].'",';
            }
            $itemListCriteo = rtrim($itemListCriteo, ",");
        ?>
        // Track Listing page
        window.criteo_q.push(
            { event: "setAccount", account: accountNumber },
            { event: "setSiteType", type: deviceType },
            { event: "setHashedEmail", email: [""] },
            { event: "viewList", item : [ <?php echo $itemListCriteo; ?>]}
        );
        <?php elseif(isset($ecViewProductDetailPage)): ?>
        // Track Product/Detail page
        window.criteo_q.push(
            { event: "setAccount", account: accountNumber },
            { event: "setSiteType", type: deviceType },
            { event: "setHashedEmail", email: [""] },
            { event: "viewItem", item : [ "<?php echo $ecViewProductDetailPage['url_name'] ?>" ]}
        );
        <?php elseif(isset($ecViewPaymentPage)):
            $viewBasketCriteo = "";
            $events = $initValues['cart'];
            foreach($events as $event) {
                $tickets = $event['tickets'];
                $price = $event['totalPrice'];
                $quantity = 0;
                $quantity += (isset($tickets['adults'])) ? $tickets['adults']['amount'] : 0;
                $quantity += (isset($tickets['seniors'])) ? $tickets['seniors']['amount'] : 0;
                $quantity += (isset($tickets['students'])) ? $tickets['students']['amount'] : 0;
                $quantity += (isset($tickets['children'])) ? $tickets['children']['amount'] : 0;
                $quantity += (isset($tickets['infants'])) ? $tickets['infants']['amount'] : 0;
                $viewBasketCriteo .= '{ id : "'.$event['url_name'].'", price : '.$price.', quantity: '.$quantity.' },';
            }
            $viewBasketCriteo = rtrim($viewBasketCriteo, ",");
        ?>
        // Track Basket/Payment page
        window.criteo_q.push(
            { event: "setAccount", account: accountNumber },
            { event: "setSiteType", type: deviceType },
            { event: "setHashedEmail", email: [""] },
            { event: "viewBasket", item : [ <?php echo $viewBasketCriteo ?> ]}
        );
        <?php endif ?>


    </script>
<?php endif ?>
