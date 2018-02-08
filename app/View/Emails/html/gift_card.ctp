<img src="https://images.walks.org/logos/ops/italy.png" height="80px" width="160px">
<p>Dear <strong><?php echo $Client['Client']['fname']?> <?php echo $Client['Client']['lname']?></strong>,</p>

<p>Your gift card has arrived -- it's almost time to take a walk with us in Italy! Please forward this email to the lucky recipient, then bask in the glow of their appreciation.</p>
<br><br>
<div class="gift-card" style="text-align:center; padding: 20px 100px; margin: 0 50px 30px 20px; border: 2px dotted #fff; border-radius: 5px; display:inline-block; background: #a08f56">
    <img style="width:122px; opacity: .8" src="https://www.walksofnewyork.com/theme/nyc2/img/partners/walks-of-italy.png">
    <div class="amount" style="font-size: 38px;color: #fff;text-shadow: 0 0 0px #888;"><?= ExchangeRate::format($gift_card_amount); ?></div>
    <div class="code" style="color: #611906;font-size: 16px; margin-top: 10px; font-weight: bold;">Code: <?= $promo['BookingsPromo']['promo_code'] ?></div>
</div>
<br><br>

<p>
    You can redeem this gift card any time in the next 5 years (book and travel within 5 years) by applying the gift code (which is case and space sensitive) in the add promo-code section of our check-out page before entering your credit card details. First, add whichever tour you like in any of our cities in Italy to your shopping cart, then proceed to checkout. At checkout level, the value of your gift card will be deducted from your total after you’ve entered the code in the “promo-code” field. If the value of your gift card is equal to or greater than the total cost of your tours, your credit card will not be charged and this code will remain active until either 5 years pass or you make further purchases to equal the full value.
</p>
<p>
    Please note you must complete the “Traveller Information” and “Billing Information” section on our website to successfully reserve your tour(s).
</p>
<p>
    If you have any questions, contact our customer service team at <a href="mailto:info@walksofitaly.com">info@walksofitaly.com</a>.
</p>





<?php if ($PaymentTransaction): ?>

    <h3>YOUR TRANSACTIONS:</h3>
    <table width="100%" border="1" cellspacing="0" cellpadding="7px" bordercolor="#ccc">
        <tr>
            <th>Date</th>
            <th>Order ID</th>
            <th>Exchange Rate</th>
            <th><?php echo ExchangeRate::getCurrency() ?></th>

        </tr>

        <?php foreach($PaymentTransaction as $PaymentTransaction_loop): ?>
            <tr>
                <td  align="center">
                    <?php echo date("F j, Y",strtotime($PaymentTransaction_loop['PaymentTransaction']['transaction_date'])) ?>
                </td>
                <td align="center">
                    <?php echo $booking_id  ?>
                </td>
                <td align="center">
                    <?php echo $PaymentTransaction_loop['PaymentTransaction']['exchange_rate'] ?>
                </td>
                <td align="center">
                    <strong>
                        <?php echo ExchangeRate::format($PaymentTransaction_loop['PaymentTransaction']['payment_amount']) ?>
                    </strong>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif; ?>



<p>Thank you for choosing Walks of Italy</p>
<p>Buon Viaggio</p>

<p>
    <strong>The Walks of Italy Team</strong><br />
    <em>Experience Italy. Take Walks.</em><br />
    <a href="http://www.walksofitaly.com">www.walksofitaly.com</a>
</p>



