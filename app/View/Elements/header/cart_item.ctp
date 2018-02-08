<li class="clearfix">
    <a class="close">✕</a>
    <p class="confirmRemove">
        Are you sure you want to delete this activity?
        <a class="button grey small cancel">Cancel</a>
        <a class="button red small" href="/remove_from_cart/<?php echo $n?>">Delete</a>
    </p>
    <div>
        <img src="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $item['image']) ?>" alt="">
        <span class="price"><?php echo ExchangeRate::convert($item['total_price']) ?></span>
    </div>
    <div>
        <?php if($item['type'] == 'private'):?>
            <span class="small">Private Tour</span>
        <?php endif; ?>
        <span class="small"><?php echo date('F j, h:ia', strtotime($item['datetime'])) ?></span>
        <strong><?php echo $item['name'] ?></strong>
        <ul class="small">
            <?php foreach(array('adults', 'seniors', 'students', 'children', 'infants') as $type) : ?>
                <?php if($item[$type]) : ?>
                    <li><strong><?php echo $item[$type] ?></strong> <?php echo ucfirst(__n(Inflector::singularize($type), $type, $item[$type])) ?></li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
    </div>
</li>