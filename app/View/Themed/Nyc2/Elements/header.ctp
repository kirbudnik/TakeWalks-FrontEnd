<div class="info-bar top">
    <div class="grid-container">
        <div class="content-wrap">
            <a href="mailto:info@walksofnewyork.com" class="hide-portrait">info@walksofnewyork.com</a>
            <div class="horizontal-separator hide-tablet">|</div>
            <a href="tel:18886838671">Toll-free: +1-888-683-8671</a>

            <div class="social hide-tablet">
                <a href="https://www.facebook.com/walksofnewyork" class="rose-hl"><i class="fa fa-facebook"></i></a>
                <a href="https://twitter.com/WalksofNewYork" class="rose-hl"><i class="fa fa-twitter"></i></a>
                <a href="http://instagram.com/walksofnewyork" class="rose-hl"><i class="fa fa-instagram"></i></a>
                <a href="https://www.youtube.com/user/walksofnewyork" class="rose-hl"><i class="fa fa-youtube-play"></i></a>
                <a href="https://plus.google.com/+Walksofnewyork/posts" class="rose-hl"><i
                        class="fa fa-google-plus"></i></a>
            </div>
        </div>
    </div>
</div>

<?php if($this->action == 'home'): ?>

    <header class="home-page">
        <div class="slide-image active"></div>


        <div class="grid-container">
            <nav>
                <div class="logo">
                    <img src="theme/nyc2/img/logo.png" alt="" class="logo-hide-tablet">
                    <img src="theme/nyc2/img/logo-black.png" alt="" class="show-tablet">
                </div>
                <div class="navigation">
                    <div class="quick-menu">
                        <div class="quick-menu-item">
                            <form action="" method="post">
                                <select name="changeCurrency" class="currency-dropdown">
                                    <option value="USD"
                                            href="" <?php echo ExchangeRate::getCurrency() == 'USD' ? 'selected' : '' ?>>
                                        &#36; USD
                                    </option>
                                    <option
                                        value="EUR" <?php echo ExchangeRate::getCurrency() == 'EUR' ? 'selected' : '' ?>>
                                        &#8364; EUR
                                    </option>
                                    <option
                                        value="GBP" <?php echo ExchangeRate::getCurrency() == 'GBP' ? 'selected' : '' ?>>
                                        &#163; GBP
                                    </option>
                                    <option
                                        value="CAD" <?php echo ExchangeRate::getCurrency() == 'CAD' ? 'selected' : '' ?>>
                                        &#36; CAD
                                    </option>
                                    <option
                                        value="AUD" <?php echo ExchangeRate::getCurrency() == 'AUD' ? 'selected' : '' ?>>
                                        &#36; AUD
                                    </option>
                                </select>
                            </form>
                        </div>

                        <a href='<?php echo FULL_BASE_URL ?>/payment' class="quick-menu-item cart">
                            <i class="fa fa-shopping-cart"></i> <?php echo count($cart) ?>
                        </a>
                    </div>

                    <div class="menu-wrap">

                        <div class="reveal-menu show-tablet" data-hideshow-toggler="menu">
                            <div class="bars">
                                <div class="bar top"></div>
                                <div class="bar middle"></div>
                                <div class="bar btm"></div>
                            </div>
                            <div>MENU</div>
                        </div>

                        <ul class="main-menu" data-hideshow-target="menu">
                            <li class="current"><a href="/new-york-tours">View Tours</a></li>
                            <li><a href="/blog">Blog</a></li>
                            <li><a href="/about">About</a></li>
                            <li><a href="/press">Press & Testimonials</a></li>
                            <li><a href="/contact">Contact Us</a></li>
                        </ul>

                    </div>
                </div>

            </nav>
            <div class="home-mobile-space" data-hideshow-target="menu" style="display: none; height:155px;"></div>
            <div class="hero">
                <h2 class="h2">Walks of New York</h2>
                <h1 class="h1">The authentic NY experience</h1>

                <p>We offer the best of both worlds: all the <b>unmissable spots</b>,<br> plus the <b>out of the
                        ordinary exclusive experience</b><br> with handpicked local guides</p>
            </div>
        </div>

        <div class="explore-scroll">
            <div class="text" data-scroll-toggler="tours">
                Explore
                <img src="/theme/nyc2/img/icons/arrow-bottom-white.png" alt="">
            </div>
        </div>
    </header>

<?php else: ?>
    <header class="general">
        <div class="grid-container">
            <nav class="general">
                <div class="logo">
                    <a href="/"><img src="/theme/nyc2/img/logo-black.png" alt=""></a>
                </div>

                <div class="navigation">
                    <div class="quick-menu">
                        <div class="quick-menu-item">
                            <form action="" method="post">
                                <select name="changeCurrency" class="currency-dropdown">
                                    <option value="USD"
                                            href="" <?php echo ExchangeRate::getCurrency() == 'USD' ? 'selected' : '' ?>>
                                        &#36; USD
                                    </option>
                                    <option
                                        value="EUR" <?php echo ExchangeRate::getCurrency() == 'EUR' ? 'selected' : '' ?>>
                                        &#8364; EUR
                                    </option>
                                    <option
                                        value="GBP" <?php echo ExchangeRate::getCurrency() == 'GBP' ? 'selected' : '' ?>>
                                        &#163; GBP
                                    </option>
                                    <option
                                        value="CAD" <?php echo ExchangeRate::getCurrency() == 'CAD' ? 'selected' : '' ?>>
                                        &#36; CAD
                                    </option>
                                    <option
                                        value="AUD" <?php echo ExchangeRate::getCurrency() == 'AUD' ? 'selected' : '' ?>>
                                        &#36; AUD
                                    </option>
                                </select>
                            </form>
                        </div>

                        <a href='<?php echo FULL_BASE_URL ?>/payment' class="quick-menu-item cart">
                            <i class="fa fa-shopping-cart"></i> <?php echo count($cart) ?>
                        </a>
                    </div>

                    <div class="menu-wrap">

                        <div class="reveal-menu show-tablet" data-hideshow-toggler="menu">
                            <div class="bars">
                                <div class="bar top"></div>
                                <div class="bar middle"></div>
                                <div class="bar btm"></div>
                            </div>
                            <div>MENU</div>
                        </div>

                        <ul class="main-menu" data-hideshow-target="menu">
                            <li class="current"><a href="/new-york-tours">View Tours</a></li>
                            <li><a href="/blog">Blog</a></li>
                            <li><a href="/about">About</a></li>
                            <li><a href="/press">Press & Testimonials</a></li>
                            <li><a href="/contact">Contact Us</a></li>
                        </ul>

                    </div>
                </div>
            </nav>
            <div class="home-mobile-space" data-hideshow-target="menu" style="display: none; height:155px;"></div>
        </div>
    </header>


<?php endif; ?>
