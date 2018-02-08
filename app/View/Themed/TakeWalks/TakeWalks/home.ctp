<?php $this->start('scripts'); ?>
    <?= $this->Html->script('/js/pages/home.js'); ?>

<?php $this->end(); ?>

<?php
$this->start('bottomHead');

//----- WebSite rich snippet -------
$WebSiteRS = $this->RichSnippets->create('WebSite');
$WebSiteRS->setVal('name','TakeWalks');
$WebSiteRS->setVal('url',FULL_BASE_URL);
echo $WebSiteRS->getJSON();

//----- Organization rich snippet -------
$organizationRS = $this->RichSnippets->create('Organization');
$organizationRS->setVal('brand','TakeWalks');
$organizationRS->setVal('name','TakeWalks');
$organizationRS->setVal('url',FULL_BASE_URL . DS);
$organizationRS->setVal('logo',FULL_BASE_URL . DS . 'theme/TakeWalks/svg/logo-green.svg');
$organizationRS->setVal('sameAs', array("https://www.facebook.com/walkingtours", "https://plus.google.com/+WalksofitalyTours", "https://twitter.com/WalksofItaly", "http://instagram.com/walksofitaly", "https://www.youtube.com/user/walksofitaly", "http://vimeo.com/walksofitaly", "https://www.pinterest.com/walksofitaly/"));
$addressRS = $organizationRS->addChild('Address','PostalAddress');
$addressRS->setVal('streetAddress','Via di Santa Maria dell\'Anima 48');
$addressRS->setVal('addressLocality','Roma');
$addressRS->setVal('addressRegion', 'Italy');
$addressRS->setVal('postalCode','00186');
$contactPointRS = $organizationRS->addChild('ContactPoint', 'ContactPoint');
$contactPointRS->setVal('email','info@walksofitaly.com');
$contactPointRS->setVal('telephone','+1-888-683-8670');
$contactPointRS->setVal('contactType','customer service');
$contactPointRS->setVal('contactOption','TollFree');
$contactPointRS->setVal('areaServed','US');
echo $organizationRS->getJSON();

$this->end();
?>

<?= $this->element('header'); ?>
<div class="hero" style="background-image: linear-gradient(rgba(0,0,0,.3), rgba(0,0,0,.3)), url('<?=$content['heroImage'] ?>?w=2000')">
    <iframe class="vimeo-player" src="" data-video-url="<?=$content['heroVideo'] ?>?autoplay=1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
    <?php if($content['heroVideo']): ?>
        <i class="icon icon-play"></i>
    <?php endif ?>
    <h1><?= $content['heroTitle'] ?></h1>
    <p><?= $content['heroDescription'] ?></p>
</div>

<section class="bordered">
    <div class="container">
        <div class="section-title center">
            <i class="icon icon-tour_guides"></i>
            <h2 class="section-heading">Meet Our Local Tour Guides</h2>
            <h3><?= $content['subHeading'] ?></h3>
        </div>

        <div class="guides-wrap">
            <?php foreach($content['tourGuides'] as $guide): ?>
                <div class="col">
                  <div class="guide-box">
                      <div class="avatar" style="background: url(<?= $guide['avatar'] ?>?w=300) no-repeat 85% 50%/cover;"></div>
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

