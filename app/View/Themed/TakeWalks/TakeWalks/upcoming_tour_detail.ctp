<?= $this->element('header'); ?>
<?= $this->element('account-header',['selectedPage' => 'account']); ?>
<?php $this->start('scripts'); ?>
<?php $this->end(); ?>

    <section class="grey bordered compact">
        <div class="container">
            <div class="upcoming-detail-heading">
                <a class="go-back" href="/account"><i class="icon icon-arrow_left icon-grey"></i></a>
                <div>
                    <h5 class="subtitle grey">Booking Details</h5>
                    <h2 class="tour-title">
                        <a href="<?= $tourDetail['tourUrl'] ?>">

                        <?= $tourDetail['contentful']['tourTitleLong'] ?>
                        </a>
                    </h2>
                </div>
            </div>
            <div class="upcoming-detail-actions my-account-tabs">
                <a href="/resend-voucher/<?=$tourDetail['api']['bookingDetailsId']?>" class="underlined normal">Resend Voucher</a>
<!--                <a href="#" class="underlined normal">Modify Date</a>-->
<!--                <a href="#" data-booking-details-id="<?= $tourDetail['api']['bookingDetailsId'] ?>" class="underlined normal" id="cancelTour">Cancel Tour</a>-->
                <a href="/upcoming/cancel/<?= $tourDetail['api']['bookingDetailsId'] ?>" class="underlined normal" >Cancel Tour</a>
            </div>

            <div class="upcoming-detail-description">
                <div class="left">
                    <img src="<?= $tourDetail['image'] ?>?w=400&q=70" alt="">
                </div>
                <div class="right">
                    <ul class="detail-description-list">
                        <li>
                            <p>Date</p>
                            <span><?= date('D, j M, Y g:i a', strtotime($tourDetail['api']['tourDateTime'])) ?></span>
                        </li>
                        <li>
                            <p>Duration</p>
                            <span><?= $tourDetail['contentful']['tourDuration'] ?></span>
                        </li>
                        <li>
                            <p>Guests</p>
                            <span><?= $tourDetail['api']['number_adults'] + $tourDetail['api']['number_students'] + $tourDetail['api']['number_children'] + $tourDetail['api']['number_seniors'] + $tourDetail['api']['number_infants'] ?></span>
                        </li>
                        <li>
                            <p>Total Cost</p>
                            <span><?= ExchangeRate::format($tourDetail['api']['exchange_amount'], $tourDetail['api']['exchange_to']) ?></span>
                        </li>
                        <li>
                            <p>Purchased</p>
                            <span><?= date('D, j M, Y g:i a', strtotime($tourDetail['api']['booking_time'])) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="compact">
        <div class="container">
            <h2 class="separated big">Where to Meet</h2>
            <?php if ( isset($tourDetail['meetingPoints']['newFromDate']) ): ?>
            <p style="font-size: 1.2em;">
                <?= str_replace('p>','span>', ContentfulWrapper::parseMarkdown($tourDetail['meetingPoints']['newFromDate']) ) ?>
            </p>
            <?php endif ?>
            <div class="upcoming-detail-description">
                <div class="left">
                    <img src="<?= $tourDetail['meetingPointImages'] ?>?w=400&q=70" alt="">

                    <div class="map">
                        <?php if ($tourDetail['googleMapPlaceId'] != ''): ?>
                        <iframe src="https://www.google.com/maps/d/embed?mid=<?= $tourDetail['googleMapPlaceId'] ?>" width="100%" height="300" frameborder="0"></iframe>
                        <?php else: ?>
                        <img src="<?= $tourDetail['meetingPointMaps'] ?>" alt="">
                        <?php endif ?>
                    </div>
                </div>
                <div class="right">
                    <ul class="detail-description-list">
                        <li>
                            <p>Start Time</p>
                            <span><?= date('g:i a', strtotime($tourDetail['api']['tourDateTime'])) ?></span>
                        </li>
                        <li>
                            <p>End Time</p>
                            <span><?= date('g:i a', strtotime($tourDetail['api']['tourDateTime'].' +'.$tourDetail['contentful']['tourDuration'])) ?></span>
                        </li>
                        <li>
                            <p>Details</p>
                            <span>
                              <?= str_replace('p>','span>', ContentfulWrapper::parseMarkdown($tourDetail['meetingPoints']['meetingPointDirectionsLong']) ) ?>
                            </span>
                        </li>
                        <li style="    margin-bottom: 0px;">
                            <p>Address</p>
                            <span><?= $tourDetail['meetingPoints']['meetingPointAddress1'] ?></span>
                        </li>
                        <li>
                            <p></p>
                            <span><a href="https://www.google.com/maps/d/viewer?mid=<?= $tourDetail['googleMapPlaceId'] ?>" target="_blank" style="text-decoration: underline">Get Directions</a></span>
                        </li>
                        <li>
                            <p>End Point</p>
                            <span><?= $tourDetail['meetingPoints']['endPoint1'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php if (isset($tourDetail['meetingPoints']['part2MeetingPointDirectionsLong']) && $tourDetail['meetingPoints']['part2MeetingPointDirectionsLong'] != ''):?>
            <div class="upcoming-detail-description">
                <div class="left">
                    <img src="<?= $tourDetail['part2MeetingPointImages'] ?>?w=400&q=70" alt="">

                    <div class="map">
                        <?php if ($tourDetail['part2GoogleMapPlaceId'] != ''): ?>
                            <iframe src="https://www.google.com/maps/d/embed?mid=<?= $tourDetail['part2GoogleMapPlaceId'] ?>" width="400" height="300" frameborder="0"></iframe>
                        <?php else: ?>
                            <img src="<?= $tourDetail['part2MeetingPointMaps'] ?>" alt="">
                        <?php endif ?>
                    </div>
                </div>
                <div class="right">
                    <ul class="detail-description-list">
                        <li>
                            <p>Part Two</p>
                            <span><?= isset($tourDetail['meetingPoints']['part2ConfirmationTitle']) ? $tourDetail['meetingPoints']['part2ConfirmationTitle'] : '' ?></span>
                        </li>
                        <li>
                            <p>Details</p>
                            <?= str_replace('p>','span>', ContentfulWrapper::parseMarkdown($tourDetail['meetingPoints']['part2MeetingPointDirectionsLong']) ) ?>
                        </li>
                        <li style="    margin-bottom: 0px;">
                            <p>Address</p>
                            <span><?= $tourDetail['meetingPoints']['meetingPointAddress2'] ?></span>
                        </li>
                        <li>
                            <p></p>
                            <span><a href="https://www.google.com/maps/d/viewer?mid=<?= $tourDetail['part2GoogleMapPlaceId'] ?>" target="_blank" style="text-decoration: underline">Get Directions</a></span>
                        </li>
                        <li>
                            <p>End Point</p>
                            <span><?= $tourDetail['meetingPoints']['endPoint2'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php endif ?>

            <?php if (isset($tourDetail['meetingPoints']['newFromDate']) && $tourDetail['meetingPoints']['newFromDate'] != ''):?>
                <h1 style="font-size: 1.2em;">New Meeting Point</h1>
            <div class="upcoming-detail-description">
                <div class="left">
                    <img src="<?= $tourDetail['newMeetingPointImages'] ?>" alt="">

                    <div class="map">
                        <?php if ($tourDetail['newGoogleMapPlaceId'] != ''): ?>
                            <iframe src="https://www.google.com/maps/d/embed?mid=<?= $tourDetail['newGoogleMapPlaceId'] ?>" width="400" height="300" frameborder="0"></iframe>
                        <?php else: ?>
                            <img src="<?= $tourDetail['newMeetingPointMaps'] ?>" alt="">
                        <?php endif ?>
                    </div>
                </div>
                <div class="right">
                    <ul class="detail-description-list">
                        <li>
                            <p>Details</p>
                            <?= str_replace('p>','span>', ContentfulWrapper::parseMarkdown($tourDetail['meetingPoints']['newMeetingPointDirectionsLong']) ) ?>
                        </li>
                        <li style="    margin-bottom: 0px;">
                            <p>Address</p>
                            <span><?= $tourDetail['meetingPoints']['newMeetingPointAddress'] ?></span>
                        </li>
                        <li>
                            <p></p>
                            <span><a href="https://www.google.com/maps/d/viewer?mid=<?= $tourDetail['newGoogleMapPlaceId'] ?>" target="_blank" style="text-decoration: underline">Get Directions</a></span>
                        </li>
                        <li>
                            <p>End Point</p>
                            <span><?= $tourDetail['meetingPoints']['newEndPoint'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php endif ?>


        </div>
    </section>

<?php if (isset($tourDetail['meetingPoints']['importantNotes']) && $tourDetail['meetingPoints']['importantNotes'] != ''):?>
    <div class="section-sep-heading">
        <div class="container">
            <h2 class="heading">Important Information</h2>
        </div>
    </div>
    <div class="container" style="font-size: 1.2em;">
            <?= ContentfulWrapper::parseMarkdown($tourDetail['meetingPoints']['importantNotes']) ?>
    </div>
<?php endif ?>

    <div class="section-sep-heading">
        <div class="container">
            <h2 class="heading">FAQs</h2>
        </div>
    </div>

    <div class="faq-section section bordered">
        <?php foreach($faqs as $faq): ?>
        <div class="faq-question">
            <div class="faq-question-title">
                <div class="container">
                    <?= $faq['question']; ?>
                    <i class="icon icon-collapse"></i>
                </div>
            </div>
            <div class="faq-question-content">
                <div class="container">
                    <?= $faq['answer']; ?>
                </div>
            </div>
        </div>
        <?php endforeach ?>
<!--        <div class="faq-question">-->
<!--            <div class="faq-question-title">-->
<!--                <div class="container">-->
<!--                    Booking in advance means no waiting in line, right?-->
<!--                    <i class="icon icon-collapse"></i>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="faq-question-content">-->
<!--                <div class="container">-->
<!--                    Cras quis nulla consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Sum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felnec. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Natoque penatibus et magnis dis parturient montes, Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="faq-question">-->
<!--            <div class="faq-question-title">-->
<!--                <div class="container">-->
<!--                    Nam porttitor blandit accumsan ut vel dictu?-->
<!--                    <i class="icon icon-collapse"></i>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="faq-question-content">-->
<!--                <div class="container">-->
<!--                    Cras quis nulla consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Sum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felnec. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Natoque penatibus et magnis dis parturient montes, Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="faq-question">-->
<!--            <div class="faq-question-title">-->
<!--                <div class="container">-->
<!--                    Nam porttitor blandit accumsan ut vel dictu?-->
<!--                    <i class="icon icon-collapse"></i>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="faq-question-content">-->
<!--                <div class="container">-->
<!--                    Cras quis nulla consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Sum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felnec. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Natoque penatibus et magnis dis parturient montes, Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="faq-question">-->
<!--            <div class="faq-question-title">-->
<!--                <div class="container">-->
<!--                    Sum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.-->
<!--                    <i class="icon icon-collapse"></i>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="faq-question-content">-->
<!--                <div class="container">-->
<!--                    Cras quis nulla consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Sum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felnec. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Natoque penatibus et magnis dis parturient montes, Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
