<?= $this->element('header'); ?>


<div class="tour-detail-content">
    <ul class="breadcrumbs">
        <li><a href="/">Take Walks</a></li>
        <li><a href="/<?= $citySlug ?>"><?= $cityName ?> Tours</a></li>
        <li><a href="/<?= $slug ?>"><?= $tagName ?></a></li>
        <li class="active"><a href="#">Compare Tours</a></li>
    </ul>


    <div class="tour-detail-top">
        <div class="left-content">
            <div class="tour-heading">
                <h2 class="tour-title"><a href="/<?=$slug ?>"><i class="icon icon-arrow_left"></i></a>Compare Tours</h2>
            </div>
        </div>
    </div>
    <a href="/<?=$slug ?>"><i class="icon icon-close compare-tours-go-back" data-modal-close></i></a>
    <div class="compare-tours-header">
        <div class="container">

            <div class="compare-row">
                <div class="compare-row-title big">Tour</div>
                <?php foreach ($tours as $tour): ?>
                    <div class="compare-row-item image">
                        <img src="<?=$tour['image'] ?>?w=500" alt="">
                        <h3 class="tour-title"><?= $tour['title'] ?></h3>
                        <div>
                            <a href="<?="/{$tour['citySlug']}/{$tour['slug']}" ?>" class="btn secondary purple lcased">Read More</a>
                        </div>
                        <div>
                            <?php if(false): ?>
                                <a href="#" class="towishlist">
                                    <i class="icon icon-star"></i>
                                    Add to Wishlist
                                </a>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>

            </div>
        </div>
    </div>

    <div class="compare-tours-body">
        <div class="compare-row">
            <div class="container">
                <div class="compare-row-title">Who It's For</div>
                <?php foreach ($tours as $tour): ?>
                    <div class="compare-row-item text-center"><p class="descr"><?= $tour['whoFor'] ?></p></div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="compare-row">
            <div class="container">
                <div class="compare-row-title">Who It's Not For</div>
                <?php foreach ($tours as $tour): ?>
                    <div class="compare-row-item text-center"><p class="descr"><?= $tour['whoNotFor'] ?></p></div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="compare-row inline">
            <div class="container">
                <div class="compare-row-title">Adult Price</div>
                <?php foreach ($tours as $tour): ?>
                    <div class="compare-row-item"><?= ExchangeRate::convert($tour['price']) ?></div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="compare-row inline">
            <div class="container">
                <div class="compare-row-title">Rating</div>
                <?php foreach($tours as $tour): ?>
                    <div class="compare-row-item">
                        <div class="tour-box-reviews">
                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 1 ? '_active' : '' ?>"></i>
                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 2 ? '_active' : '' ?>"></i>
                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 3 ? '_active' : '' ?>"></i>
                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 4 ? '_active' : '' ?>"></i>
                            <i class="icon icon-star<?= $tour['reviewsAverage'] >= 4.7 ? '_active' : '' ?>"></i>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="compare-row inline">
            <div class="container">
                <div class="compare-row-title">Departs At</div>
                <?php foreach ($tours as $tour): ?>
                    <div class="compare-row-item"><?= $tour['startTime'] ?></div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="compare-row inline">
            <div class="container">
                <div class="compare-row-title">Duration</div>
                <?php foreach ($tours as $tour): ?>
                    <div class="compare-row-item"><?= $tour['duration'] ?></div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="compare-row inline">
            <div class="container">
                <div class="compare-row-title">Group Size</div>
                <?php foreach ($tours as $tour): ?>
                    <div class="compare-row-item"><?= $tour['groupSize'] ?></div>
                <?php endforeach ?>
            </div>
        </div>
        <?php $firstRow = true; ?>
        <?php foreach($content['sitesVisited'] as $site): ?>
            <div class="compare-row inline">
                <div class="container">
                    <div class="compare-row-title"><?= $firstRow ? 'Sites Visited' : '' ?></div>
                    <?php foreach ($tours as $tour): ?>
                        <?php if(in_array($site, $tour['sitesVisited'])): ?>
                            <div class="compare-row-item"><?=$site ?></div>
                        <?php else: ?>
                            <div class="compare-row-item"><i class="icon icon-close"></i></div>
                        <?php endif ?>
                    <?php endforeach ?>
                </div>
            </div>
            <?php $firstRow = false; ?>
        <?php endforeach ?>

        <div class="compare-row inline">
            <div class="container">
                <div class="compare-row-title"></div>
                <?php foreach($tours as $tour): ?>
                    <div class="compare-row-item">
                        <a href="<?="/{$tour['citySlug']}/{$tour['slug']}" ?>" class="btn secondary purple lcased">Book Now</a>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

    </div>
</div>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
