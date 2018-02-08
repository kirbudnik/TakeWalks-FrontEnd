<?php ?>

<div class="hidden-reviews-container">
<div class="hidden-review-column">
    <?php
    $i = 0;
    $j = 0;
    foreach ($reviews as $review) :
        ?>
        <?php
        $stars = $review['event_rating'];
        $i++;
        $j++;
        ?>
        <div class="hidden-review">
            <div class="hidden-review-body">
    <?php echo ($review['feedback_text']) ? : '<i>(No comment)</i>'; ?>
            </div>
            <div class="hidden-review-footer">
                <p><?php echo $review['first_name'] ?></p>
                <div class="stars">
                    <?php if ($stars == 5) { ?>
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                    <?php } else if ($stars == 4) { ?>
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i>
                    <?php } else if ($stars == 3) { ?>
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
                    <?php } else if ($stars == 2) { ?>
                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
                    <?php } else if ($stars == 1) { ?>
                        <i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
                    <?php } ?>
                </div>
                <p class="review-date"><?php $fdate = explode(" ", $review['feedback_date']);
                    echo $fdate[0];
                    ?></p>
            </div>
        </div>
        <?php
        if ($j == 2) {
            echo '</div><!-- column --><div class="hidden-review-column">';
            $j = 0;
        }
        ?>

<?php endforeach; ?>
</div><!-- column -->
</div>
<?php 
if ($i > 0) { 
    echo '<a href="/eventrewiewshtml?e='.intval($events_id).'&p='.(intval($pagination) + 1).'"></a>';
} 
?>

