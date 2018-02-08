<article class="<?php if(false && $event['Event']['is_on_sale'] == 1) echo 'sale' ?>" data-id="<?php echo $event['Event']['id'] ?>">
    <a data-position="<?php echo $itemPosition ?>" 
       data-name="<?php echo $event['Event']['name_long'] ?>" 
       data-id="<?php echo $event['Event']['id'] ?>"
       data-sku="<?php echo $event['Event']['sku'] ?>"
       data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>" 
       onclick="WrapperGA.ecOnProductClick(event, this); "
       href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
        <?php
            $imageUrl = '';
            foreach($event['EventsImage'] as $image) {
                if($image['listing']) {
                    $imageUrl = $image['images_name'];
                    break;
                }
            }

        $showDiscount = false;
        $existRelatedInCart = false;
        $sameInCart = false;
        $type = ($event['Event']['group_private'] == 'Private') ? 'private' : 'group';
        foreach($cart as $i => $item){
            $itemsRelated = $item['related'];
            if($item['event_id'] == $event['Event']['id']){
                $sameInCart = true;
            }
            if (in_array($event['Event']['id'], $itemsRelated)){
                $existRelatedInCart = true;
            }
        }
        if ( $existRelatedInCart && $type == 'group' && !$sameInCart) {
            $showDiscount = true;
        }
        if ( $showDiscount ) :

        //apply discount
        $discount = 1 - ($discountPercentRelatedTour / 100);
        $adultsPrice = $event['Event']['adults_price'];
        $adultsPriceDiscount = round($adultsPrice * $discount, 2);

        ?>

        <div class="tour">
            <div class="image">
                <div class="triangle-price"></div>
                <div class="discount">
                    <div class="save">SAVE</div>
                    <div class="discountPercent"><?php echo $discountPercentRelatedTour; ?>%</div>
                </div>
            </div>
        </div>
        <?php endif; ?>


        <figure>
            <div style="width: 180px;
                        height: 180px;
                        background-image: url('https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $imageUrl); ?>');
                        background-size: cover;
                        background-repeat: no-repeat;
                        background-position: 50% 50%;"></div>
        </figure>
    </a>

    <a data-position="<?php echo $itemPosition ?>" 
       data-name="<?php echo $event['Event']['name_long'] ?>"
       data-id="<?php echo $event['Event']['id'] ?>"
       data-sku="<?php echo $event['Event']['sku'] ?>"
       data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>"
       onclick="WrapperGA.ecOnProductClick(event, this); "
       href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
        <h2><?php echo $event['Event']['name_long'] ?></h2>
    </a>

    <p><?php echo $this->Text->truncate($event['Event']['description_listing'], 200) ?></p>
    <span class="rating-a rating <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['letterClass'] : ''?>" style="<?php echo !isset($ratings[$event['Event']['id']]) ? 'display:none;' : ''?>">
        From
        <a data-position="<?php echo $itemPosition ?>" 
            data-name="<?php echo $event['Event']['name_long'] ?>" 
            data-id="<?php echo $event['Event']['id'] ?>"
            data-sku="<?php echo $event['Event']['sku'] ?>"
            data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}#reviews" ?>" 
            onclick="WrapperGA.ecOnProductClick(event, this); "
            href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
            <span class="num_reviews">
                <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['amount'] : ''?>
            </span>
            reviews
        </a>
    </span>
    <?php if($event['Event']['charity_id']):?>
        <div class="positive-steps-logo">
            <div class="positive-steps-info">
                This tour is part of our sustainable 'Positive Steps' program, so for every tour sold we donate to a local non-profit.
            </div>
            <img src="/theme/Italy/img/positive-steps-logo.png" alt="Positive steps" />
        </div>

    <?php endif ?>
    <?php if ( $showDiscount ) : ?>
    <div class="discount-visit">Save NOW with the item in your cart.</div>
    <?php endif; ?>
    <span class="price-a">
    <?php if ( !$showDiscount ) : ?>
        <span></span> <?php echo ExchangeRate::convert($event['Event']['adults_price'] ?: $event['Event']['private_base_price']) ?>
    <?php else : ?>
        <span class="listing-discount">
            <span class="oldPrice"><?php echo ExchangeRate::convert($adultsPrice); ?></span><br>
            <span class="newPrice"><?php echo ExchangeRate::convert($adultsPriceDiscount); ?></span><br>
        </span>
    <?php endif; ?>
    </span>

    <a class="link-a"
       data-position="<?php echo $itemPosition ?>" 
       data-name="<?php echo $event['Event']['name_long'] ?>" 
       data-id="<?php echo $event['Event']['id'] ?>"
       data-sku="<?php echo $event['Event']['sku'] ?>"
       data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>" 
       onclick="WrapperGA.ecOnProductClick(event, this); "
       href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
        <i class="fa fa-info-circle"></i> More Info
    </a>
</article>
