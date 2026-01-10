<section class="section section_custom">
  <div class="section-header">
    <h1><i class="fas fa-edit"></i> <?php echo $page_title; ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $this->lang->line("Team Manager"); ?></div>
      <div class="breadcrumb-item active"><a href="<?php echo base_url('team_member/role_list'); ?>"><?php echo $this->lang->line("Team Roles"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php 
      $access_array = ["1"=>$this->lang->line("Create"),"2"=>$this->lang->line("Update"),"3"=>$this->lang->line("Delete"),"4"=>$this->lang->line("Special")];
      $module_access = json_decode($module_access,true);
  ?>
  <?php $this->load->view('admin/theme/message'); ?>

  <div class="row">
    <div class="col-12">
      <form class="form-horizontal" action="<?php echo site_url().'team_member/update_role_action';?>" method="POST">
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
        <input type="hidden" name="role_id" id="role_id" value="<?php echo $role_id; ?>">
        <div class="card">
          <div class="card-body">
             
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="name"> <?php echo $this->lang->line("Role Name")?> *</label>
                  <input name="name" value="<?php echo $name;?>"  class="form-control" type="text">
                  <span class="text-danger"><?php echo form_error('name'); ?></span>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <label for="page_ids"> <?php echo $this->lang->line("Allowed Pages")?> *</label>
                  <?php echo form_dropdown('page_ids[]', $page_list,$page_ids,'multiple class="form-control select2" style="width:100% !important;"'); ?>                  
                  <span class="red"><?php echo form_error('page_ids'); ?></span>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                    <?php 
                    $SL=0;
                    $access_sl=0;
                    $no_action_modules = array(88,268);
                    ?>
                    <div class="table-responsive2">
                        <table class="table table-bordered">
                        <?php
                        echo "<tr>"; 
                            echo "<th class='px-3' width='20px'>"; 
                              echo $this->lang->line("#");         
                            echo "</th>";
                            echo "
                            <th class='px-3'>
                              <div class='form-check form-switch d-flex p-0'>
                                  <input  name='' id='all_modules' class='regular-checkbox' type='checkbox'/>
                                  <label class='form-check-label' for='all_modules'></label>&nbsp;&nbsp;&nbsp; ".$this->lang->line("Modules")."
                              </div>
                            </th>";                       
                            echo "<th class='px-3'>"; 
                              echo $this->lang->line("Allowed Privileges");         
                            echo "</th>";
                        echo "</tr>";

                        foreach ($modules as $module => $values) {
                          if($this->session->userdata("user_type")=="Member" && !in_array($values['id'], $this->module_access)) continue;
                          if($values['id']==350) continue;
                          $SL++; 
                          $isChecked = isset($module_access[$values['id']]) && array_key_exists($values['id'], $module_access);
                        ?>
                        <tr class="border-0">
                            <td class='text-left px-3'><?php echo $SL;  ?></td>
                            <td class='text-left px-3'>
                                <!-- Checkbox for modules -->
                                <div class="form-check form-switch d-flex p-0">
                                    <input name="modules[]" id="box<?php echo $SL; ?>" class="modules regular-checkbox" type="checkbox" value="<?php echo $values['id'] ?>"<?php if ($isChecked) echo 'checked'; ?> >
                                    <label class="form-check-label" for="box<?php echo $SL; ?>"></label> &nbsp;&nbsp;&nbsp;<?php echo $values['module_name']; ?>
                                </div>
                            </td>

                            <td class='text-left px-3'>
                                <?php $hide_action = in_array($values['id'], $no_action_modules) ? "d-none" : "";?>
                                <?php foreach ($access_array as $access_key => $access_value) { ?>
                                    <?php
                                    $access_sl++;
                                    $isChecked = isset($module_access[$values['id']]) && in_array($access_key, $module_access[$values['id']]);
                                    ?>
                                    <!-- Checkbox for access -->
                                    <input name="team_access[<?= $values['id'] ?>][]" id="team_access<?= $access_sl ?>" class="<?php echo $hide_action;?> team_access module_access<?= $values['id'] ?> regular-checkbox" type="checkbox" value="<?= $access_key ?>" <?php if ($isChecked) echo 'checked'; ?>>
                                    <label class="<?php echo $hide_action;?> form-check-label me-3 ms-0" for="team_access<?= $access_sl ?>"><?= $access_value ?></label>
                                <?php } ?>
                            </td>
                        </tr>
                          <?php } ?>
                        </table>
                        <span class="text-danger"><?php echo form_error('modules'); ?></span>
                    </div>
                </div>
              </div>
             

            </div>         
          </div>
          <div class="card-footer bg-whitesmoke">
            <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save");?></button>
            <button  type="button" class="btn btn-secondary btn-lg float-right" onclick='goBack("team_member/role_list",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
          </div>
        </div>
      </form>
    </div>
  </div>
      
</section>

          


<script type="text/javascript">
  $(document).ready(function() {
    $("#all_modules").change(function(){
      if ($(this).is(':checked')) 
      $(".modules:not(.mandatory)").prop("checked",true);
      else
      $(".modules:not(.mandatory)").prop("checked",false);
    });

    $('.modules').on('change', function(){    
        var module_id = $(this).val();
        if($(this).prop('checked')) {
            $('.module_access'+module_id+'[value=1]').prop('checked',true);
            $('.module_access'+module_id+'[value=2]').prop('checked',true);
            $('.module_access'+module_id+'[value=3]').prop('checked',true);
        }
        else $('.module_access'+module_id).prop('checked',false);
    });    
  });
</script>
