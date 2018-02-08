<?= $this->element('header'); ?>
<?= $this->element('account-header',['selectedPage' => 'settings']); ?>


<?php $this->start('scripts'); ?>
<style>
    .error-message {
        display: none;
        font-size: 18px;
        color: #af3756;
        font-weight: 400;
        border: 1px solid #adb6bc;
        padding: 7px;
        border-radius: 3px;
        margin-bottom: 12px;
        background: #f7f7f7;
        text-align: center;
    }
</style>
<?= $this->Html->script('pages/settings.js') ?>

<!--  remove icons: icon-close, icon-remove_tour, icon-circle-close -->
<script type="template" class="divUpcomingTripInfo">
    <hr>
    <br>
    <a href="" class="green has-icon remove-from-cart remove-form" title="Remove Destination"><i class="icon icon-circle-close"></i>Remove</a>
    <br>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="text" name="city[]" required="required">
            <div class="placeholder">City</div>
        </div>
    </div>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="text" name="hotel_name[]" required="required">
            <div class="placeholder">Hotel Name</div>
        </div>
    </div>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="tel" name="hotel_phone[]" required="required">
            <div class="placeholder">Hotel Phone Number</div>
        </div>
    </div>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="text" class="foo-datepick" name="start_date[]" required="required" readonly >
            <div class="placeholder">Start Date</div>
            <i class="icon icon-calendar"></i>
        </div>
        <div class="input-div input-icon md-placeholder">
            <input type="text" class="foo-datepick" name="end_date[]" required="required" readonly >
            <div class="placeholder">End Date</div>
            <i class="icon icon-calendar"></i>
        </div>
    </div>
</script>
<script type="template" class="divAdditionalTraveler">
    <hr>
    <br>
    <a href="" class="green has-icon remove-from-cart remove-form" title="Remove Traveler"><i class="icon icon-circle-close"></i>Remove</a>
    <br>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="text" name="first_name[]" required="required">
            <div class="placeholder">First Name</div>
        </div>
    </div>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="text" name="last_name[]" required="required">
            <div class="placeholder">Last Name</div>
        </div>
    </div>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="email" name="email[]" required="required">
            <div class="placeholder">Email Address</div>
        </div>
    </div>
    <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
            <input type="tel" name="phone[]" required="required">
            <div class="placeholder">Phone Number</div>
        </div>
    </div>
