<?php if (!empty($images)) { ?>
    <div class="royalSlider heroSlider rsMinW" style="overflow:hidden;height:300px;margin-bottom:20px">

        <?php foreach($images as $image): ?>
            <?php if($image['primary'] == 0 && $image['listing'] == 0 && $image['feature'] == 0): ?>
                <img class="resrc"
                     src="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W600/c=AR600x400/https:', $image['images_name']) ?>"
                     data-rsTmb="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/c=AR200x150/https:', $image['images_name']) ?>"
                     alt="<?php echo $image['label']; ?>"/>
            <?php endif ?>
        <?php endforeach ?>

    </div>

<?php } ?>

<div class="clear"></div>
