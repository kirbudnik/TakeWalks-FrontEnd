<?php $this->start('afterBody'); ?>

<!-- Formstone core -->
<?= $this->Html->script('lib/formstone-core.min.js'); ?>
<?= $this->Html->script('lib/formstone-scrollbar.min.js'); ?>
<?= $this->Html->script('lib/formstone-touch.min.js'); ?>
<?= $this->Html->script('lib/formstone-dropdown.min.js'); ?>
<?= $this->Html->script('lib/slick.min.js'); ?>
<?= $this->Html->script('lib/royalslider.min.js'); ?>
<?= $this->Html->script('lib/jquery-ui-1.9.2.min.js'); ?>

<!-- todo remove -->
<?= $this->Html->script('//maps.googleapis.com/maps/api/js?key=AIzaSyCto_hLxee_YT0pyFzw6fOjFKlWWkRLVr0'); ?>

<!-- custom ui -->
<?= $this->Html->script('ui-scripts.js'); ?>

<?= $this->Html->script('event-detail.js'); ?>

<script>window.twttr = (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function (f) {
            t._e.push(f);
        };

        return t;
    }(document, "script", "twitter-wjs"));
</script>

<?php

//-----WebSite rich snippet-------
$WebSiteRs = $this->RichSnippets->create('WebSite');
$WebSiteRs->setVal('name','Walks of New York');
$WebSiteRs->setVal('url','https://www.walksofnewyork.com/');
$searchActionRS = $WebSiteRs->addChild('potentialAction','SearchAction');
$searchActionRS->setVal('target',"https://www.walksofnewyork.com/blog/?s={search_term_string}");
$searchActionRS->setVal('query-input',"required name=search_term_string");
echo $WebSiteRs->getJSON();


//--------local business rich snippet----------
$localBusinessRS = $this->RichSnippets->create('LocalBusiness');
$localBusinessRS->setVal('additionalType','http://www.productontology.org/id/Tour_operator');
$localBusinessRS->setVal('name','Walks of New York');
$localBusinessRS->setVal('url','https://www.walksofnewyork.com/');
$localBusinessRS->setVal('logo','https://www.walksofnewyork.com/img/logos/wony-logo-blue.png');
$localBusinessRS->setVal('sameAs', array(
    'https://www.facebook.com/walksofnewyork',
    'http://instagram.com/walksofnewyork',
    'https://plus.google.com/+Walksofnewyork/posts',
    'https://twitter.com/WalksofNewYork',
    'https://www.youtube.com/user/walksofnewyork'
));
$localBusinessRS->setVal('currenciesAccepted','USD');
$founderRS = $localBusinessRS->addChild('founder','Person');
$founderRS->setVal('name','Jason Spiehler');
$founderRS->setVal('sameAs', array("https://plus.google.com/116930771859616754446", "https://www.linkedin.com/pub/jason-spiehler/35/a8b/937"));
$addressRs = $localBusinessRS->addChild('Address','PostalAddress');
$addressRs->setVal('streetAddress','1030 Avenue of the Americas');
$addressRs->setVal('addressLocality','New York');
$addressRs->setVal('addressRegion','NY');
$addressRs->setVal('postalCode','10018');
$offerRs = $localBusinessRS->addChild('makesOffer', 'Offer');
$paymentMethodRs = $offerRs->addChild('acceptedPaymentMethod','PaymentMethod');
$paymentMethodRs->setVal('name',array("http://purl.org/goodrelations/v1#AmericanExpress", "http://purl.org/goodrelations/v1#MasterCard", "http://purl.org/goodrelations/v1#VISA", "http://purl.org/goodrelations/v1#Discover"));

?>

