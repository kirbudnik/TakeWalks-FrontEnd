<header id="top">

    <h1><a href="/">Walks of Turkey</a></h1>
    <nav id="nav">
        <ul class="a">
            <li class="a"><a href="<?php echo FULL_BASE_URL . DS . $viewToursLink ?>-tours">View Tours</a>


            </li>
            <!-- temporarily repressed

            <li><a href="/about">About</a></li>
            -->
            <li><a href="/blog/">Blog</a></li>
            <li><a href="/faq">FAQ</a></li>
            <li><a href="/contact">Contact</a></li>
            <li class="currency">
                <form action="" method="POST">
                <select class="currencySelect noDefaultSelect2" name="changeCurrency">
                    <option value="USD" <?php echo ExchangeRate::getCurrency() == 'USD' ? 'selected' : '' ?>>&#36; USD</option>
                    <option value="TRY" <?php echo ExchangeRate::getCurrency() == 'TRY' ? 'selected' : '' ?>>&#8378; TRY</option>
                    <option value="EUR" <?php echo ExchangeRate::getCurrency() == 'EUR' ? 'selected' : '' ?>>&#8364; EUR</option>
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


            <li class="cart">
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
