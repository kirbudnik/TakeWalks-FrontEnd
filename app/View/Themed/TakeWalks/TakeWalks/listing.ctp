
<?php
$this->start('bottomHead');

//----- BreadcrumbList rich snippet -------
$breadcrumbListRS = $this->RichSnippets->create('BreadcrumbList');
$itemListElement = [];

$listItemRS = $this->RichSnippets->create('ListItem');
$listItemRS->setVal('position',1);
$itemRS = $listItemRS->addChild('item','Item');
$itemRS->setVal('@id', FULL_BASE_URL . DS );
$itemRS->setVal('name','Take Walks');
$listItemRS = $listItemRS->getArray();
$itemListElement[] = $listItemRS;

$listItemRS = $this->RichSnippets->create('ListItem');
$listItemRS->setVal('position',2);
$itemRS = $listItemRS->addChild('item','Item');
$itemRS->setVal('@id',$canonicalURL);
$itemRS->setVal('name',$metaTitle);
$listItemRS = $listItemRS->getArray();
$itemListElement[] = $listItemRS;

$breadcrumbListRS->setVal('itemListElement',$itemListElement);

echo $breadcrumbListRS->getJSON();

//----- ItemList rich snippet -------
$itemListRS = $this->RichSnippets->create('ItemList');
$itemListRS->setVal('url', $canonicalURL);

$productList = [];
$position = 0;
$uniqueTours = [];
foreach($content['tags'] as $tag) {
    foreach($tag['tours'] as $tour){
        $tourUrl = FULL_BASE_URL . DS . $tour['citySlug'] . DS. $tour['slug'];
        // is not allowed to have duplicated URL
        if ( !isset($uniqueTours[$tourUrl]) ) {
            $position++;
            $uniqueTours[$tourUrl] = $position;
            $listItemRS = $this->RichSnippets->create('ListItem');
            $listItemRS->setVal('position',$position);
            $listItemRS->setVal('url', $tourUrl);
            $listItemRS = $listItemRS->getArray();
            $productList[] = $listItemRS;
        }
    }
}

$itemListRS->setVal('numberOfItems', $position);
$itemListRS->setVal('itemListElement', $productList);
echo $itemListRS->getJSON();

$this->end();
?>

<?= $this->element('header'); ?>
<?php $this->start('scripts'); ?>
<?= $this->Html->script('pages/listing.js') ?>
<?php $this->end(); ?>

    <div class="city-tours-header">
        <div class="container">
            <div class="city-tour-title">
                <h1 class="page-title black"><?= $content['cityName'] ?> Tours</h1>
                <form class="city-tour-date" method="post">
                    <div class="input-icon">
                        <input value="<?= $content['startDate'] ?>" name="start_date" type="text" class="blue-hl has-datepick foo-datepick" readonly placeholder="Arriving">
                        <i class="icon icon-calendar"></i>
                    </div>
                    <div class="input-icon">
                        <input value="<?= $content['endDate'] ?>" name="end_date" type="text" class="blue-hl has-datepick foo-datepick" readonly placeholder="Leaving">
                        <i class="icon icon-calendar"></i>
                    </div>
                    <div class="buttons" style="min-width:255px">
                        <button class="btn input-aligned secondary lcased grey" type="submit">Search Dates</button>
                        <button class="btn reset-calendar input-aligned secondary lcased grey" title="Reset Dates" type="submit">Reset</button>
                    </div>
                </form>
            </div>

            <div class="tabs city-tour-tabs">
