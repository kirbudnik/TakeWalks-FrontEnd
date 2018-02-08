<?php $this->start('afterBody'); ?>

<!-- Formstone core -->
<?php echo $this->Html->script('lib/formstone-core.min.js'); ?>
<?php echo $this->Html->script('lib/formstone-scrollbar.min.js'); ?>
<?php echo $this->Html->script('lib/formstone-touch.min.js'); ?>
<?php echo $this->Html->script('lib/formstone-dropdown.min.js'); ?>
<?php echo $this->Html->script('lib/slick.min.js'); ?>
<?php echo $this->Html->script('lib/royalslider.min.js'); ?>
<?php echo $this->Html->script('lib/jquery-ui-1.9.2.min.js'); ?>

<!-- todo remove -->
<?php echo $this->Html->script('//maps.googleapis.com/maps/api/js?key=AIzaSyCL5d630NM0Hd6CYK8W18lCt5GU7O_2YP8'); ?>

<!-- custom ui -->
<?php echo $this->Html->script('ui-scripts.js'); ?>

<script>window.twttr = (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function(f) {
            t._e.push(f);
        };

        return t;
    }(document, "script", "twitter-wjs"));
</script>

<?php $this->end(); ?>


<div id="fb-root"></div>
<script>
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<div class="grid-container">
    <section class="border-bottom full home-features typo flex-grid">
        <div class="grid-item">
            <img src="/theme/nyc2/img/bg/home/all-in-one-place.jpg" alt="All in one place" class="rounded">
            <h5 class="smaller header blue">All in one place</h5>
            <p>Are you looking for fun unusual tours? We got them! Or maybe just the essential stuff?<br>Yep, we got those too</p>
        </div>
        <div class="grid-item">
            <img src="/theme/nyc2/img/bg/home/small-groups.jpg" alt="Small groups" class="rounded">
            <h5 class="smaller header blue">Small groups</h5>
            <p>Group sizes of 20 people or fewer mean more relaxed experiences, like spending<br>time with your friends</p>
        </div>
        <div class="grid-item">
            <img src="/theme/nyc2/img/bg/home/go-local.jpg" alt="Go local" class="rounded">
            <h5 class="smaller header blue">Go local</h5>
            <p>Local guides, handpicked for their expertise and passion, guarantee you an insider's<brt>view of the Big Apple</p>
        </div>
        <div class="grid-item">
            <img src="/theme/nyc2/img/bg/home/inside-scoop.jpg" alt="The inside scoop" class="rounded">
            <h5 class="smaller header blue">The inside scoop</h5>
            <p>Unique itineraries bring you<br>on an exploration of the real<br>New York, meeting the local<br> and hearing their stories</p>
        </div>
    </section>
</div>

<?php
    $featuredEvents = [
        [1130,1133,1144,1121],
        [1134,1143,1145,1146]
    ];
    function getReviewAmount($ratings, $event_id){
        return isset($ratings[$event_id]) ? $ratings[$event_id]['amount'] : 0;
    }
?>

<div class="grid-container" data-scroll-target="tours">
    <section class="spacious typo">
        <h1 class="h1">The Essential Tours</h1>
        <h4 class="h4 header blue light">The must-see stuff for an authentic NY experience</h4>
        <p class="bigger">Because in New York even the obvious is obviously amazing</p>

        <div class="tours-wrap">
            <div class="tours first-big">
                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[0][0]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[0][0]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder"><?=$homeEvents[$featuredEvents[0][0]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[0][0]) ?> review<?= getReviewAmount($ratings, $featuredEvents[0][0]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>

                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[0][1]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[0][1]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder"><?=$homeEvents[$featuredEvents[0][1]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[0][1]) ?> review<?= getReviewAmount($ratings, $featuredEvents[0][1]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="tours last-big">
                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[0][2]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[0][2]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder" ><?=$homeEvents[$featuredEvents[0][2]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[0][2]) ?> review<?= getReviewAmount($ratings, $featuredEvents[0][2]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>

                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[0][3]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[0][3]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder"><?=$homeEvents[$featuredEvents[0][3]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[0][3]) ?> review<?= getReviewAmount($ratings, $featuredEvents[0][3]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="center-btn">
            <a href="/new-york-tours" class="btn primary">See all tours</a>
        </div>

    </section>

    <section class="spacious typo">
        <h1 class="h1">Our Exclusive Tours</h1>
        <h4 class="h4 header blue light">For those who like to take it to the next step</h4>
        <p class="bigger">Looking to go beyond the usual tours and attractions? You’ve come to the right place.</p>

        <div class="tours-wrap">
            <div class="tours first-big">
                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[1][0]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[1][0]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder"><?=$homeEvents[$featuredEvents[1][0]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[1][0]) ?> review<?= getReviewAmount($ratings, $featuredEvents[1][0]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>

                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[1][1]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[1][1]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder"><?=$homeEvents[$featuredEvents[1][1]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[1][1]) ?> review<?= getReviewAmount($ratings, $featuredEvents[1][1]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="tours last-big">
                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[1][2]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[1][2]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder"><?=$homeEvents[$featuredEvents[1][2]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[1][2]) ?> review<?= getReviewAmount($ratings, $featuredEvents[1][2]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>

                <a class="tour" href="<?php echo "/{$theme->city_slug}-tours/{$homeEvents[$featuredEvents[1][3]]['Event']['url_name']}" ?>">
                    <span class="tour-img" style="background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('https://app.resrc.it/O=40(40)/<?=str_replace('https:', 's=W500/https:',$homeEvents[$featuredEvents[1][3]]['EventsImage'][0]['images_name']) ?>') no-repeat 50% 50%/cover;"></span>
                    <div class="tour-caption">
                        <h3 class="header bolder"><?=$homeEvents[$featuredEvents[1][3]]['Event']['name_short'] ?></h3>
                        <div class="reviews-count">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="num">From <?=getReviewAmount($ratings, $featuredEvents[0][3]) ?> review<?= getReviewAmount($ratings, $featuredEvents[0][3]) != 1 ? 's' : '' ?></p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="center-btn">
            <a href="/new-york-tours" class="btn primary">See all tours</a>
        </div>

    </section>
