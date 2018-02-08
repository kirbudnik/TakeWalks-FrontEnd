<?= $this->element('header'); ?>
<?= $this->element('account-header', ['selectedPage' => 'account']); ?>
<?php $this->start('scripts'); ?>
<?= $this->Html->script('pages/upcoming-tour-cancel.js') ?>
<?php $this->end(); ?>

    <section class="grey bordered compact">
        <div class="container">
            <div class="upcoming-detail-heading">
                <a href="<?= $backUrl ?>"><i class="icon icon-arrow_left icon-grey"></i></a>
                <div>
                    <h1 class="page-title sans">Request Tour Cancellation</h1>
                </div>
            </div>

            <div class="upcoming-detail-description separatd">
                <div class="right">
                    <ul class="detail-description-list">
                        <li>
                            <p>Date</p>
                            <span><?= date('D, j M, Y g:i a', strtotime($upcomingTour['tourDateTime'])) ?></span>
                        </li>
                        <li>
                            <p>Duration</p>
                            <span><?= $contentfullTour['tourDuration'] ?></span>
                        </li>
                        <li>
                            <p>Guests</p>
                            <span><?= $upcomingTour['number_adults'] + $upcomingTour['number_students'] + $upcomingTour['number_children'] + $upcomingTour['number_seniors'] + $upcomingTour['number_infants'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>

           <?php
            if (count($cancelDescriptions) > 0) {
                $index = 1;
                echo "<div class='radio-row'>";
                foreach($cancelDescriptions as $desc){
                    echo "<div class='css-checkbox big-radio normal-cb green-radio'>";
                        echo "<input type='radio' name='cancellation-reason' id='reason_{$index}' value='reason_{$index}'>";
                        echo "<label for='reason_{$index}'><div class='radio-circle'></div> {$desc}</label>";
                    echo "</div>";
                    $index++;
                }
                echo "<input type='text' name='first_name' id='cancelTourOtherInput' style='max-width: 700px;' />";
            echo "</div>";
            }
            ?>

            <div class="separated-btn">
                <button class="btn secondary green" data-booking-details-id="<?= $upcomingTour['bookingDetailsId'] ?>"  id="cancelTour" >Submit Request</button>
            </div>
        </div>
    </section>
<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
