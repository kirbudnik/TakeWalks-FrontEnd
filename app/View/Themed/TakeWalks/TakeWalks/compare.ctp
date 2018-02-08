<?= $this->element('header'); ?>
<?php $this->start('scripts'); ?>
<?= $this->Html->script('/js/pages/compare.js'); ?>

<?php $this->end(); ?>

<?php
$this->start('bottomHead');

//-----BreadcrumbList rich snippet-------
$breadcrumbListRS = $this->RichSnippets->create('BreadcrumbList');
$itemListElement = [];

$listItemRS = $this->RichSnippets->create('ListItem');
$listItemRS->setVal('position',1);
$itemRS = $listItemRS->addChild('item','Item');
$itemRS->setVal('@id', FULL_BASE_URL . DS . $citySlug);
$itemRS->setVal('name',$cityName.' Tours');
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

$this->end();
?>

<div id="compare" data-slug="<?=$slug ?>">
    <div class="page-hero tag-comparison" style="background-image: url(<?=$content['heroImage'] ?>)">

    </div>

    <div class="tabs centered hide-mobile city-nav-tabs">
        <a href="#overview" data-scroll-toggler="overview" class="tab-item city-tour-tab active">Overview</a>
        <a href="#upcomingTours" data-scroll-toggler="upcomingTours" class="tab-item city-tour-tab">Upcoming Tours</a>
        <a href="#browseTours" data-scroll-toggler="browseTours" class="tab-item city-tour-tab">Browse Tours</a>
        <a href="#highlights" data-scroll-toggler="highlights" class="tab-item city-tour-tab">Highlights</a>
        <a href="#visitingNotes" data-scroll-toggler="visitingNotes" class="tab-item city-tour-tab">Notes for Visiting</a>
        <a href="#faq" data-scroll-toggler="faq" class="tab-item city-tour-tab">FAQ</a>
    </div>

    <section class="city-description-box bordered" id="overview" data-scroll-target="overview">
        <h1 class="section-heading"><?= $content['title'] ?></h1>
        <div class="hide-mobile description-container descr">
                <?=$content['description'] ?>
        </div>
    </section>