<!-- rich snippets --->
<script type="application/ld+json">
<?php
$productRichSnippet = [
    "@context" => "http://schema.org/",
    "@type" => "Product",
    "name" => $event['name_short'],
    "image" => $images[0]['images_name'],
    "description" => $event['description_short'],
    "mpn" => $event['id'],
    "brand" => [
        "@type" => "Thing",
        "name" => "Walks of New York"
    ],
    "offers" => [
        "@type" => "Offer",
        "priceCurrency" => "USD",
        "price" => $event['group_private'] != 'Private' ? $event['adults_price'] : $event['private_base_price'],
        "priceValidUntil" => date('Y-m-d',time() + (60 * 60 * 24 * 30)),
        "availability" => "http =>//schema.org/InStock",
        "seller" => [
            "@type" => "Organization",
            "name" => "Walks of New York"
        ]
    ]
];

    if(isset($ratings[$event['id']])){
        $productRichSnippet["aggregateRating"] = [
            "@type" => "AggregateRating",
            "ratingValue" => $ratings[$event['id']]['average'],
            "reviewCount" => $ratings[$event['id']]['amount']
        ];
        $productRichSnippet["review"] = array();
        foreach($reviews as $review){

            $productRichSnippet["review"][] = [
                "@context"=> "http://schema.org/",
                'reviewBody' => $review['feedback_text'],
                'datePublished' => $review['feedback_date'],
                "@type"=> "Review",
                "author"=> [
                    "@type"=> "Person",
                    "name"=> $review['first_name'],
                ],
                "reviewRating"=> [
                    "@type"=> "Rating",
                    "ratingValue"=> $review['event_rating'],
                    "bestRating"=> 5
                ]
            ];

        }
    }


    ?>
<?= str_replace( '\/' , '/', json_encode($productRichSnippet)); ?>
</script>

