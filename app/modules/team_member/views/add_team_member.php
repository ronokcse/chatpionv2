<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-plus-circle"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo base_url('team_member/member_list'); ?>"><?php echo $this->lang->line("Team Member"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="row">
    <div class="col-12">

      <form class="form-horizontal" action="<?php echo site_url().'team_member/add_team_member_action';?>" method="POST">
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label for="name"> <?php echo $this->lang->line("Full Name")?> </label>
              <input name="name" value="<?php echo set_value('name');?>"  class="form-control" type="text">
              <span class="red"><?php echo form_error('name'); ?></span>
            </div>
             
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="email"> <?php echo $this->lang->line("Email")?> *</label>
                  <input name="email" value="<?php echo set_value('email');?>"  class="form-control" type="email">
                  <span class="red"><?php echo form_error('email'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="mobile"><?php echo $this->lang->line("Mobile")?></label>              
                  <input name="mobile" value="<?php echo set_value('mobile');?>"  class="form-control" type="text">
                  <span class="red"><?php echo form_error('mobile'); ?></span>               
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="password"> <?php echo $this->lang->line("Password")?> *</label>
                  <input name="password" value="<?php echo set_value('password');?>"  class="form-control" type="password">
                  <span class="red"><?php echo form_error('password'); ?></span>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="confirm_password"> <?php echo $this->lang->line("Confirm Password")?> *</label>
                  <input name="confirm_password" value="<?php echo set_value('confirm_password');?>"  class="form-control" type="password">
                  <span class="red"><?php echo form_error('confirm_password'); ?></span>
                </div>
              </div>
            </div>
            <div class="row" id="">
              <div class="col-12">
                <div class="form-group">
                  <label for="role_id"> <?php echo $this->lang->line("Role")?> *</label>
                  <?php echo form_dropdown('role_id', $team_roles, '1','class="form-control select2" style="width:100% !important;"'); ?>                  
                  <span class="red"><?php echo form_error('role_id'); ?></span>
                </div>
              </div>
            </div>

            
            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="status" > <?php echo $this->lang->line('Status');?></label><br>
                  <label class="custom-switch mt-2">
                    <input type="checkbox" name="status" value="1" class="custom-switch-input" checked>
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description"><?php echo $this->lang->line('Active');?></span>
                    <span class="red"><?php echo form_error('status'); ?></span>
                  </label>
                </div>
              </div>             
            </div>

          </div>

          <div class="card-footer bg-whitesmoke">
            <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("team_member/member_list",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
          </div>
        </div>
      </form>  
    </div>
  </div>
</section>

          


<script type="text/javascript">
  $(document).ready(function() {
    // if($("#price_default").val()=="0") $("#hidden").hide();
    // else $("#validity").show();
    $(".user_type").click(function(){
      if($(this).val()=="Admin") $("#hidden").hide();
      else $("#hidden").show();
    });
  });
</script>