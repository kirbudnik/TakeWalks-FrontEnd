<section class="account-section">
    <div class="container">
        <div class="account-header">
            <img src="/theme/TakeWalks/img/user.svg" alt="">
            <h2 class="heading"><?=ucwords($user['fname']) . ' ' . ucwords($user['lname']) ?></h2>
            <a href="/logout" class="green normal">Sign Out</a>
        </div>
    </div>
    <div class="tabs outlined city-nav-tabs my-account-tabs">
        <a href="/account" class="tab-item city-tour-tab <?=$selectedPage == 'account' ? 'active' : '' ?>">Upcoming Tours</a>
        <a href="/past_tours" class="tab-item city-tour-tab <?=$selectedPage == 'past_tours' ? 'active' : '' ?>">Past Tours</a>
        <a href="/wishlist" class="tab-item city-tour-tab <?=$selectedPage == 'wishlist' ? 'active' : '' ?>">Wishlist</a>
        <a href="/settings" class="tab-item city-tour-tab <?=$selectedPage == 'settings' ? 'active' : '' ?>">Settings</a>
    </div>
</section>
