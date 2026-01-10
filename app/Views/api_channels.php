<?php 
  $user_type = session()->get('user_type');
  $license_type = session()->get('license_type');
?>

<section class="section section_custom">
  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="make-nav-stick">
          <div class="list-group d-block" id="list-tab">
            <?php if($user_type == 'Admin' && $license_type == 'double') : ?>
            <a class="list-group-item list-group-item-action d-inline" href="#list-payment-list"><?php echo lang('Payment APIs'); ?></a>
            <?php endif; ?>

            <?php if($user_type == "Admin") { ?>
              <a class="list-group-item list-group-item-action d-inline" href="#list-media-list"><?php echo lang('Social Medias'); ?></a>
            <?php } ?>

            <?php if($has_autoresponder_access) : ?>
              <a class="list-group-item list-group-item-action d-inline" href="#list-autoresponder-list"><?php echo lang('Email Autoresponder'); ?></a>
            <?php endif; ?>

            <?php if($has_json_access) : ?>
              <a class="list-group-item list-group-item-action d-inline" href="#list-json-list"><?php echo lang('JSON API'); ?></a>
            <?php endif; ?>

            <?php if($has_http_api_access) : ?>
              <a class="list-group-item list-group-item-action d-inline" href="#list-http-api-list"><?php echo lang('HTTP API'); ?></a>
            <?php endif; ?>

            <a class="list-group-item list-group-item-action d-inline" href="#list-sms-list"><?php echo lang('SMS API'); ?></a>

            <a class="list-group-item list-group-item-action d-inline" href="#list-email-list"><?php echo lang('Email API'); ?></a>

            <?php if(isset($basic) && $basic->is_exist("modules",array("id"=>266)) || (isset($basic) && $basic->is_exist("modules",array("id"=>293)))) { ?>
            <a class="list-group-item list-group-item-action d-inline" href="#list-woocommerce-list"><?php echo lang('WooCommerce'); ?></a>
            <?php } ?>
            <?php if(ai_reply_exist()){ ?>
            <a class="list-group-item list-group-item-action d-inline" href="#list-openAI-list"><?php echo lang('Open AI'); ?></a>
            <?php } ?>
          </div>
        </div>
      </div>
    </div><br>

    <?php if($user_type == 'Admin' && $license_type == 'double') : ?>
    <div class="row" id="list-payment-list">
      <div class="col-12">
        <h2 class="section-title"><?php echo lang('Payment Account APIs'); ?></h2>
        <p class="section-lead text-muted">
          <?php echo lang('Set up payment gateway to receive payments from subscribed users for using this platform.'); ?>
        </p>
      </div>

      <?php foreach($payment_apis as $single_api) : ?>
      <div class="col-4 col-lg-2">
        <a href="<?php echo $payment_gateway_url; ?>" class="text-dark action_tag">
          <div class="wizard-steps mb-3">
            <div class="wizard-step mx-1 my-0">
              <div class="wizard-step-icon">
                <img class="img-fluid" width="80" src="<?php echo $single_api['img_path']; ?>" alt="">
              </div>
              <div class="wizard-step-label"><?php echo $single_api['title']; ?></div>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>

    </div>
    <?php endif; ?>

    <div class="row mt-3" id="list-media-list">
      <div class="col-12">
        <h2 class="section-title"><?php echo lang('Social Media'); ?></h2>
        <p class="section-lead text-muted">
          <?php echo lang('Integrate different social media accounts to use bot, auto reply, social posting etc features.'); ?>
        </p>
      </div>


      <?php 
      $i=0; 
      foreach($social_medias as $social_media) : 
        
        if(!$social_media['has_access']) continue;

      ?>
      <div class="col-4 col-lg-2 social_media_tag" div_count='<?php echo $i; ?>'>
        <a href="<?php echo $social_media['account_import_url']; ?>" class="text-dark action_tag">
          <div class="wizard-steps mb-3">
            <div class="wizard-step mx-1 my-0">
              <div class="wizard-step-icon">
                <img class="img-fluid" width="80" src="<?php echo $social_media['img_path']; ?>" alt="">
              </div>
              <div class="wizard-step-label"><?php echo $social_media['title']; ?></div>

              <?php if($user_type == "Admin") : ?>
                <div class="wizard-step-label wizard-icons actions<?php echo $i; ?>" style="display: none;">
                  <?php if($social_media['action_url'] != '') : ?>
                  <a href="<?php echo $social_media['action_url']; ?>" class="btn btn-circle btn-outline-primary" title="<?php echo lang('API settings'); ?>"><i class="fas fa-plug"></i></a>
                  <?php endif; ?>
                  <a href="<?php echo $social_media['account_import_url']; ?>" class="btn btn-circle btn-outline-warning" title="<?php echo lang('Import Account'); ?>"><i class="fas fa-cloud-download-alt"></i></a>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </a>
      </div>
      <?php $i++; endforeach; ?>

      <!-- For google sheet -->
      <?php if ( (isset($basic) && $basic->is_exist("add_ons",array("project_id"=>70))) && (session()->get('user_type') == 'Admin' || (isset($module_access) && is_array($module_access) && in_array(351, $module_access)))){ ?>
        <div class="col-4 col-lg-2 social_media_tag" div_count='<?php echo $i; ?>'>
          <a href="<?php echo base_url('google_sheet'); ?>" class="text-dark action_tag">
            <div class="wizard-steps mb-3">
              <div class="wizard-step mx-1 my-0">
                <div class="wizard-step-icon">
                  <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/social_media/google_sheet.png'); ?>" alt="">
                </div>
                <div class="wizard-step-label"><?php echo lang('Google Sheet'); ?></div>

                <?php if($user_type == "Admin") : ?>
                  <div class="wizard-step-label wizard-icons actions<?php echo $i; ?>" style="display: none;">
                    <a href="<?php echo base_url('google_sheet'); ?>" class="btn btn-circle btn-outline-primary" title="<?php echo lang('API settings'); ?>"><i class="fas fa-plug"></i></a>
                  </div>
                <?php endif; ?>

              </div>
            </div>
          </a>
        </div>
      <?php } ?>
      

        <!-- For google sheet -->
      <?php if ( (isset($basic) && $basic->is_exist("add_ons",array("project_id"=>72))) && (session()->get('user_type') == 'Admin' || (isset($module_access) && is_array($module_access) && in_array(353, $module_access)))){ ?>
      <div class="col-4 col-lg-2 social_media_tag" div_count='<?php echo $i; ?>'>
        <a href="<?php echo base_url('google_contacts'); ?>" class="text-dark action_tag">
          <div class="wizard-steps mb-3">
            <div class="wizard-step mx-1 my-0">
              <div class="wizard-step-icon">
                <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/social_media/google_contacts.png'); ?>" alt="">
              </div>
              <div class="wizard-step-label"><?php echo lang('Google Contacts'); ?></div>

              <?php if($user_type == "Admin") : ?>
                <div class="wizard-step-label wizard-icons actions<?php echo $i; ?>" style="display: none;">
                  <a href="<?php echo base_url('google_contacts'); ?>" class="btn btn-circle btn-outline-primary" title="<?php echo lang('API settings'); ?>"><i class="fas fa-plug"></i></a>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </a>
      </div>
    <?php } ?>




    </div>


    <?php if($has_autoresponder_access) : ?>
    <div class="row mt-3" id="list-autoresponder-list">
      <div class="col-12">
        <h2 class="section-title"><?php echo lang('Email Autoresponder APIs'); ?></h2>
        <p class="section-lead text-muted">
          <?php 
          echo lang('If you integrate email autoresponder and apply in bot manager then, email address will be forwared to auto responder account when a bot subscriber OPT-IN using email.'); 
          if($user_type == "Admin"){
            echo ' '.lang('As a admin you can use autoresponder integration when a new user sign-up to the system.'); 
          }
          ?>

        </p>
      </div>

      <?php foreach($email_autoresponder_apis as $api) : ?>
        <div class="col-4 col-lg-2">
          <a href="<?php echo $api['action_url'] ?>" class="text-dark action_tag">
            <div class="wizard-steps mb-3">
              <div class="wizard-step mx-1 my-0">
                <div class="wizard-step-icon">
                  <img class="img-fluid" width="80" src="<?php echo $api['img_path']; ?>" alt="">
                </div>
                <div class="wizard-step-label"><?php echo $api['title']; ?></div>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
      
    </div>
    <?php endif; ?>


    <?php if($has_json_access) : ?>
      <div class="row mt-3" id="list-json-list">
        <div class="col-12">
          <h2 class="section-title"><?php echo lang('JSON API Connector'); ?></h2>
          <p class="section-lead text-muted">
            <?php echo lang('JSON API Connector for Messenger bot to share collected data accross different platforms. We send data via POST method only.'); ?>
          </p>
        </div>

        <div class="col-12 col-lg-2">
          <a href="<?php echo base_url('messenger_bot_connectivity/json_api_connector'); ?>" class="text-dark action_tag">
            <div class="wizard-steps mb-3">
              <div class="wizard-step mx-1 my-0">
                <div class="wizard-step-icon">
                  <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/auto_responder/json_api.png'); ?>" alt="">
                </div>
                <div class="wizard-step-label"><?php echo lang('JSON API Connector'); ?></div>
              </div>
            </div>
          </a>
        </div>
        
      </div>
    <?php endif; ?>

    <?php if($has_http_api_access) : ?>
      <div class="row mt-3" id="list-http-api-list">
        <div class="col-12">
          <h2 class="section-title"><?php echo lang('HTTP API Integration'); ?></h2>
          <p class="section-lead text-muted">
            <?php echo lang('Connect to an external HTTP API and interact. Retrieve API response data and set this data to update subscriber information through bot flow.'); ?>
          </p>
        </div>

        <div class="col-12 col-lg-2">
          <a href="<?php echo base_url('http_api/build_api').'?channel=fb'; ?>" class="text-dark action_tag">
            <div class="wizard-steps mb-3">
              <div class="wizard-step mx-1 my-0">
                <div class="wizard-step-icon">
                  <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/http_api.png'); ?>" alt="">
                </div>
                <div class="wizard-step-label"><?php echo lang('Facebook HTTP API'); ?></div>
              </div>
            </div>
          </a>
        </div>

        <div class="col-12 col-lg-2">
          <a href="<?php echo base_url('http_api/build_api').'?channel=ig'; ?>" class="text-dark action_tag">
            <div class="wizard-steps mb-3">
              <div class="wizard-step mx-1 my-0">
                <div class="wizard-step-icon">
                  <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/http_api.png'); ?>" alt="">
                </div>
                <div class="wizard-step-label"><?php echo lang('Instagram HTTP API'); ?></div>
              </div>
            </div>
          </a>
        </div>
        
      </div>
    <?php endif; ?>

    <?php if($has_sms_access) { ?>
    <div class="row mt-3" id="list-sms-list">
      <div class="col-12">
        <h2 class="section-title"><?php echo lang('SMS APIs'); ?></h2>
        <p class="section-lead text-muted">
          <?php echo lang('Integrate SMS APIs to broadcast SMS and send SMS notification.'); ?>
        </p>
      </div>

      <?php foreach($sms_email_apis['sms'] as $sms_api) : ?>
        <div class="col-4 col-lg-2">
          <a href="<?php echo $sms_api['action_url']; ?>" class="text-dark action_tag">
            <div class="wizard-steps mb-3">
              <div class="wizard-step mx-1 my-0">
                <div class="wizard-step-icon">
                  <img class="img-fluid" width="80" src="<?php echo $sms_api['img_path']; ?>" alt="">
                </div>
                <div class="wizard-step-label"><?php echo $sms_api['title']; ?></div>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
    <?php } ?>

    <?php if($has_email_access) { ?>
    <div class="row mt-3" id="list-email-list">
      <div class="col-12">
        <h2 class="section-title"><?php echo lang('Email APIs'); ?></h2>
        <p class="section-lead text-muted">
          <?php echo lang('Integrate email APIs to broadcast SMS and send email notification.'); ?>
        </p>
      </div>

      <?php foreach($sms_email_apis['email'] as $email_api) : ?>
        <div class="col-4 col-lg-2">
          <a href="<?php echo $email_api['action_url']; ?>" class="text-dark action_tag">
            <div class="wizard-steps mb-3">
              <div class="wizard-step mx-1 my-0">
                <div class="wizard-step-icon">
                  <img class="img-fluid" width="80" src="<?php echo $email_api['img_path']; ?>" alt="">
                </div>
                <div class="wizard-step-label"><?php echo $email_api['title']; ?></div>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
    <?php } ?>

    <?php 
    if((isset($basic) && $basic->is_exist("modules",array("id"=>266))) || (isset($basic) && $basic->is_exist("modules",array("id"=>293)))) : ?>
    
      <div class="row mt-3" id="list-woocommerce-list">
        <div class="col-12">
          <h2 class="section-title"><?php echo lang('WooCommerce'); ?></h2>
          <p class="section-lead text-muted">
            <?php echo lang('WooCommerce abandoned cart recovery plugin & import WooCommerce product data.'); ?>
          </p>
        </div>
        <?php if($user_type == 'Admin' || (isset($module_access) && is_array($module_access) && in_array(266,$module_access))) : ?>
          <div class="col-12 col-lg-2">
            <a href="<?php echo base_url('woocommerce_abandoned_cart'); ?>" class="text-dark action_tag">
              <div class="wizard-steps mb-3">
                <div class="wizard-step mx-1 my-0">
                  <div class="wizard-step-icon">
                    <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/social_media/woocommerce.png'); ?>" alt="">
                  </div>
                  <div class="wizard-step-label"><?php echo lang('WC Abandoned Cart Recovery'); ?></div>
                </div>
              </div>
            </a>
          </div>
        <?php endif; ?>
        <?php if($user_type == 'Admin' || (isset($module_access) && is_array($module_access) && in_array(293,$module_access))) : ?>
          <div class="col-12 col-lg-2">
            <a href="<?php echo base_url('woocommerce_integration'); ?>" class="text-dark action_tag">
              <div class="wizard-steps mb-3">
                <div class="wizard-step mx-1 my-0">
                  <div class="wizard-step-icon">
                    <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/social_media/woocommerce.png'); ?>" alt="">
                  </div>
                  <div class="wizard-step-label"><?php echo lang('WC Product Import'); ?><br><br></div>
                </div>
              </div>
            </a>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>


    <?php 
    if(ai_reply_exist()) : ?>
    
      <div class="row mt-3" id="list-openAI-list">
        <div class="col-12">
          <h2 class="section-title"><?php echo lang('Open AI'); ?></h2>
          <p class="section-lead text-muted">
            <?php echo lang('Open AI Is for training data of using AI.'); ?>
          </p>
        </div>
          <div class="col-12 col-lg-2">
            <a href="<?php echo base_url('integration/open_ai_api_credentials'); ?>" class="text-dark action_tag">
              <div class="wizard-steps mb-3">
                <div class="wizard-step mx-1 my-0">
                  <div class="wizard-step-icon">
                    <img class="img-fluid" width="80" src="<?php echo base_url('assets/img/api_channel_icon/social_media/ai.png'); ?>" alt="">
                  </div>
                  <div class="wizard-step-label"><?php echo lang('Open AI api '); ?></div>
                </div>
              </div>
            </a>
          </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<style>
  .action_tag { text-decoration: none !important; }
  .action_tag:hover .wizard-step-label { color: var(--blue) !important; }
  .wizard-steps .wizard-step { padding: 20px;}
  .wizard-steps .wizard-step:before {content: none !important;}
  .wizard-steps .wizard-step .wizard-step-label { font-size: 12px;text-transform:capitalize;letter-spacing:0;margin-top:10px; }
  .social_media_tag:hover .wizard-icons { display: block !importan; }
  @media (max-width: 575.98px) {
    .list-group {
      display: grid !important;
    } 
    }
  /*.wizard-icons { display: none; }*/
</style>

<script>

  $(document).ready(function() {

    var user_type = '<?php echo $user_type;  ?>';
    var is_mobile = '<?php echo session()->get('is_mobile');  ?>';
    if(user_type == "Admin") {
      $('.social_media_tag .action_tag').off('click');
      $('.social_media_tag .action_tag').css('cursor', 'pointer');
      $('.social_media_tag .action_tag').removeAttr('target href');
    }

    if(is_mobile == '1') {
      $('.social_media_tag .wizard-icons').css('display','block');
    }

    $(".social_media_tag").on({
        mouseenter: function () {
          var div = $(this).attr('div_count');
          var div_show = $('.actions'+div);
          $(div_show).show();
        },
        mouseleave: function () {
          var div = $(this).attr('div_count');
          var div_show = $('.actions'+div);
          $(div_show).hide();
        }
    });

    $(document).on('click', '.list-group-item', function(event) {
      event.preventDefault();
      $(".list-group-item").removeClass('active');
      $(this).addClass('active');
      var data_href = $(this).attr("href");
      $('html, body').animate({
        scrollTop: ($(data_href).offset().top)
      }, 1000);
    });
  });
</script>