<?php $this->end(); ?>
<div class="tour-info">
    <div class="grid-container">
        <div class="breadcrumbs">
            <a href="#">Home</a>
            <a href="/<?= $domainsGroup['url_name'] ?>-tours"><?= $domainsGroup['name'] ?> Tours</a>
            <a href="#"><?= $event['name_short'] ?></a>
        </div>

        <div class="text">
            <div class="text-wrap">
                <h3 class="h3 tour-header heavy"><?= $event['name_long'] ?></h3>

                <p class="typo tour-description"><!-- <b>EXCLUSIVE TOUR:</b> --> <?= $event['description_short'] ?></p>
            </div>

            <div class="price-box-wrap">
                <p>FROM</p>

                <?php $adultPrice = $event['group_private'] != 'Private' ? $event['adults_price'] : $event['private_base_price'] ?>
                <h1 class="price-number"><?= ExchangeRate::convert($adultPrice) ?></h1>

                <?php if(isset($ratings[$event['id']])): ?>
                    <div class="reviews-count price-box">

                        <?php for ($i = 0; $i < ceil($ratings[$event['id']]['average']); $i++): ?>
                            <i class="fa fa-star"></i>
                        <?php endfor; ?>

                        <div class="num underline" onclick="javascript: $('.show-hidden-reviews-btn').click();">FROM <?= $ratings[$event['id']]['amount'] ?>
                            REVIEW<?= $ratings[$event['id']]['amount'] != 1 ? 'S' : '' ?></div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <?php
        //find the hero image (listing = 1)
        $hero = null;
        foreach($images as $image){
            if($image['listing']){
                $hero = $image;
                break;
            }
        }
        if(!$hero) $hero = $images[0];
        ?>
        <div class="image event-detail-hero" style="background-image: url('https://app.resrc.it/O=40(60)/<?= str_replace('https:', 's=W1300/https:', $hero['images_name']) ?>')">
            <!-- video if exists -->
            <div class="player">
                <div class="pl-wrap">
                    <iframe id="banner-video" src="" data-video-link="<?php echo $event['video'] ?>?autoplay=1" style="width: 100%; height: 100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </div>
            </div>



            <?php if($event['video']): ?>
                <div class="play-button">
                    <div class="watch">Watch</div>
                    <i id="play-expand" class="fa fa-play-circle playpause start"></i>
                    <div class="video">Video</div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="tour-detail-content">
    <div class="grid-container">
        <main>
            <section class="spacious tour-detail">
                <h5 class="h5 heavy header mb-default">Key Details</h5>

                <ul class="default-list icon-list detail-list blue">
                    <li><i class="fa fa-map-marker"></i>City: <?= ucwords($domainsGroup['name']); ?></li>
                    <li><i class="fa fa-clock-o"></i>Duration: <?= $event['display_duration'] ?></li>
                    <li><i class="fa fa-chevron-right"></i>Start Time: <?= $event['display_time'] ?></li>
                </ul>
            </section>

            <section class="spacious tour-detail">
                <h5 class="h5 heavy header mb-default">Tour Highlights</h5>

                <ul class="default-list icon-list blue-icons li-separated stars">
                    <li><?= $event['bullet1'] ?> </li>
                    <li><?= $event['bullet2'] ?> </li>
                    <li><?= $event['bullet3'] ?> </li>
                </ul>
            </section>

            <section class="spacious tour-detail no-top-padding">
                <div class="royalSlider tour-detail-slider rsMinW"
                     style="overflow:hidden;height:300px;margin-bottom:20px">
                    <?php foreach ($images as $image): ?>
                        <?php if(!$image['primary'] && !$image['listing'] && !$image['feature']): ?>
                            <img class="resrc"
                                 src="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W600/c=AR600x400/https:', $image['images_name']) ?>"
                                 data-rsTmb="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/c=AR200x150/https:', $image['images_name']) ?>"
                                 alt="<?php echo $image['alt_tag']; ?>"/>
                        <?php endif ?>
                    <?php endforeach ?>
                </div>

                <div class="clear"></div>
            </section>

            <section class="spacious tour-detail extended-description">
                <!--
                <p class="highlighted blue">WALK THE BEAT WITH NYPD</p>

                <div class="paragraph">
                    <p>Spend an evening on the beat with a retired NYPD officer or detective, learning the shocking truth of New York’s organized crime rings, white collar millionaires and modern scandals. Together you’ll visit the Lower East Side, Wall Street, and Little Italy; breeding grounds for all types of outrageous NYC criminals, from Captain Kidd and Boss Tweed to John Gotti, Leona Helmsley, and the real Wolves of Wall Street.</p>
                </div>

                <p class="highlighted blue">
                    FROM THE GUTTER TO THE BOARDROOM
                </p>

                <div class="paragraph">
                    <p>Fighting crime in NYC is a classless war; with corruption stretching from street-level to the top floors of Wall Street. Your tour blows the lid off every level, from mafioso thuggery to white collar extortion.</p>

                    <p>Starting in Lower Manhattan, you’ll learn of political intrigue and corruption in New York City stretching back hundreds of years. Move from power to money with tales of Wall Street run amok, whether trying to overthrow the US Government or literally getting away with murder.</p>

                    <p>Salaries drop and life becomes a little more threadbare as you move towards the Lower East Side. Here you’ll see the infamous tenement buildings where immigrant families lived in cramped conditions. Five Points was a particularly notorious spot, so infamous in the 1800s that Charles Dickens came to have look for himself, not believing it could be as bad as all that (it was, as your guide will tell you). Learn how true to life the movie Gangs of New York really was and whether Irish gangs really lived in burrows under the streets here.</p>

                    <p>Of course it wasn’t only Irish gangs that terrorized the streets of NYC in the last few centuries. Stroll through Chinatown to learn about violent turf wars fought here during the 1970s and 1980s. Little Italy has had its fair share of crime too, with infamous mafia families operating out of its coffee shops and restaurants all through the 20th century. But was it really as dramatic as The Sopranos would have you believe? Your NYPD guide has the inside scoop.</p>
                </div>

                <p class="highlighted blue">A STORYTELLING TOUR STRAIGHT FROM THE HORSE’S MOUTH</p>

                <div class="paragraph">
                    <p>Take to the streets and alleys with the men and women who know them best: New Yorkâ€™s Finest, NYPD. For two and a half hours you’ll walk the Big Apple in their shoes. Together you’ll explore neighborhoods once home to notorious criminals, learning how the city was ruled by in waves by various immigrant crime rings, corrupt politicians, and billionaires gone wild.</p>

                    <p>Your NYPD guide will separate truth from fiction, adding a little color via their personal tales, and tell you what it took to finally clean up New York City after centuries of degradation.</p>
                </div>
                -->
                <?php foreach (explode("\n", $event['description_long']) as $n => $line): ?>
                    <p>
                        <?= $line ?>
                    </p>
                <?php endforeach; ?>
            </section>

            <div class="book-tour-mobile">
                
            </div>

