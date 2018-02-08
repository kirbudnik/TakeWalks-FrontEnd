<!--
Start of DoubleClick Floodlight Tag: Please do not remove
Activity name of this tag: woi-transaction
URL of the web page where the tag is expected to be placed: https://www.walksofnewyork.com/confirm
This tag must be placed between the <body> and </body> tags, as close as possible to the opening tag.
Creation Date: 08/02/2016
-->
<?php //if (Configure::read('debug') == 0){ ?>
<iframe src="https://5370502.fls.doubleclick.net/activityi;src=5370502;type=woisale;cat=woi-t0;qty=1;cost=<?php echo $totalPrice['usd'] ?>;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;<?php echo $customMetrics ?>ord=<?php echo $booking_id ?>?" width="1" height="1" frameborder="0" style="display:none"></iframe>
<?php //} ?>
<!-- End of DoubleClick Floodlight Tag: Please do not remove -->

<div class="content wrap">
    <aside>

        <?php $total_price = 0; ?>
        <?php foreach($confirmCart as $item) : ?>
            <?php $item_price = isset($item['promo_local']) ? $item['promo_local'] : $item['total_price'] ?>
            <?php $total_price += $item_price ?>
            <?php echo $this->element('Pages/payment/item', compact('item')); ?>
            <br/>


        <?php endforeach ?>

        <ul class="ticketsSummary total">
            <?php if ($promo_discount_fixed_total): ?>
                <li>
                    <i>Subtotal</i>
                    <span><?php echo ExchangeRate::format(($totalPrice['converted'] + $promo_discount_fixed_total[$currency]),$currency) ?></span>
                </li>
                <li>
                    <i>Discount</i>
                    <span>- <?php echo ExchangeRate::format($promo_discount_fixed_total[$currency],$currency) ?></span>
                </li>
            <?php endif ?>
            <li class="large">
                <strong>Total</strong>
                <span><?php echo ExchangeRate::format($totalPrice['converted'],$currency) ?></span>
            </li>
        </ul>

    </aside>

    <main>

        <h1 class="larger">Confirmation</h1>

        <p>Thank you. Your booking is now confirmed. <!--Please enter in your travel itinerary below so you can print your vouchers.--></p>

        <h3 class="large">Traveller Information</h3>
        <table>
            <tr>
                <td>Booking Number</td>
                <td><?php echo $booking_number ?></td>
            </tr>
            <tr>
                <td>First Name</td>
                <td><?php echo $travellerInfo['first_name'] ?></td>
            </tr>
            <tr>
                <td>Last Name</td>
                <td><?php echo $travellerInfo['last_name'] ?></td>
            </tr>
            <tr>
                <td>Email address</td>
                <td><?php echo $travellerInfo['email'] ?></td>
            </tr>
            <tr>
                <td>Phone Number</td>
                <td><?php echo $travellerInfo['phone_number'] ?></td>
            </tr>
            <tr>
                <td>Street Address</td>
                <td><?php echo $travellerInfo['street_address'] ?></td>
            </tr>
            <tr>
                <td>Country</td>
                <td><?php echo $travellerInfo['country'] ?></td>
            </tr>
            <tr>
                <td>State</td>
                <td><?php echo $travellerInfo['state'] ?></td>
            </tr>
            <tr>
                <td>Zipcode</td>
                <td><?php echo $travellerInfo['zip'] ?></td>
            </tr>
            <?php if(!empty($travellerInfo['restrictions'])): ?>
                <tr>
                    <td>Restrictions</td>
                    <td><?php echo implode("<br/>\n", $travellerInfo['restrictions']) ?></td>
                </tr>
            <?php endif; ?>
        </table>

        <!--         <h3 class="large">We still need your travel details!</h3>-->
        <!--         <p>Before you can print out your vouchers for the activities and walks, we need to get your travel itinerary in case we need to contact you in case of emergency. We show you with us from <strong>--><?php //echo date('m/d/y', strtotime($start_date)) ?><!--</strong> to <strong>--><?php //echo date('m/d/y', strtotime($end_date)) ?><!--</strong>.</p>-->
        <!---->
        <!--        <form method="post">-->
        <!--            <input type="hidden" value="--><?php //echo $booking_id?><!--" />-->
        <!--            <div id="hotels">-->
        <!--                <fieldset class="serif">-->
        <!--                    <legend>Hotel</legend>-->
        <!--                    <label>-->
        <!--                        Hotel name-->
        <!--                        <input type="text" name="data[0][BookingsAddress][hotel_name]">-->
        <!--                    </label>-->
        <!--                    <label>-->
        <!--                        Hotel phone-->
        <!--                        <input type="text" name="data[0][BookingsAddress][hotel_telephone]">-->
        <!--                    </label>-->
        <!--                    <label>-->
        <!--                        Arrival-->
        <!--                        <input type="text" class="startDate" name="data[0][BookingsAddress][staying_from]">-->
        <!--                    </label>-->
        <!--                    <label>-->
        <!--                        Departure-->
        <!--                        <input type="text" class="endDate" name="data[0][BookingsAddress][staying_to]">-->
        <!--                    </label>-->
        <!--                    <a class="grey small button" id="remove_hotel">Remove this hotel</a>-->
        <!--                </fieldset>-->
        <!---->
        <!--            </div>-->
        <!--            <fieldset>-->
        <!--                <a class="blue small button" id="new_hotel">Add hotel</a>-->
        <!--            </fieldset>-->
        <!---->
        <!--            <label>-->
        <!--                <input type="checkbox" id="noHotel" name="noHotel" value="1">-->
        <!--                <span>I have not booked my hotel yet</span>-->
        <!--                <span id="conditionError" style="color: red"></span>-->
        <!--            </label>-->
        <!---->
        <!--            <input type="submit" class="pink button" value="Print Vouchers">-->
        <!--        </form>-->

    </main>