<section class="grey bordered upcoming-tours" id="upcomingTours" data-scroll-target="upcomingTours">
    <div class="container">
        <div class="section-title title-inline title-with-datepick">
            <i class="icon icon-upcoming-tours"></i>
            <h2 class="heading section-heading default">Upcoming Tours</h2>
            <div class="datepick-with-arrows">
                <span class="change-date prev-date">
                    <em>Prev</em>
                    <i class="icon icon-arrow_left left-arrow"></i>
                </span>

                <div class="input-icon">
                    <input class="blue-hl has-datepick foo-datepick" placeholder="Select a Date" readonly>
                    <i class="icon icon-calendar"></i>
                </div>

                <span class=" change-date next-date">
                    <em>Next</em>
                    <i class="icon icon-arrow_left"></i>
                </span>
            </div>
        </div>

            <div class="upcoming-tours-headings" style="display: none">
              <p>Depart</p>
              <p class="tour-name">Tour</p>
              <p>Price</p>
              <p>Duration</p>
            </div>

            <div class="upcoming-tours-items">

            </div>
            <p class="upcoming-tour-not-found compare-row-item center" style="display: none">
                There are no available tours for this date.
            </p>
            <div class="upcoming-tour-loading"  style="display: none">
                <img src="/theme/TakeWalks/svg/loading.svg">
            </div>
        </div>
    </section>

    <div class="tour-list-wrap standalone">
    <section class="bordered" id="browseTours" data-scroll-target="browseTours">
            <div class="container list-multiple-rows">
                <div class="tour-type-title">
                    <i class="icon icon-browse-tours"></i>
                    <h2 class="section-heading">Browse Tours</h2>
                </div>

                <div class="tour-list-items vertical">
                    <?php $tourCounter = 1; ?>
                    <?php foreach($tours as $tour): ?>
                        <a href="<?="/{$tour['citySlug']}/{$tour['slug']}" ?>" class="tour-box col <?=$tour['discount'] ? 'discounted' : '' ?>" data-tour-id="<?=$tour['id'] ?>">
                            <div class="tour-box-content">
                                <div class="tour-img" style="background-image: url(<?=$tour['image'] ?>?w=350)">
                                    <div class="tour-badge <?=strtolower($tour['medal']) ?>" <?= $tour['medal'] ? 'tooltip-trigger' : ''; ?>>
                                        <?php if($tour['medal']): ?>
                                            <i class="icon icon-logo_badges"></i>
                                        <?php endif ?>
                                        <div tooltip-content class="tour-box-tooltip">
                                            <h5 class="tooltip-title"><?=$tour['medal'] ?> Badge</h5>
                                            <p class="default"><?=$medals[strtolower($tour['medal'])]['toolTip'] ?></p>
                                        </div>
                                    </div>
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
                                    <h3 class="tour-title" data-title-short="<?= $tour['titleShort'] ?>" ><?= $tour['title'] ?></h3>
                                    <p class="desc"><?= $tour['description'] ?></p>
                                    <div class="tour-box-reviews">
                                        <i class="icon icon-star<?= $tour['reviewsAverage'] >= 1 ? '_active' : '' ?>"></i>
                                        <i class="icon icon-star<?= $tour['reviewsAverage'] >= 2 ? '_active' : '' ?>"></i>
                                        <i class="icon icon-star<?= $tour['reviewsAverage'] >= 3 ? '_active' : '' ?>"></i>
                                        <i class="icon icon-star<?= $tour['reviewsAverage'] >= 4 ? '_active' : '' ?>"></i>
                                        <i class="icon icon-star<?= $tour['reviewsAverage'] >= 4.7 ? '_active' : '' ?>"></i>
                                        <p class="count"><?= $tour['reviewsCount'] ?> reviews</p>
                                    </div>
                                    <div class="tour-stats">
                                        <p><i class="icon icon-clock"></i> <?= $tour['duration'] ?></p>
                                        <div class="sep"></div>
                                        <p><i class="icon icon-max"></i> Max <?= $tour['groupSize'] ?></p>
                                    </div>
                                </div>
                                <div class="tour-compare">
                                    <div class="css-checkbox compare-cb">
                                        <input type="checkbox" id="cb<?= $tourCounter ?>">
                                        <label for="cb<?= $tourCounter ?>">Compare this Tour</label>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?php $tourCounter++ ?>
                    <?php endforeach; ?>
                   <!--
                    <a href="#" class="tour-box col">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour1.jpg">
                                <div class="tour-badge gold" tooltip-trigger>
                                    <i class="icon icon-logo_badges"></i>
                                    <div tooltip-content class="tour-box-tooltip">
                                        <h5 class="tooltip-title">Gold Badge Tooltip</h5>
                                        <p class="default">Vestibulum rutrum quam vitae fringilla tincidunt. Suspendisse nec tortor.</p>
                                    </div>
                                </div>
                                <div class="tour-price"><span class="default-price">$35</span></div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">St. Peter's Basilica</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">25 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                            <div class="tour-compare">
                                <div class="css-checkbox compare-cb">
                                    <input type="checkbox" id="cb1">
                                    <label for="cb1">Compare this Tour</label>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col discounted">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour5.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$35</span>
                                    <span class="discounted-price">$22</span>
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
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">25 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                            <div class="tour-compare">
                                <div class="css-checkbox compare-cb">
                                    <input type="checkbox" id="cb2">
                                    <label for="cb2">Compare this Tour</label>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour2.jpg">
                                <div class="tour-price"><span class="default-price">$35</span></div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">St. Peter's Basilica</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">25 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                            <div class="tour-compare">
                                <div class="css-checkbox compare-cb">
                                    <input type="checkbox" id="cb3">
                                    <label for="cb3">Compare this Tour</label>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                <div class="tour-list-items vertical">
                    <a href="#" class="tour-box col">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour8.jpg">
                                <div class="tour-badge gold" tooltip-trigger>
                                    <i class="icon icon-logo_badges"></i>
                                    <div tooltip-content class="tour-box-tooltip">
                                        <h5 class="tooltip-title">Gold Badge Tooltip</h5>
                                        <p class="default">Vestibulum rutrum quam vitae fringilla tincidunt. Suspendisse nec tortor.</p>
                                    </div>
                                </div>
                                <div class="tour-price"><span class="default-price">$35</span></div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">St. Peter's Basilica</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">25 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                            <div class="tour-compare">
                                <div class="css-checkbox compare-cb">
                                    <input type="checkbox" id="cb4">
                                    <label for="cb4">Compare this Tour</label>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col discounted">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour4.jpg">
                                <div class="tour-price">
                                    <span class="default-price">$35</span>
                                    <div class="discounted-price">$22</div>
                                </div>
                            </div>
                            <div class="tour-details">
                                <h3 class="tour-title">St. Peter's Basilica</h3>
                                <div class="tour-box-reviews">
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">25 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                            <div class="tour-compare">
                                <div class="css-checkbox compare-cb">
                                    <input type="checkbox" id="cb5">
                                    <label for="cb5">Compare this Tour</label>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="tour-box col">
                        <div class="tour-box-content">
                            <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour7.jpg">
                                <div class="tour-price"><span class="default-price">$35</span></div>
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
                                    <i class="icon icon-star_active"></i>
                                    <p class="count">25 reviews</p>
                                </div>
                                <div class="tour-stats">
                                    <p><i class="icon icon-clock"></i> 2 hours</p>
                                    <div class="sep"></div>
                                    <p><i class="icon icon-max"></i> Max 12</p>
                                </div>
                            </div>
                            <div class="tour-compare">
                                <div class="css-checkbox compare-cb">
                                    <input type="checkbox" id="cb6">
                                    <label for="cb6">Compare this Tour</label>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                -->
            </div>
        </div>
    </section>
    <section class="grey bordered highlight-tabs" data-scroll-target="highlights" id="highlights">
        <div class="left">
            <div class="title-highlights">
                <div class="icon-holder">
                    <i class="icon icon-highlights"></i>
                </div>
                <h2 class="heading default small">Highlights</h2>
            </div>
            <ul class="vertical-tabs">
                <?php $highlightCounter = 1; ?>
                <?php foreach($content['highlights'] as $highlight): ?>
                    <li class="vertical-tab"><div class="tab-circle"><?= $highlightCounter++ ?></div><?= $highlight['title'] ?></li>
                <?php endforeach ?>
            </ul>
        </div>

        <style>
            .title-highlights{
                display: flex;
                padding-left: 7%;
            }
            .title-highlights .icon-holder{
                padding-top: 10px;
            }
            .float-left {
                float:  left;
                width: 520px;
            }
            .img-highlight {
                text-align: center;
                text-transform: uppercase;
                padding: .2em;
                margin-right: 30px;
            }

            .img-highlight > .img {
                width: 100%;
                max-width: 520px;
                height: 260px;
                background-size: cover;
                background-position: 50%;
                background-repeat: no-repeat;
            }
            .description-highlight p {
                font-size: 19.2px;
                line-height: 1.3;
                color: #443d47;
            }

            @media screen and (max-width: 1440px) {
                .float-left {
                    float: inherit;
                    width: 100%;
                }
            }

        </style>



        <?php foreach($content['highlights'] as $highlight): ?>
            <div class="right" style="display:none">
                <div class="description-highlight">
                    <div class="img-highlight float-left">
                        <div class="img" style="background-image: url(<?=$highlight['image'] ?>);"></div>
                    </div>
                    <h2 class="header blue separator"><?= str_replace("<p>", '<p class="descr single">', $highlight['highlightPullQuote'] ) ?></h2>
                    <?= str_replace("<p>", '<p class="descr single">', $highlight['description'] ); ?>
                </div>

                    <!--
                <div class="description">
                    <div class="img" style="background-image: url(<?=$highlight['image'] ?>);"></div>
                    <?= $highlight['description'] ?>
                    <div>
                        <h4 class="small-heading">Small Header Goes Here</h4>
                        <p class="descr">Aenean ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Sum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felnec.</p>
                        <h4 class="small-heading">Another Small Heading</h4>
                        <p class="descr">Ut enim ad minima veniam, quis nostrum exercitationem ullam. Nemo ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit Neque porro quisquam est.</p>
                    </div>
                </div>
                    -->
                <!--
                <p class="descr"></p>

                <ul class="feature-list three-cols">
                    <li><i class="icon icon-checkmark_circle"></i>Sistine Chapel</li>
                    <li><i class="icon icon-checkmark_circle"></i>Raphael Rooms</li>
                    <li><i class="icon icon-checkmark_circle"></i>Belvedere Courtyard</li>
                    <li><i class="icon icon-checkmark_circle"></i>Pinecone Courtyard</li>
                    <li><i class="icon icon-checkmark_circle"></i>Gallery of Candelabra</li>
                </ul>
                -->
            </div>
        <?php endforeach ?>



    </section>

