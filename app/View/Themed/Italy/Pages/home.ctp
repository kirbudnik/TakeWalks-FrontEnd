<?php
$this->start('headBottom');

//-----WebSite rich snippet-------
$WebSiteRs = $this->RichSnippets->create('WebSite');
$WebSiteRs->setVal('name','Walks of Italy');
$WebSiteRs->setVal('url','https://www.walksofitaly.com/');
    $searchActionRS = $WebSiteRs->addChild('potentialAction','SearchAction');
    $searchActionRS->setVal('target',"https://www.walksofitaly.com/blog/?s={search_term_string}");
    $searchActionRS->setVal('query-input',"required name=search_term_string");
echo $WebSiteRs->getJSON();


//--------local business rich snippet----------
$localBusinessRS = $this->RichSnippets->create('LocalBusiness');
$localBusinessRS->setVal('additionalType','http://www.productontology.org/id/Tour_operator');
$localBusinessRS->setVal('name','Walks of Italy');
$localBusinessRS->setVal('url','https://www.walksofitaly.com/');
$localBusinessRS->setVal('logo','https://www.walksofitaly.com/blog/wp-content/uploads/2015/02/walks-of-italy-logo.png');
$localBusinessRS->setVal('sameAs', array("https://en.wikipedia.org/wiki/Walks_of_Italy", "http://www.freebase.com/m/0g9t3zv", "https://www.facebook.com/walkingtours", "https://plus.google.com/+WalksofitalyTours", "https://twitter.com/WalksofItaly", "http://instagram.com/walksofitaly", "https://www.youtube.com/user/walksofitaly", "http://vimeo.com/walksofitaly", "https://www.pinterest.com/walksofitaly/"));
$localBusinessRS->setVal('currenciesAccepted','EUR');
$localBusinessRS->setVal('image','https://www.walksofitaly.com/blog/wp-content/uploads/2015/02/walks-of-italy-logo.png');
    $founderRS = $localBusinessRS->addChild('founder','Person');
    $founderRS->setVal('name','Jason Spiehler');
    $founderRS->setVal('sameAs', array("https://plus.google.com/116930771859616754446", "https://www.linkedin.com/pub/jason-spiehler/35/a8b/937"));
    $addressRs = $localBusinessRS->addChild('Address','PostalAddress');
    $addressRs->setVal('streetAddress','Via di Santa Maria dell\'Anima 48');
    $addressRs->setVal('addressLocality','Roma');
    $addressRs->setVal('addressRegion', 'Italy');
    $addressRs->setVal('postalCode','00186');
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
        $productOfferRs->setVal('priceCurrency','EUR');
        $productOfferRs->setVal('availability','InStock');

    $toursRS[] = $productRs->getArray();
}

$offerRs->setVal('itemOffered',$toursRS);



echo $localBusinessRS->getJSON();
$this->end();
?>

<?php
//move paris to the front
$index = 0;
foreach($heroLocations as $i => $location){
    if($location['DomainsGroup']['url_name'] == 'paris'){
        $index = $i;
        break;
    }
}
$parisHero = $heroLocations[$index];

array_splice($heroLocations, $index, 1);
array_unshift($heroLocations, $parisHero);
?>

<article id="featured" class="home-hero">

    <div class="home-banner"
         style="background:url('<?php echo "https://app.resrc.it/s=W900/https://images.walks.org/italy/featured/woi-home-banner.jpg?v=2" ?>');
                 background-size: cover;">
        <header>
            <p class="first-line">Go Beyond The Obvious</p>
            <p class="second-line">Rome, Florence, Venice & More.</p>
        </header>
    </div>

    <?php if (false): foreach ($heroLocations as $location): ?>

        <div class="carousel-item-<?php echo $location['DomainsGroup']['url_name'] ?>"
             style="background:url('<?php echo 'https://app.resrc.it/O=20(40)/s=W1300/' . $location['DomainsGroup']['hero'] . '?v=2' ? : 'http://placehold.it/1400x527' ?>');
                     background-size: cover;">
            <?php if($location['DomainsGroup']['url_name'] == 'paris'): ?>
                <header>
                    TAKE WALKS OF PARIS
                </header>
            <?php else: ?>
                <header>
                    <h2>TAKE WALKS WITH FRIENDS.</h2>

                    <p>
                        With small groups on all our tours, you're in for a much more friendly experience.
                    </p>
                </header>

                <p class="featured-more-info">Experience more when you #TakeWalks.</p>
            <?php endif ?>

            <p class="link-a b"><a href="/<?php echo $location['DomainsGroup']['url_name'] ?>-tours">View <?php echo $location['DomainsGroup']['name'] ?> tours</a></p>
        </div>
        <?php break; //only france banner ?>
    <?php endforeach; endif ?>
