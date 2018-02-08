<div>
    <?php if(isset($n)): ?>
        <a data-position="<?php echo $n ?>" 
            data-price="<?php echo $item['total_price']  ?>" 
            data-name="<?php echo $item['name'] ?>" 
            data-id="<?php echo $item['event_id'] ?>" 
            data-href="/remove_from_cart/<?php echo $n ?>" 
            onclick="ecRemoveFromCart(this);"
            href="#" class="close">✕</a>
    <?php endif; ?>
    <h3 class="large"><?php echo $item['name'] ?></h3>
    <?php if($item['type'] == 'private'):?>
        <span class="small">Private Tour</span>
    <?php endif; ?>
    <p class="timeslot"><?php echo date('F j, Y', strtotime($item['datetime'])) ?> &mdash; <?php echo date('h:ia', strtotime($item['datetime'])) ?></p>

    <ul class="ticketsSummary">
        <?php foreach(array('adults', 'seniors', 'students', 'children', 'infants') as $type) : ?>
            <?php if($item[$type]) : ?>
                <?php $price = $item[$type . '_price_converted_' . $currency]; ?>
                <li>
                    <strong><?php echo $item[$type] ?></strong> <?php echo ucfirst(__n(Inflector::singularize($type), $type, $item[$type])) ?>
                    <span class="<?php echo $price ? '' : 'free' ?>">
                        <?php echo $price > 0 ? ExchangeRate::format($price, $currency) : 'FREE' ?>
                    </span>
                </li>
            <?php endif ?>
        <?php endforeach ?>

        <?php if($item['discount_bundle_tour_percent'] != 0 ): ?>
            <li>
                <strong style="text-decoration: line-through">Original price</strong>
                <span style="text-decoration: line-through"><?php echo ExchangeRate::format($item['subtotal_converted'], $currency) ?></span>
            </li>
            <li>
                <strong>Discount</strong>
                <span> - <?php
                    $discount_bundle = $item['subtotal_converted'] * $item['discount_bundle_tour_percent'] / 100;
                    $subtotal_converted = $item['subtotal_converted'] - $discount_bundle;
                    echo ExchangeRate::format($discount_bundle, $currency) ?></span>
            </li>
            <?php if(isset($item['promo_local'])): ?>
                <li>
                    <strong>Discount promo code</strong>
                    <span> - <?php
                        $discount_promo = 0;
                        if ( isset($item['promo_discount_fixed']) || isset($item['promo_discount_fixed_by_event']) ){
                            $discount_promo = $item['promo_discount_fixed_'.$currency];
                        } else if ( isset($item['promo_discount_percentage']) ){
                            $discount_promo = $subtotal_converted * $item['promo_discount_percentage'] / 100;
                        }
                        $subtotal_converted = $subtotal_converted - $discount_promo;
                        echo ExchangeRate::format($discount_promo, $currency)
                        ?></span>
                </li>
            <?php endif ?>
            <li>
                <strong>Subtotal</strong>
                <span><?php echo ExchangeRate::format($subtotal_converted, $currency) ?></span>
            </li>
        <?php else : ?>
            <?php if(isset($item['promo_local'])): ?>
                <li>
                    <strong style="text-decoration: line-through">Original price</strong>
                    <span style="text-decoration: line-through"><?php echo ExchangeRate::format($item['subtotal_converted'], $currency) ?></span>
                </li>
                <li>
                    <strong>Discount promo code</strong>
                    <span> - <?php
                        $discount_promo = 0;
                        if ( isset($item['promo_discount_fixed']) || isset($item['promo_discount_fixed_by_event']) ){
                            $discount_promo = $item['promo_discount_fixed_'.$currency];
                        } else if ( isset($item['promo_discount_percentage']) ){
                            $discount_promo = $item['subtotal_converted'] * $item['promo_discount_percentage'] / 100;
                        }
                        $subtotal_converted = $item['subtotal_converted'] - $discount_promo;
                        echo ExchangeRate::format($discount_promo, $currency) ?></span>
                </li>
                <li>
                    <strong>Subtotal</strong>
                    <span><?php echo ExchangeRate::format($subtotal_converted, $currency) ?></span>
                </li>
            <?php else : ?>
                <li>
                    <strong>Subtotal</strong>
                    <span><?php echo ExchangeRate::format($item['subtotal_converted'], $currency) ?></span>
                </li>
            <?php endif ?>
        <?php endif; ?>
    </ul>
</div>
 