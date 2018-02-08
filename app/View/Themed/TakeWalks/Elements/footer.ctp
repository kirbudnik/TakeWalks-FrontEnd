<footer>
    <div class="footer-upper">
        <div class="footer-select">
            <div>
              <div class="currency-select-text">
                  <span><?= ExchangeRate::getSymbol() . ' ' . ExchangeRate::getCurrency() ?></span>
                  <div class="triangle"></div>
                  <div class="topnav-dropdown">
                      <a href="javascript:;" data-currency="EUR">€ EUR</a>
                      <a href="javascript:;" data-currency="USD">$ USD</a>
                      <a href="javascript:;" data-currency="GBP">£ GBP</a>
                      <a href="javascript:;" data-currency="CAD">$ CAD</a>
                      <a href="javascript:;" data-currency="AUD">$ AUD</a>
                  </div>
              </div>
            </div>
        </div>
        <div style="display:none">
            <form action="" method="POST">
                <select class="currency-select" name="changeCurrency" onchange="this.form.submit()">
                    <option value="EUR" <?php echo ExchangeRate::getCurrency() == 'EUR' ? 'selected' : '' ?>>&#8364;
                        EUR
                    </option>
                    <option value="USD" <?php echo ExchangeRate::getCurrency() == 'USD' ? 'selected' : '' ?>>&#36;
                        USD
                    </option>
                    <option value="GBP" <?php echo ExchangeRate::getCurrency() == 'GBP' ? 'selected' : '' ?>>&#163;
                        GBP
                    </option>
                    <option value="CAD" <?php echo ExchangeRate::getCurrency() == 'CAD' ? 'selected' : '' ?>>&#36;
                        CAD
                    </option>
                    <option value="AUD" <?php echo ExchangeRate::getCurrency() == 'AUD' ? 'selected' : '' ?>>&#36;
                        AUD
                    </option>
                </select>
            </form>
        </div>
        <div class="footer-links">
            <ul>
<!--                <li><a href="/about">About Us</a></li>-->
                <li><a href="/contact">Contact Us</a></li>
                <li><a href="/privacy-policy">Privacy Policy</a></li>
            </ul>
            <ul>
                <li><a href="/cancellation-policy">Cancellation Policy</a></li>
                <li><a href="/terms">Terms & Conditions</a></li>

            </ul>
        </div>

        <div class="footer-subscribe">
            <h5 class="subtitle white label">SUBSCRIBE TO OUR NEWSLETTER</h5>
            <div>
                <input type="email" id="footer-signup-email" placeholder="Email Address">
                <button class="btn green secondary input-aligned" id="footer-signup-button" >SUBSCRIBE</button>
            </div>
        </div>
    </div>

    <div class="footer-copyright">
        <div class="note"><i class="icon icon-logo_small_footer"></i>Copyright © <?= date('Y') ?> TakeWalks</div>
        <div class="social">
            Connect With Us
            <a target="_blank" href="https://www.facebook.com/walkingtours"><i class="icon icon-facebook"></i></a>
            <a target="_blank" href="https://twitter.com/walkstours"><i class="icon icon-twitter"></i></a>
            <a target="_blank" href="https://www.instagram.com/walkstours"><i class="icon icon-instagram"></i></a>
        </div>
    </div>
</footer>
