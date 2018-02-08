<div class="page-title-area" style="background-image: url('<?= $heroImage ?>?w=2000')">
    <div class="page-title-overlay">
        <section class="content no-top-padding">
            <div class="title-wrap">
                <div class="tag-page-title-text">
                    <h1 class="tag-page-title"><?php echo $contentCMS['tagPageTitle']; ?> </h1>
                    <div class="tag-page-description">
                        <?php echo ContentfulWrapper::parseMarkdown($contentCMS['tagPageIntroText']) ?>
                    </div>
                </div>
                <div class="tag-page-title-slider">
                    <img src="/theme/Italy/img/landing/<?= $mapImage ?>-map.png" alt="">
                </div>
            </div>
        </section>
    </div>
</div>

<div class="tag-anchors bxs-border-box">
    <div class="tag-anchors-inner">
        <a href="#upcoming_tours" data-scroll-toggler="upcoming_tours" class="tag-anchor-item">Upcoming Tours</a>
        <a href="#similar-tours" class="tag-anchor-item">View All Tours</a>
        <a href="#highlights" data-scroll-toggler="highlights" class="tag-anchor-item">Highlights</a>
        <a href="#faq" data-scroll-toggler="faq" class="tag-anchor-item">Faq</a>
    </div>
</div>

<section class="content" data-scroll-target="upcoming_tours" id="upcoming_tours">
    <h3>Upcoming Tours</h3>

    <div class="tag-upcoming-nav bxs-border-box">
        <div class="upcoming-nav-item label first">Choose date</div>
        <div class="upcoming-nav-item input"><input type="text" id="choose_date" readonly="readonly"/></div>
        <div class="upcoming-nav-item label nav-btn" id="btn_previous_date"><i class="fa fa-chevron-left"></i> Previous
            Day
        </div>
        <div class="upcoming-nav-item label nav-btn" id="btn_next_date">Next Day <i class="fa fa-chevron-right"></i>
        </div>
    </div>

    <table class="upcoming-tours-table">
        <thead>
        <tr>
            <th>Depart</th>
            <th>Tour Info</th>
            <th class="right">Duration</th>
            <th class="right">Price</th>
            <th></th>
        </tr>
        </thead>
        <tbody id="list_upcoming_tours">

        </tbody>
    </table>
</section>

<section class="tag-similar-tours" id="similar-tours">
    <section class="content">
        <h3><?= $contentCMS['tagPageTitle'] ?></h3>


        <?php foreach($similarTours as $similarTour) : ?>
            <div class="sim-tour bxs-border-box">
                <div class="tour-img">
                    <?php foreach($similarTour['images_name'] as $image) : ?>
                        <div style="background-image: url('<?= $image; ?>'); background-position: center center; background-size: cover; background-repeat: no-repeat;"></div>
                    <?php endforeach; ?>
                </div>
                <div class="tour-data">
                    <h3 class="tour-title"><a
                                href="<?php echo $similarTour['more_info'] ?>"><?php echo $similarTour['title'] ?></a>
                    </h3>
                    <p class="tour-descr"><?php echo $similarTour['description_listing'] ?></p>
                    <div class="tour-facts">
                        <div><i class="fa fa-clock-o"></i><span><?php echo $similarTour['duration'] ?></span></div>
                        <div><i class="fa fa-users"></i><span><?php echo $similarTour['group_size'] ?></span></div>

                        <?php
                        $review = isset($ratings[$similarTour['event_id']]) ? $ratings[$similarTour['event_id']] : null;
                        if(!is_null($review)) :
                            $stars = ceil($review['average']);
                            ?>
                            <div class="tour-reviews">
                                <?php if($stars == 5) : ?>
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star"></i><i class="fa fa-star"></i>
                                <?php elseif($stars == 4) : ?>
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star"></i><i class="fa fa-star-o"></i>
                                <?php elseif($stars == 3) : ?>
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
                                <?php elseif($stars == 2) : ?>
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i
                                            class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
                                <?php elseif($stars == 1) : ?>
                                    <i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i
                                            class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
                                <?php endif; ?>
                                <span><?php echo $review['amount'] ?> Reviews</span>
                            </div>
                        <?php endif; ?>

                        <div class="tour-price">
                            <?php echo ExchangeRate::convert(ceil($similarTour['adults_price']), false); ?>
                            <span>per adult</span>
                        </div>
                    </div>

                    <div class="sites-visited">
                        <div class="header">SITES VISITED:</div>
                        <div class="list">
                            <ul>
                                <?php foreach($similarTour['sites_included'] as $i => $bullet) : ?>
                                    <?php
                                    $bulletLink = '';
                                    if(strpos($bullet, '|') !== false) {
                                        $bullet = explode('|', $bullet);
                                        $bulletLink = str_replace("\n", '', $bullet[1]);
                                        $bullet = $bullet[0];
                                    }
                                    ?>
                                    <li>
                                        <i class="fa fa-check"></i>
                                        <?php echo ($bulletLink != '' ? "<a href='http://www.walksofitaly.com$bulletLink'>" : '') . (preg_replace('/^-\s*/', '', $bullet)) . ($bulletLink != '' ? "</a>" : '') ?>
                                    </li>
                                <?php endforeach; ?>

                            </ul>
                        </div>
                        <div class="btn"><a href="<?php echo $similarTour['more_info'] ?>" class="link-a"><i
                                        class="fa fa-eye"></i>VIEW TOUR</a></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </section>
