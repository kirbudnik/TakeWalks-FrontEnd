<div class="topnav">
    <div class="mobile-menu-btn">
        <h2>MENU</h2>
        <div><i class="icon icon-hamburger"></i></div>
    </div>
    <div class="topnav-logo mobile-logo hide-desktop">
        <a href="/">
            <img src="/theme/TakeWalks/svg/logo-fleur.svg" alt="Take Walks logo">
        </a>
    </div>
    <div class="topnav-logo-green hide-tablet">
        <a href="/">
            <img alt="Take Walks logo" src="/theme/TakeWalks/svg/logo-green.svg">
        </a>
    </div>
    <div class="topnav-nav">
        <?php foreach($header['countries'] as $name => $country): ?>
            <div class="topnav-item">
                <a href="#" class="link"><?= $name ?></a>
                <div class="topnav-dropdown">
                    <?php foreach($country['cities'] as $city): ?>
                    <a href="/<?=str_replace(' ','-',strtolower($city['name'])) ?>-tours"><?= ucwords($city['name']) ?></a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach ?>

        <div class="topnav-dropdown">
            <div class="topnav-item hide-desktop non-destination mobile-login">
                <a href="#" class="link" onclick="$('.mobile-menu-btn').trigger('click');$('.top-nav-login').click();" >Login</a>
            </div>
        </div>
        <div class="topnav-dropdown">
            <div class="topnav-item non-destination">
                <a href="/contact" class="link">Contact</a>
            </div>
        </div>
    </div>
    <div class="topnav-right">


        <?php if(isset($user) && $user): ?>
            <div class="topnav-user topnav-item">
                <!-- <img src="/theme/TakeWalks/img/foo-avatar.jpg" alt="" class="avatar"> -->
                <span>Hello <?= ucwords($user['fname']) ?></span>
                <div class="caret"></div>
                <div class="topnav-dropdown">
                    <a href="/account">Account</a>
                    <a href="/wishlist">WishList</a>
                    <a href="/past_tours">Past Tours</a>
                    <a href="/logout">Log Out</a>
                </div>
            </div>
        <?php else: ?>
            <div class="topnav-item hide-tablet separated top-nav-login"><a href="javascript:;" class="link">Login</a></div>
        <?php endif ?>

        <div class="topnav-item currency-select-item">
            <a href="#" class="link"><?= ExchangeRate::getSymbol() . ' ' . ExchangeRate::getCurrency() ?></a>
            <div class="topnav-dropdown">
                <a href="javascript:;" data-currency="EUR">€ EUR</a>
                <a href="javascript:;" data-currency="USD">$ USD</a>
                <a href="javascript:;" data-currency="GBP">£ GBP</a>
                <a href="javascript:;" data-currency="CAD">$ CAD</a>
                <a href="javascript:;" data-currency="AUD">$ AUD</a>
            </div>
        </div>

        <?php if(!isset($noCart)): ?>
            <div class="topnav-cart">
                <i class="icon icon-cart_full"></i>
                <span class="item-count"></span>
            </div>
        <?php endif ?>
    </div>

</div>
<?= $this->element('login'); ?>
<?= $this->element('register'); ?>
<?= $this->element('forgot_password'); ?>
