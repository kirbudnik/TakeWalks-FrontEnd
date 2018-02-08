<nav id="breadcrumbs">
    <h2><?php echo $event['name_long'] ?></h2>
    <ol>
        <li><a href="/">Home</a></li>
        <li><a href="/<?php echo $domainsGroup['url_name'] ?>-tours"><?php echo $domainsGroup['name'] ?> Tours</a></li>
        <li><?php echo $event['name_listing'] ?></li>
    </ol>
    <p><span class="price-a"><span>First 2 passengers</span> €<?php echo $event['adults_price'] ?></span> <span class="rating-a k">From 27 reviews</span></p>
</nav>
<article id="featured" class="event-detail-hero">
    <?php if (!empty($images)) { ?>
        <?php foreach($images as $image): ?>
            <?php if($image['primary'] == 1): ?>
                <figure><img class="resrc" src="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W1300/https:', $images[0]['images_name']) ?>"/></figure>
                <?php break ?>
            <?php endif ?>
        <?php endforeach ?>
    <?php } ?>
</article>
<section id="content">
    <div class="columns-a">
        <article>
            <ul class="list-b">
                <li><i class="fa fa-map-marker"></i> City: Rome</li>
                <li><i class="fa fa-clock-o" ></i> <?php echo $event['display_duration'] ?></li>
                <li><i class="fa fa-arrow-right"></i> Start Time: <?php echo $event['display_time'] ?></li>
            </ul>
            <p class="scheme-a"><?php echo $event['description_long'] ?></p>


            <section id="tab-target-description" class="tab-target hidden">
                <?php foreach(explode("\n", $event['description_long']) as $n => $line) : ?>
                    <?php //if(trim($line) === '') continue; ?>
                    <?php if($n == 0) : ?>
                        <h3><?php echo $line ?></h3>
                    <?php else: ?>
                    <p <?php echo $n > 7 && false ? 'class="hidden"' : ''; ?>>
                        <?php echo $line ?>
                    </p>
                    <?php endif ?>
                <?php endforeach; ?>
                <a class="large more">Read More <img src="/img/arrow-down-blue.png" alt=""></a>
            </section>

        </article>
        <aside>
            <div class="calendar-a">
                <h3>Book a tour</h3>
                <h4>Call  +1-888-683-8670 from the US,<br /><br />
                    +39-069-480-4888 from Italy and<br/><Br />
                    +1-202-684-6916 internationally.</h4>
            </div>
        </aside>
    </div>
    <h2 class="scheme-c">Similar tours in Rome</h2>
    <ul class="gallery-a">
        <?php foreach ($relatedTours as $tour): ?>
        <li>
            <?php if (!empty($tour['Event']['EventsImage'])): ?>
                <img src="<?php echo $tour['Event']['EventsImage'][0]['images_name'] ?>" alt="<?php echo $tour['Event']['EventsImage'][0]['alt_tag'] ?>">
            <?php else: ?>
                <img src="http://placehold.it/314x190" alt="Placeholder" width="314" height="190">
            <?php endif ?>
            <span class="title"><?php echo $tour['Event']['name_listing'] ?></span>
            <span class="rating-a i">From <a href="./">27 reviews</a></span>
            <a class="link-a" href="/<?php echo $theme->city_slug ?>-tours/<?php echo $event['url_name'] ?>"><i class="fa fa-info-circle"></i> More Info</a>
            <span class="price-a"><span>from</span> €<?php echo $tour['Event']['adults_price'] ?></span></li>
        <?php endforeach ?>
    </ul>
</section>
   
