<?php 
	include(APPPATH . "modules/blog/views/blog_style.php"); 
?>

<div class="container-fluid" style="padding-bottom: 50px;margin-bottom: 50px;">
	<div class="col-xs-12" >
		<div class="left-content text-center">
			<h1><span style="color:<?php echo $THEMECOLORCODE;?>; font-weight: 600;"><?php echo lang("Total"); ?> <?php echo $totalRecords; ?> <?php echo lang("blogs found"); ?></span></h1>
			<div class="col-sm-12" style="font-size: 20px;margin-bottom: 20px;">
				<strong style="text-transform: capitalize;"><?php echo lang(session()->get('filter_type')); ?></strong> : 
				<?php 
					$string = '';
					if(session()->get('filter_type') == 'Search'){
						$string = session()->get('filter_keywords');
					}else{
						$string = session()->get('filter_slug');
					};
					echo $string;
				?>
			</div>
			<ol class="breadcrumb" style="background: transparent;font-size: 17px;font-weight: 400;">
                <li><a href="<?php echo base_url(); ?>"><?php echo lang("Home");?></a></li>
                <li class="active"><a href="<?php echo base_url('blog');?>"><?php echo lang("Blogs");?></a></li>
                <li class="active"><?php echo $string;?></li>
            </ol>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<aside id="sidebar">
				<?php 
                    include(APPPATH . "modules/blog/views/blog_sidebar.php"); 
                ?>
			</aside><!--/#sidebar-->
		</div><!--/.col-sm-4-->
		<div class="col-xs-12 col-md-9">
			<?php include(APPPATH . "modules/blog/views/post_display.php") ?>
		</div><!--/.col-sm-8-->
	</div><!--/.row-->
</div><!--/.container-->