</article>
<section id="content">
    <ul class="list-a a">
        <li><span class="title"><i class="fa fa-check-square-o"></i> Excellent service</span> We don’t just show up on the day, we’re here from day 1. So if you have any questions about Italy, get in touch.</li>
        <li><span class="title"><i class="fa fa-star"></i> 5-star guides</span> We’ve earned the Trip Advisor Certificate of Excellence every year since 2011, with over 1,000 5-star reviews.</li>
        <li><span class="title"><i class="fa fa-thumbs-o-up"></i> Tried &amp; Tested</span> Recommended by Rick Steves, Fodor's, Frommers, DK Travel, USA Today, The Daily Telegraph &amp; About.com.</li>
    </ul>
    <article class="double-a b">
        <div class="module-c">
            <?php $event = $featured[0] ?>
            <?php
            $imageUrl = '';
            foreach($event['EventsImage'] as $image) {
                if($image['feature']) {
                    $imageUrl = $image['images_name'];
                    break;
                }
            }
            ?>
            <figure>
                <a href="/paris-tours">
                    <img src="/theme/Italy/img/woi-paris-homepage.png" alt="france tours" width="647">
                </a>
            </figure>


        </div>
        <div class="module-d">
            <?php $event = $featured[1]; ?>

            <?php
            $imageUrl = '';
            foreach($event['EventsImage'] as $image) {
                if($image['feature']) {
                    $imageUrl = $image['images_name'];
                    break;
                }
            }
            ?>
            <ul class="gallery-a act-near-popular-tours">
                <li data-id="<?php echo $event['Event']['id']; ?>">
                    <img src="<?php echo $this->ReSrc->resrcUrl($imageUrl, 314) ?>" alt="Placeholder" width="314" height="190">
                    <span class="title"><?php echo $event['Event']['name_listing'] ?></span>
                    <span class="rating-a <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['letterClass'] : ''?>" style="<?php echo !isset($ratings[$event['Event']['id']]) ? 'display:none' : ''; ?>">
                        From
                        <a class="reviews" href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>#reviews">
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
    <ul class="gallery-b">
        <?php $count = 0; ?>
        <?php foreach($locations as $location): ?>
            <?php if(++$count > 4) break ?>
            <li>
                <a href="/<?php echo $location['DomainsGroup']['url_name'] ?>-tours">
                    <img src="<?php echo $this->ReSrc->resrcUrl($location['DomainsGroup']['homepage'], 478) ?>" height="358">
                    <span><?php echo $location['DomainsGroup']['name'] ?></span>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
    <p class="quote-b"><q>Co-founder, art historian &amp; legendary tour guide; Jason can tell you which ancient ruin Raphael graffitied &amp; how a teenaged Leonardo da Vinci narrowly escaped prison.</q> <span><span><span>Jason Spiehler</span> -Co-Founder</span> <a href="/about">Meet the rest of the team</a></span> <img src="/theme/Italy/img/content/ceo.png" alt="CEO" width="468" height="429"></p>
    <div class="columns-b">
        <div class="news-a">
            <article>
                <figure class="image-a">
                    <iframe src="//player.vimeo.com/video/118902736" width="648" height="295" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </figure>
                <p></p>
                <p class="link-a a"><a href="https://www.youtube.com/user/walksofitaly"><i class="fa fa-play-circle-o"></i> View all the videos</a></p>
            </article>
            <article>
                <ul class="gallery-c">
                    <li>
                        <img src="<?php echo str_replace('http:','',$theme->blogUrl . $blog_posts[3]['thumbnail']) ?>" alt="<?php echo $blog_posts[3]['wp']['post_title'] ?>" width="312" height="206">
                        <span>
                            <span>
                                <?php echo $blog_posts[3]['wp']['post_title'] ?>
                            </span>
                        </span>
                        <a href="<?php echo $theme->blogUrl . $blog_posts[3]['wpt2']['slug'] . '/' . $blog_posts[3]['wp']['post_name'] ?>">
                            <span>
                                <i class="fa fa-list"></i> read more
                            </span>
                        </a>
                    </li>
                    <li>
                        <img src="<?php echo str_replace('http:','',$theme->blogUrl . $blog_posts[4]['thumbnail']) ?>" alt="<?php echo $blog_posts[4]['wp']['post_title'] ?>" width="312" height="206">
                        <span>
                            <span>
                                <?php echo $blog_posts[4]['wp']['post_title'] ?>
                            </span>

                        </span>
                        <a href="<?php echo $theme->blogUrl . $blog_posts[4]['wpt2']['slug'] . '/' . $blog_posts[4]['wp']['post_name'] ?>">
                            <span>
                                <i class="fa fa-list"></i> read more
                            </span>
                        </a>
                    </li>
                </ul>
                <p class="link-a a"><a href="<?php echo $theme->blogUrl ?>"><i class="fa fa-play-circle-o"></i> View all blog posts</a></p>
            </article>
        </div>
        <aside>
            <h3 class="header-a a">Staff picks</h3>
            <ul class="gallery-d">
                <?php for($i=0;$i<3;$i++){ $post= $blog_posts[$i]; ?>

                    <li>
                        <img src="<?php echo str_replace('http:','',$theme->blogUrl) . $post['thumbnail'] ?>" alt="<?php echo $post['wp']['post_title'] ?>">
                        <span class="title"><?php echo $post['wp']['post_title'] ?></span>
                        <?php echo $post['summary'] ?><br/>
                        <a href="<?php echo $theme->blogUrl . $post['wpt2']['slug'] . '/' . $post['wp']['post_name'] ?>"><i class="fa fa-list"></i> Read more</a>
                    </li>
                <?php } ?>
            </ul>
        </aside>
    </div>
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
//
//        feed.fetch({
//            tag: 'takewalks',
//            limit: 42,
//            callback: function(response) {
//                console.log(response);
//                $.each(response.data, function() {
//                    $('#instagram').append(
//                        '<li>' +
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

