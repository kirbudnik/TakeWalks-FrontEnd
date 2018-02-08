
<?php $this->start('scripts'); ?>
<script>
    var ForgotPassword = {
        init : function() {
            var btnResetPwd = $('#btnResetPwd');
            var form = $('#formResetPwd');
            var confirm = $('#resetPwdConfirm');
            var hideOnConfirm = $('.hide-on-confirm');
            form.submit(function(event){
                event.preventDefault();
                var data = form.serialize();
                var url = form.attr('action');
                $('body').css({cursor:'wait'});
                $.ajax({ data: data, url: url, method: 'post' }
                ).done(function(response) {
                    $('body').css({cursor:'default'});
                    if (response.success) {
                        hideOnConfirm.hide();
                        confirm.show();
                    } else {
                        window.location.reload();
                    }
                }).fail(function(response){
                    $('body').css({cursor:'default'});
                    window.location.reload();
                });
                return false;
            });
        }
    };

    (function(){ ForgotPassword.init(); })();
</script>

<?php $this->end(); ?>
<div class="sidebar login-sidebar login-sidebar-forgot-password hidden">
    <div class="sidebar-heading less-bottom account-status-hide-onclick ">
        <h2 class="heading hide-on-confirm">Forgot Your Password?</h2>
        <div class="close-cart">
            <i class="icon icon-close icon-grey"></i>
        </div>
    </div>
    <form action="/user/forgot-password" data-form-type="forgot-password" class="active hide-on-confirm" id="formResetPwd">
        <p class="descr separated">
            Please enter the email address you used to setup your account and we’ll send you a link to reset your password.
        </p>
        <div class="input-row auto foo-validate">
            <div class="input-div input-icon md-placeholder">
                <input type="email" name="email" required="required" >
                <div class="placeholder">Email Address</div>
            </div>
        </div>
        <div class="login-sidebar-buttons">
            <button class="btn secondary compact green" id="btnResetPwd">Reset Password</button>
        </div>
    </form>

    <div id="resetPwdConfirm" style="display: none;">
        <div class="sidebar-message">
            <i class="icon icon-checkmark_circle"></i>
            <div class="sidebar-heading">
                <h2 class="heading">Password Reset Link Sent</h2>
            </div>
            <p class="descr separated">If the email address you entered is associated with an existing account, you will receive an email with a link to reset your password.</p>
        </div>
    </div>

    <p class="sidebar-stick-bottom">Already have an account? <a href="javascript:;" class="underlined normal top-nav-login" >Log In</a></p>
</div>
