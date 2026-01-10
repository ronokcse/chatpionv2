<style>
	.blue{
		color: #2C9BB3 !important;
	}
</style>

<section class="section">
	<div class="section-header">
		   <h1><i class="fab fa-google"></i> <?php echo $page_title; ?></h1>
		   
		   <div class="section-header-breadcrumb">
		     <div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo lang('Integration'); ?></a></div>
		     <div class="breadcrumb-item"><a href="<?php echo base_url('social_apps/settings'); ?>"><?php echo lang('Social Apps'); ?></a></div>
		     <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		   </div>
	</div>

	
 	<?php view('admin/theme/message'); ?>

	<div class="row">
      <div class="col-12 col-md-12 col-lg-12">
          <div class="card">
            <div class="card-body ltr">
                <b><?php echo lang('Google Auth Redirect URL: '); ?></b>
                <br>
                <span class='blue'><?php echo base_url("home/google_login_back"); ?></span>
            </div>
			<?php if ( (isset($basic) && $basic->is_exist("add_ons",array("project_id"=>70))) && (session()->get('user_type') == 'Admin' || (isset($module_access) && is_array($module_access) && in_array(351, $module_access)))){ ?>
				<div class="card-body ltr">
					<b><?php echo lang('Google Auth Redirect URL:(For Google Sheet) '); ?></b>
					<br>
					<span class='blue'><?php echo base_url("google_sheet/google_get_access_token"); ?></span>
				</div>
			<?php } ?> 
			<?php if ( (isset($basic) && $basic->is_exist("add_ons",array("project_id"=>72))) && (session()->get('user_type') == 'Admin' || (isset($module_access) && is_array($module_access) && in_array(353, $module_access)))){ ?>
				<div class="card-body ltr">
					<b><?php echo lang('Google Auth Redirect URL:(For Google Contacts) '); ?></b>
					<br>
					<span class='blue'><?php echo base_url("google_contacts/google_get_access_token"); ?></span>
				</div>
			<?php } ?> 
          </div>
      </div>
  </div>

	
	<div class="section-body">
	  <div class="row">
	    <div class="col-12">
	        <form action="<?php echo base_url("social_apps/google_settings_action"); ?>" method="POST">
	        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo session()->get('csrf_token_session'); ?>">
	        <div class="card">
	          <div class="card-header"><h4 class="card-title"><i class="fas fa-info-circle"></i> <?php echo lang('App Details'); ?></h4>
				<span><?php echo lang(' *(You need to enable the Google Sheets API and Google Drive API Service for using google sheet)'); ?></span>
			  </div>
			  <?php if ( (isset($basic) && $basic->is_exist("add_ons",array("project_id"=>72))) && (session()->get('user_type') == 'Admin' || (isset($module_access) && is_array($module_access) && in_array(353, $module_access)))){ ?>
				<div class="card-header"><h4 class="card-title"><i class="fas fa-info-circle"></i> <?php echo lang('App Details(Google Contacts)'); ?></h4>
				<span><?php echo lang(' *(You need to enable the People API and Contacts API Service for using google contacts)'); ?></span>
			  </div>
				<?php } ?>
	          <div class="card-body">   
				
		            <div class="row">
			            <div class="col-12 col-md-6">
			            	<div class="form-group">
			            	    <label for=""><i class="fas fa-file-signature"></i> <?php echo lang('App Name');?> </label>
			            	    <input name="app_name" value="<?php echo isset($google_settings['app_name']) ? $google_settings['app_name'] :set_value('app_name'); ?>"  class="form-control" type="text">              
			            	    <span class="red"><?php echo form_error('app_name'); ?></span>
			            	</div>
			            </div>
			            <div class="col-12 col-md-6">
			            	<div class="form-group">
			            	    <label for=""><i class="ion ion-key"></i> <?php echo lang('API Key');?> </label>
			            	    <input name="api_key" value="<?php echo isset($google_settings['api_key']) ? $google_settings['api_key'] :set_value('api_key'); ?>"  class="form-control" type="text">              
			            	    <span class="red"><?php echo form_error('api_key'); ?></span>
			            	</div>
			            </div>
			        </div>



	              <div class="row">
		                <div class="col-12 col-md-6">
		                  <div class="form-group">
		                    <label for=""><i class="far fa-id-card"></i>  <?php echo lang('Client ID');?></label>
		                    <input name="google_client_id" value="<?php echo isset($google_settings['google_client_id']) ? $google_settings['google_client_id'] :set_value('google_client_id'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('google_client_id'); ?></span>
		                  </div>
		                </div>

		                <div class="col-12 col-md-6">
		                  <div class="form-group">
		                    <label for=""><i class="fas fa-key"></i>  <?php echo lang('Client Secret');?></label>
		                    <input name="google_client_secret" value="<?php echo isset($google_settings['google_client_secret']) ? $google_settings['google_client_secret'] :set_value('google_client_secret'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('google_client_secret'); ?></span>
		                  </div>
		                </div>
	              </div>

	              <div class="form-group">
		        	  <?php	
		        	  $status =isset($google_settings['status'])?$google_settings['status']:"";
	                  if ($status == '') $status = '1';
		        	  ?>
		        	  <label class="custom-switch mt-2">
		        	    <input type="checkbox" name="status" value="1" class="custom-switch-input"  <?php if($status=='1') echo 'checked'; ?>>
		        	    <span class="custom-switch-indicator"></span>
		        	    <span class="custom-switch-description"><?php echo lang('Active');?></span>
		        	    <span class="red"><?php echo form_error('status'); ?></span>
		        	  </label>
		          </div>
	              
	          </div>

	          <div class="card-footer bg-whitesmoke">
	            <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo lang('Save');?></button>
	            <button class="btn btn-secondary btn-lg float-right" onclick='goBack("social_apps/settings")' type="button"><i class="fa fa-remove"></i>  <?php echo lang('Cancel');?></button>
	          </div>
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	   				

</section>