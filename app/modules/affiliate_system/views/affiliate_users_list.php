<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-calendar-plus"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("affiliate_system"); ?>"><?php echo $this->lang->line("Affiliate System"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive2 data-card">
                                    <table class="table table-bordered" id="mytable_users_list">
                                        <thead>
                                            <tr>
                                                <th>#</th>      
                                                <th><?php echo $this->lang->line("Name"); ?></th>
                                                <th><?php echo $this->lang->line("Email"); ?></th>
                                                <th><?php echo $this->lang->line('Total Earn'); ?></th>
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
        </div>

    </div>
</section>


<script>

    /* Check Email valid or not from Email API section */
    function validateEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
      return regex.test(email);
    }
    
    $(document).ready(function($) {
        var base_url = '<?php echo base_url(); ?>';
        var users_list_perscroll;
        var users_list_table = $("#mytable_users_list").DataTable({
            serverSide: true,
            processing:true,
            bFilter: true,
            order: [[ 1, "desc" ]],
            pageLength: 10,
            ajax: 
            {
                "url": base_url+'affiliate_system/users_list_data',
                "type": 'POST',
            },
            language: 
            {
              url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            columnDefs: [
                {
                  targets: '',
                  className: 'text-center'
                },
                {
                  targets: '',
                  sortable: false
                }
            ],
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
              if(areWeUsingScroll)
              {
                if (users_list_perscroll) users_list_perscroll.destroy();
                users_list_perscroll = new PerfectScrollbar('#mytable_users_list_wrapper .dataTables_scrollBody');
              }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
              if(areWeUsingScroll)
              { 
                if (users_list_perscroll) users_list_perscroll.destroy();
                users_list_perscroll = new PerfectScrollbar('#mytable_users_list_wrapper .dataTables_scrollBody');
              }
            }
        });
    });
</script>
