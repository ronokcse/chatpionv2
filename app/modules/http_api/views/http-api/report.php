<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
<section class="section section_custom">
  <div class="section-header">
    <h1>
        <i class="fas fa-eye"></i> <?php echo channel_shortname_to_longname($url_channel)." ".$page_title; ?>
    </h1>
           
    <div class="section-header-button">
    </div>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('integration');?>"><?php echo $this->lang->line("Integration"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('http_api/build_api').'?channel='.$url_channel;?>"><?php echo $this->lang->line("HTTP API"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
  <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title full_width">
                        <span class="card-label"><?php echo $page_title?></span>
                        <p class="text-muted small mb-0"><?php echo $this->lang->line("All your HTTP API call report in one place")?></p>
                    </h4>                   
                </div>
                <div class="card-body data-card">
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3" id="searchbox">
                                <div class="input-group-prepend">
                                    <?php echo form_dropdown('search_settings_http_api_id', $api_list, $url_edit_id, ['id' => 'search_settings_http_api_id', 'class' => 'form-control select2','style'=>'width:100%', 'autocomplete'=>'off']); ?>
                                </div>
                                <div class="input-group-prepend">
                                    <select class="form-control select2" id="search_status">
                                        <option value=""><?php echo $this->lang->line("Any Response");?></option>
                                        <option value="1"><?php echo $this->lang->line("Success");?></option>
                                        <option value="0"><?php echo $this->lang->line("Error");?></option>
                                    </select>
                                </div>
                                <div class="input-group-prepend">
                                    <input type="text" class="form-control no-radius" autofocus id="search_value" name="search_value" placeholder="<?php echo $this->lang->line("Search...");?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class='table table-hover table-bordered table-sm w-100' id="mytable" >
                            <thead>
                            <tr class="table-light">
                                <th>#</th>
                                <th><?php echo $this->lang->line("ID") ;?></th>
                                <th><?php echo $this->lang->line("API Name") ;?></th>
                                <th><?php echo $this->lang->line("Subscriber ID") ;?></th>
                                <th><?php echo $this->lang->line("Status") ;?></th>
                                <th><?php echo $this->lang->line("Called at") ;?></th>
                                <th><?php echo $this->lang->line("Response") ;?></th>
                                <th><?php echo $this->lang->line("API Data") ;?></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
  </div>
</section>


<style>
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


</style>

<script>
    "use strict";
    var url_channel = '<?php echo $url_channel;?>';
    var perscroll;
    var table;
    var http_api_report_list_url_data = '<?php echo base_url("http_api/list_api_report_all_data");?>';

    $(document).ready(function() {
        table = $("#mytable").DataTable({
            fixedHeader: false,
            colReorder: true,
            serverSide: true,
            processing:true,
            bFilter: false,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            lengthMenu: [
                [5,10, 25, 50, 100],
                [5,10, 25, 50, 100],
            ],
            ajax:
                {
                    "url": http_api_report_list_url_data,
                    "type": 'POST',
                    data: function ( d )
                    {
                        d.search_value = $('#search_value').val();
                        d.search_settings_http_api_id = $('#search_settings_http_api_id').val();
                        d.search_status = $('#search_status').val();
                        d.search_api_type = url_channel;
                    }
                },
            language:
                {
                    url: '<?= base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>'
                },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                    targets: [1],
                    visible: false
                },
                {
                    targets: '',
                    className: 'text-center'
                },
                {
                    targets: [6,7],
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

        $(document).on('change', '#search_settings_http_api_id', function(e) {
            table.draw(false);
        });

        $(document).on('change', '#search_status', function(e) {
            table.draw(false);
        });

        $(document).on('keyup', '#search_value', function(e) {
            if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table.draw(false);
        });
});
</script>
