<!doctype html>
<html lang="en" class="<?php echo $this->request->action ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo $layoutTitle ?></title>
    <?php if(!empty($meta_description)): ?>
        <meta name="description" content="<?php echo $meta_description ?>">
    <?php endif ?>
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="320">
    <meta id="viewport" name="viewport" content="width=device-width, initial-scale=1">
    <?php echo $this->Html->script('/theme/Italy/js/lib/head.js') ?>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/lib/slick.css">

    <?php if(isset($isFrance)): ?>
        <?php echo $this->Html->css('global_france.css', array('media' => 'screen')) ?>
    <?php else: ?>
        <?php echo $this->Html->css('global.css', array('media' => 'screen')) ?>
    <?php endif ?>
    <?php echo $this->Html->css('lib/select2.css', array('media' => 'screen')) ?>
    <?php echo $this->Html->css('print.css', array('media' => 'print')) ?>
    <link rel="stylesheet" href="/css/lib/royalslider.css">
    <link rel="stylesheet" href="/css/lib/royalslider.white.css">
    <?php if (isset($css)) {
        foreach ($css as $filename) { ?>
            <link rel="stylesheet" href="/theme/Italy/css/<?php echo $filename ?>.css?v=<?php echo $version ?>">
        <?php }
    } ?>

    <?php if(isset($featuredImgUrl) && $featuredImgUrl): ?>
        <meta property="og:image" content="<?php echo $featuredImgUrl ?>"/>
    <?php endif ?>

    <?php $initValues = str_replace('\\', '\\\\', json_encode(isset($initValues) ? $initValues : array())); ?>
    <script>var initValues = JSON.parse( '<?php echo $initValues  ?>' );</script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


    <link rel="icon" type="image/x-icon" href="<?php echo FULL_BASE_URL ?>/theme/Italy/faviconItaly.ico">
    <link href="https://plus.google.com/+WalksofitalyTours" rel=publisher />

    <?php echo isset($canonicalURL) ? "<link rel=\"canonical\"  href=\"$canonicalURL\" />" : ''; ?>

    <script src="//cdn.optimizely.com/js/596251622.js"></script>

    <?php echo $this->fetch('headBottom'); ?>

    <?php
    $ecTheme = 'Italy';
    $devGA ='UA-23373029-10';
    echo $this->element('google_analytics', array(
        'ecTheme' => $ecTheme,
        'analyticsAccount' => (Configure::read('debug') == 0) ? 'UA-23373029-1' : $devGA,
        'transaction' => isset($transaction) ? $transaction : array( 'success' => 0 )
    ));

    echo $this->element('criteo_onetag', [
        'ecTheme' => $ecTheme,
        'transaction' => isset($transaction) ? $transaction : array( 'success' => 0 )
    ]);

    ?>

</head>
<body class="<?php echo isset($isFrance) ? 'france' : '' ?>">
<div id="root">
    <?php echo $this->Session->flash() ?>
    <?php echo $this->element('header') ?>
    <?php echo $this->fetch('content') ?>
    <?php echo $this->element('footer') ?>
    <?php
    if (CakeSession::check('promoCodeUrlShadowBox')) {
        echo $this->element('Pages/detail/promo-url-shadow-box');
    }
    ?>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script src="/js/lib/slick.min.js"></script>
<script src="/js/lib/jquery.royalslider.9.4.4.min.js"></script>
<script src="/js/lib/jquery.jscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script src="/js/lib/resrc-it.min.js?v=<?php echo $version ?>"></script>
<script src="https://f.vimeocdn.com/js/froogaloop2.min.js"></script>
<?php echo $this->Html->script('lib/scripts.js') ?>
<?php echo $this->Html->script('lib/mobile.js') ?>
<?php echo $this->Html->script('lib/select2.js') ?>
<?php echo $this->Html->script('global.js') ?>
<?php if (isset($js)): ?>
    <?php foreach ($js as $filename): ?>
        <?php echo $this->Html->script($filename . '.js') ?>
    <?php endforeach ?>
<?php endif ?>

<?php echo $this->element('google_tag_manager'); ?>
<?php echo $this->element('bing_tracking'); ?>
</body>
</html>
<?php echo $this->element('tapfiliate/landing_page'); ?>
