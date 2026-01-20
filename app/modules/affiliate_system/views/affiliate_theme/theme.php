<!DOCTYPE html>
<html lang="en">
	<head>
	  <meta charset="UTF-8">
	  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	  <title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
	  <link rel="shortcut icon" href="<?php echo base_url();?>assets/img/favicon.png"> 
	  <?php 
	  // CI4 fix: CI3 path `application/views/...` no longer exists
	  include(APPPATH.'Views/include/css_include_back.php'); 
	  include(APPPATH.'Views/include/js_include_back.php'); 
	  ?>
	</head>

	<body>
	  <div id="app">
	    <div class="main-wrapper">
			<?php 
			// CI4 fix: include module partials from `app/modules`
			include(APPPATH.'modules/affiliate_system/views/affiliate_theme/header.php');

			include(APPPATH.'modules/affiliate_system/views/affiliate_theme/sidebar.php');
			echo '<div class="main-content">';
				// CI4 fix: pass current template variables into inner view
				// so variables like $curency_icon, $payment_today etc are available.
				$this->load->view($body, get_defined_vars());
			echo '</div>';
			// CI4 fix: footer lives in `app/Views`
			include(APPPATH.'Views/admin/theme/footer.php'); ?>
		</div>
	  </div>
	</body>
</html>
