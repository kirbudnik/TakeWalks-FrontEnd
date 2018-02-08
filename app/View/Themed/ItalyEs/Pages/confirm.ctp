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

    </aside>

    <main>


        <h1 class="larger">Confirmación</h1>

        <p>Gracias, su reserva está confirmada. <!--Please enter in your travel itinerary below so you can print your vouchers.--></p>

        <h3 class="large">Información de Viajero</h3>
        <table>
            <tr>
                <td>Número del orden</td>
                <td><?php echo $booking_number ?></td>
            </tr>
            <tr>
                <td>Nombre</td>
                <td><?php echo $travellerInfo['first_name'] ?></td>
            </tr>
            <tr>
                <td>Apellido</td>
                <td><?php echo $travellerInfo['last_name'] ?></td>
            </tr>
            <tr>
                <td>Correo Electronico</td>
                <td><?php echo $travellerInfo['email'] ?></td>
            </tr>
            <tr>
                <td>Número de Teléfono</td>
                <td><?php echo $travellerInfo['phone_number'] ?></td>
            </tr>
            <tr>
                <td>Dirección</td>
                <td><?php echo $travellerInfo['street_address'] ?></td>
            </tr>
            <tr>
                <td>País</td>
                <td><?php echo $travellerInfo['country'] ?></td>
            </tr>
            <tr>
                <td>Estado</td>
                <td><?php echo $travellerInfo['state'] ?></td>
            </tr>
            <tr>
                <td>Código Postal</td>
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