</div>

<?php echo $this->element('google_conversion',array(
    'domain' => 'nyc',
    'item_price' => $total_price * $item['exchange_rate']
)) ?>


<!-- tracking code -->

<?php //if (Configure::read('debug') == 0){ ?>
    <!--Bing Revenue Tracking-->
<script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"5235541"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script><noscript><img src="//bat.bing.com/action/0?ti=5235541&Ver=2" height="0" width="0" style="display:none; visibility: hidden;" /></noscript>
<script>
    <?php // http://ads.bingads.microsoft.com/en-au/blog/30428/e-commerce-revenue-tracking-with-universal-event-tracking  ?>
    var amount = '<?php echo $total_price * $item['exchange_rate']; ?>';// Compute the goal value
    window.uetq = window.uetq || [];
    window.uetq.push ({ 'gv': amount }); // Pass the computed goal value
</script>
<?php //} ?>




<script type="text/javascript">

    adroll_conversion_value_in_dollars = <?php echo $totalPrice['usd'] ?>;
    adroll_adv_id = "KU3AUMDWJFAYZE6IELQKKZ";
    adroll_pix_id = "QUKHSI3ZYNAOPD5CHEJMMJ";

    (function () {
        var oldonload = window.onload;
        window.onload = function () {
            __adroll_loaded = true;
            var scr = document.createElement("script");
            var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
            scr.setAttribute('async', 'true');
            scr.type = "text/javascript";
            scr.src = host + "/j/roundtrip.js";
            ((document.getElementsByTagName('head') || [null])[0] || document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
            if (oldonload) {
                oldonload()
            }
        };
    }());

    $(function(){
        // possibly cause of error tracking revenue
        // fbq('track', 'Purchase', {value: '<?php echo ExchangeRate::convert($total_price,1,0,'USD') ?>', currency: 'USD'});
    });

</script>


<?php echo $this->element('tapfiliate/conversion_page', array('booking_id' => $booking_id, 'price' => $item_price)); ?>