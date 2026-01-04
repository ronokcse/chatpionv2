<style>
	.blue{
		color: #2C9BB3 !important;
	}
</style>

<section class="section">
	<div class="section-header">
		   <h1><i class="fab fa-facebook"></i> <?php echo $page_title; ?></h1>
		   <div class="section-header-breadcrumb">
		     <div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo lang('Integration'); ?></a></div>
		     <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		   </div>
	</div>

	
 	<?php view('admin/theme/message'); ?>


	<div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
              <div class="card-body ltr">
                  <b><?php echo lang('App Domain')."</b> : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
                  <b><?php echo lang('Site URL')." :</b> <span class='blue'>".base_url(); ?></span><br/><br>
                  <b><?php echo lang('Privacy Policy URL')." :</b> <span class='blue'>".base_url('home/privacy_policy');?></span><br/>
                  <b><?php echo lang('Terms of Service URL')." :</b> <span class='blue'>".base_url('home/terms_use');?></span><br/>
                  <b><?php echo lang('Data Deletion Callback URL')." :</b> <span class='blue'>".base_url('home/data_deletion_callback');?></span><br/><br>
                
                  <b><?php echo lang('Webhook Callback URL')." :</b> <br><span class='blue'>".base_url('home/central_webhook_callback');?></span><br/>
                  <?php 
                    if(config('MyConfig')->central_webhook_verify_token == '')
                    {
                      $verify_token= substr(uniqid(mt_rand(), true), 0, 10);
                      include('application/config/my_config.php');
                      // if(!isset($config['central_webhook_verify_token']))
                      // {                  
                        $config['central_webhook_verify_token'] = $verify_token;
                        file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
                        redirect($this->uri->uri_string());
                      // }
                    }
                  ?>
                  <b><?php echo lang('Webhook Verify Token')." :</b> <span class='blue'>".config('MyConfig')->central_webhook_verify_token;?></span><br/>
              
              </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">

              <div class="card-body" style="min-height: 165px;">
                  
                  <b><?php echo lang('Valid OAuth Redirect URI')." </b>: <br><br><span class='blue'>".base_url("home/facebook_login_back"); ?></span><br>
                  <span class="blue"><?php echo base_url('home/redirect_rx_link'); ?></span><br/>
                  <span class="blue"><?php echo base_url('social_accounts/manual_renew_account'); ?></span><br/>
                  <!-- <span class="blue"><?php echo base_url('facebook_rx_account_import/redirect_custer_link'); ?></span> -->
                  
              </div>
            </div>
        </div>
    </div>
	
	<div class="section-body">
	  <div class="row">
	    <div class="col-12">
	        <form action="<?php echo base_url("social_apps/facebook_settings_update_action"); ?>" method="POST">
	        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo session()->get('csrf_token_session'); ?>">
        	<input type="hidden" name="table_id" value="<?php echo $table_id ?>">
	        <div class="card">
	          <div class="card-header"><h4 class="card-title"><i class="fas fa-info-circle"></i> <?php echo lang('App Details'); ?></h4></div>
	          <div class="card-body">              
	              <div class="form-group">
	                  <label for=""><i class="fas fa-file-signature"></i> <?php echo lang('App Name');?> </label>
	                  <input name="app_name" value="<?php echo isset($facebook_settings['app_name']) ? $facebook_settings['app_name'] : set_value('app_name'); ?>"  class="form-control" type="text">              
	                  <span class="red"><?php echo form_error('app_name'); ?></span>
	              </div>

	              <div class="row">
		                <div class="col-12 col-md-6">
		                  <div class="form-group">
		                    <label for=""><i class="far fa-id-card"></i>  <?php echo lang('App ID');?></label>
		                    <input name="api_id" value="<?php echo isset($facebook_settings['api_id']) ? $facebook_settings['api_id'] : set_value('api_id'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('api_id'); ?></span>
		                  </div>
		                </div>

		                <div class="col-12 col-md-6">
		                  <div class="form-group">
		                    <label for=""><i class="fas fa-key"></i>  <?php echo lang('App Secret');?></label>
		                    <input name="api_secret" value="<?php echo isset($facebook_settings['api_secret']) ? $facebook_settings['api_secret'] : set_value('api_secret'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('api_secret'); ?></span>
		                  </div>
		                </div>
	              </div>

	              <div class="form-group">
		        	  <?php	
		        	  $status =isset($facebook_settings['status'])?$facebook_settings['status']:"";
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
	            <button class="btn btn-secondary btn-lg float-right" onclick='goBack("social_apps/index")' type="button"><i class="fa fa-remove"></i>  <?php echo lang('Cancel');?></button>
	          </div>
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	   				

</section>