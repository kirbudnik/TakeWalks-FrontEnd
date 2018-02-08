<?php foreach ($reviews as $review) : ?>
    <div class="review">
        <div class="comment"><?=$review['comment_stuff_edited'] ? $review['comment_stuff_edited'] : '<i>(No comment)</i>' ?></div>
        <div class="name"><?=$review['first_name'] ?></div>
        <div class="stars">
            <?php for($i=1;$i<=5;$i++): ?>
                <i class="icon icon-star<?=$i <= $review['event_rating'] ? '_active' : '' ?>"></i>
            <?php endfor ?>
        </div>
        <div class="date">
            <?= date('m-d-Y',strtotime($review['feedback_date'])); ?>
        </div>
    </div>
<?php endforeach; ?>
