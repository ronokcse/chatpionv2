<footer class="main-footer">
  <div class="footer-left">
    &copy; <?php  echo config('MyConfig')->product_short_name." ";?> <div class="bullet"></div>  <?php echo '<a  href="'.site_url().'">'.config('MyConfig')->institute_address1.'</a>'; ?>
  </div>
  <div class="footer-right">

  	<?php $current_language = isset($language_info[isset($language) ? $language : 'english']) ? $language_info[isset($language) ? $language : 'english'] : lang('Language'); ?>
    <a href="#" data-toggle="dropdown" class="dropdown-toggle dropdown-item has-icon d-inline">  <?php echo $current_language; ?></a>
    <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
      <li class="dropdown-title"><?php echo lang('Switch Language'); ?></li>
      <?php 
      foreach ($language_info as $key => $value) 
      {
        $selected='';
        // if($key==session()->get('facebook_rx_fb_user_info')) $selected='active';
        echo '<li><a href="" data-id="'.$key.'" class="dropdown-item language_switch '.$selected.'">'.$value.'</a></li>';
      } 
      ?>
    </ul>

    <!-- v<?php echo isset($APP_VERSION) ? $APP_VERSION : '1.0.0';?> -->
  </div>

  
</footer>
