<section class="blog wrap content">
    <h2 class="larger">
        <span>Latest Blog Posts</span>
        <a href="<?php echo $theme->blogUrl ?>" class="medium more">View all blog posts</a>
    </h2>
    <ul>
        <?php foreach ($blog_posts as $post) { ?>
            <li class="clearfix">
                <a href="<?php echo $theme->blogUrl . '/' . $post['wp']['post_name'] ?>">
                    <img src="<?php echo $theme->blogUrl . $post['thumbnail'] ?>" alt="<?php echo $post['wp']['post_title'] ?>">
                    <span class="date"><?php echo date('M j, Y', strtotime($post['wp']['post_date'])) ?></span>
                    <h4 class="larger"><?php echo $post['wp']['post_title'] ?></h4>
                    <p class="small serif"><?php echo $post['summary'] ?></p>
                    <span class="readmore"></span>
                </a>
            </li>
        <?php } ?>
    </ul>
</section>
