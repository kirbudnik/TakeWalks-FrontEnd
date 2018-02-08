<?php if ( count($relatedTours) > 0 ): ?>
<div id="tourBundle">
    <div class="title">Book Another Tour Now & Save <?php echo $discountPercentRelatedTour; ?>%! <br>(Offer valid on these tours only)</div>
    <div class="description">
        When booking this tour you’ll have the option to add any of the below to your cart.
        <br>
        Book both tours together and save <?php echo $discountPercentRelatedTour; ?>% on the second tour.
        <br>
        Offer only valid on listed tours purchased at the same time. Non-transferable.
    </div>
    <div class="tours">
        <?php
        $j = 0;
        foreach($relatedTours as $i => $relatedTour):
            $existInCart = false;
            foreach($cart as $k => $item){
                if ($relatedTour['Event']['id'] == $item['event_id']){
                    $existInCart = true;
                    break;
                }
            }
            if (intval($relatedTour['Event']['id']) == 0){
                $existInCart = true;
            }
            if ( !$existInCart &&  $j < 3 ) :
                $j++;
            ?>
            <div class="tour">
                <div class="image">
                    <div class="triangle-price"></div>
                    <div class="discount">
                        <div class="save">SAVE</div>
                        <div class="discountPercent"><?php echo $discountPercentRelatedTour; ?>%</div>
                    </div>
                    <?php
                    //apply discount
                    $discount = 1 - ($discountPercentRelatedTour / 100);
                    $adultsPrice = $relatedTour['Event']['adults_price'];
                    $adultsPriceDiscount = round($adultsPrice * $discount, 2);
                    //pick image
                    $imageUrl = 'http://placehold.it/300x300';
                    $imageAlt = '';
                    foreach($relatedTour['Event']['EventsImage'] as $image) {
                        if($image['publish']) {
                            $imageUrl = $image['images_name'];
                            $imageAlt = $image['alt_tag'];
                            break;
                        }
                    }
                    ?>
                    <img src="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $imageUrl); ?>" alt="<?php echo $imageAlt; ?>">
                </div>
                <div class="name"><?php echo $relatedTour['Event']['name_listing']; ?></div>
                <div class="oldPrice price-a"><?php echo ExchangeRate::convert($adultsPrice) ?></div>
                <div class="options">
                    <a class="link-a more-info" href="<?php echo "/{$relatedTour['Event']['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$relatedTour['Event']['url_name']}" ?>"
                       data-position="<?php echo $j ?>"
                       data-name="<?php echo $relatedTour['Event']['name_listing'] != '' ? $relatedTour['Event']['name_listing'] : $relatedTour['Event']['name_short'] ?>"
                       data-id="<?php echo $relatedTour['Event']['id'] ?>"
                       data-sku="<?php echo $relatedTour['Event']['sku'] ?>"
                       data-description="Clicked bundle tour <?php echo $j ?>"
                       data-href="<?php echo "/{$relatedTour['Event']['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$relatedTour['Event']['url_name']}" ?>"
                       data-price="<?php echo $adultsPriceDiscount ?>"
                       onclick="WrapperGA.ecTrackClickUI(event, this, true);"
                    >
                        <i class="fa fa-info-circle"></i>More Info
                    </a>
                    <div class="newPrice price-a"><div class="from">from</div>
                        <?php echo ExchangeRate::convert($adultsPriceDiscount); ?>
                    </div>
                </div>
            </div>
        <?php endif; endforeach; ?>
    </div>

</div>
<?php endif; ?>