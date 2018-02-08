<?php $isFrance = isset($BookingsDetail[0]['isFrance']); ?>
<?php $isFranceAndItaly = isset($BookingsDetail[0]['isFranceAndItaly']); ?>
<img src="https://images.walks.org/logos/ops/<?php echo isset($BookingsDetail[0]['isFrance']) ? 'france' : 'italy' ?>.png" height="80px" width="160px">
<p>Dear <strong><?php echo $Client['Client']['fname']?> <?php echo $Client['Client']['lname']?></strong>,</p>


<?php if(isset($BookingsDetail[0]['isFrance'])): ?>
    <p>Bienvenue á France! (Welcome to France!)</p>
<?php else: ?>
    <p>Benvenuti in Italia! (Welcome to Italy!) </p>
<?php endif ?>
<p>Thanks for choosing to take walks with us – we're excited to be part of your adventure! If you have any questions, please don't hesitate to contact us by phone at 1-888-683-8670 or by email at <a href="mailto:info@walksofitaly.com">info@walksofitaly.com</a>. <?php echo $isFrance ? 'Anyone of our representatives is happy to assist you with your Walks of France tours!' : '' ?></p>

<p>Please have this information on hand and bring all of it with you on the day of your visit.</p>

<p>
    <?php echo $isFrance ? 'France Emergency Number: (0033) 176-36-0101 / ' : '' ?>Italy Emergency Number: (0039) 334-974-4274 / <?php echo $isFrance ? 'USA Tel: (001) 202-684-6916 ' : 'Italy Tel: (0039) 069-480-4888' ?><br />
</p>

<h3>Know Before You Go:</h3>

