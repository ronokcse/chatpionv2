<?php $url_channel_param = !empty($url_channel) ? "?channel=".$url_channel : ""; ?>
<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<section class="section section_custom">
  <div class="section-header">
    <h1>
        <i class="fas fa-globe"></i> <?php echo channel_shortname_to_longname($url_channel)." ".$page_title; ?>
    </h1>
    <div class="section-header-button">
        <?php if(check_module_action_access($module_id=352,$actions=[1],$response_type='')): ?>
            <a href="" id="create-http-api" class="btn btn-primary"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('Create')?></a>
        <?php endif?>
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('integration');?>"><?php echo $this->lang->line("Integration"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <input type="hidden" name="id" id="id" class="put_id_here">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title full_width">
                        <span class="card-label"><?php echo $this->lang->line('Connected HTTP APIs')?></span>
                        <p class="text-muted small mb-0"><?php echo $this->lang->line("Connect and manage your WhatsApp HTTP APIs")?></p>
                    </h4>
                    <a href="<?php echo base_url("http_api/list_api_report_all").$url_channel_param;?>" id="" class="btn btn-outline-primary float-right"><i class="fas fa-eye"></i> <?php echo $this->lang->line('HTTP API REPORT')?></a>
                </div>
                <div class="card-body data-card">
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3" id="searchbox">
                                <div class="input-group-prepend">
                                    <select class="form-control select2" id="search_status">
                                        <option value=""><?php echo $this->lang->line("Any Status")?></option>
                                        <option value="1"><?php echo $this->lang->line("Active")?></option>
                                        <option value="0"><?php echo $this->lang->line("Inactive")?></option>
                                    </select>
                                </div>
                                <div class="input-group-prepend">
                                    <select class="form-control select2" id="search_is_mapped">
                                        <option value=""><?php echo $this->lang->line("Any Verification")?></option>
                                        <option value="1"><?php echo $this->lang->line("Verified")?></option>
                                        <option value="0"><?php echo $this->lang->line("Non-verified")?></option>
                                    </select>
                                </div>
                                <div class="input-group-prepend">
                                    <input type="text" class="form-control no-radius" autofocus id="search_value" name="search_value" placeholder="<?php echo $this->lang->line('Search...')?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class='table table-hover table-bordered table-sm w-100' id="mytable" >
                            <thead>
                            <tr class="table-light">
                                <th>#</th>
                                <th><div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div></th>
                                <th><?php echo $this->lang->line("API Name") ?></th>
                                <th><?php echo $this->lang->line("Endpoint URL") ?></th>
                                <th><?php echo $this->lang->line("Channel") ?></th>
                                <th><?php echo $this->lang->line("Status") ?></th>
                                <th><?php echo $this->lang->line("Verified") ?></th>
                                <th><?php echo $this->lang->line("Actions") ?></th>
                                <th><?php echo $this->lang->line("Created at") ?></th>
                                <th><?php echo $this->lang->line("Last Called at") ?></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div id="http_api_create_block_scroll"></div>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="col-12 mt-5" id="http_api_create_block">
            <form id="verify_http_api_form" action="#" method="post">
                <input type="hidden" name="id" class="put_id_here">
                <div class="card box-shadow" id="">
                    <div class="card-header pb-0">
                        <h4 class="card-title">
                            <span class="card-label"><?php echo $this->lang->line('API Connection Details')?></span>
                            <p class="text-muted small mb-0"><?php echo $this->lang->line("Build & verify a HTTP API connection")?></p>
                        </h4>
                    </div>
                    <div class="card-body px-4 pt-2 http_api_create_container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for=""><?php echo $this->lang->line('API Name')?></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" autofocus="" id="api_name" name="api_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for=""><?php echo $this->lang->line('Method')?> *</label>
                                            <div class="input-group">
                                                <?php echo form_dropdown('api_method', $api_method_list, '', ['id' => 'api_method', 'class' => 'form-control select2','style'=>'width:100%', 'autocomplete'=>'off']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for=""><?php echo $this->lang->line('Test Subscriber ID')?> * <span data-toggle="tooltip" title="<?php echo $this->lang->line('This subscriber`s data will be used to verify the connection.')?>"><i class="fas fa-info-circle text-primary"></i></span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" autofocus="" id="test_subscriber_unique_id" name="test_subscriber_unique_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5 d-none">
                                        <div class="form-group">
                                            <label for=""><?php echo $this->lang->line('Social Channel')?> *</label>
                                            <div class="input-group">
                                                <?php echo form_dropdown('api_type', $api_type_list, '', ['id' => 'api_type', 'class' => 'form-control','style'=>'width:100%', 'autocomplete'=>'off']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for=""><?php echo $this->lang->line('End-point URL')?> *</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" autofocus="" id="api_endpoint_url" name="api_endpoint_url">
                                                <span class="input-group-text pointer" data-toggle="modal" data-target="#variable-list-modal"><i data-toggle="tooltip" title="<?php echo $this->lang->line('Use variable in endpoint URL')?>" class="fas fa-list pointer"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="card card-primary mb-4">
                                    <div class="card-header pb-2 d-flex justify-content-between">
                                        <h6 class="pt-2"> <?php echo $this->lang->line("Header Data"); ?></h6>
                                        <div>
                                            <a href="#" class="btn btn-sm btn-outline-dark pl-1 pr-2 py-0 d-none" id="remove_header"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line("Remove"); ?></a>
                                            <a href="#" class="btn btn-sm btn-outline-primary pl-1 pr-2 py-0 ml-2" id="add_header"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add"); ?></a>
                                        </div>
                                    </div>
                                    <div class="card-body pb-4">
                                        <?php
                                        for ($i = 1; $i <= $header_level; $i++) { ?>
                                            <div class="row mt-2 d-none" id="api_header_row_<?php echo $i; ?>">
                                                <div class="col-8 col-md-4">
                                                    <div class="form-group mb-0 api_header_row_key_container" id="api_header_row_key_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Header")?> : <?php echo $this->lang->line("Key")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control api_header_row_key" placeholder="<?php echo $this->lang->line("Key"); ?>" name="api_header_row_key[]" id="api_header_row_key_<?php echo $i; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="form-group mb-0 api_header_row_type_container" data-i="<?php echo $i?>" id="api_header_row_type_container_<?php echo $i;?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Header")?> : <?php echo $this->lang->line("Type")?> </b> <b class="text-warning">*</b></label>
                                                        <select class="form-control select select2 d-block api_header_row_type" style="width: 100%" id="api_header_row_type_<?php echo $i; ?>" name="api_header_row_type[]">
                                                            <option value="static"><?php echo $this->lang->line("Static Value"); ?></option>
                                                            <option value="dynamic"><?php echo $this->lang->line("Dynamic Value"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 api_header_row_value_container" id="api_header_row_value_container_<?php echo $i;?>">
                                                    <div class="form-group mb-0 api_header_row_static_value_container" id="api_header_row_static_value_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Header")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control" placeholder="<?php echo $this->lang->line("Value"); ?>" name="api_header_row_static_value[]" id="api_header_row_static_value_<?php echo $i; ?>">
                                                    </div>
                                                    <div class="form-group mb-0 api_header_row_dynamic_value_container d-none" data-i="<?php echo $i?>"  id="api_header_row_dynamic_value_container_<?php echo $i;?>">
                                                        <div class="input-group" data-i="<?php echo $i?>">
                                                            <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Header")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                            <span class="refresh_custom_field refresh_custom_field_header pointer text-primary ml-2" data-i="<?php echo $i?>">(<?php echo $this->lang->line("Refresh");?>)</span>
                                                            <select class="form-control select select2 api_row_dynamic_value" name="api_header_row_dynamic_value[]" id="api_header_row_dynamic_value_<?php echo $i?>">
                                                                <option value=""><?php echo $this->lang->line("Select Field"); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card card-primary mb-4">
                                    <div class="card-header pb-2 d-flex justify-content-between">
                                        <h6 class="pt-2"> <?php echo $this->lang->line("Body Data"); ?></h6>
                                        <div>
                                            <a href="#" class="btn btn-sm btn-outline-dark pl-1 pr-2 py-0 d-none" id="remove_body"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line("Remove"); ?></a>
                                            <a href="#" class="btn btn-sm btn-outline-primary pl-1 pr-2 py-0 ml-2" id="add_body"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add"); ?></a>
                                        </div>
                                    </div>
                                    <div class="card-body pb-4">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="form-check form-switch float-left">
                                                        <input type="radio" class="form-check-input api_body_data_type" id="api_body_data_type_default" name="api_body_data_type" value="raw" checked>
                                                        <label class="form-check-label mr-3 pointer" for="api_body_data_type_default"><?php echo strtolower($this->lang->line("Default"))?></label>
                                                    </div>

                                                    <div class="form-check form-switch float-left">
                                                        <input type="radio" class="form-check-input api_body_data_type" id="api_body_data_type_form_data" name="api_body_data_type" value="form-data">
                                                        <label class="form-check-label mr-3 pointer" for="api_body_data_type_form_data"><?php echo strtolower($this->lang->line("Form-data"))?></label>
                                                    </div>

                                                    <div class="form-check form-switch float-left">
                                                        <input type="radio" class="form-check-input api_body_data_type" id="api_body_data_type_x_www_form_urlencoded" name="api_body_data_type" value="x-www-form-urlencoded">
                                                        <label class="form-check-label mr-3 pointer" for="api_body_data_type_x_www_form_urlencoded"><?php echo strtolower($this->lang->line("X-www-form-urlencoded"))?></label>
                                                    </div>

                                                    <div class="form-check form-switch float-left">
                                                        <input type="radio" class="form-check-input api_body_data_type" id="api_body_data_type_json" name="api_body_data_type" value="json">
                                                        <label class="form-check-label mr-3 pointer" for="api_body_data_type_json"><?php echo strtolower($this->lang->line("JSON"))?></label>
                                                    </div>

                                                    <div class="form-check form-switch float-left">
                                                        <input type="radio" class="form-check-input api_body_data_type" id="api_body_data_type_binary" name="api_body_data_type" value="binary">
                                                        <label class="form-check-label mr-3 pointer" for="api_body_data_type_binary"><?php echo strtolower($this->lang->line("Binary"))?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        for ($i = 1; $i <= $body_level; $i++) { ?>
                                            <div class="row mt-2 d-none" id="api_body_row_<?php echo $i; ?>">
                                                <div class="col-8 col-md-4">
                                                    <div class="form-group mb-0 api_body_row_key_container" id="api_body_row_key_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Body")?> : <?php echo $this->lang->line("Key")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control api_body_row_key" placeholder="<?php echo $this->lang->line("Key"); ?>" name="api_body_row_key[]" id="api_body_row_key_<?php echo $i; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="form-group mb-0 api_body_row_type_container" data-i="<?php echo $i?>" id="api_body_row_type_container_<?php echo $i;?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Body")?> : <?php echo $this->lang->line("Type")?> </b> <b class="text-warning">*</b></label>
                                                        <select class="form-control select select2 d-block api_body_row_type" style="width: 100%" id="api_body_row_type_<?php echo $i; ?>" name="api_body_row_type[]">
                                                            <option value="static"><?php echo $this->lang->line("Static Value"); ?></option>
                                                            <option value="dynamic"><?php echo $this->lang->line("Dynamic Value"); ?></option>
                                                            <option value="file"><?php echo $this->lang->line("File Upload"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 api_body_row_value_container" id="api_body_row_value_container_<?php echo $i;?>">
                                                    <div class="form-group mb-0 api_body_row_static_value_container" id="api_body_row_static_value_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Body")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control" placeholder="<?php echo $this->lang->line("Value"); ?>" name="api_body_row_static_value[]" id="api_body_row_static_value_<?php echo $i; ?>">
                                                    </div>
                                                    <div class="form-group mb-0 api_body_row_dynamic_value_container d-none" data-i="<?php echo $i?>"  id="api_body_row_dynamic_value_container_<?php echo $i;?>">
                                                        <div class="input-group" data-i="<?php echo $i?>">
                                                            <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Body")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                            <span class="refresh_custom_field refresh_custom_field_body pointer text-primary ml-2" data-i="<?php echo $i?>">(<?php echo $this->lang->line("Refresh");?>)</span>
                                                            <select class="form-control select select2 api_row_dynamic_value" name="api_body_row_dynamic_value[]" id="api_body_row_dynamic_value_<?php echo $i?>">
                                                                <option value=""><?php echo $this->lang->line("Select Field"); ?></option>
                                                            </select> 
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-0 api_body_row_file_value_container d-none" id="api_body_row_file_value_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Body")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control" placeholder="<?php echo $this->lang->line("File URL"); ?>" name="api_body_row_file_value[]" id="api_body_row_file_value_<?php echo $i; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        } ?>

                                        <div class="row mt-2 d-none" id="api_body_row_json_value_container">
                                            <div class="col-12 col-md-8 col-lg-9">
                                                <div class="form-group mb-0" id="">
                                                    <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Body")?> : <?php echo $this->lang->line("JSON")?> </b> <b class="text-warning">*</b></label>
                                                    <textarea name="api_body_row_json_value" id="api_body_row_json_value" cols="30" rows="10" class="form-control" style="min-height: 325px;"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4 col-lg-3">
                                                <div class="form-group mb-0">
                                                    <label class="pb-1 control-label mb-0 w-100">
                                                        <i class="fas fa-hashtag text-primary"></i> <b class="text-dark"><?php echo $this->lang->line("Body")?> : <?php echo $this->lang->line("Variables")?> </b>
                                                        <i class="fas fa-info-circle text-primary pointer" data-toggle="tooltip" title="<?php echo $this->lang->line("Copy and paste the variable name to include it in your JSON content.")?>"></i>
                                                        <div class="input-group mt-1">
                                                            <input type="text" class="form-control no_radius" onkeyup="search_in_ul(this,'variable-list')" style="height:27px;" placeholder="<?php echo $this->lang->line("Search");?>..." name="" id="">
                                                            <span onclick="ajax_generate_dynamic_value_list()" class="input-group-text refresh_custom_field_list bg-primary text-white border-primary pointer no_radius" style="height:27px;" data-toggle="tooltip" title="<?php echo $this->lang->line("Refresh list")?>"><i class="fas fa-sync pt-1" style="font-size: 10px"></i></span>
                                                        </div>
                                                    </label>
                                                    <ul class="list-group rounded-0 border-bottom" id="variable-list" style="height: 292px;overflow-x:auto;">
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card card-primary mb-4">
                                    <div class="card-header pb-2 d-flex justify-content-between">
                                        <h6 class="pt-2"> <?php echo $this->lang->line("Option Data"); ?></h6>
                                        <div>
                                            <a href="#" class="btn btn-sm btn-outline-dark pl-1 pr-2 py-0 d-none" id="remove_option"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line("Remove"); ?></a>
                                            <a href="#" class="btn btn-sm btn-outline-primary pl-1 pr-2 py-0 ml-2" id="add_option"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add"); ?></a>
                                        </div>
                                    </div>
                                    <div class="card-body pb-4">
                                        <?php
                                        for ($i = 1; $i <= $option_level; $i++) { ?>
                                            <div class="row mt-2 d-none" id="api_option_row_<?php echo $i; ?>">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group mb-0 api_option_row_key_container" data-i="<?php echo $i?>" id="api_option_row_key_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Option")?> : <?php echo $this->lang->line("key")?> </b> <b class="text-warning">*</b></label>
                                                        <select name="api_option_row_key[]" class="form-control select2 api_option_row_key" id="api_option_row_key_<?php echo $i?>" style="width:100% !important;">
                                                            <option value=""><?php echo $this->lang->line("Select Option")?></option>
                                                            <?php foreach ($option_list as $key=>$value) :?>
                                                                    <option value="<?php echo $key?>" data-type="<?php echo $value['type']?>" data-default="<?php echo $value["default"]?>"><?php echo str_replace(["_","CURLOPT"],[" ",""],$key)?> [type=<?php echo $value['type']?><?php if($value['type']=='boolean') echo "(0,1)";?> <?php if($value['type']!='string') echo " | default=".$value['default'];?>]</option>
                                                            <?php endforeach ;?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 api_option_row_value_container" id="api_option_row_value_container_<?php echo $i;?>">
                                                    <div class="form-group mb-0 api_option_row_static_value_container" id="api_option_row_static_value_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Option")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control" placeholder="<?php echo $this->lang->line("Value"); ?>" name="api_option_row_static_value[]" id="api_option_row_static_value_<?php echo $i; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card card-primary mb-4">
                                    <div class="card-header pb-2 d-flex justify-content-between">
                                        <h6 class="pt-2"> <?php echo $this->lang->line("Cookie Data"); ?></h6>
                                        <div>
                                            <a href="#" class="btn btn-sm btn-outline-dark pl-1 pr-2 py-0 d-none" id="remove_cookie"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line("Remove"); ?></a>
                                            <a href="#" class="btn btn-sm btn-outline-primary pl-1 pr-2 py-0 ml-2" id="add_cookie"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add"); ?></a>
                                        </div>
                                    </div>
                                    <div class="card-body pb-4">
                                        <?php
                                        for ($i = 1; $i <= $cookie_level; $i++) { ?>
                                            <div class="row mt-2 d-none" id="api_cookie_row_<?php echo $i; ?>">
                                                <div class="col-8 col-md-4">
                                                    <div class="form-group mb-0 api_cookie_row_key_container" id="api_cookie_row_key_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Cookie")?> : <?php echo $this->lang->line("key")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control api_cookie_row_key" placeholder="<?php echo $this->lang->line("Key"); ?>" name="api_cookie_row_key[]" id="api_cookie_row_key_<?php echo $i; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-4 col-md-3">
                                                    <div class="form-group mb-0 api_cookie_row_type_container" data-i="<?php echo $i?>" id="api_cookie_row_type_container_<?php echo $i;?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Cookie")?> : <?php echo $this->lang->line("Type")?> </b> <b class="text-warning">*</b></label>
                                                        <select class="form-control select select2 d-block api_cookie_row_type" style="width: 100%" id="api_cookie_row_type_<?php echo $i; ?>" name="api_cookie_row_type[]">
                                                            <option value="static"><?php echo $this->lang->line("Static Value"); ?></option>
                                                            <option value="dynamic"><?php echo $this->lang->line("Dynamic Value"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-5 api_cookie_row_value_container" id="api_cookie_row_value_container_<?php echo $i;?>">
                                                    <div class="form-group mb-0 api_cookie_row_static_value_container" id="api_cookie_row_static_value_container_<?php echo $i; ?>">
                                                        <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Cookie")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                        <input type="text" class="form-control" placeholder="<?php echo $this->lang->line("Value"); ?>" name="api_cookie_row_static_value[]" id="api_cookie_row_static_value_<?php echo $i; ?>">
                                                    </div>
                                                    <div class="form-group mb-0 api_cookie_row_dynamic_value_container d-none" data-i="<?php echo $i?>"  id="api_cookie_row_dynamic_value_container_<?php echo $i;?>">
                                                        <div class="input-group" data-i="<?php echo $i?>">
                                                            <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Cookie")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                            <span class="refresh_custom_field refresh_custom_field_cookie pointer text-primary ml-2" data-i="<?php echo $i?>">(<?php echo $this->lang->line("Refresh");?>)</span>
                                                            <select class="form-control select select2 api_row_dynamic_value" name="api_cookie_row_dynamic_value[]" id="api_cookie_row_dynamic_value_<?php echo $i?>">
                                                                <option value=""><?php echo $this->lang->line("Select Field"); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-warning p-4 border-warning border-dashed"  role="alert">
                                    <h5 class="alert-heading text-dark">
                                        <i class="fas fa-plug fs-1 float-left mt-1 mr-3"></i>
                                        <?php echo $this->lang->line('HTTP API')?> : <small><?php echo $this->lang->line('How it works?')?></small>
                                    </h5>
                                    <p class=""><?php  echo $this->lang->line('Connect to an external HTTP API by providing the necessary API connection details. After entering the details, verify the connection to retrieve API response data. Use this data to update subscriber information. Note that any changes to the API connection details will not be saved until the connection is verified.') ?> <b><?php echo $this->lang->line("Finally, you need to save the API to activate it.")?></b></p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-footer px-4 py-3 bg-light http_api_create_container" style="">
                        <div class="mb-0">
                            <button type="submit" id="http_api_verify" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $this->lang->line('Verify Connection')?></button>
                        </div>
                    </div>

                </div>
            </form>

            <form id="submit_http_api_form" action="#" method="post">
                <input type="hidden" name="id" class="put_id_here">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-7">
                        <div class="card box-shadow d-none" id="http_api_data_container">
                            <div class="card-header pb-0">
                                <h4 class="card-title d-flex align-iteml-start flex-column">
                                    <span class="card-label"><?php echo $this->lang->line('HTTP API Response Mapping')?></span>
                                    <p class="text-muted small mb-0"><?php echo $this->lang->line("Update subscriber data based on data received from HTTP API call")?></p>
                                </h4>
                            </div>
                            <div class="card-body" style="padding-top:0 !important">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card card-primary mb-0 border-0 no_shadow">
                                            <div class="card-header m-0 p-0 d-flex justify-content-between px-0 py-0 border-0">
                                                <h6 class="pt-2"></h6>
                                                <div>
                                                    <a href="#" class="btn btn-sm btn-outline-dark pl-1 pr-2 py-0 d-none" id="remove_map"><i class="fas fa-times-circle"></i> <?php echo $this->lang->line("Remove"); ?></a>
                                                    <a href="#" class="btn btn-sm btn-outline-primary pl-1 pr-2 py-0 ml-2" id="add_map"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add"); ?></a>
                                                </div>
                                            </div>
                                            <div class="card-body pb-0 px-0" style="padding-top: 0 !important;">
                                                <?php
                                                for ($i = 1; $i <= $map_level; $i++) { ?>
                                                    <div class="row mt-2 d-none api_map_row" id="api_map_row_<?php echo $i; ?>">

                                                        <div class="col-6 col-md-4 pr-1">
                                                            <div class="form-group mb-0 api_map_row_key_container" data-i="<?php echo $i?>"  id="api_map_row_key_container_<?php echo $i;?>">
                                                                <div class="input-group" data-i="<?php echo $i?>">
                                                                    <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Map")?> : <?php echo $this->lang->line("Field")?> </b> <b class="text-warning">*</b></label>
                                                                    <span class="refresh_custom_field refresh_custom_field_map pointer text-primary ml-2" data-i="<?php echo $i?>">(<?php echo $this->lang->line("Refresh");?>)</span>
                                                                    <select class="form-control select select2 api_row_dynamic_value" name="api_map_row_dynamic_value[]" id="api_map_row_dynamic_value_<?php echo $i?>">
                                                                        <option value=""><?php echo $this->lang->line("Select Field"); ?></option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-6 col-md-4 pl-1 pr-md-1">
                                                            <div class="form-group mb-0 api_map_row_value_container" data-i="<?php echo $i?>" id="api_map_row_value_container_<?php echo $i;?>">
                                                                <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Map")?> : <?php echo $this->lang->line("Value")?> </b> <b class="text-warning">*</b></label>
                                                                <select class="form-control select select2 d-block api_map_row_value" style="width: 100%" id="api_map_row_value_<?php echo $i; ?>" name="api_map_row_value[]">
                                                                    <option value=""><?php echo $this->lang->line("Select Map Data"); ?></option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-md-4 pl-md-1">
                                                            <div class="form-group mb-0 api_map_row_formatter_container" data-i="<?php echo $i?>"  id="api_map_row_formatter_container_<?php echo $i;?>">
                                                                <label class="pb-1 control-label mb-0"><b class="text-dark"><?php echo $this->lang->line("Map")?> : <?php echo $this->lang->line("Formatters")?></b></label>
                                                                <select multiple class="form-control select select2 api_map_row_formatter_value" name="api_map_row_formatter_value_<?php echo $i?>[]" id="api_map_row_formatter_value_<?php echo $i?>">
                                                                    <option value=""><?php echo $this->lang->line("Select Formatter"); ?> </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer px-4 py-3 bg-light">
                                <button type="submit" id="save-http-api" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> <?php  echo $this->lang->line('Save API') ?></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-5">
                        <div class="card box-shadow d-none" id="api_data_display_container">
                            <div class="card-header">
                                <h4 class="card-title d-flex align-iteml-start flex-column">
                                    <span class="card-label"><?php echo $this->lang->line('Data Formatters')?></span>
                                    <p class="text-muted small mb-0"><?php echo $this->lang->line("Format or manipulate API data")?></p>
                                </h4>
                            </div>
                            <div class="card-body data-card" id="put_formatter_data">
                                <div class="row">
                                    <div class="col-7 col-lg-5">
                                        <div class="input-group mb-0" id="searchbox">
                                            <div class="input-group-prepend" style="width:100% !important;">
                                                <input type="text" class="form-control no-radius" autofocus id="search_value_formatter" name="search_value_formatter" placeholder="<?php echo $this->lang->line("Search...")?>">
                                                <input type="hidden" name="" id="hidden_http_api_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-5 col-lg-7">
                                        <?php if(check_module_action_access($module_id=352,$actions=[1],$response_type='')): ?>
                                            <a href="" id="create-formatter" class="btn btn-bg btn-outline-primary float-right"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line('New')?></a>
                                        <?php endif ?>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class='table table-hover table-bordered table-sm w-100' id="mytable1" >
                                        <thead>
                                        <tr class="table-light">
                                            <th>#</th>
                                            <th><div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox"  id="datatableSelectAllRows"></div></th>
                                            <th><?php echo $this->lang->line("Name") ?></th>
                                            <th><?php echo $this->lang->line("Type") ?></th>
                                            <th><?php echo $this->lang->line("Formatter Definition") ?></th>
                                            <th><?php echo $this->lang->line("Actions") ?></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-header border-top mb-0 pb-0">
                                <h4 class="card-title d-flex align-iteml-start flex-column">
                                    <span class="card-label"><?php echo $this->lang->line('Raw Data')?></span>
                                    <p class="text-muted small mb-0"><?php echo $this->lang->line("The original API reponse data received in raw format")?></p>
                                </h4>

                                <div class="alert alert-danger alert-dismissible fade show p-4 border-danger border-dashed d-none" role="alert" id="data_error_container">
                                    <h5 class="alert-heading text-dark">
                                        <i class="fas fa-bug fs-1 float-left mt-1 mr-3"></i>
                                        <?php echo $this->lang->line("Error")?> : <small><?php echo $this->lang->line("Error calling API")?></small>
                                    </h5>
                                    <p id="data_error"></p>
                                </div>

                            </div>
                            <div class="card-body">
                                <p>
                                <a class="btn btn-primary" data-toggle="collapse" href="#collapseRawData" role="button" aria-expanded="false" aria-controls="collapseRawData">
                                    <?php echo $this->lang->line("Load Data")?>
                                </a>
                                </p>
                                <div class="collapse" id="collapseRawData">
                                    <textarea style="white-space: pre;overflow-wrap: normal;overflow-x: scroll; height:300px !important" class="form-control mb-4 d-none d-lg-block" rows="12"  id="api_data_display"></textarea>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </form>

        </div>

    </div>
  </div>
</section>






<div class="modal fade" id="formatter-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="template-modal-form" action="#" method="post">
                <div class="modal-header">
                    <h5 class="modal-title full_width" id="">
                        <i class="fas fa-circle"></i> <?php  echo $this->lang->line('Data Formatter') ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">               
                        <span aria-hidden="true">×</span>          
                    </button> 
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="" class="pb-1"><?php  echo $this->lang->line('Name') ?> *</label>
                            <input type="text" class="form-control" id="formatter_name" name="formatter_name" placeholder="<?php echo $this->lang->line('Name it to identify it later')?>">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="" class="pb-1"><?php  echo $this->lang->line('Action') ?> *</label>
                            <?php echo $formatter_dropdown; ?>
                        </div>
                    </div>
                    <div class="row" id="formatter_params_container">

                    </div>

                    <div class="col-12 p-0" id="formatter_example"></div>
                    <div class="col-12 p-0">
                        <div class="alert alert-info alert-dismissible fade show p-4 border-info border-dashed"  role="alert">
                            <h5 class="alert-heading text-dark">
                                <i class="fas fa-keyboard fs-1 float-left mt-1 mr-3"></i>
                                <?php echo $this->lang->line('How to use `Whitespace` ?')?>
                            </h5>
                            <p class="">
                                <?php echo $this->lang->line('You can use whitespace as parameter value. Just type [space] as parameter value.') ;?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex align-iteml-center justify-content-start">
                    <button type="submit" id="save_formatter" class="btn btn-primary fw-bold"><i class="fas fa-save"></i> <?php  echo $this->lang->line('Save Formatter') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="variable-list-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title full_width" id="">
                    <i class="fas fa-circle"></i> <?php  echo $this->lang->line('Variables') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">               
                	<span aria-hidden="true">×</span>          
                </button> 
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <ol class="list-group rounded-0 border-bottom" id="variable-list2"></ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

    .select2-container{
        width: 100% !important;
    }

    .form-control,.select2-selection.select2-selection--single,.select2-selection.select2-selection--multiple{
        border-radius: 0 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered{
        padding-left: 12px !important;
    }
    .select2-container .select2-search--inline .select2-search__field{
        padding-left: 8px !important;
    }    
    .input-group-prepend {
        width: 24.75% !important;
        padding-block-end: 8px;
    }
    .input-group-prepend:not(:last-child) {
        padding-inline-end: 8px;
    }
    .input-group-prepend input,
    .input-group-prepend select {
        width: 100% !important;
    }

    @media (max-width: 575.98px) {
        .input-group-prepend {
        width: 100% !important;
        padding-inline-end: 0 !important;
        }

        .input-group-prepend:nth-child(2),
        .input-group-prepend:nth-child(3) {
        width: 50% !important;
        padding-inline-end: 0px !important;
        }

        .input-group-prepend input,
        .input-group-prepend select {
        width: 100% !important;
        }
    }

    .ui-widget.ui-widget-content{z-index: 9999 !important;}
    .btn-progress-custom {
        position: relative;
        background-image: url("data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJsb2FkZXItMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQogd2lkdGg9IjQwcHgiIGhlaWdodD0iNDBweCIgdmlld0JveD0iMCAwIDUwIDUwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1MCA1MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggZmlsbD0iI2ZmZiIgZD0iTTQzLjkzNSwyNS4xNDVjMC0xMC4zMTgtOC4zNjQtMTguNjgzLTE4LjY4My0xOC42ODNjLTEwLjMxOCwwLTE4LjY4Myw4LjM2NS0xOC42ODMsMTguNjgzaDQuMDY4YzAtOC4wNzEsNi41NDMtMTQuNjE1LDE0LjYxNS0xNC42MTVjOC4wNzIsMCwxNC42MTUsNi41NDMsMTQuNjE1LDE0LjYxNUg0My45MzV6Ij4NCjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZVR5cGU9InhtbCINCiAgYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIg0KICB0eXBlPSJyb3RhdGUiDQogIGZyb209IjAgMjUgMjUiDQogIHRvPSIzNjAgMjUgMjUiDQogIGR1cj0iMC42cyINCiAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiLz4NCjwvcGF0aD4NCjwvc3ZnPg0K");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 20px;
        pointer-events: none;
    }

    /* Modal right */
    @media screen and (min-width: 1400px) {
    .modal{
        overflow : hidden !important;
    }
    .modal-dialog {
        margin-inline-end: 0 !important;
        margin-top: 0 !important;
        height: 100% !important;
    }
    .modal-content form {
        height: 100% !important;
    }
    .modal-content {
        height: 100% !important;
        border-radius: 0 !important;
        overflow:hidden !important;
    }
    .modal-body{
        height: calc(100%  - 140px) !important;
        overflow-y:auto  !important;
        overflow-x:hidden  !important;
    }
}
</style>
<?php include('application/modules/http_api/views/http-api/build_js.php'); ?>