</section>

<section class="content" data-scroll-target="highlights" id="highlights">
    <h3><b>HIGHLIGHTS</b> In <?= substr($contentCMS['tagPageTitle'], 0, -5); ?></h3>

    <div class="tag-tabs-wrap">
        <div class="tab-togglers">
            <?php $highlightCounter = 1; ?>
            <?php foreach($highlights as $highlight): ?>
                <div class="tag-tab-toggler <?php echo ($highlightCounter == 1) ? 'active' : ''; ?>"
                     data-tab-toggler="tab<?php echo $highlightCounter ?>">

                    <?php if(isset($highlight['image'])): ?>
                        <div class="img"
                             style="background-image: url('<?=$highlight['image'] ?>')">
                        </div>
                        <?=$highlight['title'] ?>
                    <?php else: ?>
                        <div class="img" style="background-image: url(http://placehold.it/200x200)">
                        </div>
                        Other
                    <?php endif; ?>

                </div>
                <?php $highlightCounter++ ?>
            <?php endforeach ?>
        </div>
        <?php $highlightCounter = 1; ?>
        <?php foreach($highlights as $highlight): ?>
            <div class="tag-tab-target <?= ($highlightCounter == 1) ? 'active' : ''; ?>" data-tab-target="tab<?= $highlightCounter ?>">
                <?= ContentfulWrapper::parseMarkdown($highlight['description']) ?>
            </div>
            <?php $highlightCounter++ ?>
        <?php endforeach ?>
    </div>
</section>

<section class="faq-grey" data-scroll-target="faq" id="faq">
    <section class="content">
        <h3>FAQ</h3>
        <div class="faq-questions">
            <?php for($i = 1; $i <= 100; $i++) : ?>
                <?php if(!isset($contentCMS['tagPagesFaqQuestion' . $i]))
                    break; ?>
                <div class="tag-question">
                    <div class="tag-question-title">
                        <?php echo ContentfulWrapper::parseMarkdown($contentCMS['tagPagesFaqQuestion' . $i]) ?>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                    <div class="tag-question-answer">
                        <?php echo ContentfulWrapper::parseMarkdown($contentCMS['tagPagesFaqAnswer' . $i]) ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </section>
</section>
<script type="text/javascript">
    var eventIds = [<?php echo $contentCMS['tagPageRelatedTours'] ?>];
</script>
