<li class="dropdown" id="xxx"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
  <img src="<?php echo session()->get('brand_logo'); ?>" class="rounded-circle mr-1">
  <div class="d-none d-md-inline-block"><?php echo (isset($is_manager) && $is_manager!==1) ? session()->get('username') : session()->get('username')." (".session()->get('team_username').")"; ?></div></a>
  <div class="dropdown-menu dropdown-menu-right">

    <div class="dropdown-title"><?php echo config('MyConfig')->product_short_name." - ".lang(session()->get('user_type')); ?></div>
    
    <?php if(!isset($is_manager) || $is_manager!=1):?>
      <a href="<?php echo base_url('myprofile/edit_profile'); ?>" class="dropdown-item has-icon">
        <i class="far fa-user"></i> <?php echo lang('Profile'); ?>
      </a>
      <a href="<?php echo base_url('calendar/index'); ?>" class="dropdown-item has-icon">
        <i class="fas fa-bolt"></i> <?php echo lang('Activities'); ?>
      </a>
    <?php endif; ?>

    <?php if(isset($basic) && $basic->is_exist("add_ons",array("unique_name"=>"api_documentation"))) : ?>
        <?php if(session()->get('user_type') == 'Admin' || (isset($module_access) && in_array(285, $module_access))) : ?>
        <a href="<?php echo base_url('native_api/get_api_key'); ?>" class="dropdown-item has-icon">
          <i class="fas fa-plug"></i> <?php echo lang('API Key'); ?>
        </a>
        <?php endif; ?>
    <?php endif; ?>

  
    <div class="dropdown-divider"></div>  
    <div class="dropdown-title"><i class="fab fa-facebook"></i> <?php echo lang('Facebook Account'); ?></div>
    <?php $current_account = isset($fb_rx_account_switching_info[session()->get('facebook_rx_fb_user_info')]['name']) ? $fb_rx_account_switching_info[session()->get('facebook_rx_fb_user_info')]['name'] : lang('No Account'); ?>
    <a class="dropdown-item has-icon active" data-toggle="collapse" href="#collapseExampleFBA" role="button" aria-expanded="false" aria-controls="collapseExampleFBA">
     <?php echo $current_account; ?> (<?php echo lang('Change'); ?>)
    </a>
    <div class="collapse" id="collapseExampleFBA">
      <?php 
      foreach ($fb_rx_account_switching_info as $key => $value) 
      {
        $selected='';
        if($key==session()->get('facebook_rx_fb_user_info')) $selected='d-none';
        echo '<a href="" data-id="'.$key.'" class="dropdown-item account_switch '.$selected.'"><i class="fas fa-check-circle"></i> '.$value['name'].'</a>';
      } 
      ?>
    </div>


    <div class="dropdown-divider"></div>

    
    <a href="<?php echo base_url('change_password/reset_password_form'); ?>" class="dropdown-item has-icon">
        <i class="fas fa-key"></i> <?php echo lang('Change Password'); ?>
    </a>

    <a href="<?php echo base_url('home/logout'); ?>" class="dropdown-item has-icon text-danger">
      <i class="fas fa-sign-out-alt"></i> <?php echo lang('Logout'); ?>
    </a>


  </div>
</li>