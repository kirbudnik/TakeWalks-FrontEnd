<?php
Class WpPost extends AppModel{
    var $name = 'WpPost';
    var $useDbConfig = 'blog';

    public $hasMany = array(
        'WpPostmeta' => array(
            'className'    => 'WpPostmeta',
            'foreignKey'   => 'post_id',
            'dependent' => true
        )
    );


    public function getFeaturedPosts($num_posts = 3) {
        // Check cache
        $cache_key = 'recent_blog_posts_'.date('Ymd');
        $check = Cache::read($cache_key);
        if ($check) return $check;


        $dbconfig = new DATABASE_CONFIG();
        if (!isset($dbconfig->blog)) {
            return array();
        }

        $posts = $this->query("
			select wp.post_title, wp.post_date, wp.post_content, wp.post_name, wpm2.meta_value, wpt2.slug
			from wp_posts wp, wp_postmeta wpm, wp_postmeta wpm2, wp_terms wpt, wp_term_taxonomy wptt, wp_term_relationships wptr, wp_terms wpt2, wp_term_taxonomy wptt2, wp_term_relationships wptr2
			where wp.post_status = 'publish'
			and wp.post_type = 'post'
			and wp.ID = wpm.post_id
			and wpm.meta_key = '_thumbnail_id'
			and wpm.meta_value = wpm2.post_id
			and wpm2.meta_key = '_wp_attachment_metadata'
			and wpt.name = 'featured'
			and wptt.term_id = wpt.term_id
			and wptr.term_taxonomy_id= wptt.term_taxonomy_id
			and wp.ID = wptr.object_id
			and wptt2.taxonomy = 'category'
			and wptt2.term_id = wpt2.term_id
			and wptr2.term_taxonomy_id= wptt2.term_taxonomy_id
			and wp.ID = wptr2.object_id
			group by wp.ID
			order by post_date desc
			limit $num_posts
		");

        foreach ($posts as &$post) {
            $meta_data = unserialize($post['wpm2']['meta_value']);
            $post['thumbnail'] = '/wp-content/uploads/' . preg_replace('/[^\/]*$/', '', $meta_data['file']) . $meta_data['sizes']['thumbnail']['file'];

            $desc = preg_replace('/\[.*?\]/', '', strip_tags(mb_convert_encoding($post['wp']['post_content'], 'HTML-ENTITIES', 'UTF-8')));
            $desc = str_replace(
                array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                array("'", "'", '"', '"', '-', '--', '...'),
                $desc);
            $desc = preg_replace('/\s+\S+$/', '', substr($desc, 0, 200)) . ($desc > 200 ? '...' : '');
            $post['summary'] = $desc;
        }
        unset($post);

        // Write cache
        Cache::write($cache_key, $posts);

        return $posts;
    }

    public function getRecentPosts($num_posts = 2) {
        // Check cache
        $cache_key = 'recent_blog_posts_'.date('Ymd');
        $check = Cache::read($cache_key);
        if ($check) return $check;


        $dbconfig = new DATABASE_CONFIG();
        if (!isset($dbconfig->blog)) {
            return array();
        }

        $posts = $this->query("
			select wp.post_title, wp.post_date, wp.post_content, wp.post_name, wpm2.meta_value
			from wp_posts wp, wp_postmeta wpm, wp_postmeta wpm2
			where wp.post_status = 'publish'
			and wp.post_type = 'post'
			and wp.ID = wpm.post_id
			and wpm.meta_key = '_thumbnail_id'
			and wpm.meta_value = wpm2.post_id
			and wpm2.meta_key = '_wp_attachment_metadata'
			order by post_date desc
			limit $num_posts
		");

        foreach ($posts as &$post) {
            $meta_data = unserialize($post['wpm2']['meta_value']);
            $post['thumbnail'] = '/wp-content/uploads/' . preg_replace('/[^\/]*$/', '', $meta_data['file']) . $meta_data['sizes']['thumbnail']['file'];

            $desc = preg_replace('/\[.*?\]/', '', strip_tags(mb_convert_encoding($post['wp']['post_content'], 'HTML-ENTITIES', 'UTF-8')));
            $desc = str_replace(
                array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                array("'", "'", '"', '"', '-', '--', '...'),
                $desc);
            $desc = preg_replace('/\s+\S+$/', '', substr($desc, 0, 200)) . ($desc > 200 ? '...' : '');
            $post['summary'] = $desc;
        }
        unset($post);

        // Write cache
        Cache::write($cache_key, $posts);
        return $posts;
    }
}
?>
