<?php
$this->start('headBottom');
//-----WebSite rich snippet-------
$WebSiteRs = $this->RichSnippets->create('WebSite');
$WebSiteRs->setVal('name','Walks of Turkey');
$WebSiteRs->setVal('url','https://www.walksofturkey.com/');
$searchActionRS = $WebSiteRs->addChild('potentialAction','SearchAction');
$searchActionRS->setVal('target',"https://www.walksofturkey.com/blog/?s={search_term_string}");
$searchActionRS->setVal('query-input',"required name=search_term_string");
echo $WebSiteRs->getJSON();


//--------local business rich snippet----------
$localBusinessRS = $this->RichSnippets->create('LocalBusiness');
$localBusinessRS->setVal('additionalType','http://www.productontology.org/id/Tour_operator');
$localBusinessRS->setVal('name','Walks of Turkey');
$localBusinessRS->setVal('url','https://www.walksofturkey.com/');
$localBusinessRS->setVal('logo','https://www.walksofturkey.com/theme/Turkey/img/turkey-logo.png');
$localBusinessRS->setVal('sameAs', array(
        'https://www.facebook.com/walksofturkey',
        'https://plus.google.com/u/0/105841538390894538446',
        'https://twitter.com/walksofturkey',
    )
);
$localBusinessRS->setVal('currenciesAccepted','LIR');
$founderRS = $localBusinessRS->addChild('founder','Person');
$founderRS->setVal('name','Jason Spiehler');
$founderRS->setVal('sameAs', array("https://plus.google.com/116930771859616754446", "https://www.linkedin.com/pub/jason-spiehler/35/a8b/937"));
$addressRs = $localBusinessRS->addChild('Address','PostalAddress');
$addressRs->setVal('streetAddress','Intas Sitesi D12');
$addressRs->setVal('addressLocality','Zühtüpaşa, Hasan Kamil Sporel Sk.');
$addressRs->setVal('addressRegion','Kadıköy/İstanbul');
$addressRs->setVal('postalCode','34724');
$offerRs = $localBusinessRS->addChild('makesOffer', 'Offer');
$paymentMethodRs = $offerRs->addChild('acceptedPaymentMethod','PaymentMethod');
$paymentMethodRs->setVal('name',array("http://purl.org/goodrelations/v1#AmericanExpress", "http://purl.org/goodrelations/v1#MasterCard", "http://purl.org/goodrelations/v1#VISA", "http://purl.org/goodrelations/v1#Discover"));

//get all of the featured tours
$toursRS = array();
foreach($events as $event) {
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
    $productOfferRs->setVal('priceCurrency','LIR');
    $productOfferRs->setVal('availability','InStock');

    $toursRS[] = $productRs->getArray();
}

$offerRs->setVal('itemOffered',$toursRS);



echo $localBusinessRS->getJSON();
$this->end();
?>
<nav id="breadcrumbs" style="background: url('theme/Turkey/img/yellowBackground.png'); background-size: cover ;">
    <div>
        <h2><?php echo $city['name'] ?> Tours</h2>
        <ol>
            <li><a href="/">Home</a></li>
            <li><?php echo $city['name'] ?> Tours</li>
        </ol>
    </div>
</nav>
<section id="content">
    <form method="post" class="form-a">
        <fieldset>
            <p class="a">
                <label class="hidden" for="fac">Sort by:</label>
                <select id="fac" name="fac" data-placeholder="Sort by:" data-minimumResultsForSearch="-1">
                    <option
                        data-url="<?php echo strtolower($city['name']) ?>-tours?sort=popular"
                        <?php if(!isset($query['sort']) || $query['sort'] == 'popular') echo 'selected' ?>>
                        Most popular
                    </option>
                    <option
                        data-url="<?php echo strtolower($city['name']) ?>-tours?sort=best"
                        <?php if(isset($query['sort']) && $query['sort'] == 'best') echo 'selected' ?>>
                        Best Rated
                    </option>
                    <option
                        data-url="<?php echo strtolower($city['name']) ?>-tours?sort=priceLow"
                        <?php if(isset($query['sort']) && $query['sort'] == 'priceLow') echo 'selected' ?>>
                        Lowest Price
                    </option>
                    <option
                        data-url="<?php echo strtolower($city['name']) ?>-tours?sort=priceHigh"
                        <?php if(isset($query['sort']) && $query['sort'] == 'priceHigh') echo 'selected' ?>>
                        Highest Price
                    </option>
                </select>
            </p>
        </fieldset>
    </form>
    <div class="columns-c no-match-height">
        <div>
            <?php if(!empty($filters['type'])): ?>
                <nav class="nav-a">
                    <h2>Tags</h2>
                    <ul>
                        <li><?php echo $city['name'] ?></li>
                        <?php foreach($tags as $tag): ?>
                            <?php if(!empty($filters['type']) && in_array($tag['Tag']['id'], $filters['type'])): ?>
                                <li data-id="<?php echo $tag['Tag']['id'] ?>">
                                    <?php echo $tag['Tag']['name'] ?> <a class="close">Remove</a>
                                </li>
                            <?php endif ?>
                        <?php endforeach ?>
                    </ul>
                </nav>
            <?php endif ?>
            <div class="news-b">
                <?php if(empty($events)) : ?>
                    <p style="font-weight: bold">Your search did not match any tours.  Please broaden your search criteria.  In the meantime, here are some featured tours:</p>
                    <?php $events = $featured; ?>
                <?php endif; ?>
                <?php $itemPosition = 0; foreach($events as $event) : ?>
                    <?php $itemPosition++; echo $this->element('Pages/listing/result', compact('event', 'itemPosition')); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <!--
        repressed filter box
        <aside>
            <?php //echo $this->element('Pages/listing/filters')?>
        </aside>
        -->
    </div>
</section>