</div>

<section class="rose-bg typo separated slick-slider-container">
    <div class="grid-container">
        <div class="item first-half">
            <h3 class="header blue heavy">Latest Blog Posts</h3>
            <a href="<?php echo $theme->blogUrl ?>" class="black-link rose-hl">View all Blog posts ></a>

            <div class="blog-posts">
                <?php foreach (array_slice($blog_posts,0,2) as $post): ?>
                <div class="post">
                    <div class="image">
                        <img src="<?php echo $theme->blogUrl . $post['thumbnail'] ?>" class="bordered" alt="<?php echo $post['wp']['post_title'] ?>">
                    </div>
                    <div class="text">
                        <p class="date"><?php echo date('M j, Y', strtotime($post['wp']['post_date'])) ?></p>
                        <a href="<?php echo $theme->blogUrl . '/' . $post['wp']['post_name'] ?>" class="blog-title rose-hl"><?php echo $post['wp']['post_title'] ?></a>
                    </div>
                </div>
                <?php endforeach ?>

            </div>
        </div>
        <div class="item second-half">
            <h3 class="header blue heavy">Testimonials</h3>
            <a href="https://www.walksofnewyork.com/press" class="black-link rose-hl">View all Testimonials ></a>

            <div class="testimonial-slider-wrap">
                <div class="testimonial">
                    <div class="body">I learnt so much about places in NYC that I see every day. So much history and culture in the stories about the buildings which have made NYC what it is today! Loved It.</div>

                    <div class="author">
                        Helena G, TripAdvisor
                    </div>
                </div>

                <div class="testimonial">
                    <div class="body">From the moment we met our tour guide Jason, we knew we were in for a great afternoon. Not only was he enthusiastic about the tour, but he clearly is well versed on all things related to the Met.</div>

                    <div class="author">
                        WhatBoundaries, TripAdvisor
                    </div>
                </div>

                <div class="testimonial">
                    <div class="body">I took the LES tour and learned so much about that neighborhood and the immigrants that came to New York. Plus, there were loads of yummy foodie treats along the way.</div>

                    <div class="author">
                        Lauren D, TripAdvisor
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grid-container">
    <section class="condensed double-bordered typo">
        <h3 class="header blue heavy section-header no-top">Latest Tweets</h3>

        <div class="tweets-slider">
            <?php foreach($tweets as $tweet): ?>
                <div class="tweet">
                    <div class="inner-content">
                        <div class="img">
                            <i class="fa fa-twitter"></i>
                        </div>
                        <div class="text">
                            <p><?= $tweet->text ?></p>
                            <p class="date grey"><?=date('g:i a - j M Y', strtotime($tweet->created_at)); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </section>
</div>

<div class="grid-container">
    <section class="separated bigger typo border-bottom full">
        <div class="item first-half double-border-right">
            <h3 class="header fb-blue heavy">Like us on Facebook <i class="fa fa-facebook-official"></i></h3>
            <a href="https://www.facebook.com/walksofnewyork" class="black-link rose-hl">Go to Facebook ></a>

            <div class="social-feed facebook">
                <div class="fb-page" data-href="https://www.facebook.com/walksofnewyork" data-tabs="timeline" data-width="405" data-height="340" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/walksofnewyork" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/walksofnewyork">Walks of New York</a></blockquote></div>
            </div>
        </div>
        <div class="item second-half">
            <h3 class="header heavy">#takewalks <i class="fa fa-instagram"></i></h3>
            <a href="http://instagram.com/walksofnewyork" class="black-link rose-hl">View on Instagram ></a>

            <div class="social-feed instagram">
                <?php foreach(array_slice($instagram,0,12) as $photo): ?>
                    <a href="<?php echo $photo->display_src ?>"  target="_blank">
                        <img src="<?php echo $photo->thumbnail_src ?>" /><span class="viewphoto"></span>
                    </a>

                <?php endforeach ?>
            </div>
        </div>
    </section>
</div>

<section>
    <div class="grid-container">
        <h3 class="header blue section-header smaller no-top">Everybody's talking about us</h3>
        <div class="header-link">
            <a href="/press" class="black-link rose-hl">View all Press ></a>
        </div>

        <div class="press-container">
            <div class="grid-item">
                <img src="theme/nyc2/img/press/tripadvisor.png" alt="">
            </div>
            <div class="grid-item">
                <img src="theme/nyc2/img/press/yahoo-travel.png" alt="">
            </div>
            <div class="grid-item">
                <img src="theme/nyc2/img/press/huffpost.png" alt="">
            </div>
            <div class="grid-item">
                <img src="theme/nyc2/img/press/nomadic.png" alt="">
            </div>
        </div>
    </div>
</section>








