<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo config('MyConfig')->product_name;?>" width="200"></a>
      </div>

      <div class="card card-primary" >
        <div class="card-header"><h4><i class="fas fa-key"></i> <?php echo lang('Change Password'); ?></h4></div>

        <div class="card-body" id="recovery_form">
          <?php
          if(session()->get('error'))
		  echo '<div class="alert alert-warning text-center">'.session()->get('error').'</div>';
		  session()->remove('error');
		  ?>

          <form method="POST" action="<?php echo base_url("change_password/reset_password_action"); ?>">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo session()->get('csrf_token_session'); ?>">
          	<div class="form-group">
          		<label for="old_password"><?php echo lang('Old Password'); ?></label>
          		<div>
          			<input type="password" class="form-control" id="old_password" name="old_password">
          			<span class="red"><?php echo form_error('old_password');?></span>
          		</div>
          	</div>
          	<div class="form-group">
          		<label for="new_password"><?php echo lang('New Password'); ?></label>
          		<div>
          			<input type="password" class="form-control" id="new_password" name="new_password">
          			<span class="red"><?php echo form_error('new_password');?></span>
          		</div>
          	</div>
          	<div class="form-group">
          		<label for="confirm_new_password"><?php echo lang('Confirm Password'); ?></label>
          		<div>
          			<input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
          			<span class="red"><?php echo form_error('confirm_new_password');?></span>
          		</div>
          	</div>

          	<div class="form-group">
              <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-exchange-alt"></i> <?php echo lang('Change Password');?></button>
    	      <a class="btn btn-secondary btn-lg float-right" href="<?php echo base_url('myprofile/edit_profile'); ?>"><i class="fas fa-arrow-circle-left"></i>  <?php echo lang('Go Back');?></a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>