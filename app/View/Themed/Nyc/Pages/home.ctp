<?php
$this->start('headBottom');

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

//get all of the featured tours
$toursRS = array();
foreach($featured as $event) {
    $imageUrl = null;

    //get the featured image
    foreach($event['EventsImage'] as $image) {
        if($image['feature']) {
            $imageUrl = $image['images_name'];
            break;
        }
    }

    $productRs = $this->RichSnippets->create('Product');
    $productRs->setVal('name',$event['Event']['name_short']);
    $productRs->setVal('url',FULL_BASE_URL . "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}");
    $productRs->setVal('image',$imageUrl);
    $productOfferRs = $productRs->addChild('offers','Offer');
    $productOfferRs->setVal('price', isset($event['EventsStagePaxRemaining'][0]) ? $event['EventsStagePaxRemaining'][0]['adults_price'] : $event['Event']['adults_price']);
    $productOfferRs->setVal('priceCurrency','USD');
    $productOfferRs->setVal('availability','InStock');

    $toursRS[] = $productRs->getArray();
}

$offerRs->setVal('itemOffered',$toursRS);



echo $localBusinessRS->getJSON();
$this->end();
?>
<section class="hero">
    <video autoplay loop poster="https://app.resrc.it/O=20(40)/https://www.walksofnewyork.com/theme/nyc/video/broll.jpg">
        <source src="/theme/nyc/video/broll.webm" type="video/webm">
        <source src="/theme/nyc/video/broll.mp4" type="video/mp4">
    </video>
    <div class="heroContent">
        <h1>New York</h1>
        <p class="serif">For a more authentic experience of the Big Apple, take walks.</p>
        <a href="/new-york-tours" class="blue button">View Tours</a>
    </div>
</section>

<section class="sellingPoints">
    <ul class="wrap content">
        <li>
            <h3>Relax with friends</h3>
            <p class="serif small">Small group sizes of 12 people or fewer mean more relaxed experiences. Like time spent with friends rather than a traditional tour.</p>
        </li>
        <li>
            <h3>Go local</h3>
            <p class="serif small">Local guides, handpicked for their expertise and passion, guarantee you an insider's view of the Big Apple.</p>
        </li>
        <li>
            <h3>Get the inside scoop</h3>
            <p class="serif small">Unique itineraries bring you on an exploration of the real New York, meeting the locals and hearing their stories.</p>
        </li>
    </ul>
</section>

<section class="featured wrap content">
    <h2 class="larger">
        <span>Featured Tours</span>
        <a href="/<?php echo $theme->city_slug ?>-tours" class="medium more">View all <span><?php echo $num_results ?></span> tours</a>
    </h2>
    <ul class="tours two-thirds">
        <li style="background-image: url('https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $featured[0]['EventsImage'][0]['images_name']); ?>');">
            <!-- This is the "superfeatured" tour if there is one set -->
            <?php echo $this->element('Pages/home/featured_tile', array('featured' => $featured[0])); ?>
        </li>
        <li class="half" style="background-image: url('https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $featured[1]['EventsImage'][0]['images_name']); ?>');">
            <?php echo $this->element('Pages/home/featured_tile', array('featured' => $featured[1])); ?>
        </li>
        <li class="half" style="background-image: url('https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $featured[2]['EventsImage'][0]['images_name']); ?>');">
            <?php echo $this->element('Pages/home/featured_tile', array('featured' => $featured[2])); ?>
        </li>
        <li style="background-image: url('https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $featured[3]['EventsImage'][0]['images_name']); ?>');">
            <?php echo $this->element('Pages/home/featured_tile', array('featured' => $featured[3])); ?>
        </li>
    </ul>
    <ul class="tours one-third">
        <li style="background-image: url('https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $featured[4]['EventsImage'][0]['images_name']); ?>');">
            <?php echo $this->element('Pages/home/featured_tile', array('featured' => $featured[4])); ?>
        </li>
        <li class="talking">
            <h3 class="large">Everybody's Talking About Us</h3>
            <ul class="individuals">
                <li>
                    <blockquote class="serif small">I learnt so much about places in NYC that I see every day. So much history and culture in the stories about the buildings which have made NYC what it is today! Loved It.</blockquote>
                    <cite>Helena G, TripAdvisor</cite>
                </li>
                <li>
                    <blockquote class="serif small">From the moment we met our tour guide Jason, we knew we were in for a great afternoon. Not only was he enthusiastic about the tour, but he clearly is well versed on all things related to the Met.</blockquote>
                    <cite>WhatBoundaries, TripAdvisor</cite>
                </li>
                <li>
                    <blockquote class="serif small">I took the LES tour and learned so much about that neighborhood and the immigrants that came to New York. Plus, there were loads of yummy foodie treats along the way.</blockquote>
                    <cite>Lauren D, TripAdvisor</cite>
                </li>
                <li>
                    <blockquote class="serif small">Perhaps the biggest highlight was entering the Kehila Kedosha Janina Greek Jewish synagogue and sitting down to talk to one of its founding members.</blockquote>
                    <cite>Monica S, TripAdvisor</cite>
                </li>
                <li>
                    <blockquote class="serif small">(My Met Tour) was a treat from start to finish, and if anything inspired me to go back for more. All the stories and facts were spot on and enhanced the experience. No hesitation in recommending.</blockquote>
                    <cite>lizjh, TripAdvisor</cite>
                </li>
                <li>
                    <blockquote class="serif small">Both tours were just excellent. I have taken a lot of walking tours in my time. The quality of both these walking tours make them the best that I have taken in New York City.</blockquote>
                    <cite>fshapiro, TripAdvisor</cite>
                </li>

            </ul>
            <ul class="press">
                <li>
                    <a href="/press">
                        <blockquote class="serif small">They offer carefully organized intimate tours of interest to tourists, as well as native New Yorkers. So much we don't know! Trust me; this is time and money well spent.</blockquote>
                        <cite>Used York City</cite>
                    </a>
                </li>
                <li>
                    <a href="/press">
                        <blockquote class="serif small">In three hours you'll feel like you've earned an advanced degree in art appreciation – plus seen the very best works in the collection.</blockquote>
                        <cite>Yahoo! Travel</cite>
                    </a>
                </li>
                <li>
                    <a href="/press">
                        <blockquote class="serif small">Thanks to Jason Spiehler from "Walks of New York," I learned more in a 2 1⁄2 hour walk thru the MET then I did in the entire spring semester of my Art History class!</blockquote>
                        <cite>Used York City</cite>
                    </a>
                </li>
            </ul>
            <ul class="logos">
                <li><a href="/press"><img src="/theme/nyc/img/social/tripadvisor.png" alt="Recommended on Tripadvisor"></a></li>
                <li><a href="/press"><img src="/theme/nyc/img/social/yahoo-travel.jpg" alt="Recommended on Yahoo Travel"></a></li>
                <li><a href="/press"><img src="/theme/nyc/img/social/huffpost-travel.png" alt="Recommended on Huffpost Travel"></a></li>
                <li><a href="/press"><img src="/theme/nyc/img/social/nomadicmatt.png" alt="Recommended on Nomadic Matt"></a></li>
                <li><a href="/press"><img src="/theme/nyc/img/social/nyc-co-top-10-tours.jpg" alt="NYC & Company Top 10 Tours"></a></li>
            </ul>
        </li>
    </ul>
</section>

<?php echo $this->element('blog_posts') ?>
<?php echo $this->element('instagram') ?>