<?= $this->element('header'); ?>
<?= $this->element('account-header', ['selectedPage' => 'account']); ?>
<?php $this->start('scripts'); ?>
<?= $this->Html->script('pages/past-tour-refund.js') ?>
<?php $this->end(); ?>

    <section class="grey bordered compact">
        <div class="container">
            <div class="upcoming-detail-heading">
                <a href="/past_tours"><i class="icon icon-arrow_left icon-grey"></i></a>
                <div>
                    <h1 class="page-title sans">Report Issue</h1>
                </div>
            </div>

            <div class="upcoming-detail-description compact">
                <div class="right">
                    <ul class="detail-description-list">
                        <li>
                            <p>Date</p>
                            <span><?= date('D, j M, Y g:i a', strtotime($pastTour['tourDateTime'])) ?></span>
                        </li>
                        <li>
                            <p>Duration</p>
                            <span><?= $contentfullTour['tourDuration'] ?></span>
                        </li>
                        <li>
                            <p>Guests</p>
                            <span><?= $pastTour['number_adults'] + $pastTour['number_students'] + $pastTour['number_children'] + $pastTour['number_seniors'] + $pastTour['number_infants'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <p class="descr separated limited">
                All refunds need to be approved by our team, who will most likely contact you for more details. To help speed the process up, please select your primary reason for requesting a refund below:
            </p>

            <?php 
            if (count($refundDescriptions) > 0) {
                $index = 1;
                echo "<div class='radio-row'>";
                foreach($refundDescriptions as $desc){
                    echo "<div class='css-checkbox big-radio normal-cb green-radio'>";
                        echo "<input type='radio' name='cancellation-reason' id='reason_{$index}' value='reason_{$index}'>";
                        echo "<label for='reason_{$index}'><div class='radio-circle'></div> {$desc}</label>";
                    echo "</div>";
                    $index++;
                }   
                echo "</div>";
            }   
            ?>
          
            <div class="separated-btn">
                <button class="btn secondary green" data-booking-details-id="<?= $bookingDetailsId ?>" id="refundTour">Submit Request
                </button>
            </div>
        </div>
    </section>
<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