<!--                <a href="#promotions" class="tab-item city-tour-tab">Promotions</a>-->
<!--                <a href="#partnerTours" class="tab-item city-tour-tab">Partner Tours</a>-->
                <?php foreach($content['tags'] as $tag): ?>
                    <a href="#tag-<?= strtolower(str_replace(' ','-',preg_replace("/[^A-Za-z0-9- ]/", '',$tag['name']))) ?>" class="tab-item city-tour-tab"><?= $tag['name'] ?></a>
                <?php endforeach ?>
            </div>
        </div>
    </div>

    <div class="tour-list-wrap">

        <!--
        <div class="tour-list-section" id="promotions">
            <div class="container">
                <div class="tour-type-title">
                    <i class="icon icon-promotions"></i>
                    <h2 class="section-heading">Promotions</h2>
                    <p class="descr">Donec facilisis tortor ut augue lacina, at viverra est semper</p>
                </div>

                <div class="tour-list-items vertical">
                    <a href="#" class="tour-box col discounted">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour4.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$45</span>
                                    <span class="discounted-price">$35</span>
                                </div>
                                <div class="tour-tag sale">
                                    <div class="icon-holder"><i class="icon icon-sale"></i></div>
                                    <div class="text-holder">Sale</div>
                                </div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">St. Peter's Basilica</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star"></i>
                                    <p class="count">63 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col discounted">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour5.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$49</span>
                                    <span class="discounted-price">$37</span>
                                </div>
                                <div class="tour-tag sale">
                                    <div class="icon-holder"><i class="icon icon-sale"></i></div>
                                    <div class="text-holder">Sale</div>
                                </div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">Panthenon</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star"></i>
                                    <p class="count">18 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 1.5 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 10</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col discounted">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour6.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$37</span>
                                    <span class="discounted-price">$23</span>
                                </div>
                                <div class="tour-tag sale">
                                    <div class="icon-holder"><i class="icon icon-sale"></i></div>
                                    <div class="text-holder">Sale</div>
                                </div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">The Sistine Chapel</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">27 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 3 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 8</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="section-cta">
                <a class="btn primary green">See All Promotions</a>
            </div>
        </div>
        -->
        <?php foreach($content['tags'] as $tag): ?>
            <div class="tour-list-section <?=$tag['slug'] ? '' : 'no-link' ?>" id="tag-<?= strtolower(str_replace(' ','-',preg_replace("/[^A-Za-z0-9- ]/", '',$tag['name']))) ?>">
                <div class="container">
                    <div class="tour-type-title">
                        <?php if(false): ?>

                        <?php endif ?>
                        <i class="icon icon-<?= strtolower(str_replace(' ','-',preg_replace("/[^A-Za-z0-9- ]/", '',$tag['name']))) ?>"></i>
                        <h2 class="section-heading"><?= $tag['name'] ?></h2>
                        <p class="descr desktop-only"><?=$tag['description'] ?></p>
                    </div>


                    <div class="tour-list-items vertical">
                        <?php foreach($tag['tours'] as $tour): ?>
                            <a href="<?="/{$tour['citySlug']}/{$tour['slug']}" ?>" class="tour-box col <?=$tour['discount'] ? 'discounted' : '' ?>">

                                <div class="tour-box-content">
                                    <div class="tour-img" style="background-image: url('<?= $tour['image'] ?>?w=500'">
                                        <?php if($tour['medal']): ?>
                                            <div class="tour-badge <?=strtolower($tour['medal']) ?>" tooltip-trigger>
                                                <i class="icon icon-logo_badges"></i>
                                                <div tooltip-content class="tour-box-tooltip">
                                                    <h5 class="tooltip-title"><?= $tour['medal'] ?> Badge</h5>
                                                    <p class="default"><?= $medals[strtolower($tour['medal'])]['toolTip'] ?></p>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <div class="tour-price">
                                            <?php if($tour['discount']): ?>
                                                <span class="default-price"><?=ExchangeRate::convert($tour['discount'], false) ?></span>
                                                <span class="discounted-price"><?=ExchangeRate::convert($tour['price'],false); ?></span>
                                            <?php else: ?>
                                                <span class="default-price"><?=ExchangeRate::convert($tour['price'],false); ?></span>
                                            <?php endif ?>



                                        </div>
                                        <?php if($tour['flag']): ?>
                                            <?php if($tour['flag'] == 'Likely To Sell Out'): ?>
                                                <div class="tour-tag sale">
                                                    <div class="icon-holder"><i class="icon icon-sell_out"></i></div>
                                                    <div class="text-holder"><?= $tour['flag'] ?></div>
                                                </div>
                                            <?php elseif($tour['flag'] == 'New'): ?>
                                                <div class="tour-tag new">
                                                    <div class="icon-holder"><i class="icon icon-new"></i></div>
                                                    <div class="text-holder"><?= $tour['flag'] ?></div>
                                                </div>
                                            <?php elseif($tour['flag'] == 'Exclusive'): ?>
                                                <div class="tour-tag exclusive">
                                                    <div class="icon-holder"><i class="icon icon-exclusive"></i></div>
                                                    <div class="text-holder"><?= $tour['flag'] ?></div>
                                                </div>
                                            <?php endif ?>
                                        <?php endif ?>
                                    </div>
                                    <div class="tour-details">
                                        <h3 class="tour-title"><?= $tour['name'] ?></h3>
                                        <p class="desc"><?= $tour['listingText'] ?></p>
                                        <div class="tour-box-reviews">
                                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 1 ? '_active' : '' ?>"></i>
                                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 2 ? '_active' : '' ?>"></i>
                                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 3 ? '_active' : '' ?>"></i>
                                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 4 ? '_active' : '' ?>"></i>
                                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 4.7 ? '_active' : '' ?>"></i>
                                            <p class="count"><?= $tour['reviewsCount'] ?> reviews</p>
                                        </div>
                                        <div class="tour-stats">
                                            <p><i class="icon icon-clock"></i><?= $tour['duration'] ?></p>
                                            <div class="sep"></div>
                                            <p><i class="icon icon-max"></i> Max <?= $tour['groupSize'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach ?>

                    </div>

                    <div class="section-cta">
                        <?php if($tag['slug']): ?>
                            <p class="descr">Can't decide which tour is best for you? See below for more details & to search availability by date.</p>

                            <a class="btn primary green" href="<?=$tag['slug'] ?>">Compare Tours</a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!--
        <div class="tour-list-section" id="partnerTours">
            <div class="container">
                <div class="tour-type-title">
                    <i class="icon icon-partner_tours"></i>
                    <h2 class="section-heading">Partner Tours</h2>
                    <p class="descr">Donec facilisis tortor ut augue lacina, at viverra est semper</p>
                </div>

                <div class="tour-list-items vertical">
                    <a href="#" class="tour-box col">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour1.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$35</span>
                                </div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">St. Peter's Basilica</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star"></i>
                                    <p class="count">63 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour2.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$39</span>
                                </div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">Panthenon</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star"></i>
                                    <p class="count">18 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 1.5 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 10</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour3.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$12</span>
                                </div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">The Sistine Chapel</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">27 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 3 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 8</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        -->
        <div class="tour-list-section">
            <div class="container">
                <div class="badges-legend">
                    <div class="badge-legend col">
                        <div class="tour-badge gold"><i class="icon icon-logo_badges"></i></div>
                        <div>
                            <h3>Gold</h3>
                            <p class="descr">
                                <?= $content['medals']['gold']['cityListingTourStyleDescription']; ?>
                            </p>
                        </div>
                    </div>
                    <div class="badge-legend col">
                        <div class="tour-badge silver"><i class="icon icon-logo_badges"></i></div>
                        <div>
                            <h3>Silver</h3>
                            <p class="descr"><?= $content['medals']['silver']['cityListingTourStyleDescription']; ?></p>
                        </div>
                    </div>
                    <div class="badge-legend col">
                        <div class="tour-badge bronze"><i class="icon icon-logo_badges"></i></div>
                        <div>
                            <h3>Bronze</h3>
                            <p class="descr"><?= $content['medals']['bronze']['cityListingTourStyleDescription']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
