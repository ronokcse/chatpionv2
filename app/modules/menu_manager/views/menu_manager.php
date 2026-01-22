<link rel="stylesheet" href="<?php echo base_url("plugins/menu_manager/css/bootstrap-iconpicker.min.css");?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/menu_manager/css/menu.css'); ?>">
<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-link"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-button">
         <a class="btn btn-primary reset_menu" href="#">
            <i class="fas fa-retweet"></i> <?php echo lang("Reset To Default"); ?>
         </a> 
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url("menu_manager/index"); ?>"><?php echo lang("Menu Manager"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div> 
        </div>
    </div>

    <div class="section-body">

        <?php if(session()->get('user_type') == 'Admin' && session()->get('license_type') == 'double') : ?>
            <div class="alert alert-light alert-dismissible show fade mt-0 mb-4">
                <div class="alert-body text-center text-primary">
                    <button class="close" data-dismiss="alert">
                        <span>×</span>
                    </button>
                    <i class="fas fa-bell fa-2x"></i> 
                   <BIG> <?php echo lang("Some links that are available in member panel such as 'Payment' (Renew Package, Transaction Log, Usage Log) or support desk are not included here as they are statically added inside application/views/admin/theme/sidebar.php"); ?></BIG>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-bars"></i> <?php echo lang("Current Links"); ?></h4>
                    </div>
                    <div class="card-body">
                        <ul id="myEditor" class="sortableLists list-group">
                        </ul>
                    </div>
                    <div class="card-footer bg-whitesmoke">
                        <button id="btnOut" type="button" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo lang('save'); ?> </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-folder-open"></i> <?php echo lang("Manage Links"); ?></h4>
                    </div>
                    <div class="card-body">
                        <form id="frmEdit">
                            <div class="form-group">
                                <label for="name"><?php echo lang('Name'); ?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control item-menu required" name="text" id="name" data-validation="name" style="width: 160px;">
                                    <div class="input-group-append" title="<?php echo lang('Choose icon color'); ?>" id="color-picker">
                                        <input type="color" name="color" id="color" class="form-control item-menu no_radius" style="width: 50px;" value="#0D8BF1">
                                    </div>
                                    <div class="input-group-btn" id="icon-picker">
                                        <button type="button" id="myEditor_icon" class="btn btn-secondary icon-btn" data-iconset="fontawesome"></button>
                                    </div>
                                    <input type="hidden" name="icon" class="item-menu" id="iconPicker">
                                </div>
                                <span class="red" id="error_msg"></span>
                                <span class="red" id="error_msg4"></span>
                            </div>

                            <!-- Targets -->
                            <div class="form-group">
                                <label for="target"><?php echo lang('Target'); ?></label>
                                <select name="target" id="target" class="form-control item-menu">
                                    <option value="0"><?php echo lang('Internal'); ?></option>
                                    <option value="1" selected="select"><?php echo lang('External'); ?></option>
                                </select>
                            </div>

                            <!-- URL -->
                            <div class="form-group" id="one">
                                <label for="href"><?php echo lang('URL'); ?></label>
                                <input type="text" class="form-control item-menu" id="href" name="href" placeholder="https://example.com">
                                <span class="red" id="error_msg2"><?php echo form_error('url'); ?></span>
                            </div>


                            <!-- Page List -->
                            <div class="form-group" id="two" style="display: none;">
                                <label for="page_list"><?php echo lang('Pages'); ?></label>
                                <select name="page_list" id="page_list" class="form-control item-menu">
                                    <option value=""><?php echo lang('select'); ?></option>
                                    <?php foreach ($page_value as $singlePage) : ?>
                                        <option value="<?php echo $singlePage['id']; ?>"><?php echo $singlePage['page_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="red" id="error_msg3"><?php echo form_error('page_list'); ?></span>
                            </div>
                            
                            <!-- Module Access -->
                            <div class="form-group" style="display: none;">
                                <label for="module_access"><?php echo lang('Module Access'); ?></label>
                                <input type="text" name="module_access" class="form-control item-menu" id="module_access">
                            </div>
                            
                            <!-- Is Menu Manager -->
                            <div class="form-group" style="display: none;">
                                <label for="is_menu_manager"><?php echo lang('Is Menu Manager'); ?></label>
                                <input type="text" name="is_menu_manager" class="form-control item-menu" id="is_menu_manager" value="1">
                            </div>

                            <!-- Only Admin -->
                            <div class="form-group">
                                <label for="only_admin"><?php echo lang('Only Admin'); ?></label>
                                <select name="only_admin" id="only_admin" class="form-control item-menu">
                                    <option value="1"><?php echo lang('Yes'); ?></option>
                                    <option value="0" selected="select"><?php echo lang('No'); ?></option>
                                </select>
                            </div>


                            <!-- Only Member -->
                            <div class="form-group">
                                <label for="only_member"><?php echo lang('Only Member'); ?></label>
                                <select name="only_member" id="only_member" class="form-control item-menu">
                                    <option value="1"><?php echo lang('Yes'); ?></option>
                                     <option value="0" selected="select"><?php echo lang('No'); ?></option>
                                </select>
                            </div>


                            <!-- Addons Id -->
                            <div class="form-group" style="display: none;">
                                <label for="add_ons_id"><?php echo lang('Addons Id'); ?></label>
                                <input type="text" name="add_ons_id" class="form-control item-menu" id="add_ons_id">
                            </div>

                            <div class="form-group">
                                <label><?php echo lang("Header Text"); ?></label>
                                <input type="text" class="form-control item-menu" name="header_text" id="header_text">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-whitesmoke">
                        <button type="button" id="btnUpdate" class="btn btn-warning btn-lg float-right" disabled><i class="fas fa-edit"></i> <?php echo lang('Update'); ?></button>
                        <button type="button" id="btnAdd" class="btn btn-primary btn-lg float-left"><i class="fas fa-plus-circle"></i> <?php echo lang('Add'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    var notAllowed = '<?php echo lang("Menu having link cannot be used as parent.") ?>';
    var three_level_allowed = '<?php echo lang('Third level menu is not allowed.') ?>';
    var drag_drop_not_allowed = '<?php echo lang('System default menu cannot be re-ordered.') ?>';
</script>

<script type="text/javascript" src="<?php echo base_url("plugins/menu_manager/jquery-menu-editor.min.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("plugins/menu_manager/js/iconset/fontawesome5-3-1.min.js")?>"></script>
<script type="text/javascript" src="<?php echo base_url("plugins/menu_manager/js/bootstrap-iconpicker.min.js")?>"></script>

<?php include(APPPATH . "modules/menu_manager/views/menu_manager_js.php"); ?>
