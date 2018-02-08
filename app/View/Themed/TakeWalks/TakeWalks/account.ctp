<?= $this->element('header'); ?>
<?= $this->element('account-header',['selectedPage' => 'account']); ?>
    <section class="grey bordered">
        <?php if(empty($upcomingTours)): ?>
            <div class="account-no-content">
                <p class="descr">You don't have any upcoming tours yet!</p>
                <div class="center-btn small">
                    <a href="/" class="btn secondary green">Find A Tour</a>
                </div>
            </div>
        <?php else: ?>
            <div class="upcoming-tours">
                <div class="container">
                    <table class="upcoming-tours compact">
                        <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Tour Name</th>
                            <th class="hide-mobile">Guests</th>
                            <th class="hide-mobile">Total Cost</th>
                            <th class="hide-mobile"></th>
                            <th class="hide-mobile"></th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($upcomingTours as $upcomingTour): ?>
                            <tr>
                                <td>
                                    <?= date('D, j M, Y', strtotime($upcomingTour['tourDateTime'])); ?><br>
                                    <?= date('g:i a', strtotime($upcomingTour['tourDateTime'])); ?>
                                </td>
                                <td>
                                    <?= $upcomingTour['name'] ?>
                                    <span>Purchased on: <?= date('D, j M, Y', strtotime($upcomingTour['bookingTime'])); ?></span>
                                </td>
                                <td class="hide-mobile"><?= $upcomingTour['guests'] ?></td>
                                <td class="hide-mobile"><?= $upcomingTour['price'] ?></td>
                                <td class="hide-mobile"><a href="/resend-voucher/<?=$upcomingTour['bookingDetailsId']?>" class="normal underlined" >Resend Voucher</a></td>
                                <!--                <td><a href="my-account-upcoming-detail" class="btn secondary grey green-hover">See Details</a></td>-->
                                <td><a href="/upcoming<?= DS.$upcomingTour['citySlug'].DS.$upcomingTour['slug'].DS.$upcomingTour['bookingDetailsId']; ?>" class="btn secondary grey green-hover">See Details</a></td>
                            </tr>
                        <?php endforeach; ?>


                        <!--          <tr>-->
                        <!--            <td>-->
                        <!--              Tue, 25 Mar, 2017<br>-->
                        <!--              9:00 am-->
                        <!--            </td>-->
                        <!--            <td>-->
                        <!--              St. Peter's Basilica Tour with Dome Climb & Crypt (3 hrs)-->
                        <!--              <span>Purchased on: Wed, Feb 5, 2017</span>-->
                        <!--            </td>-->
                        <!--            <td>3 Adults</td>-->
                        <!--            <td>$275</td>-->
                        <!--            <td><a href="" class="normal underlined" data-modal-togler"shareItinerary">Share Itinerary</a></td>-->
                        <!--            <td><a href="" class="normal underlined" data-modal-toggler="resendVoucher">Resend Voucher</a></td>-->
                        <!--            <td><a href="my-account-upcoming-detail" class="btn secondary grey green-hover">See Details</a></td>-->
                        <!--          </tr>-->
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif ?>

    </section>
<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