<!--
            <section class="spacious tour-detail">
                <h5 class="h5 heavy header mb-default">Route</h5>

                <div class="gmap-wrapper">
                    <div id="gmap"></div>
                </div>

                <div class="tour-route">
                    <div class="grid-item">
                        <h6 class="h6 header blue list-header">SITES VISITED</h6>
                        <ul class="default-list icon-list blue-icons li-gap-small pins">
                            <?php foreach (explode("\n", trim($event['sites_included'])) as $i => $bullet): ?>
                                <li><?= preg_replace('/^-\s*/', '', $bullet) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="grid-item">
                        <h6 class="h6 header blue list-header">INCLUDED:</h6>
                        <ul class="default-list icon-list blue-icons li-gap-small checkmarks">
                            <?php foreach (explode("\n", trim($event['price_includes'])) as $bullet): ?>
                                <li> <?= preg_replace('/^-\s*/', '', $bullet) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </section>
            -->

            <?php if(isset($ratings[$event['id']])): ?>
                <section class="tour-detail" data-scroll-target="reviews">
                    <div class="tour-reviews">
                        <h5 class="h5 heavy">Reviews</h5>
                        <p class="separated">5-stars from <?= $ratings[$event['id']]['amount'] ?> Customer
                            Review<?= $ratings[$event['id']]['amount'] != 1 ? 's' : '' ?></p>
                        <div class="stars blue">
                            <?php for ($i = 0; $i < ceil($ratings[$event['id']]['average']); $i++): ?>
                                <i class="fa fa-star"></i>
                            <?php endfor; ?>
                        </div>

                        <div class="testimonial-slider-wrap">

                            <?php $i = 0; ?>
                            <?php foreach ($reviews as $review): ?>
                                <?php if(empty($review['feedback_text']) || strlen($review['feedback_text']) > 300 || $review['event_rating'] != 5) continue; ?>
                                <?php if($i++ == 10) break; ?>
                                <div class="testimonial">
                                    <div class="body"><?= $review['feedback_text'] ?></div>

                                    <div class="author">
                                        <?= $review['first_name'] ?><br>
                                        <span><?= $review['feedback_date'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </section>


                <div class="center-btn separated">
                    <a href="#" class="btn primary show-hidden-reviews-btn">See all reviews</a>
                </div>
            <?php else: ?>
                <div class="center-btn separated"></div>
            <?php endif; ?>
        </main>

        <aside class="tour-book">
            <h1 class="h1 section-title">Book a Tour</h1>
            <form class="book" action="/add_to_cart" method="post" onsubmit="return ecAddToCart();">
                <input type="hidden" id="ec_quantity" value="" />
                <input type="hidden" id="ec_price" value="" />
                <input type="hidden" name="events_id" value="<?php echo $event['id'] ?>" />
                <input type="hidden" name="type" value="<?php echo $event['group_private'] == 'Private' ? 'private' : 'group' ?>"/>
                <input type="hidden" name="date" value="">
                <div class="aside-content">
                    <div class="book-item-block nav-tabs">
                        <?php if($event['group_private'] == 'Group'): ?>
                            <div class="nav-tab active">Group</div>
                        <?php elseif($event['group_private'] == 'Private'): ?>
                            <div class="nav-tab active" id="private_tab">Private</div>
                        <?php else: ?>
                            <div class="nav-tab active">Group</div>
                            <div class="nav-tab" id="private_tab">Private</div>
                        <?php endif; ?>
                            <b style="display: none;width: 0px; height: 0px"><?php echo $event['group_private'];?></b>
                    </div>
                    <div class="book-item-block">
                        <div class="item-header">1. CHOOSE DATE</div>
                        <div class="item-content">
                            <div class="datepick-input calendar"></div>
                        </div>
                    </div>
                    <div class="book-item-block time">
                        <div class="item-header">2. CHOOSE TIME</div>

                        <div class="item-content select-full-width">
                            <select name="time" id="" class="condensed">
                            </select>
                        </div>
                    </div>
                    <div class="book-item-block people">
                        <div class="item-header">3. SELECT GUESTS</div>

                        <div class="private-details"> <span class="fa fa-info-circle"></span> <span class="base-price"></span> base price includes 2 tickets </div>

                        <div class="item-content">
                            <span class="item-content-person">Adults</span>
                            <span class="item-content-price adults"></span>
                            <div class="item-content-select">
                                <select name="adults" id="" class="condensed">
                                    <option value="0" selected>0</option>
                                    <?php foreach(range(1,12) as $guestAmount): ?>
                                    <option value="<?=$guestAmount ?>"><?=$guestAmount ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="item-content">
                            <span class="item-content-person">Children <span class="age">(4 - 12)</span></span>
                            <span class="item-content-price children"></span>
                            <div class="item-content-select">
                                <select name="children" id="" class="condensed">
                                    <option value="0" selected>0</option>
                                    <?php foreach(range(1,12) as $guestAmount): ?>
                                        <option value="<?=$guestAmount ?>"><?=$guestAmount ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="item-content">
                            <span class="item-content-person">Infants <span class="age">(Under 4)</span></span>
                            <span class="item-content-price infants free">FREE</span>
                            <div class="item-content-select">
                                <select name="infants" id="" class="condensed">
                                    <option value="0" selected>0</option>
                                    <?php foreach(range(1,12) as $guestAmount): ?>
                                        <option value="<?=$guestAmount ?>"><?=$guestAmount ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="item-content book">
                            <p class="summary">2 guests for <span class="item-content-price free total">$118.00</span></p>
                            <button class="btn secondary full-width filled"><i class="fa fa-shopping-cart"></i> Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </aside>
    </div>
</div>


<div class="hidden-reviews">
    <div class="close-hidden-reviews"></div>
    <div class="hidden-reviews-container">
        <div class="hidden-review-column">
            <?php $i = 0;
            $j = 0;
            foreach ($reviews as $review) : ?>
                <?php
                $stars = $review['event_rating'];
                $i++;
                $j++;
                ?>
                <div class="hidden-review">
                    <div class="hidden-review-body">
                        <?php echo ($review['feedback_text']) ? : '<i>(No comment)</i>'; ?>
                    </div>
                    <div class="hidden-review-footer">
                        <p><?php echo $review['first_name'] ?></p>
                        <div class="stars">
                            <?php foreach(range(1,$stars) as $star): ?>
                                <i class="fa fa-star"></i>
                            <?php endforeach ?>
                        </div>
                        <p class="review-date"><?php $fdate = explode(" ", $review['feedback_date']); echo $fdate[0]; ?></p>
                    </div>
                </div>
                <?php
                if ($j == 1) {
                    echo '</div><!-- column --><div class="hidden-review-column">';
                    $j = 0;
                }
                if ($i == 48)
                    break;
                ?>

            <?php endforeach; ?>
        </div><!-- column -->
    </div>
    <a href="/eventrewiewshtml?e=<?php echo $event['id']; ?>&p=2"></a>
</div>