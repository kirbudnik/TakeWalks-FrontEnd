<?= $this->element('header'); ?>

<main class="default contact-page">
  <div class="container">
    <h1 class="page-title">Contact</h1>
  </div>

  <div class="tabs centered city-nav-tabs">
<?php if (!$loggedIn){?>
      <a href="#login" class="tab-item city-tour-tab active">Login</a>
<?php } ?>
      <a href="#customerservice" class="tab-item city-tour-tab">Customer Service</a>
      <a href="#problems" class="tab-item city-tour-tab">Problems While Travelling</a>
  </div>

<?php if (!$loggedIn){?>
  <section id="login">
    <div class="container">
      <div class="section-title small center">
        <i class="icon icon-customer-login"></i>
        <h2 class="section-heading">Customer Login</h2>
      </div>

      <p class="descr center">Resend your voucher, request a cancellation & check your meeting point details.</p>
      <p class="descr center separated">Did you know you can do all of these things by availing of your customer profile? Just request a password and log in to see more details about your tour or, in case plans change, to request a cancellation and refund (free up to 72 hour before your tour).</p>

      <div class="center-btn smaller">
        <a href="javascript:;" class="btn secondary grey top-nav-register">Request Password</a>
        <a href="javascript:;" class="btn secondary purple top-nav-login">Log In</a>
      </div>

    </div>
  </section>
<?php } ?>

      <div class="center-btn smaller">
        <p class="descr single center"><a href="/cancellation-policy" class="green underlined">Change & Cancellation Policy</a></p><br>
      </div>

  <section class="grey" id="customerservice">
    <div class="container">
      <div class="section-title small center">
        <i class="icon icon-call-customer-support"></i>
        <h2 class="section-heading">Call Customer Service</h2>
      </div>


      <p class="descr center">The best way to reach us for non-urgent matters us is by email: <a class="green phone-num" href="mailto:info@takewalks.com">info@takewalks.com</a></p>
      <p class="descr center separated">Our team will try to get back to you within 24 hours. Or you can speak to a member of our customer service team by calling the numbers below:</p>

      <p class="descr single center"><b>From the US (toll-free): <a href="tel:+18886838670" class="green phone-num"  style="white-space: nowrap">+1-888-683-8670</a></b></p>
      <p class="descr single center"><b>From the UK: <a href="tel:+448455916256" class="green phone-num">+44-845-591-6256</a></b></p>
      <p class="descr single center"><b>International: <a href="tel:+12026846916" class="green phone-num">+1-202-684-6916</a></b></p>
    </div>
  </section>
  <section id="problems">
    <div class="container">
      <div class="section-title small center">
        <i class="icon icon-problems-while-traveling"></i>
        <h2 class="section-heading">Problems While Travelling</h2>
      </div>


      <p class="descr center separated">If you have a problem with your tour or finding a location, you can get in touch with our local teams (office hours are in local time):</p>
        <?= $this->element('contact_phone_number'); ?>
    </div>
  </section>
</main>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
