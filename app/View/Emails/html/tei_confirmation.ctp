<img src="https://images.walks.org/logos/ops/italy.png" height="80px" width="160px">
<p>Hola <strong><?php echo $Client['Client']['fname']?> <?php echo $Client['Client']['lname']?></strong>,</p>

<p>Benvenuti in Italia!</p>

<p>Gracias por elegirnos para tus visitas - estamos muy contentos de ser parte de tu aventura! Si tienes alguna pregunta, por favor no dudes en contactar con nosotros por teléfono al España: +34-911-232-780, Mexico: +52-555-350-2403, Argentina +54-115-984-1444 o por correo electrónico a <a href="mailto:informacion@walksofitaly.com">informacion@walksofitaly.com</a>.</p>

<h3>Información Importante</h3>

<p>Recuerda que la hora de quedar por el tour es 15 minutos ANTES de la hora del arranque. Si vas retrasado o no puedes encontrar tu guía o conductor por favor llama a nuestro número de emergencia y intentaremos ayudarte. Queremos que te juntes con nosotros pero, por desgracia no podemos retrasar el arranque del tour. Si no tienes un móvil disponible, no dudes en pedir uno de un Italiano, en general, estarían feliz de ayudar.</p>

<p>Y Por favor: no te olvides de tener esta información en la mano y llevar todo contigo el día de tu visita.</p>

<h3>Lo que debes saber antes de ir</h3>
<ul>

    <li><strong>El tiempo</strong> - todos los servicios de Walks of Italy se realizarán con lluvia o sol, así que por favor asegúrate de vestir adecuadamente.</li>

    <li><strong>Vestimenta</strong> - Todos los lugares religiosos en Italia requieren a hombres y mujeres usar ropa que cubra los hombros y las rodillas. Si tu visita incluye entrar en una iglesia o lugar sagrado, por favor asegúrate de que tu vestimenta cumple con estos requisitos, Walks of Italy no se hace responsable de la entrada negada debido a la vestimenta inapropiada.</li>

    <li><strong>Estudiantes</strong> – Si has reservado un boleto de estudiante debes traer una identificación válida.</li>

    <li><strong>Cancelaciones y Modificaciones</strong> - No se proporcionarán reembolsos por servicios que se cancelen dentro de 3 días de la hora de inicio programada. Por favor contáctenos lo mas pronto possible si necesita hacer un cambio a unos de sus servicios reservados. Todas las enmiendas deben ser aprobadas por un representante de Walks of Italy primero, y están sujetas a disponibilidad y tarifas aplicables.
        Por favor, haga clic en el siguiente enlace para obtener más información : [<a href="https://es.walksofitaly.com/cancellation">https://es.walksofitaly.com/cancellation</a>].
    </li>

    <li><strong>Propinas</strong> - Todas nuestras excursiones, visitas, y servicios de traslado incluyen todos los gastos de reserva, entradas y honorarios de guía. Las propinas no están incluidas. Aunque las propinas no son esperadas son siempre apreciadas.</li>


</ul>


<h3>Sus Servicios Reservados:</h3>






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
                            <td colspan="2"><strong> 
                                <?php 
                                setlocale(LC_ALL,"es_ES");
                                $date = DateTime::createFromFormat("Y-m-d H:i:s", $BookingsDetail_loop['BookingsDetail']['events_datetimes']);
                                echo strftime("%B %e, %Y (%A)",$date->getTimestamp());
                                        ?> 
                                </strong></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong> <?php echo $BookingsDetail_loop['Event']['name_long'] ?></strong></td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Ciudad/Región:</strong> <?php echo $BookingsDetail_loop['EventsPrimaryGroup']['primary_name'] ?><br />
                                <?php if ($BookingsDetail_loop['BookingsDetail']['private_group'] == 'Group'): ?>
                                    <strong>Hora de Encuentro:</strong> <?php echo date("H:i",strtotime($meetingTime_datetime)) ?><br />
                                <?php endif ?>
                                <strong>Hora de Inicio:</strong>  <?php echo  date("H:i",strtotime($BookingsDetail_loop['BookingsDetail']['events_datetimes'])) ?><br />
                                <strong>Número de Personas:</strong> <?php echo $total_pax ?><br />  <?php echo $pax_display ?><br />
                            </td>

                            <td>
                                <strong>Tipo de Visita:</strong>  <?php echo ($BookingsDetail_loop['BookingsDetail']['private_group'] == 'Group') ? 'Grupo': 'Privada'; ?><br />
                                <strong>Número de Orden:</strong>  <?php echo  $booking_id  ?><br />
                                <strong>Precio:</strong>  <?php echo ExchangeRate::format($BookingsDetail_loop['BookingsDetail']['amount_converted'])  ?>

                                <?php $discount = $BookingsDetail_loop['BookingsDetail']['charged_converted_amount'] - $BookingsDetail_loop['BookingsDetail']['amount_converted']; ?>
                                <?php if ($discount): ?>
                                    <br /><strong>Descuento</strong>
                                    - <?php $discount = abs($discount); echo ExchangeRate::format($discount); ?>
                                <?php endif ?>

                                <br />

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <br /><strong>Punto de Encuentro:</strong> <?php echo $BookingsDetail_loop['Event']['mp_text_'.$gp] ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Punto Final:</strong> <?php echo $BookingsDetail_loop['Event']['endpoint_'.$gp] ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>Direcciones:</strong> <?php echo $BookingsDetail_loop['Event']['directions_'.$gp] ?>
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

    <?php if ($promo_discount_fixed_total): ?>
        <h3>Su descuento con el código:</h3>
        <table width="100%" border="1" cellspacing="0" cellpadding="7px" bordercolor="#ccc">
            <tr>
                <th>Subtotal</th>
                <th>Descuento</th>
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


    <h3>Sus Transacciones:</h3>
    <table width="100%" border="1" cellspacing="0" cellpadding="7px" bordercolor="#ccc">
        <tr>
            <th>Fecha</th>
            <th>Identificación de Orden</th>
            <th>Tipo de Cambio</th>
            <th><?php echo ExchangeRate::getCurrency() ?></th>

        </tr>

        <?php setlocale(LC_ALL,"es_ES");
        foreach($PaymentTransaction as $PaymentTransaction_loop): ?>
            <tr>
                <td  align="center">
                    <?php 
                    //echo date("F j, Y",strtotime($PaymentTransaction_loop['PaymentTransaction']['transaction_date'])) 
                    $date = DateTime::createFromFormat("Y-m-d H:i:s", $PaymentTransaction_loop['PaymentTransaction']['transaction_date']);
                    echo strftime("%B %e, %Y (%A)",$date->getTimestamp());
                    ?>
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



<p>Gracias por viajar con nosotros!</p>
<p>Buon Viaggio</p>

<p>
    <strong>El Equipo de Walks of Italy</strong><br />
    <a href="https://es.walksofitaly.com">es.walksofitaly.com</a>

</p>



