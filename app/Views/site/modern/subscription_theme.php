<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?php echo config('MyConfig')->product_name." | ".$page_title;?></title>
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/img/favicon.png'); ?>?v=1.0">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/img/favicon.png'); ?>?v=1.0">
  <link rel="shortcut icon" type="image/png" href="<?php echo base_url('assets/img/favicon.png'); ?>?v=1.0">
  <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/favicon.png'); ?>?v=1.0">
  <?php if($is_rtl) 
  { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/rtl/bootstrap.min.css">
    <?php 
  } 
  else 
  { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
    <?php
  } ?>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap-social/bootstrap-social.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/v4-shims.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/components.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
  <?php if($is_rtl) { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/rtl.css">
  <?php } ?>
  <?php include(APPPATH."views/include/js_variables_front.php");?>
  <script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>
</head>

<body class="bg-info-light-alt gradient">
  <div id="app">
    <section class="section">
      <?php echo view($body); ?>
    </section>
  </div>
</body>

<?php echo view("include/fb_px"); ?> 
<?php echo view("include/google_code"); ?> 
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/inline.css');?>">