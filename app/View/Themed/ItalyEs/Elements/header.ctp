<!--div class="top-subnav">
    <a href="" class="phone-number">
        <img src="theme/ItalyEs/img/sspain-flag.png" alt="">
        <span class="text">+275 2727 27272 272</span>
    </a>
    <a href="" class="phone-number">
        <img src="theme/ItalyEs/img/worldwide-flag.png" alt="">
        <span class="text">+275 2727 27272 272</span>
    </a>
    <a href="" class="phone-number">
        <img src="theme/ItalyEs/img/worldwide-flag.png" alt="">
        <span class="text">+275 2727 27272 272</span>
    </a>
</div-->
<header id="top">
    <h1><a href="/">Tours En Italia</a></h1>
    <nav id="nav">
        <ul class="a">
            <li class="a"><a href="/">Visitas Guiadas</a></li>
            <li><a href="<?php echo $theme->blogUrl ?>">BLOG</a></li>
            <li><a href="/about">CONÓCENOS</a></li>
            <li><a href="/contact">CONTÁCTENOS</a></li>
            <li class="currency">
                <form action="" method="POST">
                    <select class="currencySelect noDefaultSelect2" name="changeCurrency">
                        <option value="EUR" <?php echo ExchangeRate::getCurrency() == 'EUR' ? 'selected' : '' ?>>&#8364; EUR</option>
                        <option value="USD" <?php echo ExchangeRate::getCurrency() == 'USD' ? 'selected' : '' ?>>&#36; USD</option>
                        <option value="GBP" <?php echo ExchangeRate::getCurrency() == 'GBP' ? 'selected' : '' ?>>&#163; GBP</option>
                        <option value="CAD" <?php echo ExchangeRate::getCurrency() == 'CAD' ? 'selected' : '' ?>>&#36; CAD</option>
                        <option value="AUD" <?php echo ExchangeRate::getCurrency() == 'AUD' ? 'selected' : '' ?>>&#36; AUD</option>
                    </select>
                </form>
            </li>
        </ul>
        <ul class="b">
            <!--
            <li><a href="./"><i class="fa fa-user"></i> Login</a></li>
            <li><a href="./">Register</a></li>
            -->
            <li class="flag"><a href="http://www.walksofitaly.com" hreflang="es" class="flag-us"><span class="hidden">English?</span></a></li>


            <li class="cart">
                <!--a href="#" class="user-login-actions">
                    <i class="fa fa-user"></i>
                    INICIAR SESIÓN | INSCRIBIRSE
                </a-->
                <a href="<?php echo FULL_BASE_URL ?>/payment">
                    <span><?php echo count($cart) ?></span>
                    <span class="hidden">Cart</span>
                    <i class="fa fa-shopping-cart"></i>
                </a>
            </li>
        </ul>
    </nav>
    <div class="fit-a"></div><div class="fit-a"><div class="menu-helper">Menu</div></div>


</header>