<section class="city-description-box notes" id="visitingNotes" data-scroll-target="visitingNotes" style="padding-left: 0">
    <div class="section-sep-heading" style="background-color: #ffffff; border: 1px solid #ffffff;" >
        <div class="container">
            <i class="icon icon-notes-for-visiting"></i>
            <h2 class="heading">Notes for Visiting</h2>
        </div>
    </div>
    <div class="container" style="text-align: justify">
        <!--        <p class="descr"><i>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</i></p>-->
        <?php if($tagPageNotes != ''): ?>
            <p class="descr"><?= $tagPageNotes ?></p>
        <?php endif; ?>
    </div>
</section>
<div class="section-sep-heading" id="faq" data-scroll-target="faq">
    <div class="container">
        <i class="icon icon-faq-new"></i>
        <h2 class="heading">FAQ</h2>
    </div>
</div>

    <section class="last-section" id="faq">
        <div class="faq-section section bordered">
            <?php foreach($content['faq'] as $question): ?>
                <div class="faq-question">
                    <div class="faq-question-title">
                        <div class="container">
                            <?= $question['question'] ?> <i class="icon icon-collapse"></i>
                        </div>
                    </div>

                    <div class="faq-question-content">
                        <div class="container">
                            <?=$question['answer'] ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="comparison-bar">
        <div class="container">
            <h5 class="subtitle white">Select Up<br>To 3 Tours</h5>
            <button class="btn btn-compare-tours secondary green lcased">Compare Tours</button>
        </div>
    </div>
</div>
</div>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
<?= $this->element('templates/compare-upcoming-tour'); ?>
