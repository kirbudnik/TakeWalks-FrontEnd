<section class="instagram wrap content">
    <h2 class="larger">
        <span>Latest Instagram Photos</span>
        <a href="http://instagram.com/walksofnewyork" class="medium more">View all photos</a>
    </h2>
    <ul id="instagram">
        <?php
        $countPhoto = 0;
        foreach($instagram as $photo):
            if ($instagramCount == $countPhoto) {break;}
            $countPhoto++;
            ?>
            <li>
                <a href="<?php echo $photo->display_src ?>"  target="_blank">
                    <img src="<?php echo $photo->thumbnail_src ?>" /><span class="viewphoto"></span>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</section>
<script type="text/javascript" src="/js/lib/instafeed.min.js"></script>
<script type="text/javascript">
    $(document).on('ready', function() {
//        var feed = new Instafeed({
//            get: 'user',
//            userId: 419613846,
//            accessToken: '419613846.467ede5.2c30880dc6124ae789bd25e2d56eca52',
//            target: 'instagram',
//            clientId: 'dcdfdf8445c4415a9a621a6b96051217',
//            template: '<li><a href="{{link}}"><img src="{{image}}" /><span class="viewphoto"></span></a></li>',
//            limit: 7
//        });
//        feed.run();
    });
</script>
