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
    $productOfferRs->setVal('price', $event['Event']['adults_price']);
    $productOfferRs->setVal('priceCurrency','LIR');
    $productOfferRs->setVal('availability','InStock');

    $toursRS[] = $productRs->getArray();
}

$offerRs->setVal('itemOffered',$toursRS);



echo $localBusinessRS->getJSON();

$this->end();
?>
<article id="featured" class="home-hero" style="background-image: url(/theme/Turkey/img/hero.jpg); background-size: cover;">

        <div class="carousel-item">
            <div class="header-inner">
                <header>
                    <h2>Tours you can trust.</h2>

                    <p>
                        With strict limits on group sizes, licensed guides, skip the line access and a No Surprise Guarantee: What you see is what you get.
                    </p>

                    <div class="top-right-border-outer"></div>
                    <div class="top-right-border-inner"></div>
                </header>

                <p class="link-a b"><a href="/<?php echo $viewToursLink ?>-tours">View our tours</a></p>
            </div>
        </div>

</article>
<section id="content">
    <ul class="list-a a">
        <li><span class="title"><i class="fa fa-check-square-o"></i>Small Groups Guaranteed</span> Enjoy a friendlier, more personal experience with small groups of 15 people or fewer: Guaranteed.</li>
        <li><span class="title"><i class="fa fa-star"></i> 5-Star Guides<br /><br /></span> Relax in the company of our fun, fluent tour guides. With no external<br /> affiliations they’ll never lead you astray.</li>
        <li><span class="title"><i class="fa fa-thumbs-o-up"></i> International Standards</span> Set your expectations to international standards; we provide the same level of service here as in Turkey & the US.</li>
    </ul>
    <div class="outer-borders-wrap below-excellent">
        <div class="fakenav-borders-wrapper">
            <div class="fakenav-inner"></div>
            <div class="fakenav-outer"></div>
        </div>
    </div>
    <article class="double-a b">
        <div class="module-c">
            <?php
            $event = $featured[0];
            //get feature image
            $featureImage = '';
            foreach($event['EventsImage'] as $eventImage){
                if($eventImage['feature']){ $featureImage = $eventImage['images_name']; break; }
            }
            ?>

            <figure>
                <?php

                ?>
                <img src="https://app.resrc.it/O=20(40)/s=W647/<?php echo $featureImage ?>" alt="Placeholder" width="647" height="393">

                <div class="activity-info inside red">
                    <div class="activity-name">
                        <p><?php echo $event['Event']['name_listing'] ?></p>
                    </div>

                    <ul>
                        <li class="rating-a white  <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['letterClass'] : ''?>" data-id="<?php echo $event['Event']['id']; ?>" style="<?php echo !isset($ratings[$event['Event']['id']]) ? 'display:none' : ''; ?>"></li>
                    </ul>

                    <p class="price-a"><span>from</span> <?php echo ExchangeRate::convert($event['Event']['adults_price']) ?></p>

                    <p class="link-a">
                        <a href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
                            <i class="fa fa-info-circle"></i> More Info
                        </a>
                    </p>

                    <div class="top-right-border-outer"></div>
                    <div class="top-right-border-inner"></div>
                </div>
            </figure>

        </div>
        <div class="module-d">
            <?php
            $event = $featured[1];
            //get feature image
            $featureImage = '';
            foreach($event['EventsImage'] as $eventImage){
                if($eventImage['feature']){ $featureImage = $eventImage['images_name']; break; }
            }
            ?>
            <ul class="gallery-a act-near-popular-tours">
                <li data-id="<?php echo $event['Event']['id']; ?>" class="first" style="background: url('<?php echo "https://app.resrc.it/O=20(40)/s=W314/$featureImage" ?>') center center;  background-size: cover;">

                    <div class="activity-info inside orange">
                        <div class="activity-name">
                            <p><?php echo $event['Event']['name_listing'] ?></p>
                        </div>

                        <ul>
                            <li class="rating-a white <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['letterClass'] : ''?>" data-id="<?php echo $event['Event']['id']; ?>"  style="<?php echo !isset($ratings[$event['Event']['id']]) ? 'display:none' : ''; ?>"></li>
                        </ul>

                        <p class="price-a">
                            <span>from</span> <?php echo ExchangeRate::convert($event['Event']['adults_price'] ?: $event['Event']['private_base_price'], 0) ?>
                        </p>

                        <p class="link-a">
                            <a href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
                                <i class="fa fa-info-circle"></i> More Info
                            </a>
                        </p>

                        <div class="top-right-border-outer"></div>
                        <div class="top-right-border-inner"></div>

                    </div>

                    <!-- <span class="title"><?php echo $event['Event']['name_listing'] ?></span>
                    <span class="rating-a g">
                        From
                        <a class="reviews" href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>#reviews">
                            27
                        </a>
                        reviews
                    </span>
                    <a class="link-a" href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
                        <i class="fa fa-info-circle"></i> More Info
                    </a>
                    <span class="price-a">
                        <span>from</span> <?php echo ExchangeRate::convert($event['Event']['adults_price']) ?>
                    </span> -->
                </li>
            </ul>
        </div>
        <!--<div class="module-d">
            <h2 class="header-b"><span>Get a <span>suggested</span></span> itinerary</h2>
            <p class="scheme-d">First-time traveler? Mobility issues? Children? We have what you need!</p>
            <p>Answer a few questions and get a suggested itinerary that is specific to your needs and desires.</p>
            <p class="link-a"><a href="./"><i class="fa fa-info-circle"></i> More Info</a></p>
        </div>-->
    </article>
    <ul class="gallery-a">
        <?php for($i = 2; $i < 5; $i++) : ?>
            <?php $event = $featured[$i]; ?>

            <?php
            $imageUrl = '';
            foreach($event['EventsImage'] as $image) {
                if($image['feature']) {
                    $imageUrl = $image['images_name'];
                    break;
                }
            }
            ?>

            <li data-id="<?php echo $event['Event']['id']; ?>">
                <img src="<?php echo $this->ReSrc->resrcUrl($imageUrl, 314) ?>" alt="<?php echo $event['Event']['name_listing'] ?>">
                <span class="title"><?php echo $event['Event']['name_listing'] ?></span>
                <span class="rating-a <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['letterClass'] : ''?>" style="<?php echo !isset($ratings[$event['Event']['id']]) ? 'display:none' : ''; ?>">
                    From
                    <a class="reviews" href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
                        <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['amount'] : ''?>
                    </a>
                     reviews
                </span>
                <a class="link-a" href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
                    <i class="fa fa-info-circle"></i> More Info
                </a>
                <span class="price-a">
                    <span>from</span> <?php echo ExchangeRate::convert($event['Event']['adults_price']) ?>
                </span>
            </li>
        <?php endfor ?>
    </ul>
    <div class="outer-borders-wrap">
        <div class="fakenav-borders-wrapper">
            <div class="fakenav-inner"></div>
            <div class="fakenav-outer"></div>
        </div>
    </div>
    <p class="quote-b">
        <span class="title">our <i>No Surprise</i> Guarantee</span>
        <q>
            Our <b>No Surprise Guarantee</b> means no hidden charges, no carpet shops and no pressure to buy your weight in saffron. When you book a Walks of Turkey tour, what you see is what you get; delivered by fun, enthusiastic tour guides with no external affiliations.<br/> <b>Guaranteed.</b>
        </q>
    </p>

            <!-- hide blogs for now
    <div class="columns-b">
        <div class="news-a">
            <article>
                <figure class="image-a">
                    <iframe src="//player.vimeo.com/video/118902736" width="648" height="295" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </figure>
                <p></p>
                <p class="link-a a"><a href="./"><i class="fa fa-play-circle-o"></i> View all the videos</a></p>
            </article>
            <article>
                <ul class="gallery-c">
                    <li>
                        <img src="<?php //echo str_replace('http:','',$theme->blogUrl . $blog_posts[3]['thumbnail']) ?>" alt="<?php echo $blog_posts[3]['wp']['post_title'] ?>" width="312" height="206">
                        <span>
                            <span>
                                <?php //echo $blog_posts[3]['wp']['post_title'] ?>
                            </span>
                        </span>
                        <a href="<?php //echo $theme->blogUrl . $blog_posts[3]['wpt2']['slug'] . '/' . $blog_posts[3]['wp']['post_name'] ?>">
                            <span>
                                <i class="fa fa-list"></i> read more
                            </span>
                        </a>
                    </li>
                    <li>
                        <img src="<?php //echo str_replace('http:','',$theme->blogUrl . $blog_posts[4]['thumbnail']) ?>" alt="<?php echo $blog_posts[4]['wp']['post_title'] ?>" width="312" height="206">
                        <span>
                            <span>
                                <?php //echo $blog_posts[4]['wp']['post_title'] ?>
                            </span>

                        </span>
                        <a href="<?php //echo $theme->blogUrl . $blog_posts[4]['wpt2']['slug'] . '/' . $blog_posts[4]['wp']['post_name'] ?>">
                            <span>
                                <i class="fa fa-list"></i> read more
                            </span>
                        </a>
                    </li>
                </ul>

                <p class="link-a a"><a href="<?php //echo $theme->blogUrl ?>"><i class="fa fa-play-circle-o"></i> View all blog posts</a></p>

            </article>
        </div>
        <aside>
            <h3 class="header-a a">Staff picks</h3>
            <ul class="gallery-d">
                <?php //for($i=0;$i<3;$i++){ $post= $blog_posts[$i]; ?>

                    <li>
                        <img src="<?php //echo str_replace('http:','',$theme->blogUrl) . $post['thumbnail'] ?>" alt="<?php //echo $post['wp']['post_title'] ?>">
                        <span class="title"><?php //echo $post['wp']['post_title'] ?></span>
                        <?php //echo $post['summary'] ?><br/>
                        <a href="<?php //echo $theme->blogUrl . $post['wpt2']['slug'] . '/' . $post['wp']['post_name'] ?>"><i class="fa fa-list"></i> Read more</a>
                    </li>
                <?php //} ?>
            </ul>
        </aside>
    </div>
    -->
