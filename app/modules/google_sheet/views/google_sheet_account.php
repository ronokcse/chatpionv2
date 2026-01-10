<style>
  .card {
    padding-top: 0 !important;
  }

  .card .media-body .media-title {
    margin-bottom: 0px !important;
  }

  .card .media-body .page_email {
    line-height: 12px !important;
  }

  .card .page_delete {
    margin-top: 10px;
    margin-right: 10px;
    padding: .1rem .5rem !important;
  }

  .card .right-button {
    margin-top: 10px;
    margin-right: 10px;
    padding: .1rem .5rem !important;
  }

  .card .enable_webhook {
    margin-top: 10px;
    padding: .1rem .5rem !important;
  }

  .card .disable_webhook {
    margin-top: 10px;
    padding: .1rem .5rem !important;
  }

  /* .profile-widget-header {margin-bottom: -18px !important;} */
  /* .profile-widget-header img { margin: -20px -5px 0 22px !important; } */
  /* .profile-widget-header h6 { text-align: left;margin-left: 20px; } */
  /*.profile-widget-header .delete_account { position: absolute;top:10px;right:10px;}*/
  .profile-widget .profile-widget-items:after {
    position: relative;
  }

  .list-unstyled .media {
    padding-right: 10px;
  }

  /* .profile-widget-item{border:none;} */
  .btn-circle {
    margin: 0 !important;
  }

  @media (max-width: 575.98px) {
    .profile-widget {
      margin-top: 0 !important;
    }
  }

  .update_account {
    cursor: pointer;
  }
</style>
<style type="text/css">
  .profile-widget .profile-widget-items .profile-widget-item {
    text-align: left !important;
    padding-left: 20px !important;
  }
</style>

