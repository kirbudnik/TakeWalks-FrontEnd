<?php echo $this->element('Pages/listing/filters'); ?>

<?php echo $this->Html->script('/theme/Italy/js/lib/head.js') ?>
<?php echo $this->Html->css('global.css', array('media' => 'screen')) ?>
<?php echo $this->Html->css('lib/select2.css', array('media' => 'screen')) ?>
<?php echo $this->Html->css('print.css', array('media' => 'print')) ?>
<?php echo $this->Html->css('listings.css') ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

<?php echo $this->Html->script('lib/scripts.js') ?>
<?php echo $this->Html->script('lib/mobile.js') ?>
<?php echo $this->Html->script('lib/select2.js') ?>
<?php echo $this->Html->script('listings.js'); ?>