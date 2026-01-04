<style type="text/css" media="screen">
  #payment_options .list-group-item-action{margin-bottom: 30px;}
  #payment_options .list-group-item-action{margin-bottom: 30px;}
  #payment_options img{margin-right: 20px;}
</style>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-cart-plus"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
      <a href="<?php echo base_url('payment/transaction_log'); ?>" class="btn btn-primary"><i class="fas fa-history"></i> <?php echo lang('Transaction Log'); ?></a>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('payment/buy_package'); ?>"><?php echo lang('Payment'); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></a></div>
    </div>
  </div>

  <div class="section-body">

    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-cart-plus"></i> <?php echo lang('Payment Options');?></h4>
        </div>
        <div class="card-body">
            <div id="payment_options"><?php echo $buttons_html; ?></div>
            <br>
            <?php 
            if ($last_payment_method != '')
            { 
              
              $payment_type = ($has_reccuring == 'true') ? lang('Recurring') : lang('Manual');

              echo '<br><div class="alert alert-light alert-has-icon">
                      <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                      <div class="alert-body">
                        <div class="alert-title">'.lang('Last Payment').'</div>
                        '.lang('Last Payment').' : '.$last_payment_method.' ('.$payment_type.')
                      </div>
                    </div>';
            }?>
        </div>
        <div class="card-footer">
            <?php if ('yes' == $manual_payment): ?>
              <button type="button" id="manual-payment-button" class="btn btn-outline-warning btn-lg"><?php echo lang('Manual Payment'); ?></button>      
            <?php endif; ?>
        </div>
    </div>

  </div>
</section>



<?php if ('yes' == $manual_payment): ?>
<div class="modal fade" role="dialog" id="manual-payment-modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo lang('Manual payment');?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">

          <?php if (isset($manual_payment_instruction) && ! empty($manual_payment_instruction)): ?>
          <div class="row">
            <div class="col-lg-12 mb-4">
              <!-- Manual payment instruction -->
              <h6  class="display-6"><i class="far fa-lightbulb"></i> <?php echo lang('Manual payment instructions'); ?></h6>
                  <?php echo $manual_payment_instruction; ?>
            </div>
          </div>
          <?php endif; ?>

          <!-- Paid amount and currency -->
          <div class="row">
            <div class="col-lg-6 mb-4">
              <div class="form-group">
                <label for="paid-amount"><i class="fa fa-money-bill-alt"></i> <?php echo lang('Paid Amount'); ?>:</label>
                <input type="number" name="paid-amount" id="paid-amount" class="form-control" min="1">
                <input type="hidden" id="selected-package-id" value="<?php echo $package_id; ?>">
              </div>
            </div>
            <div class="col-lg-6 mb-4">
              <div class="form-group">
                <label for="paid-currency"><i class="fa fa-coins"></i> <?php echo lang('Currency'); ?></label>              
                <?php echo form_dropdown('paid-currency', $currency_list, $currency, ['id' => 'paid-currency', 'class' => 'form-control select2','style'=>'width:100%']); ?>
              </div>
            </div>
          </div>          
          
          <div class="row">
            <!-- Image upload - Dropzone -->
            <div class="col-lg-6">
              <div class="form-group">
                <label><i class="fa fa-paperclip"></i> <?php echo lang('Attachment'); ?> <?php echo lang('(Max 5MB)');?> </label>
                <div id="manual-payment-dropzone" class="dropzone mb-1">
                  <div class="dz-default dz-message">
                    <input class="form-control" name="uploaded-file" id="uploaded-file" type="hidden">
                    <span style="font-size: 20px;"><i class="fas fa-cloud-upload-alt" style="font-size: 35px;color: var(--blue);"></i> <?php echo lang('Upload'); ?></span>
                  </div>
                </div>
                <span class="red">Allowed types: pdf, doc, txt, png, jpg and zip</span>
              </div>
            </div>

            <!-- Additional Info -->
            <div class="col-lg-6">
              <div class="form-group">
                <label for="paid-amount"><i class="fa fa-info-circle"></i> <?php echo lang('Additional Info'); ?>:</label>
                &nbsp;
                <textarea name="additional-info" id="additional-info" class="form-control"></textarea>
              </div>
            </div>  
          </div>

        </div><!-- ends container -->
      </div><!-- ends modal-body -->

      <!-- Modal footer -->
      <div class="modal-footer bg-whitesmoke br">
        <button type="button" id="manual-payment-submit" class="btn btn-primary"><?php echo lang('Submit'); ?></button>      
        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fa fa-remove"></i> <?php echo lang('Close'); ?></button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('.modal').on("hidden.bs.modal", function (e) { 
          if ($('.modal:visible').length) { 
            $('body').addClass('modal-open');
          }
        });
    });