</script>
<?php $this->end(); ?>


    <section class="compact grey bordered">
        <div class="container">
            <div class="paired-fields">
                <!--      <div class="left">-->
                <!--        <h4 class="input-row-heading">Profile Photo</h4>-->
                <!--        <div class="profile-photo">-->
                <!--          <img src="/theme/TakeWalks/svg/profile_icon.svg" alt="">-->
                <!--          <div>-->
                <!--            <button class="btn secondary compact green lcased">Upload a Photo</button>-->
                <!--            <button class="btn secondary compact facebook lcased">Use Facebook Photo</button>-->
                <!--          </div>-->
                <!--        </div>-->
                <!--      </div>-->
                <div class="left">
                    <h4 class="input-row-heading">Connected Social Accounts</h4>

                    <div class="connect-social-acc <?= $facebook['class']; ?>">
                        <div class="icon-circle share connect-social facebook">
                            <i class="icon icon-facebook"></i>
                            <div class="connect-success">
                                <i class="icon icon-checkmark" style="left: 0px;"></i>
                            </div>
                        </div>
                        <span><?= $facebook['status']; ?></span>
                        <form id="form-social-facebook" class="formSubmit" action="/user/social">
                            <input type="hidden" name="socialProviderId" value="<?= $facebook['socialProviderId']; ?>">
                            <a class="btn secondary compact lcased facebook btnLoginFacebook" data-social-action="connect" data-connect>Connect Facebook</a>
                            <button class="btn secondary compact lcased facebook" type="submit" data-disconnect>Disconnect Facebook</button>
                        </form>
                    </div>
                    <div class="connect-social-acc <?= $google['class']; ?>">
                        <div class="icon-circle share connect-social google">
                            <i class="icon icon-google-plus"></i>
                            <div class="connect-success">
                                <i class="icon icon-checkmark"></i>
                            </div>
                        </div>
                        <span><?= $google['status']; ?></span>
                        <form id="form-social-google" class="formSubmit" action="/user/social">
                            <input type="hidden" name="socialProviderId" value="<?= $google['socialProviderId']; ?>">
                            <a class="btn secondary compact lcased google btnLoginGoogle" data-social-action="connect" id="btnLoginGoogle3" data-connect>Connect Google</a>
                            <button class="btn secondary compact lcased google" type="submit" data-disconnect>Disconnect Google</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="compact bordered">
        <div class="container">
            <div class="paired-fields">
                <div class="left">
                    <form id="form-edit" class="formSubmit" action="/user/update">
                        <h4 class="input-row-heading">Edit Account Information</h4>
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <div class="input-row auto foo-validate">
                            <div class="input-div input-icon md-placeholder">
                                <input type="text" name="first_name" value="<?= isset($user['fname']) ? $user['fname'] : '' ?>" required="required">
                                <div class="placeholder">First Name</div>
                            </div>
                        </div>
                        <div class="input-row auto foo-validate">
                            <div class="input-div input-icon md-placeholder">
                                <input type="text" name="last_name" value="<?= isset($user['lname']) ? $user['lname'] : '' ?>" required="required">
                                <div class="placeholder">Last Name</div>
                            </div>
                        </div>
                        <div class="input-row auto foo-validate">
                            <div class="input-div input-icon md-placeholder">
                                <input type="email" name="email" value="<?= $user['email'] ?>" required="required">
                                <div class="placeholder">Email</div>
                            </div>
                        </div>
                        <div class="input-row auto foo-validate">
                            <div class="input-div input-icon md-placeholder">
                                <input type="tel" name="mobile_number" value="<?= isset($user['mobile_number']) ? $user['mobile_number'] : '' ?>">
                                <div class="placeholder">Phone</div>
                            </div>
                        </div>
                        <div class="error-message"></div>
                        <div class="center-btn">
                            <button class="btn secondary green" type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
                <div class="right">
                    <form id="form-change-pass" class="formSubmit" action="/user/change/password" autocomplete="off">
                        <h4 class="input-row-heading">Change Password</h4>

                        <div class="input-row auto foo-validate">
                            <div class="input-div input-icon md-placeholder">
                                <input type="password" name="passwordOld" required="required">
                                <div class="placeholder">Old Password</div>
                            </div>
                        </div>

                        <div class="input-row auto foo-validate">
                            <div class="input-div input-icon md-placeholder">
                                <input type="password" id="password1" name="passwordNew" required="required" minlength="6">
                                <div class="placeholder">Create New Password</div>
                            </div>
                        </div>

                        <div class="input-row auto foo-validate">
                            <div class="input-div input-icon md-placeholder">
                                <input type="password" id="password2" name="passwordNewVerify" required="required" minlength="6">
                                <div class="placeholder">Confirm New Password</div>
                            </div>
                        </div>
                        <div class="error-message"></div>
                        <div class="center-btn">
                            <button class="btn secondary green" type="submit">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php /* --
    <section class="compact grey bordered">
        <form id="form-upcoming-trip" class="formSubmit" action="/user/upcoming-trip">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            <div class="container">
                <div class="paired-fields">
                    <div class="left">
                        <h4 class="input-row-heading">Upcoming Trip Information</h4>
                        <div id="upcoming-trip-information">
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="text" name="city[]" required="required">
                                    <div class="placeholder">City</div>
                                </div>
                            </div>
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="text" name="hotel_name[]" required="required">
                                    <div class="placeholder">Hotel Name</div>
                                </div>
                            </div>
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="tel" name="hotel_phone[]" required="required">
                                    <div class="placeholder">Hotel Phone Number</div>
                                </div>
                            </div>
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="text" class="foo-datepick" name="start_date[]" required="required" readonly >
                                    <div class="placeholder">Start Date</div>
                                    <i class="icon icon-calendar"></i>
                                </div>
                                <div class="input-div input-icon md-placeholder">
                                    <input type="text" class="foo-datepick" name="end_date[]" required="required" readonly >
                                    <div class="placeholder">End Date</div>
                                    <i class="icon icon-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <a href="" class="green has-icon" id="addDestination" title="Add Destination"><i class="icon icon-add"></i>Destination</a>
                    </div>
                    <div class="right">
                        <h4 class="input-row-heading">Additional Travelers</h4>
                        <div id="additional-travelers">
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="text" name="first_name[]" required="required">
                                    <div class="placeholder">First Name</div>
                                </div>
                            </div>
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="text" name="last_name[]" required="required">
                                    <div class="placeholder">Last Name</div>
                                </div>
                            </div>
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="email" name="email[]" required="required">
                                    <div class="placeholder">Email Address</div>
                                </div>
                            </div>
                            <div class="input-row auto foo-validate">
                                <div class="input-div input-icon md-placeholder">
                                    <input type="tel" name="phone[]" required="required">
                                    <div class="placeholder">Phone Number</div>
                                </div>
                            </div>
                        </div>
                        <a href="" class="green has-icon" id="addTraveler" title="Add Traveler"><i class="icon icon-add"></i>Traveler</a>
                    </div>
                </div>
            </div>

            <div class="center-btn medium">
                <button class="btn secondary more-side green" type="submit">Submit</button>
            </div>
        </form>
    </section>

-- */ ?>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
