<?php $this->start('scripts'); ?>
<?= $this->Html->script('/js/pages/event-detail.js'); ?>

<?php $this->end(); ?>

<?php $this->start('bottomHead'); ?>


<!--  prod app id  -->
<meta property="fb:app_id" content="1856044651312694" />
<!--  <meta property="fb:app_id" content="1989124628036695" />-->

<meta property="og:site_name" content="TakeWalks" />
<meta property="og:title" content="<?= $metaTitle ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="<?= $canonicalURL ?>" />
<meta property="og:image" content="<?= isset($content['gallery'][0]['url']) ? 'http:'.$content['gallery'][0]['url'] : '' ?>" />
<meta property="og:locale" content="en_US" />
<meta property="og:description" content="" />


<?php

//----- BreadcrumbList rich snippet -------
$breadcrumbListRS = $this->RichSnippets->create('BreadcrumbList');
$itemListElement = [];

$listItemRS = $this->RichSnippets->create('ListItem');
$listItemRS->setVal('position',1);
$itemRS = $listItemRS->addChild('item','Item');
$itemRS->setVal('@id', FULL_BASE_URL . DS . $content['citySlug']);
$itemRS->setVal('name',$content['cityName'].' Tours');
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

//----- Product/aggregateRating rich snippet -------

$productRS = $this->RichSnippets->create('Product');
$productRS->setVal('description', $metaDescription);
$productRS->setVal('name', $metaTitle);

$imageProductRS = isset($content['gallery'][0]['url']) ? $content['gallery'][0]['url'] : '';
$imageProductRS = ( $imageProductRS != '' && strpos($imageProductRS, 'http') === false ) ? 'http:'.$imageProductRS : '';
$productRS->setVal('image', $imageProductRS);

$reviewCount = count($reviews);
$ratingValue = array_reduce($reviews, function($total, $review){ return $total + $review['event_rating']; }, 0);
$ratingValue = ($reviewCount > 0) ? round($ratingValue / $reviewCount, 1) : 0;

$aggregateRatingRS = $productRS->addChild('aggregateRating','AggregateRating');
$aggregateRatingRS->setVal('ratingValue', $ratingValue);
$aggregateRatingRS->setVal('reviewCount', $reviewCount);


$offerRS = $productRS->addChild('offers','Offer');
$offerRS->setVal('availability', 'http://schema.org/InStock');
$offerRS->setVal('price', $event['adults_price']);
$offerRS->setVal('priceCurrency', 'EUR');

$reviewsRS = [];
foreach ($reviews as $cr => $review) {
    $reviewRS = $this->RichSnippets->create('Review2');
    $reviewRS->setVal('author', $review['first_name']);
    $reviewRS->setVal('datePublished',date('Y-m-d', strtotime($review['feedback_date'])));
    $reviewRS->setVal('description',$review['feedback_text']);
    $ratingRS = $reviewRS->addChild('reviewRating','Rating');
    $ratingRS->setVal('bestRating','5');
    $ratingRS->setVal('ratingValue', $review['event_rating']);
    $ratingRS->setVal('worstRating','1');
    $reviewRS = $reviewRS->getArray();
    $reviewsRS[] = $reviewRS;
    if($cr == 50) break;
}

$productRS->setVal('review',$reviewsRS);
echo $productRS->getJSON();


$this->end();
?>

<?= $this->element('header'); ?>

