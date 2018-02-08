<div class="sidebar login-sidebar login-sidebar-login hidden <?=$this->request->action == 'login' ? 'active' : '' ?>" <?=$this->request->action == 'login' ? 'style="display:block"' : '' ?>>
    <div class="sidebar-heading">
        <h2 class="heading">Login</h2>
        <?php if($this->request->action != 'login'): ?>
            <div class="close-cart">
                <i class="icon icon-close icon-grey"></i>
            </div>
        <?php endif ?>
    </div>
    <form action="/user/login" data-form-type="login">
        <div class="input-row auto foo-validate">
            <div class="input-div input-icon md-placeholder">
                <input type="email" name="email">
                <div class="placeholder">Email Address</div>
            </div>
        </div>

        <div class="input-row auto item-below foo-validate">
            <div class="input-div input-icon md-placeholder">
                <input type="password" name="password">
                <div class="placeholder">Password</div>
            </div>
        </div>

        <a href="javascript:;" class="underlined normal top-nav-forgot-password">Forgot your password?</a>


        <div class="login-sidebar-buttons">
            <div class="error-message"></div>
            <button class="btn secondary compact green">Log In</button>
            <p class="or">or</p>
            <button class="btn secondary compact lcased facebook btnLoginFacebook" data-social-action="register">Log In with Facebook</button>
            <button class="btn secondary compact lcased google btnLoginGoogle" data-social-action="register" id="btnLoginGoogle">Log In with Google</button>

        </div>
    </form>
    <p class="sidebar-stick-bottom">Don't have an account? <a href="javascript:;" class="underlined normal top-nav-register" id="btnSignUp">Sign Up</a></p>
</div>
