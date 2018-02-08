<!--
Start of DoubleClick Floodlight Tag: Please do not remove
Activity name of this tag: woi-transaction
URL of the webpage where the tag is expected to be placed: https://www.walksofitaly.com/confirm
This tag must be placed between the <body> and </body> tags, as close as possible to the opening tag.
Creation Date: 05/11/2016
-->
<?php if (Configure::read('debug') == 0){ ?>
<iframe src="https://5370502.fls.doubleclick.net/activityi;src=5370502;type=woisale;cat=woi-t0;qty=1;cost=<?php echo $totalPrice['usd'] ?>;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;<?php echo $customMetrics ?>ord=<?php echo $booking_id ?>?" width="1" height="1" frameborder="0" style="display:none"></iframe>
<?php } ?>
<!-- End of DoubleClick Floodlight Tag: Please do not remove -->

<div class="content wrap">
    <aside>

        <?php $total_price = $totalPrice['local']; ?>
        <?php foreach($confirmCart as  $item) : ?>
            <?php $item_price = $total_price['subtotal_local'] ?>

            <?php echo $this->element('Pages/payment/item', compact('item')); ?>
            <br/>

        <?php endforeach ?>

        <?php if(CakeSession::read('charitiesDonatedTo')): ?>
            <h3 class="large">Donations</h3>
            <ul>
            <?php foreach(CakeSession::read('charitiesDonatedTo') as $charityName => $donationAmount): ?>
                <li><?php echo $charityName ?> <?php echo ExchangeRate::convert($donationAmount) ?> (&euro;<?php echo number_format($donationAmount,2,',','.'); ?>)</li>
                <?php $totalPrice['converted'] += ExchangeRate::convert($donationAmount,1,0); ?>
            <?php endforeach ?>
            </ul>
        <?php endif ?>

        <ul class="ticketsSummary total">
            <?php if ($promo_discount_fixed_total): ?>
                <li class="large">
                    <strong>Subtotal</strong>
                    <span><?php echo ExchangeRate::format(($totalPrice['converted'] + $promo_discount_fixed_total[$currency]),$currency) ?></span>
                </li>
                <li class="large">
                    <strong>Discount</strong>
                    <span>- <?php echo ExchangeRate::format($promo_discount_fixed_total[$currency],$currency) ?></span>
                </li>
            <?php endif ?>
            <li class="large">
                <strong>Total</strong>
                <span><?php echo ExchangeRate::format($totalPrice['converted'],$currency) ?></span>
            </li>
        </ul>

        <?php if(isset($promo) && $promo['BookingsPromo']['promo_name'] == 'Gift Card'): ?>
            <ul>
                <li><strong>Gift card used</strong></li>
                <li><strong>Remaining balance:</strong> <?php echo ExchangeRate::convert($promo['BookingsPromo']['discount_amount']) ?></li>
            </ul>
        <?php endif ?>

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

        <?php if(CakeSession::read('charitiesDonatedTo')): ?>
            <h3 class="large" style="margin-top:40px;">Charity donations</h3>
            <table>
                <?php foreach(CakeSession::read('charitiesDonatedTo') as $charityName => $donationAmount): ?>
                    <tr>
                        <td><?php echo $charityName ?></td>
                        <td><?php echo ExchangeRate::convert($donationAmount); ?> (&euro;<?php echo number_format($donationAmount,2,',','.'); ?> )</td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php endif ?>
    </main>
    <!-- START Adestra Conversion Tag -->
    <img src="https://takewalks.msgfocus.com/v/?tag=purchase&value=<?php echo $totalPrice['usd'] ?> &order=<?php echo $booking_id ?>">
    <!-- END Adestra Conversion Tag -->


</div>
<?php echo $this->element('google_conversion',array(
    'domain' => 'italy',
    'item_price' => $totalPrice['converted']
)) ?>

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
        fbq('track', 'Purchase', {value: '<?php echo ExchangeRate::convert($total_price,1,0,'USD') ?>', currency: 'USD'});
    });

</script>

<?php echo $this->element('tapfiliate/conversion_page', array('booking_id' => $booking_id, 'price' => $total_price)); ?>