</script>


<?php if ('yes' == $manual_payment): ?>
<script>
  $(document).ready(function() {

    $(document).on('click', '#manual-payment-button', function() {
      $('#payment_modal').modal('toggle');
      $('#manual-payment-modal').modal();
    });

    // Uploads files
    var uploaded_file = $('#uploaded-file');
    Dropzone.autoDiscover = false;
    $("#manual-payment-dropzone").dropzone({ 
      url: '<?php echo base_url('payment/manual_payment_upload_file'); ?>',
      maxFilesize:5,
      uploadMultiple:false,
      paramName:"file",
      createImageThumbnails:true,
      acceptedFiles: ".pdf,.doc,.txt,.png,.jpg,.jpeg,.zip",
      maxFiles:1,
      addRemoveLinks:true,
      success:function(file, response) {
        var data = JSON.parse(response);

        // Shows error message
        if (data.error) {
          swal({
            icon: 'error',
            text: data.error,
            title: '<?php echo lang('Error!'); ?>'
          });
          return;
        }

        if (data.filename) {
          $(uploaded_file).val(data.filename);
        }
      },
      removedfile: function(file) {
        var filename = $(uploaded_file).val();
        delete_uploaded_file(filename);
      },
    });

    // Handles form submit
    $(document).on('click', '#manual-payment-submit', function() {
      
      // Reference to the current el
      var that = this;

      // Shows spinner
      $(that).addClass('disabled btn-progress');

      var data = {
        paid_amount: $('#paid-amount').val(),
        paid_currency: $('#paid-currency').val(),
        package_id: $('#selected-package-id').val(),
        additional_info: $('#additional-info').val(),
      };

      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: '<?php echo base_url('payment/manual_payment'); ?>',
        data: data,
        success: function(response) {
          if (response.success) {
            // Hides spinner
            $(that).removeClass('disabled btn-progress');

            // Empties form values
            empty_form_values();
            $('#selected-package-id').val('');  

            // Shows success message
            swal({
              icon: 'success',
              title: '<?php echo lang('Success!'); ?>',
              text: response.success,
            });

            // Hides modal
            $('#manual-payment-modal').modal('hide');
          }

          // Shows error message
          if (response.error) {
            // Hides spinner
            $(that).removeClass('disabled btn-progress');

            swal({
              icon: 'error',
              title: '<?php echo lang('Error!'); ?>',
              text: response.error,
            });
          }
        },
        error: function(xhr, status, error) {
          $(that).removeClass('disabled btn-progress');
        },
      });
    });

    $('#manual-payment-modal').on('hidden.bs.modal', function (e) {
      var filename = $(uploaded_file).val();
      delete_uploaded_file(filename);
      $('#selected-package-id').val(''); 
    });

    function delete_uploaded_file(filename) {
      if('' !== filename) {     
        $.ajax({
          type: 'POST',
          dataType: 'JSON',
          data: { filename },
          url: '<?php echo base_url('payment/manual_payment_delete_file'); ?>',
          success: function(data) {
            $('#uploaded-file').val('');
          }
        });
      }

      // Empties form values
      empty_form_values();     
    }

    // Empties form values
    function empty_form_values() {
      $('#paid-amount').val(''),
      $('.dz-preview').remove();
      $('#additional-info').val(''),
      $('#paid-currency').prop("selectedIndex", 0);
      $('#manual-payment-dropzone').removeClass('dz-started dz-max-files-reached');

      // Clears added file
      Dropzone.forElement('#manual-payment-dropzone').removeAllFiles(true);
    }

  });
</script>
<?php endif; ?>

<script>
  var sslcommers_mode = '<?php echo $sslcommers_mode; ?>';
  var ssl_post_data = '<?php echo $postdata_array; ?>';
  var ssl_post_json_data = JSON.parse(ssl_post_data);
  $('#sslczPayBtn').prop('postdata', ssl_post_json_data);

  (function (window, document) {
      var loader = function () {
        var sslcommerzUrl = '';
        if(sslcommers_mode == 'live') sslcommerzUrl = "https://seamless-epay.sslcommerz.com/embed.min.js?";
        else sslcommerzUrl = "https://sandbox.sslcommerz.com/embed.min.js?";
        var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
        script.src = sslcommerzUrl + Math.random().toString(36).substring(7);
        tag.parentNode.insertBefore(script, tag);
      };

      window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
  })(window, document);

</script>
