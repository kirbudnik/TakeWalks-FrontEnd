
<div class="content wrap">
    <aside style="text-align: center">


    </aside>

    <main>

        <h1 class="larger" style="margin-bottom: 30px;" >Confirmation</h1>

        <div class="gift-card" style="text-align:center; padding: 20px 100px; margin: 0 50px 30px 20px; border: 2px dotted #000; border-radius: 5px; display:inline-block; background: #F6F6F6">
            <img style="width:122px; opacity: .8" src="https://www.walksofnewyork.com/theme/nyc2/img/logo-black.png">
            <div class="amount" style="font-size: 38px;color: #000;text-shadow: 0 0 0px #888;"><?= ExchangeRate::format($gift_card['amount']) ?></div>
            <div class="code" style="color: #338dfb;font-size: 16px; margin-top: 10px; font-weight: bold;">Code: <?= $gift_card['code'] ?></div>
        </div>


        <p style="max-width: 700px;font-size: 15px;line-height: 25px;">
            Thanks for giving the gift of walks! In the next few minutes you will receive an email from us with a gift card inside. Whenever you're ready, forward this to the lucky recipient. They'll be able to claim their gift by applying the gift code (which is case and space sensitive) in the add promo-code section of our check-out page before entering their credit card details. The gift code can be used on a single purchase or for multiple purchases on any of our tours in New York and is valid for 5 years after purchase. Please note exchange fees will apply if final reservation is made in a different currency from gift card.
        </p>

        <h3 class="large" style="margin-top: 20px;">Buyer Information</h3>
        <table>
            <tr>
                <td style="width: 200px;" >Purchase Number</td>
                <td><?php echo $booking_id ?></td>
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
                <td>Zip code</td>
                <td><?php echo $travellerInfo['zip'] ?></td>
            </tr>
        </table>
    </main>

</div>