<section class="section">

  <div class="section-header">
    <h1><i class="fab fa-google"></i> <?php echo $this->lang->line("Google Accounts") ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('integration') ?>"><?php echo $this->lang->line("Integration"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php
  if ($this->session->flashdata('google_sheet_auth_connect') == '1') {
    echo "<div class='alert alert-success'><h4 class='alert-heading'>" . $this->lang->line('Successful') . "</h4><p>" . $this->lang->line('Account Successfully Imported') . "</p></div>";
    unset($_SESSION['google_sheet_auth_connect']);
  }
  if ($this->session->userdata('limit_cross') != '') {
    echo "<div class='alert alert-danger text-center'><i class='fas fa-times'></i> " . $this->session->userdata('limit_cross') . "</div><br/>";
    $this->session->unset_userdata('limit_cross');
  }
  $is_demo = $this->is_demo;
  if ($is_demo && $this->session->userdata("user_type") == "Admin") {
    echo '<div class="alert alert-warning text-center">Account import has been disabled in admin account because you will not be able to unlink the Google account you import as admin. If you want to test with your own accout then <a href="' . base_url('home/sign_up') . '" target="_BLANK">sign up</a> to create your own demo account then import your Google account there.</div>';
  }
  ?>

  <div class="section-body">
    <div class="float-right">
      <p data-toggle="tooltip" data-placement="bottom" title="<?php echo $this->lang->line("You must be logged in your Google account to connect it."); ?>">
        <a href="<?php echo base_url("google_sheet/connect_action") ?>" target="_blank" class="btn bg-white btn-lg text-dark" style="border: 1px solid #ccc !important;"><img class="" width="20px" src="<?php echo base_url('assets/img/google.png'); ?>"><b class="pl-2"><?php echo $this->lang->line('Sign in with') . " " . $this->lang->line("Google"); ?></b></a>
      </p>
    </div>

    <div class="clearfix"></div>
    <div class="row">
      <?php if (count($sheet_account_details) > 0) :
        foreach ($sheet_account_details as $key => $account): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <div class="card author-box">
              <div class="card-body pl-3 pr-2">
                <div class="author-box-left">
                  <a target="_BLANK" href="">
                    <img src="<?php echo str_replace('\\', '', $account->image); ?>" class="rounded-circle author-box-picture mt-2" style="width: 100px !important;" onerror="this.src='<?php echo base_url('assets/img/google.png'); ?>';">
                  </a>
                </div>
                <div class="author-box-details">
                  <div class="author-box-name"><?php echo $account->name; ?></div>
                  <div class="author-box-job">
                    <i class="fas fa-at"></i> <?php echo $account->email; ?>
                  </div>
                  <div class="mt-3"></div>
                  <a href="#"
                    class="px-2 pt-2 btn btn-sm btn-outline-primary btn-circle sync_google_sheet"
                    data-flag='0' data-id="<?php echo $account->id; ?>"
                    title="<?php echo $this->lang->line('Sync : Sync your account data.'); ?>"
                    data-toggle="tooltip"
                    data-placement="top"
                    data-custom-class="custom-tooltip">
                    <i class="fas fa-sync"></i>
                  </a>
                  <a href="#"
                    class="px-2 pt-2 btn btn-sm btn-dark btn-circle unlink_google_sheet_account"
                    data-flag="0"
                    data-id="<?php echo $account->id; ?>"
                    title="<?php echo $this->lang->line('Unlink : Unlink the google sheet account from :appname but You can always resync to retrieve it.'); ?>"
                    data-toggle="tooltip"
                    data-placement="top"
                    data-custom-class="custom-tooltip">
                    <i class="fas fa-unlink"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
    </div>
  <?php else : ?>
    <div class="col-12">
      <div class="card" id="nodata">
        <div class="card-body">
          <div class="empty-state">
            <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-nature-man-colour.svg'); ?>" alt="image">
            <h2 class="mt-0"><?php echo $this->lang->line("You haven not connected any account yet.") ?><p><a href="<?php echo base_url("google_sheet/connect_action") ?>" target="_blank"><?php echo $this->lang->line("Sign in to connect your account."); ?></a></p>
            </h2>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  </div>

  <?php $this->load->view('admin/theme/message'); ?>
  <?php if($count_account > 0){ ?>
    <div class="page-title mt-4">
      <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
          <h3>
            <?php echo $this->lang->line('Google Sheets'); ?>
            <span id="subtitle"></span>
            <!-- Neeeds module access there -->
            <a href="#" id="create_google_sheet" class="btn btn-outline-primary">
              <i class="fas fa-plus-circle"></i>
              <?php echo $this->lang->line('Create New Sheet'); ?>
            </a>
            <a href="#" id="add_existing_sheet" class="btn btn-outline-primary">
              <i class="fas fa-plus-circle"></i>
              <?php echo $this->lang->line('Add Existing Sheet'); ?>
            </a>

          </h3>
          <p class="text-subtitle text-muted">
            <?php echo $this->lang->line('List of Google Sheets'); ?>
          </p>
        </div>
      </div>
    </div>
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body data-card">
              <div class="row">
                <div class="col-12 col-md-9">
                  <?php echo
                  '<div class="input-group mb-3" id="searchbox">
                    <div class="input-group-prepend">
                      ' . form_dropdown('google-account-select', $account_list, '', 'class="form-control select2" id="account-select"') . '
                    </div>
                    <div class="input-group-prepend">'; ?>
                </div>

                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">


                <?php
                echo
                '<input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="' . $this->lang->line("Search...") . '" style="max-width:30%;">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">' . $this->lang->line("Search") . '</span></button>
                    </div>
                  </div>'; ?>
              </div>
            </div>

            <div class="table-responsive2">
              <input type="hidden" id="hidden_account_id">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('ID'); ?></th>
                    <th><?php echo $this->lang->line('Sheet Title'); ?></th>
                    <th><?php echo $this->lang->line('Sheet Id'); ?></th>
                    <th><?php echo $this->lang->line('Actions'); ?></th>
                    <th><?php echo $this->lang->line('Created at'); ?></th>
                    <th><?php echo $this->lang->line('Modified at'); ?></th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

</section>


<div class="modal fade" id="create_google_sheet_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="min-width: 30%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-pencil"></i> <?php echo $this->lang->line('Create Google Sheet'); ?></h5>
        <input type="hidden" id="hidden_sheet_account_id">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 mb-3">
            <label for="account-select"><?php echo $this->lang->line('Select Account'); ?> *</label>
            <select name="email" class="form-control select2" style="width:100% !important" id="account-select2" autocomplete="off">

              <?php foreach ($account_list as $id => $email): ?>
                <option value="<?php echo $id; ?>"><?php echo $email; ?></option>
              <?php endforeach; ?>
            </select>
            <span id="account_select_err" class="text-danger"></span>
          </div>
          <div class="col-12 mb-3">
            <label><?php echo $this->lang->line('Sheet Title'); ?> *</label>
            <input type="text" name="sheet_title" id="sheet_title" class="form-control">
          </div>
          <div class="col-12 mb-3">
            <button type="button" id="add_header" class="btn btn-outline-success"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Header'); ?></button>
          </div>

          <!-- Container for dynamically added headers -->
          <div class="col-12 mb-2" id="headers_container"></div>
        </div>
      </div>

      <div id="result_status"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line('Close'); ?></button>
        <button id="save_google_sheet" type="button" class="btn btn-outline-primary"><i class="fas fa-save"></i> <?php echo $this->lang->line('Save'); ?></button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="add_existing_sheet_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Existing Sheet'); ?></h5>
        <input type="hidden" id="hidden_sheet_account_id2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12 mb-2">
            <label for="account-select"><?php echo $this->lang->line('Select Account'); ?> *</label>
            <select name="email" class="form-control select2" style="width:100% !important" id="account-select3" autocomplete="off">
              <?php foreach ($account_list as $id => $email): ?>
                <option value="<?php echo $id; ?>"><?php echo $email; ?></option>
              <?php endforeach; ?>
            </select>
            <span id="account_select_err" class="text-danger"></span>
          </div>
          <div class="col-12">
            <label><?php echo $this->lang->line('Sheet ID'); ?> *</label>
            <input type="text" name="sheet_id" id="sheet_existing_id" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer d-flex align-items-center justify-content-start">
        <button type="button" id="save_existing_google_sheet" class="btn btn-outline-primary">
          <i class="fas fa-save"></i> <span><?php echo $this->lang->line('Save'); ?></span>
        </button>
      </div>
    </div>
  </div>
</div>


<?php $csrf_token = $this->session->userdata('csrf_token_session'); ?>

<script>
  var base_url = "<?php echo site_url(); ?>";
  var csrf_token = "<?php echo $csrf_token; ?>";

  $(document).ready(function() {

    var perscroll;

    var table = $("#mytable").DataTable({
      serverSide: true,
      processing: true,
      bFilter: true,
      order: [
        [1, "desc"]
      ],
      pageLength: 10,
      ajax: {
        url: base_url + 'google_sheet/account_list_data',
        type: 'POST',
        data: function(d) {
          d.search_value = $('#search_value').val();
          d.sheet_account_id = $('#hidden_account_id').val();
        }
      },
      language: {
        url: "<?php echo base_url('assets/modules/datatables/language/' . $this->language . '.json'); ?>"
      },
      dom: '<"top">rt<"bottom"lip><"clear">',
      columns: [{
          data: null,
          title: "#",
          orderable: false,
          searchable: false
        }, // For row number
        {
          data: 'id',
          visible: false
        },
        {
          data: 'name',
          title: "Name"
        },
        {
          data: 'sheet_id',
          title: "Sheet ID"
        },
        {
          data: null,
          orderable: false,
          render: function(data, type, row, meta) {
            var view_url = "https://docs.google.com/spreadsheets/d/" + row.sheet_id + "/edit";
            var edit_url = base_url + 'team_member/edit_role/' + row.id;
            var delete_url = '#';
            var unlink_confirm_message = "<?php echo htmlspecialchars($this->lang->line('Unlinking the sheet will only remove its data from system. You can always resync to     retrieve it. However, unlinking will cause the loss of data linked with this sheet.')); ?>";
            var delete_confirm_message = "<?php echo htmlspecialchars($this->lang->line('Do you really want to delete this sheet? This action cannot be undone and will cause the loss of data linked with this sheet.',)); ?>";
            var actions = "";
            actions += "<a class='btn btn-circle btn-outline-primary' href='" + view_url + "' target='_blank' title='View'><i class='fas fa-eye'></i></a>";

            actions += "<a href='" + delete_url + "' data-sheet_account_id='" + row.google_account_id + "' data-id='" + row.id + "' data-table-name='table12' data-soft-delete='1' data-lang-confirm-yes='<?php echo htmlspecialchars($this->lang->line('Unlink')); ?>' data-lang-confirm-message='" + unlink_confirm_message + "' class='btn btn-circle btn-outline-warning delete_google_sheet' title='<?php echo htmlspecialchars($this->lang->line('Unlink: Unlink from system but stays in Google Sheets end')); ?>'>" +
              "<i class='fa fa-unlink'></i></a>";

            actions += "&nbsp;&nbsp;&nbsp;&nbsp;<a href='" + delete_url + "' data-sheet_account_id='" + row.google_account_id + "' data-id='" + row.id + "' data-table-name='table12' data-soft-delete='2' class='btn btn-circle btn-outline-danger delete_google_sheet' data-lang-confirm-message='" + delete_confirm_message + "' title='<?php echo htmlspecialchars($this->lang->line('Delete: Unlink from system and from Google Sheets end as well.')); ?>'>" +
              "<i class='fa fa-trash'></i></a>";

            // Return the actions wrapped in a div
            return "<div style='min-width: 130px'>" + actions + "</div>";
          }
        },
        {
          data: 'created_at',
          title: "Created At"
        },
        {
          data: 'updated_at',
          title: "Modified at"
        },
      ],
      columnDefs: [{
        targets: 0,
        render: function(data, type, row, meta) {
          return meta.row + 1; // Display row number
        }
      }]
    });


    $('#account-select').on('change', function(event) {
      var selectedValue = $(this).val();
      $('#hidden_account_id').val(selectedValue);
      table.draw();
    });
    $(document).on('keyup', '#search_value', function(e) {
      if (e.which == 13 || $(this).val().length > 2 || $(this).val().length == 0) table.draw(false);
    });

    /* This will sync all the sheet of particular google sheet account */

    $('.sync_google_sheet').on('click', function(event) {
      event.preventDefault();
      var button = $(this);
      var icon = button.find('i');

      button.addClass("disabled");
      button.addClass("btn-progress");
      button.addClass("btn-primary");
      button.removeClass("btn-outline-primary");

      var id = button.data('id');
      $.ajax({
        url: base_url + 'google_sheet/google_sheet_sync',
        type: 'POST',
        data: {
          'id': id
        },
        success: function(response) {
          button.removeClass("disabled");
          button.removeClass("btn-progress");
          button.removeClass("btn-primary");
          button.addClass("btn-outline-primary");
          if (response.error) {
            iziToast.error({
              title: '',
              message: response.message,
              position: 'bottomRight'
            });
            return false;
          } else {
            iziToast.success({
              title: '',
              message: response.message,
              position: 'bottomRight'
            });
            table.draw();
            return true;
          }
        },
        error: function() {
          button.removeClass("disabled");
          button.removeClass("btn-progress");
          button.removeClass("btn-primary");
          button.addClass("btn-outline-primary");
          Swal.fire('<?php echo $this->lang->line("Error") ?>', 'An unexpected error occurred.', 'error');
        }
      });
    });

    /* This will unlink google sheet account from the database */
    $('.unlink_google_sheet_account').on('click', function(event) {
      event.preventDefault();
      var button = $(this);
      var icon = button.find('i');
      button.addClass("disabled");
      button.addClass("btn-progress");
      var id = button.data('id');
      swal({
        title: '<?php echo $this->lang->line("Confirm") ?>',
        text: '<?php echo $this->lang->line("Do you really want to delete this record? This action cannot be undone and will delete any other related data if needed.") ?>',
        icon: 'warning',
        buttons: {
          cancel: {
            text: '<?php echo $this->lang->line("Cancel") ?>',
            visible: true,
            closeModal: true,
          },
          confirm: {
            text: '<?php echo $this->lang->line("Confirm") ?>',
            closeModal: false
          }
        },
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          $.ajax({
            url: base_url + 'google_sheet/unlink_google_sheet_account',
            type: 'POST',
            data: {
              'id': id
            },
            success: function(response) {
              button.removeClass("disabled");
              button.removeClass("btn-progress");
              if (response.error) {
                swal('<?php echo $this->lang->line("Error") ?>', response.message, 'error');
                return false;

              } else {
                iziToast.success({
                  title: '',
                  message: response.message,
                  position: 'bottomRight'
                });
                swal('<?php echo $this->lang->line("Success") ?>', response.message, 'success')
                  .then((value) => {
                    window.location.reload();
                  });
              }
            }
          });
        } else {
          button.removeClass("disabled");
          button.removeClass("btn-progress");
        }
      });
    });

    $('#create_google_sheet').on('click', function(event) {
      $("#create_google_sheet_modal").modal('show');
      $('#account-select2').val(null).trigger('change');
      $('#sheet_title').val('');
      $('#headers_container').empty();
    });

    $('#account-select2').on('change', function(event) {
      var selectedValue = $(this).val();
      $('#hidden_sheet_account_id').val(selectedValue);
    });

    $('#save_google_sheet').on('click', function(event) {
      var button = $(this);
      var icon = button.find('i');
      var sheet_account_id = $('#hidden_sheet_account_id').val();
      var sheet_title = $('#sheet_title').val().trim();
      var missing_input = false;
      if (sheet_account_id === '' || sheet_title === '') {
        missing_input = true;
      }

      if (missing_input) {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please fill the required fields."); ?>', 'warning');
        return false;
      }

      button.addClass("disabled");
      button.addClass("btn-progress");
      button.addClass("btn-primary");
      button.removeClass("btn-outline-primary");
      // Collect header values
      var headers = [];
      $('input[name="headers[]"]').each(function() {
        headers.push($(this).val().trim());
      });

      $.ajax({
        url: base_url + 'google_sheet/google_sheet_save',
        type: 'POST',
        data: {
          'sheet_account_id': sheet_account_id,
          'sheet_title': sheet_title,
          'headers_name': headers
        },
        headers: {
          'X-CSRF-TOKEN': csrf_token
        },
        success: function(response) {
          button.removeClass("disabled");
          button.removeClass("btn-progress");
          button.removeClass("btn-primary");
          button.addClass("btn-outline-primary");
          if (response.error) {
            swal('<?php echo $this->lang->line("Error") ?>', response.message, 'error');
            return false;
          } else {
            swal('<?php echo $this->lang->line("Success") ?>', response.message, 'success').then((result) => {
              $('#sheet_title').val('');
              $("#create_google_sheet_modal").modal('hide');
              $('#account-select2').val(null).trigger('change');
              $('#sheet_title').val('');
              $('#headers_container').empty();
              table.draw();
              return true;
            });
          }
        },
        error: function() {
          button.removeClass("disabled");
          button.removeClass("btn-progress");
          button.removeClass("btn-primary");
          button.addClass("btn-outline-primary");
          swal('<?php echo $this->lang->line("Error") ?>', 'An unexpected error occurred.', 'error');
        }
      });


    });

    $('#account-select3').on('change', function(event) {
      var selectedValue = $(this).val();
      $('#hidden_sheet_account_id2').val(selectedValue);
    });

    $('#save_existing_google_sheet').on('click', function(event) {
      var button = $(this);
      var icon = button.find('i');
      var sheet_account_id = $('#hidden_sheet_account_id2').val();
      var sheet_existing_id = $('#sheet_existing_id').val();
      var missing_input = false;
      if (sheet_account_id === '' || sheet_existing_id === '') {
        missing_input = true;
      }
      if (missing_input) {
        swal('<?php echo $this->lang->line("Warning"); ?>', '<?php echo $this->lang->line("Please fill the required fields."); ?>', 'warning');
        return false;
      }

      button.addClass("disabled");
      button.addClass("btn-progress");
      button.addClass("btn-primary");
      button.removeClass("btn-outline-primary");

      $.ajax({
        url: base_url + 'google_sheet/existing_google_sheet_save',
        type: 'POST',
        data: {
          'sheet_account_id': sheet_account_id,
          'sheet_existing_id': sheet_existing_id
        },
        success: function(response) {
          button.removeClass("disabled");
          button.removeClass("btn-progress");
          button.removeClass("btn-primary");
          button.addClass("btn-outline-primary");
          if (response.error) {
            swal('<?php echo $this->lang->line("Error") ?>', response.message, 'error');
            return false;
          } else {
            swal('<?php echo $this->lang->line("Success") ?>', response.message, 'success').then((result) => {
              $('#sheet_existing_id').val('');
              $("#add_existing_sheet_modal").modal('hide');
              $('#account-select2').val(null).trigger('change');
              $('#sheet_existing_id').val('');
              table.draw();
              return true;
            });
          }
        },
        error: function() {
          button.removeClass("disabled");
          button.removeClass("btn-progress");
          button.removeClass("btn-primary");
          button.addClass("btn-outline-primary");
          swal('<?php echo $this->lang->line("Error") ?>', 'An unexpected error occurred.', 'error');
        }
      });


    });


    $(document).on('click', '.delete_google_sheet', function(event) {
      event.preventDefault();
      var id = $(this).data('id');
      var data_lang_confirm_message = $(this).data('lang-confirm-message');
      var sheet_account_id = $(this).data('sheet_account_id');
      var soft_delete = $(this).data('soft-delete'); // 1 for unlink and 2 for parmanent delete
      swal({
          title: '<?php echo $this->lang->line("Warning"); ?>',
          text: data_lang_confirm_message,
          icon: 'warning',
          buttons: true,
          dangerMode: true,
          showCancelButton: true,
        })
        .then((willreset) => {
          if (willreset) {
            $(this).addClass("disabled");
            if (soft_delete == '2') {
              $(this).addClass("btn-danger");
              $(this).removeClass("btn-outline-danger");
            } else {
              $(this).addClass("btn-warning");
              $(this).removeClass("btn-outline-warning");
            }
            $(this).addClass("btn-progress");

            $.ajax({
              context: this,
              type: 'POST',
              url: base_url + "google_sheet/delete_sheet",
              dataType: 'json',
              data: {
                'id': id,
                'sheet_account_id': sheet_account_id,
                'soft_delete': soft_delete
              },
              success: function(response) {
                $(this).removeClass("disabled");
                if (soft_delete == '2') {
                  $(this).removeClass("btn-danger");
                  $(this).addClass("btn-outline-danger");
                } else {
                  $(this).removeClass("btn-warning");
                  $(this).addClass("btn-outline-warning");
                }
                $(this).removeClass("btn-progress");

                if (response.error) {
                  swal('<?php echo $this->lang->line("Error"); ?>', response.message, 'error');
                  return false;
                } else {
                  swal('<?php echo $this->lang->line("Success"); ?>', response.message, 'success').then((value) => {
                    table.draw();
                  });
                }
              }
            });
          }
        });
    });

    let headerCount = 0;

    $('#add_header').on('click', function() {
      headerCount++; // Increment the counter to create unique IDs for each header field

      // Create a new input field with a remove button
      let headerInput = `
        <div class="input-group mb-2" id="header_${headerCount}">
            <input type="text" name="headers[]" class="form-control" placeholder="Header Name">
            <button type="button" class="btn btn-danger remove-header" data-id="${headerCount}"><i class="fas fa-trash"></i></button>
        </div>
    `;

      // Append the input field to the headers container
      $('#headers_container').append(headerInput);
    });

    // Remove header input field on cross button click
    $(document).on('click', '.remove-header', function() {
      let id = $(this).data('id'); // Get the ID of the header field to be removed
      $(`#header_${id}`).remove(); // Remove the corresponding header field
    });

    $('#add_existing_sheet').on('click', function(event) {
      $("#add_existing_sheet_modal").modal('show');
      $('#account-select2').val(null).trigger('change');
      $('#sheet_existing_id').val('');
    });


  });
</script>