<?php if($isFrance): ?>
    <ul>
        <li><strong>Weather</strong> - All Walks of France services will run rain or shine, so please make sure to dress appropriately.</li>
        <li><strong>Dress</strong> - All religious sites in France require both men and women to wear clothing that covers the shoulders and knees. If your tour is scheduled to enter a church or holy place, please make sure your attire meets these requirements as Walks of France cannot be held responsible for denied entry due to improper dress.</li>
        <li><strong>Students</strong> – If you booked a student ticket you must bring a valid I.D .</li>
        <li><strong>Cancellations & Amendments</strong> - Refunds will not be provided for group tours that are cancelled within 72 hours of the scheduled start time, or for private tours that are cancelled within 7 days of the scheduled start time. Please contact us as soon as possible if you need to request a change to one of your booked services. All amendments must be approved by a Walks of France representative first and are subject to availability and applicable fees. Please click the following link for more information: [<a href="https://www.walksofitaly.com/cancellation">https://www.walksofitaly.com/cancellation</a>].</li>
        <li><strong>Tips & Gratuities</strong> - All of our excursions and tours include all reservation fees, tickets & guide fees. Tips and gratuities are not included. While tips are never expected they are always appreciated.</li>
    </ul>
<?php else: ?>
    <ul>
        <li><strong>Weather</strong> - All Walks of Italy services will run rain or shine, so please make sure to dress appropriately.</li>
        <li><strong>Dress</strong> - All religious sites in Italy require both men and women to wear clothing that covers the shoulders and knees. If your tour is scheduled to enter a church or holy place, please make sure your attire meets these requirements as Walks of Italy cannot be held responsible for denied entry due to improper dress.</li>
        <li><strong>Students</strong> – If you booked a student ticket you must bring a valid I.D .</li>
        <li><strong>Cancellations & Amendments</strong> - Refunds will not be provided for group tours that are cancelled within 72 hours of the scheduled start time, or for private tours that are cancelled within 7 days of the scheduled start time. Please contact us as soon as possible if you need to request a change to one of your booked services. All amendments must be approved by a Walks of Italy representative first and are subject to availability and applicable fees. Please click the following link for more information: [<a href="https://www.walksofitaly.com/cancellation">https://www.walksofitaly.com/cancellation</a>].</li>
        <li><strong>Tips & Gratuities</strong> - All of our excursions, tours, & transfer services include all reservation fees, tickets & guide fees. Tips and gratuities are not included. While tips are never expected they are always appreciated.</li>
    </ul>
<?php endif ?>




<h3>Your Booked Services:</h3>






<?php if ($BookingsDetail): ?>
    <table width="100%">

        <?php foreach ($BookingsDetail as $BookingsDetail_loop): ?>

            <?php $gp = $BookingsDetail_loop['BookingsDetail']['private_group'] == 'Group' ? 'g' : 'p'; ?>

            <?php $meetingTime_datetime = date('Y-m-d H:i:s', strtotime($BookingsDetail_loop['BookingsDetail']['events_datetimes'] . ' - '.$BookingsDetail_loop['Event']['meet_before'].' mins')); ?>

            <?php $total_pax = array_sum(array(
                $BookingsDetail_loop['BookingsDetail']['number_adults'],
                $BookingsDetail_loop['BookingsDetail']['number_students'],
                $BookingsDetail_loop['BookingsDetail']['number_children'],
                $BookingsDetail_loop['BookingsDetail']['number_seniors'],
                $BookingsDetail_loop['BookingsDetail']['number_infants']
            ));
            ?>

            <?php $pax_display_array = array(); ?>
            <?php foreach(array('adults','seniors','students','children','infants') as $ticketType): ?>
                <?php if ($BookingsDetail_loop['BookingsDetail']['number_' . $ticketType] > 0): ?>
                    <?php $pax_display_array[] = substr($ticketType,0,2) . ': ' . $BookingsDetail_loop['BookingsDetail']['number_' . $ticketType]; ?>
                <?php endif ?>
            <?php endforeach ?>

            <?php $pax_display = '(# '. implode(' \ ', $pax_display_array) .')'; ?>




            <tr>
                <td valign="top">

                    <table>

                        <tr>
                            <td colspan="2"><strong> <?php echo date("F j, Y (l)",strtotime($BookingsDetail_loop['BookingsDetail']['events_datetimes'])) ?> </strong></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong> <?php echo $BookingsDetail_loop['Event']['name_long'] ?></strong></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>City/Region:</strong> <?php echo $BookingsDetail_loop['EventsPrimaryGroup']['primary_name'] ?><br />
                                <?php if ($BookingsDetail_loop['BookingsDetail']['private_group'] == 'Group'): ?>
                                    <strong>Meeting Time:</strong> <?php echo date("g:ia",strtotime($meetingTime_datetime)) ?><br />
                                <?php endif ?>
                                <strong>Start Time:</strong>  <?php echo  date("g:ia",strtotime($BookingsDetail_loop['BookingsDetail']['events_datetimes'])) ?><br />
                                <strong>Total Pax:</strong> <?php echo $total_pax ?><br />  <?php echo $pax_display ?><br />
                            </td>

                            <td>
                                <strong>Style:</strong>  <?php echo $BookingsDetail_loop['BookingsDetail']['private_group'] ?><br />
                                <strong>Order ID:</strong>  <?php echo  $booking_id  ?><br />
                                <strong>Price:</strong>  <?php echo ExchangeRate::format($BookingsDetail_loop['BookingsDetail']['amount_converted'])  ?>

                                <?php $discount = $BookingsDetail_loop['BookingsDetail']['charged_converted_amount'] - $BookingsDetail_loop['BookingsDetail']['amount_converted']; ?>
                                <?php if ($discount): ?>
                                    <br /><strong>Discount</strong>
                                    - <?php $discount = abs($discount); echo ExchangeRate::format($discount); ?>
                                <?php endif ?>

                                <br />

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <br /><strong>Meeting Point:</strong> <?php echo $BookingsDetail_loop['Event']['mp_text_'.$gp] ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>End Point:</strong> <?php echo $BookingsDetail_loop['Event']['endpoint_'.$gp] ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Directions:</strong> <?php echo $BookingsDetail_loop['Event']['directions_'.$gp] ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="280" valign="top">
                    <a href="http://images.walks.org/italy/meetingpoint/<?php echo $BookingsDetail_loop['Event']['id'] ?>L.jpg">
                        <img src="http://images.walks.org/italy/meetingpoint/<?php echo $BookingsDetail_loop['Event']['id'] ?>L.jpg" width="280" height="320">
                    </a>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr />
                </td>
            </tr>

        <?php endforeach ?>
    </table>
<?php endif ?>

<?php if ($PaymentTransaction): ?>
    <?php if($promo && $promo['BookingsPromo']['promo_name'] == 'Gift Card'): ?>
        <h3>YOUR GIFT CARD:</h3>
        <p>
            The following reflects the status of your gift card, with 'Total' representing how much of your gift card value remains. You can spend this remaining value by using your gift code in your original gift card e-mail and purchasing on our site (as you just did). Your gift card is valid for all Italy tours booked and taken within 5 years from date of issue.
        </p>
        <table width="100%" border="1" cellspacing="0" cellpadding="7px" bordercolor="#ccc">
            <tr>
                <td>Gift card balance: <?php echo ExchangeRate::convert($promo['BookingsPromo']['discount_amount']) ?></td>
            </tr>
        </table>

    <?php elseif ($promo_discount_fixed_total): ?>
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
                <td align="center"><?php echo ExchangeRate::convert($charity['CharitiesDonation']['amount_local']) ?> (&euro;<?php echo number_format($charity['CharitiesDonation']['amount_local'],  2, ',', '.'); ?>)</td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<?php if($isFrance): ?>
    <p>Thank you for choosing to travel with us!</p>
    <p>Bon Voyage! (Have a nice trip!)</p>
    <p>
        <strong>The Walks of France Team</strong><br>
        <em>Experience France Take Walks.</em><br>
        <a href="www.walksofitaly.com/paris-tours">www.walksofitaly.com/paris-tours</a>
    </p>
<?php else: ?>
    <p>Thank you for choosing to travel with us!</p>
    <p>Buon Viaggio (Have a nice trip!)</p>

    <p>
        <strong>The Walks of Italy Team</strong><br />
        <em>Experience Italy. Take Walks.</em><br />
        <a href="http://www.walksofitaly.com">www.walksofitaly.com</a>
    </p>

<?php endif ?>




