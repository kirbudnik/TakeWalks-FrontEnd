<!DOCTYPE html>
<!--[if lt IE 7]>      <html lt-ie9 lt-ie8 lt-ie7" lang="en" class="<?php echo $this->request->action ?>"> <![endif]-->
<!--[if IE 7]>         <html lt-ie9 lt-ie8" lang="en" class="<?php echo $this->request->action ?>"> <![endif]-->
<!--[if IE 8]>         <html lt-ie9" lang="en" class="<?php echo $this->request->action ?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" class="<?php echo $this->request->action ?>"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $layoutTitle ?></title>

    <?php if (isset($meta_description)) { ?>
        <meta name="description" content="<?php echo htmlentities($meta_description) ?>">
    <?php } else { ?>
        <meta name="description" content="Relax with friends. Go local. Get the inside scoop. For a more authentic experience of the Big Apple, take walks.">
    <?php } ?>



    <link href="https://plus.google.com/+Walksofnewyork" rel=publisher />
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <?php echo $this->Html->script('/theme/Italy/js/lib/head.js') ?>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/lib/slick.css">
    <?php echo $this->Html->css('global.css', array('media' => 'screen')) ?>
    <?php echo $this->Html->css('lib/select2.css', array('media' => 'screen')) ?>
    <link rel="stylesheet" href="/css/lib/royalslider.css">
    <link rel="stylesheet" href="/css/lib/royalslider.white.css">
    <?php if (isset($css)) {
        foreach ($css as $filename) { ?>
            <link rel="stylesheet" href="/theme/nyc/css/<?php echo $filename ?>.css?v=<?php echo $version ?>">
        <?php }
    } ?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
    <script src="/js/lib/slick.min.js"></script>
    <script src="/js/lib/jquery.royalslider.9.4.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="/js/lib/jquery.jscroll.min.js"></script>
    <script src="/js/lib/resrc-it.min.js?v=<?php echo $version ?>"></script>
    <script src="https://f.vimeocdn.com/js/froogaloop2.min.js"></script>
    <?php echo $this->Html->script('lib/scripts.js') ?>
    <?php echo $this->Html->script('lib/mobile.js') ?>
    <?php echo $this->Html->script('lib/select2.js') ?>
    <?php echo $this->Html->script('global.js') ?>

    <script>
        var initValues = JSON.parse('<?php echo json_encode(isset($initValues) ? $initValues : array()) ?>');
    </script>
    <?php if (isset($js)) {
        foreach ($js as $filename) { ?>
            <?php echo $this->Html->script($filename . '.js') ?>
        <?php }
    } ?>

    <script src="/js/lib/instafeed.min.js"></script>

    <!--[if lt IE 9]><script src="/js/lib/rem.min.js"></script><![endif]-->
    <!--[if lt IE 9]><script src="/js/lib/html5shiv.js"></script><![endif]-->
    <!--[if lt IE 9]><script src="/js/lib/selectivizr.min.js"></script><![endif]-->

    <?php echo isset($canonicalURL) ? "<link rel=\"canonical\"  href=\"$canonicalURL\" />" : ''; ?>

    <script src="//cdn.optimizely.com/js/596251622.js"></script>

    <?php echo $this->fetch('headBottom'); ?>
    <?php
    $ecTheme = 'nyc';
    $devGA ='UA-23373029-11';
    echo $this->element('google_analytics', array(
        'ecTheme' => $ecTheme,
        'analyticsAccount' => (Configure::read('debug') == 0) ? 'UA-23373029-6' : $devGA,
        'transaction' => isset($transaction) ? $transaction : array( 'success' => 0 )
    ));
    ?>

</head>

<body>
<?php
echo $this->element('google_tag_manager',array(
    'account' => 'GTM-N5Q9B8'
));
?>
<div id="root">
<?php echo $this->Session->flash(); ?>
<?php echo $this->element('header') ?>
<?php echo $this->fetch('content') ?>
<?php
if (CakeSession::check('promoCodeUrlShadowBox')) {
    echo $this->element('Pages/detail/promo-url-shadow-box');
}
?>
<?php echo $this->element('footer') ?>
</div>
<?php echo $this->element('bing_tracking_wony'); ?>
</body>
</html>
<?php echo $this->element('tapfiliate/landing_page'); ?>