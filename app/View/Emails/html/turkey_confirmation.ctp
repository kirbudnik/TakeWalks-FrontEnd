<img src="https://images.walks.org/logos/ops/turkey.png" height="80px" width="160px">
<p>Dear <strong><?php echo $Client['Client']['fname']?> <?php echo $Client['Client']['lname']?></strong>,</p>

<p>Welcome to Turkey!</p>

<p>Thanks for choosing to take walks with us – we're excited to be part of your adventure! If you have any questions, please don't hesitate to contact us by phone at 1-866-671-1430 or by email at <a href="mailto:info@walksofturkey.com">info@walksofturkey.com</a>.</p>

<p>Please have this information on hand and bring all of it with you on the day of your visit.</p>

<p>Turkish Tel: +90-541-796-6295/ USA Tel: (001) 917-310-1554</p>

<h3>Know Before You Go:</h3>
<ul>

<li><strong>Weather</strong> - All Walks of Turkey  services will run rain or shine, so please make sure to dress appropriately.</li>

<li><strong>Dress</strong> - All mosques in Turkey require women to cover their heads and all guests to remove their shoes before entry. Christian churches require all guests, men and women, to cover their shoulders and knees. If your tour is scheduled to enter a mosque or a church, please make sure your attire meets these requirements as Walks of Turkey cannot be held responsible for denied entry due to improper dress.</li>

<li><strong>Students</strong> – If you booked a student ticket you must bring a valid I.D .</li>

