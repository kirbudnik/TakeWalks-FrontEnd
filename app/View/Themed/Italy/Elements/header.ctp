<header id="top">

    <h1><a href="/">Walks of Italy</a></h1>
    <nav id="nav">
        <ul class="a">

            <li class="a"><a href="#">View Tours</a>
                <?php
                $remainingLocations = $locations;
                $mainLocations = array_splice($remainingLocations, 0, 5);
                ?>
                <ul class="mobile-tours">
                    <?php foreach (array_merge($mainLocations, $remainingLocations) as $loc): ?>
                        <li>
                            <a href="/<?php echo $loc['DomainsGroup']['url_name'] ?>-tours">
                                <i class="fa fa-info-circle"></i> <?php echo ucwords(explode('-', $loc['DomainsGroup']['url_name'])[0]); ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
                <div>
                    <div>
                        <ul class="list-g">
                            <?php foreach ($mainLocations as $loc): ?>
                                <li>
                                    <img src="<?php echo $this->ReSrc->resrcUrl($loc['DomainsGroup']['thumb'], 79) ?>"
                                         width="79" height="79" alt="<?php echo $loc['DomainsGroup']['name'] ?>"/>
                                    <?php echo $loc['DomainsGroup']['name'] ?>
                                    <a href="/<?php echo $loc['DomainsGroup']['url_name'] ?>-tours">
                                        <i class="fa fa-info-circle"></i> View tours
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                        <ul class="list-h">
                            <?php foreach ($remainingLocations as $loc): ?>
                                <li>
                                    <a href="/<?php echo $loc['DomainsGroup']['url_name'] ?>-tours">
                                        <?php echo $loc['DomainsGroup']['name'] ?>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="top-rated-tours">
                        <div>
                            <h2>Top rated tours</h2>
                            <ul class="list-i">
                                <?php $fourFeatured = array_slice($featured, 0, 4); ?>
                                <?php foreach ($fourFeatured as $event): ?>
                                    <?php
                                    $imageUrl = '';
                                    foreach ($event['EventsImage'] as $image) {
                                        if($image['listing']) {
                                            $imageUrl = $image['images_name'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <li data-id="<?php echo $event['Event']['id'] ?>">
                                        <a href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
                                            <img src="<?php echo $this->ReSrc->resrcUrl($imageUrl, 79) ?>" width="79"
                                                 height="79">
                                            <span><?php echo $event['Event']['name_listing'] ?></span>

                                            <a class="reviews rating-a rating-a-white <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['letterClass'] : '' ?>"
                                               href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>#reviews">
                                                <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['amount'] . ' reviews' : '' ?>
                                            </a>

                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                    <!-- <div class="tours-by-type">
                    <h2>Tours by type</h2>
                    <ul>
                        <?php foreach ($featuredTags as $tag): ?>
                            <li>
                                <a href="/rome-tours?type[]=<?php echo $tag['Tag']['id'] ?>">
                                    <?php echo $tag['Tag']['name'] ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div> -->
                </div>
            </li>


            <li><a href="/transfers">Transfers</a></li>
            <li><a href="/blog">Blog</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/faq">FAQ</a></li>
            <li><a href="/contact">Contact</a></li>
            <li class="currency">
                <form action="" method="POST">
                    <select class="currencySelect noDefaultSelect2" name="changeCurrency">
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
            </li>
        </ul>
        <ul class="b">
            <!--
            <li><a href="./"><i class="fa fa-user"></i> Login</a></li>
            <li><a href="./">Register</a></li>
            -->
            <li><a href="/gift_cards">Gift Cards</a></li>
            <li class="flag"><a href="http://es.walksofitaly.com" class="flag-spain"><span
                            class="hidden">Español?</span></a></li>


            <li class="cart">
                <a href="<?php echo FULL_BASE_URL ?>/payment">
                    <span id="cart_count" class="cart_count"><?php echo count($cart) ?></span>
                    <span class="hidden">Cart</span>
                    <i class="fa fa-shopping-cart"></i>
                </a>
            </li>
        </ul>
    </nav>
    <div class="fit-a"></div>
    <div class="fit-a">
        <div class="menu-helper">Menu</div>
    </div>


</header>
