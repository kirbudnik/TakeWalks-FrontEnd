<a href="<?php echo "/{$theme->city_slug}-tours/{$featured['Event']['url_name']}" ?>">
    <span>
        <h3 class="larger"><?php echo $featured['Event']['name_short'] ?></h3>
        <ul class="clearfix">
            <li class="stars">
                <img src="/theme/nyc/img/star-full.png" alt="5 out of 5 stars">
                <img src="/theme/nyc/img/star-full.png" alt="">
                <img src="/theme/nyc/img/star-full.png" alt="">
                <img src="/theme/nyc/img/star-full.png" alt="">
                <img src="/theme/nyc/img/star-full.png" alt="">
            </li>
            <li class="price large"><?php echo ExchangeRate::convert($featured['Event']['adults_price']) ?></li>
        </ul>
    </span>
</a>