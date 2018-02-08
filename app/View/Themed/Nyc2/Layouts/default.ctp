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

    <script>
        var initValues = JSON.parse('<?php echo json_encode(isset($initValues) ? $initValues : array()) ?>');
    </script>

    <?php echo $this->fetch('bottomHead'); ?>

    <?php echo isset($canonicalURL) ? "<link rel=\"canonical\"  href=\"$canonicalURL\" />" : ''; ?>

    <script src="//cdn.optimizely.com/js/596251622.js"></script>

    <?php
    $ecTheme = 'nyc';
    $devGA ='UA-23373029-11';
    echo $this->element('google_analytics', array(
        'ecTheme' => $ecTheme,
        'analyticsAccount' => (Configure::read('debug') == 0) ? 'UA-23373029-6' : $devGA,
        'transaction' => isset($transaction) ? $transaction : array( 'success' => 0 )
    ));

    echo $this->element('google_tag_manager',array(
        'account' => 'GTM-N5Q9B8'
    ));

    ?>

    <?php echo $this->Html->css('app.min.css') ?>
    <?php echo $this->Html->css('app-less.min.css') ?>
    <?php echo $this->Html->css('app-prefixed.min.css') ?>
    <?php echo $this->Html->css('//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css'); ?>
    <?php echo $this->Html->script('//code.jquery.com/jquery-2.2.4.min.js'); ?>

</head>

<body>

<?php echo $this->fetch('afterBody'); ?>

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