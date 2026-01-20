<!DOCTYPE html>
<html lang="en">
	<head>
	  <meta charset="UTF-8">
	  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
		<?php 
		include(APPPATH.'Views/include/css_include_back.php'); 
		include(APPPATH.'Views/include/js_include_back.php'); 
		?>

		<script src="<?php echo base_url('assets/js/system/theme_iframe.js');?>"></script>
		<link rel="stylesheet" href="<?php echo base_url('assets/css/system/theme_iframe.css');?>">

	</head>
	<body>
		<div class="text-center preloading_body">
		  <i class="fas fa-spinner fa-spin blue text-center"></i>
		</div>
		<div id="theme_iframe_container"> 
			<?php 
			// Check if view exists in module directory
			$controller_name = isset($uri) && $uri ? $uri->segment(1) : '';
			$module_view_path = '';
			if (isset($controller_name) && !empty($controller_name)) {
				$module_view_file = APPPATH . 'modules/' . $controller_name . '/views/' . $body . '.php';
				if (file_exists($module_view_file)) {
					$module_view_path = $module_view_file;
				}
			}
			
			if ($module_view_path) {
				// Include module view directly
				include($module_view_path);
			} else {
				// Use standard CI4 view
				echo view($body);
			}
			?>
		</div>
	</body>
</html>
<link rel="stylesheet" href="<?php echo base_url('assets/css/system/inline.css');?>">