<li><strong>Cancellations & Amendments</strong> - Refunds will not be provided for services that are cancelled within 4 days of the scheduled start time. Cancellations and changes made within 30 days of the tour/service commencement are subject to the applicable fees. Please click the following link for more information: [<a href="https://www.walksofturkey.com/cancellation">https://www.walksofturkey.com/cancellation</a>].</li>

<li><strong>Tips & Gratuities</strong> - All of our excursions, tours, & transfer services include all reservation fees, tickets & guide fees. Tips and gratuities are not included. While tips are never expected they are always appreciated.</li>


</ul>


<h3>Your Booked Services:</h3>



<?php

if ($BookingsDetail){
    echo '<table width="100%">';
    foreach ($BookingsDetail as $BookingsDetail_loop){

        if ($BookingsDetail_loop['BookingsDetail']['private_group'] == 'Group'){
            $gp = 'g';
        } else {
            $gp = 'p';
        }

        $meetingTime_datetime = date('Y-m-d H:i:s', strtotime($BookingsDetail_loop['BookingsDetail']['events_datetimes'] . ' - '.$BookingsDetail_loop['Event']['meet_before'].' mins'));

        $total_pax = 0;
        $total_pax += $BookingsDetail_loop['BookingsDetail']['number_adults'];
        $total_pax += $BookingsDetail_loop['BookingsDetail']['number_students'];
        $total_pax += $BookingsDetail_loop['BookingsDetail']['number_children'];
        $total_pax += $BookingsDetail_loop['BookingsDetail']['number_seniors'];
        $total_pax += $BookingsDetail_loop['BookingsDetail']['number_infants'];


        $pax_display = '';
        if ($BookingsDetail_loop['BookingsDetail']['number_adults'] > 0){
            if ($pax_display != ''){
                $pax_display .= ' / ';
            }
            $pax_display .= 'Ad: '.$BookingsDetail_loop['BookingsDetail']['number_adults'];
        }
        if ($BookingsDetail_loop['BookingsDetail']['number_seniors'] > 0){
            if ($pax_display != ''){
                $pax_display .= ' / ';
            }
            $pax_display .= 'Se: '.$BookingsDetail_loop['BookingsDetail']['number_seniors'];
        }
        if ($BookingsDetail_loop['BookingsDetail']['number_students'] > 0){
            if ($pax_display != ''){
                $pax_display .= ' / ';
            }
            $pax_display .= 'Stu: '.$BookingsDetail_loop['BookingsDetail']['number_students'];
        }
        if ($BookingsDetail_loop['BookingsDetail']['number_children'] > 0){
            if ($pax_display != ''){
                $pax_display .= ' / ';
            }
            $pax_display .= 'Ch: '.$BookingsDetail_loop['BookingsDetail']['number_children'];
        }

        if ($BookingsDetail_loop['BookingsDetail']['number_infants'] > 0){
            if ($pax_display != ''){
                $pax_display .= ' / ';
            }
            $pax_display .= 'In: '.$BookingsDetail_loop['BookingsDetail']['number_infants'];
        }
        $pax_display = '(# '.$pax_display.')';


        echo '<tr>';
        echo '<td valign="top">';

        echo '<table>';

        echo '<tr>';
        echo '<td colspan="2"><strong>'.date("F j, Y (l)",strtotime($BookingsDetail_loop['BookingsDetail']['events_datetimes'])).'</strong></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="2"><strong>'.$BookingsDetail_loop['Event']['name_long'].'</strong></td>';
        echo '</tr>';


        echo '<tr>';
        echo '<td>';
        echo '<strong>City/Region:</strong> '.$BookingsDetail_loop['EventsPrimaryGroup']['primary_name'].'<br />';
        if ($BookingsDetail_loop['BookingsDetail']['private_group'] == 'Group'){
            echo '<strong>Meeting Time:</strong> '.date("g:ia",strtotime($meetingTime_datetime)).'<br />';
        }
        echo '<strong>Start Time:</strong> '.date("g:ia",strtotime($BookingsDetail_loop['BookingsDetail']['events_datetimes'])).'<br />';
        echo '<strong>Total Pax:</strong> '.$total_pax.'<br />';
        echo ''.$pax_display.'<br />';
        echo '</td>';



        echo '<td>';
        echo '<strong>Style:</strong> '.$BookingsDetail_loop['BookingsDetail']['private_group'].'<br />';


        echo '<strong>Order ID:</strong> '. $booking_id .'<br />';


        echo '<strong>Price:</strong> ' . ExchangeRate::format($BookingsDetail_loop['BookingsDetail']['amount_converted']);
        $discount = $BookingsDetail_loop['BookingsDetail']['charged_converted_amount'] - $BookingsDetail_loop['BookingsDetail']['amount_converted'];
        if ($discount){
            $discount = abs($discount);
            echo '<br /><strong>Discount</strong> -'.ExchangeRate::format($discount);
        }

        echo '<br />';

        echo '</td>';
        echo '</tr>';



        echo '<tr><td colspan="2"><br /><strong>Meeting Point:</strong> '.$BookingsDetail_loop['Event']['mp_text_'.$gp].'</td></tr>';
        echo '<tr><td colspan="2"><strong>End Point:</strong> '.$BookingsDetail_loop['Event']['endpoint_'.$gp].'</td></tr>';
        echo '<tr><td colspan="2"><strong>Directions:</strong> '.$BookingsDetail_loop['Event']['directions_'.$gp].'</td></tr>';
        echo '</table>';
        echo '</td>';
        //echo '<td width="280"><img src="http://devbeta.walks.org/walks/app/webroot/img/meetingimages/'.$BookingsDetail_loop['Event']['id'].'s.jpg" width="280" height="320"></td>';
        echo '<td width="280" valign="top"><a href="http://images.walks.org/turkey/meetingpoint/'.$BookingsDetail_loop['Event']['id'].'L.jpg"><img src="http://images.walks.org/turkey/meetingpoint/'.$BookingsDetail_loop['Event']['id'].'L.jpg" width="280" height="320"></a></td>';
        echo '</tr>';
        echo '<tr><td colspan="2"><hr /></td></tr>';

    }
    echo '</table>';
}

?>

<p>Note: To take public transportation, simply insert &#8378;4.00 in one of the vending machines present at each stop, it will give you a token to use to go through the turnstiles. You will not need a physical ticket once you are on the vehicle.</p>

<?php if ($PaymentTransaction): ?>

    <?php if ($promo_discount_fixed_total): ?>
        <h3>YOUR PROMO CODE DISCOUNT:</h3>
        <table width="100%" border="1" cellspacing="0" cellpadding="7px" bordercolor="#ccc">
            <tr>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Total</th>

            </tr>
            <?php foreach($PaymentTransaction as $PaymentTransaction_loop): ?>
                <tr>
                    <td  align="center">
                        <?php echo ExchangeRate::format(($PaymentTransaction_loop['PaymentTransaction']['payment_amount'] + $promo_discount_fixed_total[ExchangeRate::getCurrency()] )) ?>
                    </td>
                    <td align="center">
                        - <?php echo ExchangeRate::format($promo_discount_fixed_total[ExchangeRate::getCurrency()]) ?>
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
    
<?php if($charities): ?>
    <h3>YOUR DONATIONS:</h3>
    <table width="100%" border="1" cellspacing="0" cellpadding="7px" bordercolor="#ccc">
        <tr>
            <th>Organization</th><th>Donation amount</th>
        </tr>
        <?php foreach($charities as $charity): ?>
            <tr>
                <td align="center"><?php echo $charity['Charity']['charity_name']; ?></td>
                <td align="center">$<?php echo number_format($charity['CharitiesDonation']['amount_local'],2,'.',',')?></td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<p>Thank you for choosing to travel with us!</p>

<p>Have a nice trip!</p>

<p>The Walks of Turkey Team</p>
