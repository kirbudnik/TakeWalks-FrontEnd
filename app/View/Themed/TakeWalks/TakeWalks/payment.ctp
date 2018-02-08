<?php $this->start('scripts'); ?>
<?= $this->Html->script('/js/pages/payment.js'); ?>

<?php $this->end(); ?>
<?= $this->element('header',['noCart' => true]); ?>

<main class="sidebar-layout-left payment">
    <div class="container">
        <h1 class="page-title">Checkout</h1>
    </div>
    <div class="container">
        <div class="sidebar-container">
            <div class="currency-togglers">
                <label class="input-label">Currency</label>

                <div class="buttons">
                    <div class="currency-toggler" data-currency="USD">USD</div>
                    <div class="currency-toggler" data-currency="EUR">EUR</div>
                    <div class="currency-toggler" data-currency="GBP">GBP</div>
                    <div class="currency-toggler" data-currency="CAD">CAD</div>
                    <div class="currency-toggler" data-currency="AUD">AUD</div>
                </div>
            </div>

            <div class="sidebar shopping-cart standalone">
                <div class="payment-item-container">

                </div>
                <div class="sidebar-heading">
                    <h2 class="heading">Total</h2>
                    <div class="price"></div>
                </div>
            </div>

            <div class="sidebar-payment-item sidebar-promocode">
                <a href="#" class="green promo-code active">Add Promo Code</a>
                <div class="promocode-input">
                    <form method="post" action="/pages/apply_promo" class="form-d" id="apply_promo_form">
                        <div class="input-icon">
                            <input type="text" name="promo" placeholder="Promo Code">
                        </div>
                        <button class="btn secondary purple input-aligned">Apply</button>
                    </form>
                </div>
                <div class="error-msg">Sorry, that code is invalid!</div>
            </div>

            <div class="sidebar-payment-item sidebar-iata">
                <span class="sidebar-title">Travel Agents Only:</span>
                <a href="#" class="green promo-code active">Add IATA Code</a>
                <div class="iata-input valid">
                    <form action="">
                        <div class="input-icon">
                            <input type="text" name="iata-input" placeholder="IATA code">
                        </div>
                        <button class="btn secondary purple input-aligned">Apply</button>
                    </form>
                </div>
                <div class="error-msg">Sorry, that code is invalid!</div>
            </div>
        </div>
        <div class="main-container">
            <form method="post" id="formPayment" data-form-type="payment" >
                <input type="hidden" name="currency">
                <input type="hidden" name="iata">
                <h4 class="input-row-heading"><i class="icon icon-contact_information green"></i>Contact Information
                </h4>

                <?php if($user): ?>
                    <div class="tooltip-container">
                        <i>i</i> <span>Why can't I edit my information?</span>
                        <div class="tooltip">
                            <div class="arrow-up"></div>
                            Your account information is used to associate your account with your bookings. This information can be updated on the account page.
                        </div>
                    </div>

                <?php
                endif;

                $contactInfo = [];
                $contactInfo['street_address'] = '';
                $contactInfo['city'] = '';
                $contactInfo['zip'] = '';
                $contactInfo['state'] = '';
                $contactInfo['country'] = '';
                if (empty($postData)) {
                    $contactInfo['first_name'] = (isset($user['fname']) && $user['fname'] != '') ?  $user['fname'] : '';
                    $contactInfo['last_name'] = (isset($user['lname']) && $user['lname'] != '') ?  $user['lname'] : '';
                    $contactInfo['email'] = (isset($user['email']) && $user['email'] != '') ?  $user['email'] : '';
                    $contactInfo['phone_number'] = (isset($user['mobile_number']) && $user['mobile_number'] != '') ?  $user['mobile_number'] : '';
                } else {
                    $contactInfo = $postData;
                }

                ?>

                <div class="input-row auto foo-validate">
                    <div class="input-div input-icon">
                        <input type="text" name="first_name" placeholder="First Name" value="<?= $contactInfo['first_name'] ?>"
                            <?= ( isset($user['id']) ) ? ' readonly' : ''; ?> required
                           pattern="(?=.*([\w]).*)[ \w]*"
                           oninvalid="setCustomValidity('Please fill out this field.')"
                           onchange="try{setCustomValidity('')}catch(e){}"

                        >
                    </div>
                    <div class="input-div input-icon">
                        <input type="text" name="last_name" placeholder="Last Name" value="<?= $contactInfo['last_name'] ?>"
                            <?= ( isset($user['id']) ) ? ' readonly' : ''; ?> required
                           pattern="(?=.*([\w]).*)[ \w]*"
                           oninvalid="setCustomValidity('Please fill out this field.')"
                           onchange="try{setCustomValidity('')}catch(e){}"

                        >
                    </div>
                    <input type="hidden" name="ccFirstName" value="">
                    <input type="hidden" name="ccLastName" value="">
                </div>

                <div class="input-row auto foo-validate">
                    <div class="input-div input-icon">
                        <input type="email" name="email" placeholder="Email Address" value="<?= $contactInfo['email'] ?>"
                            <?= ( isset($user['id']) ) ? ' readonly' : ''; ?> required maxlength="100" >
                    </div>
                    <div class="input-div input-icon">
                        <input type="text" name="phone_number" oninput="this.value=this.value.replace(/[^0-9\-+]/g,'');" placeholder="Phone" required
                           value="<?= $contactInfo['phone_number'] ?>" <?= ( isset($user['id']) && $contactInfo['phone_number'] != '') ? ' readonly' : ''; ?>
                           pattern="(?=.*([\w]).*)[ \w]*"
                           oninvalid="setCustomValidity('Please fill out this field.')"
                           onchange="try{setCustomValidity('')}catch(e){}"

                        >
                    </div>
                </div>

                <h4 class="input-row-heading"><i class="icon icon-payment_information green"></i>Payment Information</h4>

                <div class="input-row foo-validate auto">
                    <div class="input-div input-icon">
                        <input type="text" autocomplete="off" class="cc cc-num" name="ccNo" placeholder="Credit Card Number" required onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    </div>
                </div>

                <div class="input-row foo-validate auto collapse">
                    <div class="input-div input-icon">
                        <select name="ccMonth"  class="single-select" data-placeholder="Month" required>
                            <option></option>
                            <?php foreach(range(1,12) as $month): ?>
                                <option value="<?= $month ?>"><?=str_pad($month,2,'0',STR_PAD_LEFT) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-div input-icon">
                        <select name="ccYear"  class="single-select" data-placeholder="Year" required>
                            <option></option>
                            <?php foreach(range(date('Y'),date('Y') + 20) as $month): ?>
                                <option value="<?= $month ?>"><?=str_pad($month,2,'0',STR_PAD_LEFT) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-div input-icon">
                        <input type="text" autocomplete="off" class="cc" name="ccCCV" required placeholder="CVV"  oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="4">
                        <i class="icon icon-cvv"></i>
                    </div>
                </div>

                <div class="input-row auto foo-validate collapse">
                    <div class="input-div input-icon">
                        <input type="text" name="street_address" placeholder="Address" 
                        maxlength="30"
                        value="<?= $contactInfo['street_address'] ?>" required
                        pattern="^[a-zA-Z0-9\s,\.'-]*$"
                        oninvalid="setCustomValidity('Please fill out this field.')"
                        onchange="try{setCustomValidity('')}catch(e){}"
                        >
                    </div>
                    <div class="input-div input-icon">
                        <select name="country" id="" class="single-select" data-placeholder="Country" required>
                            <option></option>
                            <?php foreach($countries as $countryAbrv => $country): ?>
                                <option value="<?= $countryAbrv ?>" <?= ($contactInfo['country'] == $countryAbrv) ? ' selected ' : '' ?> ><?= $country ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>

                <div class="input-row collapse auto">
                    <div class="input-div input-icon">
                        <input type="text" name="city" placeholder="City"  value="<?= $contactInfo['city'] ?>" required
                           pattern="(?=.*([\w]).*)[ \w]*"
                           oninvalid="setCustomValidity('Please fill out this field.')"
                           onchange="try{setCustomValidity('')}catch(e){}"
                        >
                    </div>
                    <div class="input-div input-icon foo-validate">
                        <input type="text" class="cc" name="zip" placeholder="ZIP" value="<?= $contactInfo['zip'] ?>" required
                           pattern="(?=.*([\w]).*)[ \w]*"
                           oninvalid="setCustomValidity('Please fill out this field.')"
                           onchange="try{setCustomValidity('')}catch(e){}"
                        >
                    </div>
                    <div class="input-div input-icon foo-validate" style="display: none;">
                        <input type="text" id="state_text" placeholder="State / County" value="<?= $contactInfo['state'] ?>">
                    </div>
                    <div class="input-div input-icon">
                        <select id="state_select" class="single-select" data-placeholder="State" required>
                            <option></option>
                            <?php foreach($states as $stateAbrv => $state): ?>
                                <option value="<?= $stateAbrv ?>" <?= ($contactInfo['state'] == $stateAbrv) ? ' selected ' : '' ?> ><?= $state ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                </div>

                <div class="checkbox-center">
                    <div class="css-checkbox compare-cb green-cb">
                        <input type="checkbox" id="ccb_01">
                        <label for="ccb_01">Yes, I have read and agree to the <a href="/terms" target="_blank">Terms & Conditions</a></label>
                    </div>
                </div>


                <div class="center-btn medium">
                    <div class="error-message">Invalid something.</div>
                    <button class="btn primary green complete_booking">Complete Booking</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
<?= $this->element('templates/payment-item'); ?>
<?= $this->element('templates/payment-item-guest-row'); ?>
<?= $this->element('templates/payment-item-discount-row'); ?>
