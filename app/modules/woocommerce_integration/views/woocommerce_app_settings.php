<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fab fa-wordpress"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
        <a class="btn btn-primary" href="<?php echo base_url('woocommerce_integration/add_woocommerce_settings') ?>"><i class="fas fa-plus-circle"></i> <?php echo lang('Connect WooCommerce API'); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('integration#'); ?>"><?php echo $page_title; ?></a></div>
    </div>
  </div>

  <?php 
  include(APPPATH . 'Views/admin/theme/message.php');
  if(session()->getFlashdata('error_message_woocommerce')!='')
  echo "<div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".session()->getFlashdata('error_message_woocommerce')."</div>";
  ?>

  <div class="section-body">

    <?php 
    if(!empty($info))
    {       
      echo "<div class='row'>";
      foreach($info as $value)
      {  ?>
        <div class="col-12 col-sm-6">
          <div class="card profile-widget mt-4">
              <div class="profile-widget-header">
                <div class="profile-widget-items">
                  <div class="profile-widget-item">
                    <div class="profile-widget-item-value">
                      <a target='_BLANK' href="<?php echo base_url("woocommerce_integration/store/".$value["id"]);?>"  class='btn btn-outline-info ' data-toggle='tooltip' data-placement='top' title="<?php echo lang('Visit Store Webview');?>"><i class='fas fa-eye'></i> <?php echo lang('Store Webview');?></a>
                    </div>
                  </div>
                  <div class="profile-widget-item">
                    <div class="profile-widget-item-value">
                     <a href='' data-site="<?php echo $value["home_url"];?>" data-id="<?php echo $value['id'];?>"  class='btn btn-outline-primary show_product' data-toggle='tooltip' data-placement='top' title="<?php echo lang('Product List');?>"><i class='fas fa-box-open'></i> <?php echo lang('Products');?></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="profile-widget-description" style="padding-bottom: 0;">
                <div class="profile-widget-name text-center ltr"><a href='<?php echo $value["home_url"];?>' target="_BLANK"><i class='fab fa-wordpress'></i> <?php echo $value["home_url"];?></a></div>
                <div class="profile-widget-name text-center">
                  <small  data-toggle='tooltip' data-placement='top' title="<?php echo lang('Consumer Key');?>"><i class='fas fa-key'></i> <?php echo ($is_demo!='1') ? $value["consumer_key"]:"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";?></small><br>
                  <small  data-toggle='tooltip' data-placement='top' title="<?php echo lang('Consumer Secret');?>"><i class='fas fa-mask'></i> <?php echo ($is_demo!='1') ? $value["consumer_secret"]:"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";?></small><br>
                  <small  data-toggle='tooltip' data-placement='top' title="<?php echo lang('Last Updated');?>"><i class='far fa-clock'></i> <?php echo date("M j, y H:i",strtotime($value["last_updated_at"]));?></small>
                </div>
              </div>
              <div class="card-footer text-center" style="padding-top: 10px;">
                
               <a href='#' csrf_token="<?php echo session()->get('csrf_token_session');?>" class='mt-2 btn btn-outline-danger delete_app' table_id="<?php echo $value['id'];?>" data-toggle='tooltip' data-placement='top' title="<?php echo lang('Delete');?>"><i class='fas fa-trash-alt'></i> <?php echo lang('Delete');?></a>

               <a href="<?php echo base_url('woocommerce_integration/edit_woocommerce_settings/').$value['id'];?>" class='mt-2 btn btn-outline-primary' data-toggle='tooltip' data-placement='top' title="<?php echo lang('Update');?>"><i class='fas fa-edit'></i> <?php echo lang('Update');?></a>

               <a href="" class='mt-2 btn btn-outline-dark copy_url'  data-id="<?php echo $value['id'];?>" data-toggle='tooltip' data-placement='top' title="<?php echo lang('Copy URL');?>"><i class='fas fa-copy'></i> <?php echo lang('Copy URL');?></a>

               <a href="<?php echo base_url('woocommerce_integration/sync_woocommerce_data/').$value['id'];?>" class='mt-2 btn btn-warning' data-toggle='tooltip' data-placement='top' title="<?php echo lang('Re-sync Data');?>"><i class='fas fa-sync-alt'></i> <?php echo lang('Re-sync Data');?></a>

              </div>
            </div>
          
        </div>            
        <?php 
      }
      echo "</div>";
    }
    else
    { ?>
      <div class="card">
          <div class="card-body">
            <div class="empty-state" data-height="400" style="height: 400px;">
              <div class="empty-state-icon">
                <i class="fas fa-times"></i>
              </div>
              <h2><?php echo lang("No WooCommerce Integration found."); ?></h2>
              <p>&nbsp;</p>
              <a class="btn btn-primary" href="<?php echo base_url('woocommerce_integration/add_woocommerce_settings') ?>"><i class="fas fa-plus-circle"></i> <?php echo lang('Connect WooCommerce API');?></a>
            </div>
          </div>
        </div>

      <?php
    }
    ?>  
    
  </div>
</section>





<script>       
  var base_url="<?php echo site_url(); ?>";  
 
  $(document).ready(function() {

    "use strict";

    $(document).on('click','.show_product',function(e){
      e.preventDefault();
      var id = $(this).attr('data-id');
      $("#show_products_modal").modal();
      $("#show_products_modal iframe").attr('src',base_url+'woocommerce_integration/product_list/'+id);
    });

    $(document).on('click','.copy_url',function(e){
      e.preventDefault();
      var id = $(this).attr('data-id');
      $("#copy_url_modal").modal();
      $("#copy_url_modal iframe").attr('src',base_url+'woocommerce_integration/copy_url/'+id);
    });


    $(document).on('click','.delete_app',function(e){
      e.preventDefault();
      var ifyoudeletethisaccount = "<?php echo lang('Are you sure that you want to delete this API? Deleting API does not affect products exported to E-commerce.'); ?>";
      swal({
        title: '<?php echo lang("Are you sure?"); ?>',
        text: ifyoudeletethisaccount,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) 
        {
          var app_table_id = $(this).attr('table_id');
          var csrf_token = $(this).attr('csrf_token');
          $(this).removeClass('btn-outline-danger');
          $(this).addClass('btn-danger');
          $(this).addClass('btn-progress');

          $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo site_url();?>woocommerce_integration/delete_action",
            dataType: 'json',
            data:{app_table_id : app_table_id,csrf_token:csrf_token},
            success:function(response){ 
              
              $(this).removeClass('btn-progress');
              $(this).removeClass('btn-danger');
              $(this).addClass('btn-outline-danger');

              if(response.status == 1)
              {
                swal('<?php echo lang("Success"); ?>', response.message, 'success').then((value) => {
                   location.reload();
                });
              }
              else
              {
                swal('<?php echo lang("Error"); ?>', response.message, 'error');
              }
            }
          });
        } 
      });
    });


  });
</script>


<div class="modal fade" role="dialog" id="show_products_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-box-open"></i> <?php echo lang("Products");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
          <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>

      </div>
    </div>
  </div>
</div>


<div class="modal fade" role="dialog" id="copy_url_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-copy"></i> <?php echo lang("Copy URL");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body">
          <iframe src="" frameborder="0" width="100%" onload="resizeIframe(this)"></iframe>

      </div>
    </div>
  </div>
</div>


<style type="text/css">.profile-widget .profile-widget-items:after{left:0;}</style>