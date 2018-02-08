<!DOCTYPE html>
<!--[if lt IE 7]>
<html lt-ie9 lt-ie8 lt-ie7" lang="en" class="<?php echo $this->request->action ?>"> <![endif]-->
<!--[if IE 7]>
<html lt-ie9 lt-ie8" lang="en" class="<?php echo $this->request->action ?>"> <![endif]-->
<!--[if IE 8]>
<html lt-ie9" lang="en" class="<?php echo $this->request->action ?>"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang="en" class="<?php echo $this->request->action ?>"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= isset($metaTitle) ? $metaTitle : 'Take Walks' ?></title>

    <?php if(isset($metaDescription)) { ?>
        <meta name="description" content="<?php echo htmlentities($metaDescription) ?>">
    <?php } else { ?>
        <meta name="description"
              content="Relax with friends. Go local. Get the inside scoop. For a more authentic experience of the Big Apple, take walks.">
    <?php } ?>

    <link href="https://fonts.googleapis.com/css?family=Alegreya+Sans:300i,300,400,500,700|Cormorant+Infant:400,400i,600,700" rel="stylesheet">
    <link href="https://plus.google.com/+Walksofnewyork" rel=publisher/>

    <link rel="apple-touch-icon" sizes="180x180" href="/theme/TakeWalks/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/theme/TakeWalks/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/theme/TakeWalks/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">

    <meta name="theme-color" content="#ffffff">
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <script>
        var initValues = JSON.parse('<?=isset($initValues) ? str_replace('\\', '\\\\',json_encode($initValues, JSON_HEX_APOS)) : ''; ?>');
    </script>

    <?php echo $this->fetch('bottomHead'); ?>

    <?php echo isset($canonicalURL) ? "<link rel=\"canonical\"  href=\"$canonicalURL\" />" : ''; ?>
    <!-- <script src="//cdn.optimizely.com/js/596251622.js"></script> -->


    <?= $this->Html->css('app.css') ?>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-K2FSVLT');</script>
    <!-- End Google Tag Manager -->

</head>

<body>

<div id="root" canvas="container">
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->fetch('content') ?>

    <div class="back-to-top default">
      <i class="icon icon-arrow-upward"></i>
    </div>
</div>
<div id="offcanvas" off-canvas="id-1 left reveal">

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<?= $this->Html->script('lib/jquery.plugin.min.js') ?>
<?= $this->Html->script('lib/jquery.datepick.min.js') ?>
<?= $this->Html->script('lib/fotorama.js') ?>
<?= $this->Html->script('lib/swiper.jquery.min.js') ?>
<?= $this->Html->script('lib/select2.full.min.js') ?>
<?= $this->Html->script('lib/moment.min.js') ?>
<?= $this->Html->script('lib/underscore.min.js') ?>
<?= $this->Html->script('lib/notify.js') ?>
<?= $this->Html->script('lib/helper.js') ?>
<?= $this->Html->script('lib/waves.js') ?>
<?= $this->Html->script('lib/slidebars.min.js') ?>
<?= $this->Html->script('app.js') ?>
<?= $this->Html->script('global.js') ?>

<?= $this->fetch('scripts'); ?>
<?= $this->element('analytics/gtm'); ?>

</body>