</section>
<article class="module-b">
    <h2><i class="fa fa-heart-o"></i> Share the love</h2>
    <p>Latest Instagram photos <a rel="external" href="./">#takewalks</a></p>
    <ul class="gallery-e" id="instagram">
        <?php
        $countPhoto = 0;
        foreach($instagram as $photo):
            if ($instagramCount == $countPhoto) {break;}
            $countPhoto++;
            ?>
            <li>
                <a href="<?php echo $photo->display_src ?>" target="_blank">
                    <img src="<?php echo $photo->thumbnail_src ?>" />
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</article>
<!--<script type="text/javascript" src="/js/lib/instafetch.js"></script>-->
<script type="text/javascript">
    $(document).on('ready', function() {
//        var feed = new Instafetch('dcdfdf8445c4415a9a621a6b96051217');
//        feed.fetch({
//            tag: 'takewalks',
//            limit: 42,
//            callback: function(response) {
//                console.log(response);
//                $.each(response.data, function() {
//                    $('#instagram').append(
//                      '<li>' +
//                        '<a href="'+this.link+'" target="_blank">' +
//                        '<img src="'+this.images.thumbnail.url.replace('http:','') +'" />' +
//                        '</a>' +
//                        '</li>'
//                    )
//                })
//            }
//        });
    });
</script>