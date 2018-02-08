<?php $this->start('bottomHead');?>
<script src="https://apis.google.com/js/api:client.js"></script>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1856044651312694',
            //appId      : '1989124628036695',
            xfbml      : true,
            version    : 'v2.10'
        });
        FB.AppEvents.logPageView();
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<?php $this->end(); ?>

<?php $this->start('scripts'); ?>
<?= $this->Html->script('FacebookWrapper.js') ?>
<?= $this->Html->script('GoogleSignInWrapper.js') ?>
<script>
    (function(){
        setTimeout(function(){
            FacebookWrapper.init();
            GoogleSignInWrapper.init();
        }, 1000);
    })();
</script>

<?php $this->end(); ?>
<div class="sidebar login-sidebar login-sidebar-register hidden">
    <div class="sidebar-heading">
        <h2 class="heading">Sign Up</h2>
        <?php if($this->request->action != 'login'): ?>
            <div class="close-cart">
                <i class="icon icon-close icon-grey"></i>
            </div>
        <?php endif ?>
    </div>

    <div class="input-row account-status-hide-onclick">
        <div class="btn-togglers">
            <div class="btn-toggler active" data-toggle-toggler="booked">Already booked a Tour?</div>
            <div class="btn-toggler" data-toggle-toggler="new">I'm New</div>
        </div>
    </div>

    <div data-toggle-target="booked">
        <form action="/user/register_booked" data-form-type="register_booked">
            <p class="descr separated">
                Please enter the email address you used to book your tour with and we’ll send you a link to reset your password.
            </p>

            <div class="input-row auto foo-validate">
                <div class="input-div input-icon md-placeholder">
                    <input type="email" name="email">
                    <div class="placeholder">Email Address</div>
                </div>
            </div>
            <div class="error-message"></div>
            <div class="login-sidebar-buttons">
                <button class="btn secondary compact green" >Request Account</button>
            </div>
        </form>
    </div>

    <div data-toggle-target="new">
        <form action="/user/register" data-form-type="register">
            <input type="hidden" name="facebook_id"  id="facebook_id" value="">

            <div class="input-row auto foo-validate">
                <div class="input-div input-icon md-placeholder">
                    <input type="text" name="first_name" required="required" minlength="2">
                    <div class="placeholder">First Name</div>
                </div>
            </div>

            <div class="input-row auto foo-validate">
                <div class="input-div input-icon md-placeholder">
                    <input type="text" name="last_name" required="required" minlength="2">
                    <div class="placeholder">Last Name</div>
                </div>
            </div>

            <div class="input-row auto foo-validate">
                <div class="input-div input-icon md-placeholder">
                    <input type="email" name="email" required="required" minlength="6">
                    <div class="placeholder">Email Address</div>
                </div>
            </div>

            <div class="input-row auto item-below foo-validate">
                <div class="input-div input-icon md-placeholder">
                    <input type="password" name="password" required="required" minlength="6">
                    <div class="placeholder">Password</div>
                </div>
            </div>

            <div class="login-sidebar-buttons">
                <div class="error-message"></div>
                <button class="btn secondary compact green">Create Account</button>
                <p class="or">or</p>
                <button class="btn secondary compact lcased facebook social-login btnLoginFacebook" data-social-action="register">Log In with Facebook</button>
                <button class="btn secondary compact lcased google social-login btnLoginGoogle" data-social-action="register" id="btnLoginGoogle2">Log In with Google</button>
            </div>
        </form>
    </div>

    <div data-toggle-target="requestAcc">
        <div class="sidebar-message">
            <i class="icon icon-checkmark_circle"></i>
            <div class="sidebar-heading">
                <h2 class="heading">Temporary Account Password Sent</h2>
            </div>
            <p class="descr separated">Check your email address for a password reset link, to access your account.</p>
        </div>
    </div>

    <p class="sidebar-stick-bottom">Already have an account? <a href="javascript:;" class="underlined normal top-nav-login" >Log In</a></p>
</div>