<section class="bordered">
    <div class="container">
        <div class="section-title center">
            <i class="icon icon-most-popular-destinations"></i>
            <h2 class="section-heading">Most Popular Destinations</h2>
            <p class="descr">We take walks all over the world. These are just a few of our favourite destinations.</p>
        </div>

        <div class="places-wrap">
            <div class="places first">
                <?php foreach(array_slice($content['featuredCities'],0,2) as $city): ?>
                    <a class="place" href="/<?= str_replace(' ','-',strtolower($city['name'])) ?>-tours">
                        <span class="place-img"
                              style="background: linear-gradient(rgba(0,0,0,.4), rgba(0,0,0,.4)), url(<?= $city['image'] ?>?w=1200) no-repeat 50% 50%/cover;"></span>
                        <div class="place-caption">
                            <h3 class="header"><?= $city['name'] ?></h3>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>

            <div class="places second">
                <?php foreach(array_slice($content['featuredCities'],2) as $city): ?>
                    <a class="place" href="/<?= str_replace(' ','-',strtolower($city['name'])) ?>-tours">
                        <span class="place-img"
                              style="background: linear-gradient(rgba(0,0,0,.4), rgba(0,0,0,.4)), url(<?= $city['image'] ?>?w=1200) no-repeat 50% 50%/cover;"></span>
                        <div class="place-caption">
                            <h3 class="header"><?= $city['name'] ?></h3>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">

        <div class="section-title center">
            <i class="icon icon-walk"></i>
            <h2 class="section-heading"><?= $content['sellingPointsTitle'] ?></h2>

            <h5 class="subtitle purple separated-top"><?= $content['sellingPointsSubheading'] ?></h5>
            <p class="descr"><?= $content['sellingPointsDescription'] ?></p>
        </div>

        <div class="tabs with-icon">
            <a href="#" class="tab-item active" data-toggle-toggler="sellingPoint1">
                <i class="icon icon-small_groups-active" icon-inactive></i>
                <img class="ic" src="/theme/TakeWalks/svg/small_groups-active.svg" alt="">
                <span><?= $content['sellingPoints'][0]['title'] ?></span>
            </a>
            <a href="#" class="tab-item"  data-toggle-toggler="sellingPoint2">
                <i class="icon icon-best_local_guides-active" icon-inactive></i>
                <img class="ic" src="/theme/TakeWalks/svg/best_local_guides-active.svg" alt="">
                <span><?= $content['sellingPoints'][1]['title'] ?></span>
            </a>
            <a href="#" class="tab-item"  data-toggle-toggler="sellingPoint3">
                <i class="icon icon-guaranteed_departures-active" icon-inactive></i>
                <img class="ic" src="/theme/TakeWalks/svg/guaranteed_departures-active.svg" alt="">
                <span><?= $content['sellingPoints'][2]['title'] ?></span>
            </a>
            <a href="#" class="tab-item" data-toggle-toggler="sellingPoint4">
                <i class="icon icon-best_access-active" icon-inactive></i>
                <img class="ic" src="/theme/TakeWalks/svg/best_access-active.svg" alt="">
                <span><?= $content['sellingPoints'][3]['title'] ?></span>
            </a>
        </div>

        <div class="tab-content slow active" data-toggle-target="sellingPoint1">
            <div class="plan-trip-wrap">
                <div class="section-title left">
                    <h2 class="section-heading small"><?= $content['sellingPoints'][0]['title'] ?></h2>
                    <p class="descr"><?= $content['sellingPoints'][0]['description'] ?></p>
                </div>

                <!-- Can we fetch lower res photo here? ~800px wide should be more than enough. -->
                <div class="plan-img">
                    <img src="<?= $content['sellingPoints'][1]['image'] ?>" alt="" class="bottom-left">
                    <img src="<?= $content['sellingPoints'][0]['image'] ?>" alt="" class="center">
                    <img src="<?= $content['sellingPoints'][2]['image'] ?>" alt="" class="top-right">
                </div>
            </div>
        </div>

        <div class="tab-content slow" data-toggle-target="sellingPoint2">
            <div class="plan-trip-wrap">
                <div class="section-title left">
                    <h2 class="section-heading small"><?= $content['sellingPoints'][1]['title'] ?></h2>
                    <p class="descr"><?= $content['sellingPoints'][1]['description'] ?></p>
                </div>

                <div class="plan-img">
                    <img src="<?= $content['sellingPoints'][1]['image'] ?>" alt="" class="center">
                    <img src="<?= $content['sellingPoints'][0]['image'] ?>" alt="" class="bottom-left">
                    <img src="<?= $content['sellingPoints'][2]['image'] ?>" alt="" class="top-right">
                </div>
            </div>
        </div>

        <div class="tab-content slow" data-toggle-target="sellingPoint3">
            <div class="plan-trip-wrap">
                <div class="section-title left">
                    <h2 class="section-heading small"><?= $content['sellingPoints'][2]['title'] ?></h2>
                    <p class="descr"><?= $content['sellingPoints'][2]['description'] ?></p>
                </div>

                <div class="plan-img">
                    <img src="<?= $content['sellingPoints'][1]['image'] ?>" alt="" class="bottom-left">
                    <img src="<?= $content['sellingPoints'][0]['image'] ?>" alt="" class="top-right">
                    <img src="<?= $content['sellingPoints'][2]['image'] ?>" alt="" class="center">
                </div>
            </div>
        </div>

        <div class="tab-content slow" data-toggle-target="sellingPoint4">
            <div class="plan-trip-wrap">
                <div class="section-title left">
                    <h2 class="section-heading small"><?= $content['sellingPoints'][3]['title'] ?></h2>
                    <p class="descr"><?= $content['sellingPoints'][3]['description'] ?></p>
                </div>

                <div class="plan-img">
                    <img src="<?= $content['sellingPoints'][1]['image'] ?>" alt="" class="top-right">
                    <img src="<?= $content['sellingPoints'][2]['image'] ?>" alt="" class="bottom-left">
                    <img src="<?= $content['sellingPoints'][3]['image'] ?>" alt="" class="center">
                </div>
            </div>
        </div>



    </div>
