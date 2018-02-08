<div class="topnav">
  <div class="mobile-menu-btn">
    <h2>MENU</h2>
    <div><i class="icon icon-hamburger"></i></div>
  </div>
  <div class="topnav-logo hide-desktop">
      <i class="icon icon-logo"></i>
  </div>
  <div class="topnav-logo-green hide-tablet">
      <img src="/theme/TakeWalks/svg/logo-green.svg" alt="">
  </div>
  <div class="topnav-nav">
    <div class="topnav-item active"><a href="#" class="link">Italy</a></div>
    <div class="topnav-item"><a href="#" class="link">Paris</a></div>
    <div class="topnav-item"><a href="#" class="link">Istanbul</a></div>
    <div class="topnav-item"><a href="#" class="link">New York</a></div>
    <div class="topnav-item non-destination"><a href="#" class="link">Contact</a></div>
    <div class="topnav-item hide-desktop"><a href="my-account" class="link">My Profile</a></div>
    <div class="topnav-item hide-desktop"><a href="login" class="link">Log In</a></div>
    <div class="topnav-item hide-desktop"><a href="signup" class="link">Sign Up</a></div>
  </div>
  <div class="topnav-right login">
    <div class="topnav-item hide-tablet separated"><a href="" class="link">Login</a></div>
    <div class="topnav-item hide-tablet separated"><a href="" class="link">Sign Up</a></div>
    <div class="topnav-cart">
        <i class="icon icon-cart_full"></i>
        <span>0</span>
    </div>
  </div>
</div>

<div class="login-page">
  <div class="login-page-content">
    <i class="icon icon-logo_small_footer"></i>
    <h5 class="subtitle green">Welcome To TakeWalks.com</h5>
    <h2>Discover the world<br> one step at a time</h2>
  </div>

  <div class="sidebar login-sidebar">
    <div class="sidebar-heading signup-heading">
      <h1>Sign Up</h1>
    </div>

    <div class="close-sidebar">
      <i class="icon icon-close icon-grey close-signup-sidebar"></i>
    </div>

    <div class="input-row account-status-hide-onclick">
      <div class="btn-togglers">
        <div class="btn-toggler active" data-toggle-toggler="new">I'm New</div>
        <div class="btn-toggler" data-toggle-toggler="booked">Already booked a Tour?</div>
      </div>
    </div>

    <div data-toggle-target="booked">
      <p class="descr separated">
        Please enter the email address you used to book your tour with and we’ll send you a link to reset your password.
      </p>

      <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
          <input type="email">
          <div class="placeholder">Email Address</div>
        </div>
      </div>

      <div class="login-sidebar-buttons">
        <button class="btn secondary compact green" data-toggle-toggler="requestAcc">Request Account</button>
      </div>
    </div>

    <div class="active" data-toggle-target="new">
      <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
          <input type="text">
          <div class="placeholder">First Name</div>
        </div>
      </div>

      <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
          <input type="text">
          <div class="placeholder">Last Name</div>
        </div>
      </div>

      <div class="input-row auto foo-validate">
        <div class="input-div input-icon md-placeholder">
          <input type="email">
          <div class="placeholder">Email Address</div>
        </div>
      </div>

      <div class="input-row auto item-below foo-validate">
        <div class="input-div input-icon md-placeholder">
          <input type="password">
          <div class="placeholder">Password</div>
        </div>
      </div>

      <div class="login-sidebar-buttons">
        <button class="btn secondary compact green">Create Account</button>
        <p class="or">or</p>
        <button class="btn secondary compact lcased facebook">Log In with Facebook</button>
        <button class="btn secondary compact lcased google">Log In with Google</button>
      </div>
    </div>

    <div data-toggle-target="requestAcc">
      <div class="sidebar-message">
        <i class="icon icon-checkmark_circle"></i>
        <div class="sidebar-heading">
          <h1>Temporary Account Password Sent</h1>
        </div>
        <p class="descr separated">Check your email address for a password reset link, to access your new account.</p>
      </div>
    </div>

    <p class="sidebar-stick-bottom">Already have an account? <a href="login" class="underlined normal">Log In</a></p>
  </div>
</div>
