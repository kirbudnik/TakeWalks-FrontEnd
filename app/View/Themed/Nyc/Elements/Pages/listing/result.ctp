<li class="clearfix">
    <a data-position="<?php echo $itemPosition ?>" 
       data-name="<?php echo $event['Event']['name_short'] ?>" 
       data-id="<?php echo $event['Event']['id'] ?>" 
       data-href="<?php echo "/{$theme->city_slug}-tours/{$event['Event']['url_name']}" ?>" 
       onclick="ecOnProductClick(event, this); return !ga.loaded;"
       href="<?php echo "/{$theme->city_slug}-tours/{$event['Event']['url_name']}" ?>">

        <div class="tile">
            <div class="image" style="background-image: url('https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $event['EventsImage'][0]['images_name']); ?>');"></div>
            <?php if($event['Event']['is_on_sale'] == 1): ?><span class="sale">Sale</span><?php endif; ?>
            <ul class="categories">
                <?php foreach($event['Tag'] as $tag) : ?>
                    <li class="<?php echo $tag['url_name'] ?>"></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <h3 class="large"><?php echo $event['Event']['name_short'] ?></h3>
        <p class="description serif"><?php echo $event['Event']['description_short'] ?></p>
        <?php if(isset($event['Event']['is_on_sale']) && $event['Event']['is_on_sale'] == 1): ?><p class="notice sale">Sale</p><?php endif;?>
        <?php if(isset($event['Event']['is_sellout']) && $event['Event']['is_sellout'] == 1): ?><p class="notice sellout">Likely to sell out</p> <?php endif; ?>
        <ul class="meta serif">
            <li class="price"> from
                <span class="large"><?php echo ExchangeRate::convert($event['Event']['adults_price']) ?>
                    <?php if($event['Event']['is_on_sale'] == 1): ?>
<!--                    <del class="smalll"><?php echo number_format($event['Event']['adults_price'] + 10, 2) ?></del>-->
                    <del class="small"><?php echo ExchangeRate::convert($event['Event']['adults_price'] + ExchangeRate::convert(10,0,0,'USD')) ?></del>
                    <?php endif; ?>
                </span>
            </li>
            <li class="reviews">
                <span class="stars">
                    <img src="../img/star-full.png" alt="3 out of 5 stars">
                    <img src="../img/star-full.png" alt="">
                    <img src="../img/star-full.png" alt="">
                    <img src="../img/star-full.png" alt="">
                    <img src="../img/star-full.png" alt="">
                </span>
            </li>
            <li class=""><span class="blue button book">More Info</span></li>

            <li class="duration"><?php echo $this->Event->formatDuration($event['Event']['duration']) ?></li>

            <?php foreach($event['Tag'] as $tag) : ?>
                <li class="<?php echo $tag['url_name'] ?>"><?php echo $tag['name'] ?></li>
            <?php endforeach; ?>
        </ul>
    </a>
</li>