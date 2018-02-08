<?= $this->element('header'); ?>
<?php
    $singular = [
            'adults' => 'adult',
            'seniors' => 'senior',
            'students' => 'student',
            'children' => 'child',
            'infants' => 'infant'
    ];
    $totalConvertedPrice = 0;
?>
<main class="sidebar-layout-left checkout-success">
    <div class="container">
        <div class="sidebar-container">
            <div class="sidebar shopping-cart standalone">

                <?php foreach($confirmCart as $tour): ?>
                <div class="right-sidebar-item">
                    <div class="sidebar-subheading">
                        <div class="checkout-sidebar-item">
                            <a href="#" class="event-title"><?= $tour['name'] ?></a>
                        </div>

                        <p class="event-date"><?= date('D, j M, Y \a\t h:i a',strtotime($tour['datetime'])) ?></p>

                        <div class="checkout-summary">
                            <?php foreach(['adults','seniors','students','children','infants'] as $paxType): ?>
                                <?php if($tour[$paxType] > 0): ?>
                                    <p>
                                        <?=$tour[$paxType] . ' ' . ucfirst($tour[$paxType] == 1 ? $singular[$paxType] : $paxType) ?>
                                        <span><?=ExchangeRate::format($tour[$paxType . '_price_converted_' . $currency],$currency);  ?></span>
                                    </p>
                                <?php endif ?>
                            <?php endforeach; ?>

                            <!-- <p class="discount-row">Discount 10% <span>-$19.00</span></p> -->
                            <?php $totalConvertedPrice += $tour['subtotal_converted']; ?>
                            <p class="subtotal-row">Subtotal <span><?=ExchangeRate::format($tour['subtotal_converted'],$currency); ?></span></p>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
                <!--
                <div class="right-sidebar-item">
                    <div class="sidebar-subheading">
                        <div class="checkout-sidebar-item">
                            <a href="#" class="event-title">Secrets of Castel Sant'Angelo Tour</a>
                            <a href="#" class="remove-from-cart"><i class="icon icon-remove_tour"></i></a>
                        </div>

                        <p class="event-date">Tues, 14 Mar, 2017 at 9:00 am</p>

                        <div class="checkout-summary">
                            <p>3 Students <span>$85.00</span></p>
                            <p class="subtotal-row">Subtotal <span>$255.00</span></p>
                        </div>

                    </div>
                </div>
                -->
                <div class="sidebar-heading">
                    <div class="heading">Price</div>
                    <div class="price"><?=ExchangeRate::format($totalConvertedPrice,$currency); ?></div>
                </div>
            </div>
        </div>

        <div class="main-container book-success">
            <div class="success-heading">
              <i class="icon icon-checkmark_circled green icon-big"></i>
              <h1 class="page-title">Confirmed</h1>
            </div>

            <h2 class="default">Pack your bags: You’re booked!</h2>

            <p class="descr">
                We’re excited to take walks with you very soon. Your email confirmation will be with you in the next hour.
            </p>

            <p class="descr">
                First though, please take a moment to create a customer profile. This will give you access to additional meeting point directions and allow you to easily resend your voucher.
            </p>

            <p class="descr">
              Just click the 'Request Password' button below and we'll email you a link to set up your password. If you already have a customer profile, log in to access your tour information.
            </p>

            <a href="javascript:;" class="btn secondary grey top-nav-register">Request Password</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="javascript:;" class="btn secondary purple top-nav-login">Log In</a>

        </div>

    </div>
</main>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>

<div class="dropdown-menu simple store-select-dd" data-dropdown-target="dd_ex">
    <a href="#" class="dropdown-menu-item">Apple Calendar</a>
    <a href="#" class="dropdown-menu-item">Google Calendar</a>
    <a href="#" class="dropdown-menu-item">Outlook</a>
</div>
