<?= $this->element('header'); ?>
<?= $this->element('account-header', ['selectedPage' => 'past_tours']); ?>
    <section class="grey bordered">
        <?php if (empty($pastTours)): ?>
            <div class="account-no-content">
                <p class="descr">You don't have any past tours yet!</p>
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
                            <th>Guests</th>
                            <th>Total Cost</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($pastTours as $pastTour): ?>
                            <tr>
                                <td>
                                    <?= date('D, j M, Y', strtotime($pastTour->tourDateTime)); ?><br>
                                    <?= date('g:i a', strtotime($pastTour->tourDateTime)); ?>
                                </td>
                                <td>
                                    <?= $pastTour->name; ?>
                                    <span>Purchased on: <?= date('D, j M, Y', strtotime($pastTour->booking_time)); ?></span>
                                </td>
                                <td><?= $pastTour->number_adults + $pastTour->number_students + $pastTour->number_children + $pastTour->number_seniors + $pastTour->number_infants ?></td>
                                <td><?= ExchangeRate::format($pastTour->exchange_amount, $pastTour->exchange_to) ?></td>
                                <td><a href="/past_tours/refund/<?= $pastTour->bookingDetailsId ?>" class="normal underlined">Report Issue</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif ?>

    </section>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