<div class="tour-detail-content">
    <ul class="breadcrumbs">
        <li><a href="/">Take Walks</a></li>
        <li><a href="/<?= $content['citySlug'] ?>"><?=$content['cityName'] ?> Tours</a></li>
        <li class="active"><a href="#"><?=$content['shortTitle'] ?></a></li>
    </ul>


    <div class="tour-detail-top">
        <div class="left-content">
            <div class="tour-heading">
                <h1 class="page-title"><?=$content['title'] ?></h1>
                <div class="tour-header-info">
                    <div class="tour-stats">
                        <p><i class="icon icon-clock"></i> <?= $content['duration'] ?></p>
                        <div class="sep"></div>
                        <p><i class="icon icon-max"></i> Max <?= $content['maxGroupSize'] ?></p>
                    </div>
                    <div class="tour-box-reviews">
                        <i class="icon icon-star<?= $content['reviewsAverage'] >= 1 ? '_active' : '' ?>"></i>
                        <i class="icon icon-star<?= $content['reviewsAverage'] >= 2 ? '_active' : '' ?>"></i>
                        <i class="icon icon-star<?= $content['reviewsAverage'] >= 3 ? '_active' : '' ?>"></i>
                        <i class="icon icon-star<?= $content['reviewsAverage'] >= 4 ? '_active' : '' ?>"></i>
                        <i class="icon icon-star<?= $content['reviewsAverage'] >= 4.7 ? '_active' : '' ?>"></i>
                        <p class="count" data-scroll-toggler="reviews"><?=count($reviews); ?> reviews</p>
                    </div>
                    <?php if ($user): ?>
                    <div class="tour-stats">
                        <div class="sep"></div>
                        <a href="javascript:;" class="btn secondary compact grey" id="addToWishlist">Add to Wishlist</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?= str_replace("<p>", '<p class="descr">', ContentfulWrapper::parseMarkdown( $content['intro']) ); ?>

            <div class="tour-image-slider">
                <div class="fotorama"
                     data-nav="thumbs"
                     data-navwidth="100%"
                     data-ratio="1600/950"
                     data-thumbwidth="240px"
                     data-thumbheight="150px"
                     data-width="100%"
                     data-fit="cover"
                    >
                    <?php if($content['video']): ?>
                        <div class="fotorama" data-ratio="1600/953" data-width="100%" style="background:#fff">
                            <a href="<?= $content['video'] ?>"></a>
                        </div>
                    <?php endif ?>
                    <?php foreach($content['gallery'] as $img): ?>
                        <img src="<?=$img['url'] ?>?w=800&q=80" data-caption="<?=$img['description'] ?>" alt="<?=$img['description'] ?>"/>
                    <?php endforeach ?>

                </div>
            </div>

            <div class="feature-lists">
                <ul class="feature-list">
                    <li class="list-title">Sites Visited</li>
                    <?php foreach($content['sitesVisited'] as $site): ?>
                        <li><i class="icon icon-checkmark_circle"></i><?= $site ?></li>
                    <?php endforeach ?>
                </ul>
                <ul class="feature-list">
                    <li class="list-title">Tour Includes</li>
                    <?php foreach($content['tourIncludes'] as $includes): ?>
                        <li><i class="icon icon-checkmark_circle"></i><?=$includes ?></li>
                    <?php endforeach ?>
                </ul>
            </div>

            <h2 class="header blue separator"><?= str_replace("<p>", '<p class="descr single">', ContentfulWrapper::parseMarkdown( $content['descriptionTitle']) ) ?></h2>

            <?= str_replace("<p>", '<p class="descr single">', ContentfulWrapper::parseMarkdown( $content['description']) ); ?>


        </div>

        <div class="right-book">
            <form class="book" action="/add_to_cart" method="post">
                <input type="hidden" id="ec_quantity" value="" />
                <input type="hidden" id="ec_price" value="" />
                <input type="hidden" name="events_id" value="<?php echo $event['id'] ?>" />
                <input type="hidden" name="show_discount" value="" />
                <input type="hidden" name="date" />
                <input type="hidden" name="seniors" value="0">
                <input type="hidden" name="type" value="group">
                <div class="sidebar">
                    <div class="sidebar-heading">
                        <div class="heading">Price</div>

                        <div class="price">
                            <div class="original-price"></div>
                            <span class="price-value"></span>
                        </div>
                    </div>

                    <div class="date-picker-container right-sidebar-item" data-section="datePicker">
                        <div class="sidebar-subheading">
                            <div class="sidebar-circle">1</div>
                            <p>Select a Date</p>
                        </div>

                        <div class="sidebar-content">
                            <div class="datepick"></div>
                            <?php if($content['tagSlug']): ?>
                                <a href="/<?= $content['tagSlug'] ?>" class="underlined similar-tours-link">Not available on your date? See similar tours</a>
                            <?php endif ?>
                        </div>

                        <div class="sidebar-selected-value">
                            <div class="input-icon">
                              <input type="text" class="blue-hl selected-date" readonly>
                              <i class="icon icon-calendar"></i>
                            </div>
                        </div>
                    </div>

                    <div class="right-sidebar-item time-picker-container" data-section="timePicker">
                        <div class="sidebar-subheading ">
                            <div class="sidebar-circle">2</div>
                            <p>Select a Time</p>
                        </div>

                        <div class="sidebar-content select-fullwidth sidebar-selected-value">
                            <select name="time" class="single-select time-select" data-placeholder="Select a Time"></select>
                        </div>

                    </div>
                    <div class="right-sidebar-item pax-picker-container" data-section="paxPicker">
                        <div class="sidebar-subheading">
                            <div class="sidebar-circle">3</div>
                            <p>Number of Guests</p>
                        </div>

                        <div class="sidebar-content">
                            <!--- Adults ---->
                            <div class="guest-select-row adults-container">
                                <div class="select-item">
                                    <select name="adults" id="" class="single-select">
                                        <?php for($i=0;$i<=12;$i++): ?>
                                            <option value="<?=$i ?>"><?=$i ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>

                                <div class="guest-label">
                                    <span>Adults</span>
                                </div>

                                <div class="guest-price">$95.00</div>
                            </div>

                            <!-- infants ---->
                            <div class="guest-select-row infants-container">
                                <div class="select-item">
                                    <select name="infants" id="" class="single-select">
                                        <?php for($i=0;$i<=12;$i++): ?>
                                            <option value="<?=$i ?>"><?=$i ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>

                                <div class="guest-label">
                                    <span>Infants</span>
                                    <em>(Under 2)</em>
                                </div>

                                <div class="guest-price">Free</div>
                            </div>

                            <!-- Children ---->
                            <div class="guest-select-row children-container">
                                <div class="select-item">
                                    <select name="children" id="" class="single-select">
                                        <?php for($i=0;$i<=12;$i++): ?>
                                            <option value="<?=$i ?>"><?=$i ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>

                                <div class="guest-label">
                                    <span>Children</span>
                                    <em>(Age: 2 - 14)</em>
                                </div>

                                <div class="guest-price">$75.00</div>
                            </div>

                            <!-- Students ---->
                            <div class="guest-select-row students-container">
                                <div class="select-item">
                                    <select name="students" id="" class="single-select">
                                        <?php for($i=0;$i<=12;$i++): ?>
                                            <option value="<?=$i ?>"><?=$i ?></option>
                                        <?php endfor ?>
                                    </select>
                                </div>

                                <div class="guest-label">
                                    <span>Students</span>
                                </div>

                                <div class="guest-price">$85.00</div>
                            </div>

                            <h2 class="small default total" style="display: none"><span class="guest-amount"></span> Guests for <span class="total-price"></span></h2>
                        </div>
                    </div>


                    <div class="right-sidebar-item book-now-container active">
                        <div class="sidebar-content cta">
                            <div class="error-message">
                                Sorry, there are only 3 tickets left for this tour time
                            </div>
                            <button class="btn primary btn-book-now" disabled>Book Now</button>
                        </div>
                    </div>
                </div>

                <div class="right-book-wishlist">
                    <div style="display:none">
                        <button class="btn primary condensed add-wishlist green has-icon"><i class="icon icon-star_transparent"></i>Add
                            to Wishlist
                        </button>
                    </div>
                    <div>
                        <h5 class="subtitle black">SHARE</h5>
                        <?php
                        $twitterShare = "https://twitter.com/intent/tweet?url=$canonicalURL&amp;text=".urlencode("Join me at '".$content['shortTitle']."'");
                        $facebookShare = "http://www.facebook.com/share.php?u=".urlencode($canonicalURL);

                        //$emailShare = "mailto:info@walks.com?cc=info@walks.com&bcc=info@walks.com";
                        $emailShare = "mailto:?";
                        $emailShare .= "&subject=The subject of the email";
                        $emailShare .= "&body=Lets go to '".$content['shortTitle']."' $canonicalURL";

                        ?>
                        <a href="<?= $emailShare ?>"><i class="icon icon-grey icon-email"></i></a>
                        <a href="<?= $twitterShare ?>" onclick="window.open('<?= $twitterShare ?>','popup','width=600,height=600'); return false;" target="popup"
                           title="Tweet about this tour">
                            <i class="icon icon-grey icon-twitter"></i>
                        </a>
                        <a href="<?= $facebookShare ?>" onclick="window.open('<?= $facebookShare ?>','popup','width=600,height=600'); return false;" target="popup"
                           title="Share this tour on Facebook">
                            <i class="icon icon-grey icon-facebook"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <section data-scroll-target="reviews">
        <div class="container">
            <div class="section-title center">
                <i class="icon icon-verified-reviews"></i>
                <h2 class="section-heading"><?=count($reviews); ?> Verified Reviews</h2>
            </div>
            <div class="review-boxes">
                <?php foreach(array_slice($content['featuredReviews'],0,2) as $review): ?>
                    <div class="col">
                        <div class="review-box">
                          <div class="tour-box-reviews">
                              <?php for($i=1;$i<=5;$i++): ?>
                                  <i class="icon icon-star<?= $review['numberOfStars'] >= $i ? '_active' : '' ?>"></i>
                              <?php endfor ?>
                          </div>

                          <h4 class="review-box-heading"><?=$review['reviewTitle'] ?></h4>

                          <p class="descr">
                              <?= $review['reviewContent'] ?>
                          </p>

                          <div class="review-box-footer">
                              <span class="green author"><?= $review['customerName'] ?></span>
                              <span class="typo"><?=date('M j, Y', strtotime($review['date'])); ?></span>
                          </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>


            <div class="center-btn none">
                <button class="btn primary purple" data-modal-toggler="reviews">SEE ALL REVIEWS</button>
            </div>
        </div>
    </section>

    <div class="section-sep-heading">
        <div class="container">
            <img src="/theme/TakeWalks/svg/faq.svg" class="ic">
            <h2 class="heading">FAQ</h2>
        </div>
    </div>

    <div class="faq-section section bordered">
        <?php foreach($content['faqs'] as $faq): ?>
            <div class="faq-question">
                <div class="faq-question-title">
                    <div class="container">
                        <?= $faq['question']; ?>
                        <i class="icon icon-collapse"></i>
                    </div>
                </div>
                <div class="faq-question-content">
                    <div class="container">
                        <?= $faq['answer']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <?php if($content['tourGuides']): ?>
        <section class="grey bordered">
            <div class="container">
                <div class="section-title center">
                    <i class="icon icon-the-perfect-guide-everytime"></i>
                    <h2 class="section-heading">The Perfect Guide, Every Time</h2>
                    <p class="descr">We take care to assign our guides to the perfect tour for their particular skill set;
                        so you’ll always have the perfect companion for your experience. Here are a few of the guides that
                        regularly lead this tour.</p>
                </div>

                <div class="guides-wrap">
                    <?php foreach(array_splice($content['tourGuides'],0,2) as $guide): ?>
                        <div class="col">
                            <div class="guide-box">
                                <img style="background: url('<?=$guide['image'] ?>?w=300') 95% 50% / cover no-repeat;" alt="" class="avatar">
                                <div>
                                    <a href="/guide/<?= str_replace(' ','-',strtolower($guide['name'])) ?>">
                                        <h2><?= $guide['name'] ?></h2>
                                    </a>
                                    <h5 class="subtitle green"><?= strtoupper($guide['city']) ?>, <?= strtoupper($guide['country']) ?></h5>
                                    <p><?= $guide['description'] ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </section>
    <?php endif ?>
    <?php if($content['similarTours']): ?>
        <div class="tour-list-section">
            <div class="container">
                <div class="tour-type-title">
                    <i class="icon icon-similar-tours"></i>
                    <h2 class="tour-list-title">Similar Tours</h2>
                </div>
                <div class="tour-list-items vertical">
                    <?php foreach($content['similarTours'] as $tour): ?>
                        <a href="/<?=$tour['citySlug'] ?>/<?=$tour['slug'] ?>" class="tour-box col">
                            <div class="tour-box-content">
                                <div class="tour-img" style="background-image: url(<?= $tour['image'] ?>)">
                                    <div class="tour-badge <?=strtolower($tour['medal']) ?>" tooltip-trigger>
                                        <i class="icon icon-logo_badges"></i>
                                        <div tooltip-content class="tour-box-tooltip">
                                            <h5 class="tooltip-title"><?=$tour['medal'] ?> Badge</h5>
                                            <p class="default"><?=$medals[strtolower($tour['medal'])]['toolTip'] ?></p>
                                        </div>
                                    </div>
                                    <div class="tour-price"><span class="default-price"><?=ExchangeRate::convert($tour['price'],false); ?></span></div>
                                </div>
                                <div class="tour-details">
                                    <h3 class="tour-title"><?=$tour['title'] ?></h3>
                                    <p class="descr"><?= $tour['description'] ?></p>
                                    <div class="tour-stats">
                                        <p><i class="icon icon-clock"></i><?=$tour['duration'] ?></p>
                                        <div class="sep"></div>
                                        <p><i class="icon icon-max"></i> Max <?=$tour['maxGroup'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>

                    <?php endforeach ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<div class="back-to-top event-detail-back">
  <i class="icon icon-cart_full"></i>
</div>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
<?= $this->element('/modals/reviews'); ?>
<?php if(false): ?>
    <div class="chat-bubble">
        Ask a <br> Question
    </div>
<?php endif ?>
