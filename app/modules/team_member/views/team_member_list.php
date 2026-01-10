<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-user-ninja"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-button">
     <a class="btn btn-primary"  href="<?php echo site_url('team_member/add_team_member');?>">
        <i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("New Team Member"); ?>
     </a> 
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Team Member"); ?></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">

    <div class="row">
      <div class="col-12">
        <div class="card">

          <div class="card-body data-card">            
            <div class="table-responsive2">
              <table class="table table-bordered" id="mytable">
                <thead>
                  <tr>
                    <th>#</th>      
                    <th style="vertical-align:middle;width:20px">
                        <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                    </th>
                    <th><?php echo $this->lang->line("ID"); ?></th>           
                    <th><?php echo $this->lang->line("Avatar"); ?></th>           
                    <th><?php echo $this->lang->line("Name"); ?></th>      
                    <th><?php echo $this->lang->line("Email"); ?></th>
                    <th><?php echo $this->lang->line("Role"); ?></th>
                    <th><?php echo $this->lang->line("Status"); ?></th>
                    <th style="min-width: 150px"><?php echo $this->lang->line("Actions"); ?></th>
                    <th><?php echo $this->lang->line("Registered"); ?></th>
                    <th><?php echo $this->lang->line("Last Login"); ?></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>             
          </div>

        </div>
      </div>
    </div>
    
  </div>
</section>


<script>       
    var base_url="<?php echo site_url(); ?>";
   
    $(document).ready(function() {

      $('div.note-group-select-from-files').remove();
      
      var perscroll;
      var table = $("#mytable").DataTable({
          serverSide: true,
          processing:true,
          bFilter: true,
          order: [[ 4, "asc" ]],
          pageLength: 10,
          ajax: {
              "url": base_url+'team_member/member_list_data',
              "type": 'POST'
          },
          language: 
          {
            url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
          },
          dom: '<"top"f>rt<"bottom"lip><"clear">',
          columnDefs: [
            {
                targets: [1,2,3],
                visible: false
            },
            {
                targets: [0,1,3,7,8,9],
                className: 'text-center'
            },
            {
                targets: [0,1,2,8],
                sortable: false
            }
          ],
          fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
              }
          },
          scrollX: 'auto',
          fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              {
                if (perscroll) perscroll.destroy();
                perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
              }
          }
      });

    $(document).on('click', '.change_password', function(e) {
        e.preventDefault();

          var id = $(this).attr('data-id');
          var user_name = $(this).attr('data-user');

          $("#putname").html(user_name);
          $("#putid").val(id);

          $("#change_password").modal();
      });

      var confirm_match=0;
      $(".password").keyup(function(){
        
          var new_pass=$("#password").val();
          var conf_pass=$("#confirm_password").val();

          if(new_pass=='' || conf_pass=='') 
          {
            return false;
          }

          if(new_pass==conf_pass)
          {
              confirm_match=1;
              $("#password").removeClass('is-invalid');
              $("#confirm_password").removeClass('is-invalid');
          }
          else
          {
              confirm_match=0;
              $("#confirm_password").addClass('is-invalid');
          }

      });

      $(document).on('click', '#save_change_password_button', function(e) {
        e.preventDefault();

        var id =  $("#putid").val();
        var password =  $("#password").val();
        var confirm_password =  $("#confirm_password").val();
        var csrf_token = $("#csrf_token").val();

        password = password.trim();
        confirm_password = confirm_password.trim();

        if(password=='' || confirm_password=='')
        {
            $("#password").addClass('is-invalid');
            return false;
        }
        else
        {
            $("#password").removeClass('is-invalid');
        }

        if(confirm_match=='1')
        {
            $("#confirm_password").removeClass('is-invalid');
        }
        else
        {
            $("#confirm_password").addClass('is-invalid');
            return false;
        }

        $("#save_change_password_button").addClass("btn-progress");

        $.ajax({
        url: base_url+'team_member/change_team_member_password_action',
        type: 'POST',
        dataType: 'JSON',
        data: {id:id,password:password,confirm_password:confirm_password,csrf_token:csrf_token},
          success:function(response)
          {
            $("#save_change_password_button").removeClass("btn-progress");

            if(response.status == "1")  
              swal('<?php echo $this->lang->line("Success")?>',response.message, 'success')
             .then((value) => {
                 $("#change_password").modal('hide');
              });

            else  swal('<?php echo $this->lang->line("Error")?>',response.message, 'error');
          },
          error:function(response){
            var span = document.createElement("span");
            span.innerHTML = response.responseText;
            swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
          }
      });

      });
  });

   
 
</script>



<div class="modal fade" tabindex="-1" role="dialog" id="change_password" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-key"></i> <?php echo $this->lang->line("Change Password");?> (<span id="putname"></span>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">  
              <form class="form-horizontal" action="<?php echo site_url().'team_member/change_team_member_password_action';?>" method="POST">
                <div id="wait"></div>
                <input id="putid" value="" class="form-control" type="hidden">           
                <div class="form-group">
                  <label for="password"><?php echo $this->lang->line("New Password"); ?> *  </label>                  
                  <input id="password" class="form-control password" type="password">             
                  <div class="invalid-feedback"><?php echo $this->lang->line("You have to type new password twice"); ?></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?php echo $this->lang->line("Confirm New Password"); ?> * </label>                  
                    <input id="confirm_password"  class="form-control password" type="password">             
                   <div class="invalid-feedback"><?php echo $this->lang->line("Passwords does not match"); ?></div>
                </div>
              </form>            
            </div>


            <div class="modal-footer bg-whitesmoke br">
              <button type="button" id="save_change_password_button" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save"); ?></button>
              <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
            </div>

        </div>
    </div>
</div>