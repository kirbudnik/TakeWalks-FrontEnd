<article class="<?php if(false && $event['Event']['is_on_sale'] == 1) echo 'sale' ?>" data-id="<?php echo $event['Event']['id'] ?>">
    <a data-position="<?php echo $itemPosition ?>" 
       data-name="<?php echo $event['Event']['name_long'] ?>" 
       data-id="<?php echo $event['Event']['id'] ?>" 
       data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>" 
       onclick="ecOnProductClick(event, this); return !ga.loaded;"
       href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
        <?php
            $imageUrl = '';
            foreach($event['EventsImage'] as $image) {
                if($image['listing']) {
                    $imageUrl = $image['images_name'];
                    break;
                }
            }
        ?>
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
       data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>" 
       onclick="ecOnProductClick(event, this); return !ga.loaded;"
       href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
        <h2><?php echo $event['Event']['name_long'] ?></h2>
    </a>

    <p><?php


 echo $this->Text->truncate($event['Event']['description_listing'], 200) ?></p>
    <span class="rating-a rating <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['letterClass'] : ''?>" style="<?php echo !isset($ratings[$event['Event']['id']]) ? 'display:none;' : ''?>">
        From
        <a data-position="<?php echo $itemPosition ?>" 
            data-name="<?php echo $event['Event']['name_long'] ?>" 
            data-id="<?php echo $event['Event']['id'] ?>" 
            data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}#reviews" ?>" 
            onclick="ecOnProductClick(event, this); return !ga.loaded;"
            href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
            <span class="num_reviews">
                <?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['amount'] : ''?>
            </span>
            reviews
        </a>
    </span>
    <span class="price-a">
        <?php echo ExchangeRate::convert($event['Event']['adults_price'] ?: $event['Event']['private_base_price']) ?>
    </span>
    <a class="link-a" 
       data-position="<?php echo $itemPosition ?>" 
       data-name="<?php echo $event['Event']['name_long'] ?>" 
       data-id="<?php echo $event['Event']['id'] ?>" 
       data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>" 
       onclick="ecOnProductClick(event, this); return !ga.loaded;"
       href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
        <i class="fa fa-info-circle"></i> More Info
    </a>
</article>