</section>

<div class="container">
    <section class="bordered">
        <div class="sustainable-travel">
            <div class="text">
                <div class="green-circle">
                    <i class="icon icon-staff_pick"></i>
                </div>
                <div class="section-title left">
                    <h5 class="subtitle green">Staff Pick</h5>
                    <h2 class="section-heading small"><?= $content['featuredTourTitle'] ?></h2>
                    <p class="descr"><?=$content['featuredTourDescription'] ?></p>
                    <a href="<?= $content['featuredTourURL'] ?>" class="btn primary green">Learn More</a>
                </div>
            </div>
            <div class="img">
                <a href="<?= $content['featuredTourURL'] ?>">
                    <img src="<?= $content['featuredTourHeroImage'] ?>" alt="">
                </a>
            </div>
        </div>
    </section>
</div>

<section>
    <div class="container">
        <div class="section-title center">
            <h5 class="subtitle black">RECOMMENDED BY</h5>
        </div>
        <div class="partner-list">
            <div><img src="theme/TakeWalks/img/partners/svg/usatoday.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/theguardian.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/ricksteves.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/nyt.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/huffpost.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/frommers.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/fodorstravel.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/dktravel.svg" alt=""></div>
            <div><img src="theme/TakeWalks/img/partners/svg/cnt.svg" alt=""></div>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="section-title center">
            <i class="icon icon-sign_up green"></i>
            <h2 class="section-heading">Sign Up With Us</h2>
            <p class="descr signup-descr"><?= $content['signUpDescription'] ?></p>
        </div>
    </div>

    <div class="signup-box">
        <div class="inputs">
            <div class="input-row col md-placeholder">
                <input type="text" id="signup-name" >
                <div class="placeholder">Name</div>
            </div>
            <div class="input-row col md-placeholder">
                <input type="email" id="signup-email">
                <div class="placeholder">Email Address</div>
            </div>
        </div>
        <div class="error-message" ></div>
        <div class="center-btn smaller">
            <button class="btn primary purple" id="signup-button" >Sign Up</button>
        </div>

    </div>
</section>
<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>

<?php if(false): ?>
    <div class="chat-bubble">
        Ask a <br> Question
    </div>
<?php endif ?>
