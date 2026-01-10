<script>
"use strict";

var url_edit_id = '<?php echo $url_edit_id;?>';
var url_channel = '<?php echo $url_channel;?>';
var http_api_list_url_data = '<?php echo base_url("http_api/list_api_data");?>';
var http_api_formatter_list_data = '<?php echo base_url("http_api/list_formatter_data");?>';
var http_api_formatter_save_url = '<?php echo base_url("http_api/save_formatter");?>';
var http_api_get_dynamic_value_dropdown_url = '<?php echo base_url("http_api/get_dynamic_value_dropdown");?>';
var http_api_get_dynamic_value_list_url = '<?php echo base_url("http_api/get_dynamic_value_list");?>';
var http_api_verify_url = '<?php echo base_url("http_api/verify_api");?>';
var http_api_edit_url = '<?php echo base_url("http_api/edit_api");?>';
var http_api_submit_url = '<?php echo base_url("http_api/submit_api");?>';
var lang_error_select_channel = '<?php echo $this->lang->line("Please select social channel first");?>';
var header_level = "<?php echo $header_level; ?>";
var body_level = "<?php echo $body_level; ?>";
var option_level = "<?php echo $option_level; ?>";
var cookie_level = "<?php echo $cookie_level; ?>";
var map_level = "<?php echo $map_level; ?>";
var lang_fill_required_fields = "<?php echo $this->lang->line('Please fill the required fields.')?>";
var lang_label_select_dynamic_data = "<?php echo $this->lang->line('Select Field')?>";
var lang_label_select_formatter_data = "<?php echo $this->lang->line('Select Formatter')?>";
var lang_formatter_example = "<?php echo $this->lang->line("Use Case");?>";
var lang_fill_required_header_fields = "<?php echo $this->lang->line('Please fill the required header fields')?>";
var lang_fill_required_body_fields = "<?php echo $this->lang->line('Please fill the required body fields')?>";
var lang_fill_invalid_body_json = "<?php echo $this->lang->line('The body JSON provided is not a valid JSON')?>";
var lang_fill_required_option_fields = "<?php echo $this->lang->line('Please fill the required option fields')?>";
var lang_fill_data_error_option_fields = "<?php echo $this->lang->line('Please check the values of option fields')?>";
var lang_fill_required_cookie_fields = "<?php echo $this->lang->line('Please fill the required cookie fields')?>";
var lang_fill_required_map_fields = "<?php echo $this->lang->line('Please fill the required map fields')?>";
var lang_save_missing_validation = "<?php echo $this->lang->line("Something missing");?>";
var lang_save_variable_list_validation = "<?php echo $this->lang->line("All list type data field must have a `Concat List Items` type formatter.");?>";
var lang_save_variable_list_multiple_validation = "<?php echo $this->lang->line("A list type data field can have only one `Concat List Items` type formatter.");?>";
var lang_save_static_multiple_validation = "<?php echo $this->lang->line("If you are using a `Static Value` formatter, you cannot use another one.");?>";
var csrf_token = $("#csrf_token").val();
if (typeof(csrf_token)==='undefined') csrf_token = '';

var perscroll;
var perscroll1;
var table;
var table1='';
var is_mapped = '0';

var api_header_row_count = 0;
var api_header_row_count_default = 0;
var api_header_row_count_default_original = api_header_row_count_default;
var api_header_row_count_rquired = 0;
var header_validation_enabled = true;

var body_level_original = body_level;
var api_body_row_count = 0;
var api_body_row_count_default = 0;
var api_body_row_count_default_original = api_body_row_count_default;
var api_body_row_count_rquired = 0;
var body_validation_enabled = true;

var api_cookie_row_count = 0;
var api_cookie_row_count_default = 0;
var api_cookie_row_count_default_original = api_cookie_row_count_default;
var api_cookie_row_count_rquired = 0;
var cookie_validation_enabled = true;

var api_option_row_count = 0;
var api_option_row_count_default = 0;
var api_option_row_count_default_original = api_option_row_count_default;
var api_option_row_count_rquired = 0;
var option_validation_enabled = true;

var api_map_row_count = 0;
var api_map_row_count_default = 0;
var api_map_row_count_default_original = api_map_row_count_default;
var api_map_row_count_rquired = 0;
var map_validation_enabled = true;

var dataCustomFieldDropdownStorage = null;
var dataResponseFieldDropdownStorage = null;
var dataFormatterFieldDropdownStorage = null;
var dataFormatterMapStorage = null;
var dataSingleValueStorage = [];


$(document).ready(function() {

    setTimeout(function() {
        if(url_channel!=''){
            $("#api_type").val(url_channel).trigger("change");
            // $("#api_type").parent().parent().parent().hide();
            // $("#api_type").parent().parent().parent().prev().removeClass("col-md-4").addClass("col-md-6");
            // $("#api_type").parent().parent().parent().prev().prev().removeClass("col-md-3").addClass("col-md-6");
        }
        load_default_api_rows();
        ajax_generate_dynamic_value_list(true);
    }, 1000);

    $("#create-http-api").click(function(e) {
        e.preventDefault();
        reinitiate_formdata(true);
    });

    $(document).on('click','.delete-http-api',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
        var id = $(this).attr('data-id');   

        swal({
          title: global_lang_confirm,
          text: global_lang_delete_confirmation,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => 
        {
          if (willDelete){
            $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id,csrf_token},
                    url: link,
                    success: function (response) {
                        if (false === response.error) {
                            iziToast.success({title: global_lang_success,message: response.message,position: 'bottomRight'});
                            table.draw('page');

                        }
                        if (true === response.error) iziToast.error({title: global_lang_error,message: response.message,position: 'bottomRight'});
                        return false;
                    },
                    error: function (xhr, statusText) {
                        const msg = handleAjaxError(xhr, statusText);
                        swal({title: global_lang_error,text: msg,icon: 'error'});
                        return false;
                    },
                });
          } 
        });

    });

    $(document).on('click', '#http_api_verify', function(e) {
        e.preventDefault();
        let api_body_data_type = $('input[name="api_body_data_type"]:checked').val();
        let api_body_row_json_value = $("#api_body_row_json_value").val();
        if($("#api_method").val()=="" || $("#api_type").val()=="" || $("#api_endpoint_url").val()=="" ||  api_body_data_type=="" || $("#test_subscriber_unique_id").val()==""){
            show_erorr(lang_fill_required_fields);
            return false;
        }

        if(api_body_data_type=='json' && api_body_row_json_value==""){
            show_erorr(lang_fill_required_fields);
            return false;
        }

        if(api_body_data_type=='json' && !isValidJSON(api_body_row_json_value)){
            show_erorr(lang_fill_invalid_body_json);
            return false;
        }

        for(var i=1;i<=api_header_row_count;i++){
            if(!header_validation(i)) return false;
        }

        for(var i=1;i<=api_body_row_count;i++){
            if(!body_validation(i)) return false;
        }

        for(var i=1;i<=api_cookie_row_count;i++){
            if(!cookie_validation(i)) return false;
        }

        for(var i=1;i<=api_option_row_count;i++){
            if(!option_validation(i)) return false;
        }

        $("#http_api_verify").addClass('btn-progress');
        var formData = new FormData($("#verify_http_api_form")[0]); //queryString
        formData.append("csrf_token", csrf_token);
        if(api_body_data_type=='json') formData.append('api_body_row_json_value', JSON.stringify(JSON.parse(api_body_row_json_value)));
        $.ajax({
            type: 'POST',
            url: http_api_verify_url,
            data: formData,
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            context: this,
            success: function(response) {
                $("#http_api_verify").removeClass('btn-progress');
                populate_submit_formdata(response,true);
            },
            error: function (xhr, statusText) {
                $("#http_api_verify").removeClass('btn-progress');
                const msg = handleAjaxError(xhr, statusText);
                swal({title: global_lang_error,text: msg,icon: 'error'});
                return false;
            }
        });
    });

    $(document).on('click', '.verify-api', function(e) {
        e.preventDefault();
        reinitiate_formdata(true);
        const id = $(this).data('id');
        is_mapped = $(this).data('is-mapped');

        $.ajax({
            url: http_api_edit_url,
            method: "POST",
            dataType: "JSON",
            async : false,
            data: {id,csrf_token},
            success:function(response)
            {
                populate_verify_formdata(response);
                populate_submit_formdata(response,false);
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                swal({title: global_lang_error,text: msg,icon: 'error'});
                return false;
            }
        });

    });

    $(document).on('click', '#save-http-api', function(e) {
        e.preventDefault();

        for(var i=1;i<=api_map_row_count;i++){
            if(!map_validation(i)) return false;
        }

        const apiMapRowValueElement = $('.api_map_row_value');
        var listMappingError = false
        var listMappingMultipleError = false
        var staticMappingMultipleError = false
        const apiMapRowValueValues = apiMapRowValueElement.map((index, element) => {
            const i = $(element).parent().data('i')
            const title = $(element).data('title');
            const data_type = $("#api_map_row_value_"+i+" option:selected").attr('data-type');
            const formatterValue = $("#api_map_row_formatter_value_"+i).val();
            const formatterType = $("#api_map_row_formatter_value_"+i+" option:selected").map(function() {
              return $(this).data("formatter-type");
            }).get();
            if(data_type=='list' && formatterValue.length==0) listMappingError = true;
            if(data_type=='list' && formatterValue.length>1) listMappingMultipleError = true;
            if(formatterValue.length>1 && jQuery.inArray('static-value', formatterType) !== -1) staticMappingMultipleError = true; //in array
            return {title, value: element.value, i }
        }).get();
        if(listMappingError){            
            swal({title: lang_save_missing_validation,text: lang_save_variable_list_validation,icon: 'warning'});
            return;
        }
        if(listMappingMultipleError){
            swal({title: lang_save_missing_validation,text: lang_save_variable_list_multiple_validation,icon: 'warning'});
            return;
        }
        if(staticMappingMultipleError){
            swal({title: lang_save_missing_validation,text: lang_save_static_multiple_validation,icon: 'warning'});
            return;
        }

        $("#save-http-api").addClass('btn-progress');
        var formData = new FormData($("#submit_http_api_form")[0]); //queryString
        formData.append("csrf_token", csrf_token);
        $.ajax({
            type: 'POST',
            url: http_api_submit_url,
            data: formData,
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            context: this,
            success: function(response) {
                $("#save-http-api").removeClass('btn-progress');
                if(response.error === true){
                    swal({title: global_lang_something_went_wrong,text: response.message,icon: 'error'});
                }
                else{
                   swal({
                    title: global_lang_success,
                    text: response.message,
                    icon: "success"
                    })
                    .then((willDelete) => {
                        if (willDelete) 
                        {
                            location.reload();
                        }
                    });                   
               }
            },
            error: function (xhr, statusText) {
                $("#save-http-api").removeClass('btn-progress');
                const msg = handleAjaxError(xhr, statusText);
                swal({title: global_lang_error,text: msg,icon: 'error'});
                return false;
            }
        });
    });

    $(document).on('change', '#api_method', function(e) {
        e.preventDefault();
        const api_method = $(this).val();

        if(api_method=='GET'){
            $("#api_body_data_type_default").prop("checked",true);
            $("#api_body_data_type_form_data").attr("disabled",true);
            $("#api_body_data_type_x_www_form_urlencoded").attr("disabled",true);
            $("#api_body_data_type_json").attr("disabled",true);
            $("#api_body_data_type_binary").attr("disabled",true);
        }
        else{
            if(api_method=='') $("#api_body_data_type_default").prop("checked",true);
            $("#api_body_data_type_form_data").removeAttr("disabled");
            $("#api_body_data_type_x_www_form_urlencoded").removeAttr("disabled");
            $("#api_body_data_type_json").removeAttr("disabled");
            $("#api_body_data_type_binary").removeAttr("disabled");
        }
    });

    $(document).on('change', '.api_body_data_type', function(e) {
        e.preventDefault();
        const api_body_data_type = $('input[name="api_body_data_type"]:checked').val();
        while (api_body_row_count > 0) {
            $("#remove_body").trigger("click");
        }
        $("#api_body_row_json_value").val("");
        if(api_body_data_type=='binary'){
            body_level = 1;
            $("#add_body").removeClass("d-none");
            $("#add_body").trigger("click");
            $("#remove_body").addClass("d-none");
            $(".api_body_row_type option").removeAttr('disabled');
            $(".api_body_row_type option[value='static']").attr('disabled',true);
            $(".api_body_row_type option[value='dynamic']").attr('disabled',true);
            $(".api_body_row_type").val('file').trigger("change");
            $("#api_body_row_json_value_container").addClass("d-none");
        }
        else{
            body_level = body_level_original;
            if(api_body_data_type=='json'){
                $("#add_body").addClass("d-none");
                $("#remove_body").addClass("d-none");
                ajax_generate_dynamic_value_list(false);
                $("#api_body_row_json_value_container").removeClass("d-none");
            }
            else{
                $("#add_body").removeClass("d-none");
                $("#add_body").trigger("click");
                $("#remove_body").removeClass("d-none");
                $(".api_body_row_type option").removeAttr('disabled');
                $(".api_body_row_type").val('static').trigger("change");
                $("#api_body_row_json_value_container").addClass("d-none");
            }
        }
    });

    $(document).on('change', '#api_type', function(e) {
        e.preventDefault();
        ajax_generate_dynamic_value_dropdown(false,false);
        ajax_generate_dynamic_value_list(false);
    });

    $(document).on('click', '.refresh_custom_field', function(e) {
        e.preventDefault();
        ajax_generate_dynamic_value_dropdown(false,true);
    });

    $(document).on('change', '.api_header_row_type', function(e) {
        e.preventDefault();
        const i = $(this).parent().data('i');
        var value_type = $(this).val();
        if(value_type == 'static'){
            $("#api_header_row_dynamic_value_container_"+i+" input").val('');
            $("#api_header_row_dynamic_value_container_"+i+" select option:first").prop('selected', true).trigger('change');

            $("#api_header_row_dynamic_value_container_"+i).addClass('d-none');
            $("#api_header_row_static_value_container_"+i).removeClass('d-none');
        }
        else {
            $("#api_header_row_static_value_container_"+i+" input").val('');
            $("#api_header_row_static_value_container_"+i+" select option:first").prop('selected', true).trigger('change');

            $("#api_header_row_static_value_container_"+i).addClass('d-none');
            $("#api_header_row_dynamic_value_container_"+i).removeClass('d-none');
            ajax_generate_dynamic_value_dropdown(true,true);
        }
    });

    $(document).on('change', '.api_body_row_type', function(e) {
        e.preventDefault();

        const i = $(this).parent().data('i');
        var value_type = $(this).val();
        if(value_type == 'static'){
            $("#api_body_row_dynamic_value_container_"+i+" input").val('');
            $("#api_body_row_dynamic_value_container_"+i+" select option:first").prop('selected', true).trigger('change');
            $("#api_body_row_file_value_container_"+i+" input").val('');
            $("#api_body_row_file_value_container_"+i+" select option:first").prop('selected', true).trigger('change');

            $("#api_body_row_dynamic_value_container_"+i).addClass('d-none');
            $("#api_body_row_file_value_container_"+i).addClass('d-none');
            $("#api_body_row_static_value_container_"+i).removeClass('d-none');
        }
        else if(value_type == 'dynamic') {
            $("#api_body_row_static_value_container_"+i+" input").val('');
            $("#api_body_row_static_value_container_"+i+" select option:first").prop('selected', true).trigger('change');
            $("#api_body_row_file_value_container_"+i+" input").val('');
            $("#api_body_row_file_value_container_"+i+" select option:first").prop('selected', true).trigger('change');

            $("#api_body_row_static_value_container_"+i).addClass('d-none');
            $("#api_body_row_file_value_container_"+i).addClass('d-none');
            $("#api_body_row_dynamic_value_container_"+i).removeClass('d-none');
            ajax_generate_dynamic_value_dropdown(true,true);
        }
        else { //file
            $("#api_body_row_static_value_container_"+i+" input").val('');
            $("#api_body_row_static_value_container_"+i+" select option:first").prop('selected', true).trigger('change');
            $("#api_body_row_dynamic_value_container_"+i+" input").val('');
            $("#api_body_row_dynamic_value_container_"+i+" select option:first").prop('selected', true).trigger('change');

            $("#api_body_row_static_value_container_"+i).addClass('d-none');
            $("#api_body_row_dynamic_value_container_"+i).addClass('d-none');
            $("#api_body_row_file_value_container_"+i).removeClass('d-none');
        }
    });

    $(document).on('change', '.api_cookie_row_type', function(e) {
        e.preventDefault();
        const i = $(this).parent().data('i');
        var value_type = $(this).val();
        if(value_type == 'static'){
            $("#api_cookie_row_dynamic_value_container_"+i+" input").val('');
            $("#api_cookie_row_dynamic_value_container_"+i+" select option:first").prop('selected', true).trigger('change');

            $("#api_cookie_row_dynamic_value_container_"+i).addClass('d-none');
            $("#api_cookie_row_static_value_container_"+i).removeClass('d-none');
        }
        else {
            $("#api_cookie_row_static_value_container_"+i+" input").val('');
            $("#api_cookie_row_static_value_container_"+i+" select option:first").prop('selected', true).trigger('change');

            $("#api_cookie_row_static_value_container_"+i).addClass('d-none');
            $("#api_cookie_row_dynamic_value_container_"+i).removeClass('d-none');
            ajax_generate_dynamic_value_dropdown(true,true);
        }
    });

    $(document).on('change', '.api_option_row_key', function(e) {
        e.preventDefault();
        const i = $(this).parent().data('i');
        const id = $(this).attr('id');
        var data_type = $("#"+id+" option:selected").data('type');
        var default_value = $("#"+id+" option:selected").data('default');
        $("#api_option_row_static_value_"+i).attr('data-type',data_type);
        $("#api_option_row_static_value_"+i).attr('data-default',data_type);
        if(option_validation_enabled) $("#api_option_row_static_value_"+i).val(default_value);
    });

    $(document).on('click', '#add_header', function(e) {
        e.preventDefault();
        if (header_validation(api_header_row_count)) {
            api_header_row_count++;

            $("#api_header_row_" + api_header_row_count).removeClass('d-none');
            if(api_header_row_count == header_level) $("#add_header").addClass("d-none");
            else $("#add_header").removeClass("d-none");

            if(api_header_row_count == api_header_row_count_rquired) $("#remove_header").addClass('d-none');
            else $("#remove_header").removeClass('d-none');
        }
    });

    $(document).on('click', '#add_body', function(e) {
        e.preventDefault();
        if (body_validation(api_body_row_count)) {
            api_body_row_count++;

            $("#api_body_row_" + api_body_row_count).removeClass('d-none');
            if(api_body_row_count == body_level) $("#add_body").addClass("d-none");
            else $("#add_body").removeClass("d-none");

            if(api_body_row_count == api_body_row_count_rquired) $("#remove_body").addClass('d-none');
            else $("#remove_body").removeClass('d-none');
        }
    });

    $(document).on('click', '#add_option', function(e) {
        e.preventDefault();
        if (option_validation(api_option_row_count)) {
            api_option_row_count++;

            $("#api_option_row_" + api_option_row_count).removeClass('d-none');
            if(api_option_row_count == option_level) $("#add_option").addClass("d-none");
            else $("#add_option").removeClass("d-none");

            if(api_option_row_count == api_option_row_count_rquired) $("#remove_option").addClass('d-none');
            else $("#remove_option").removeClass('d-none');
        }
    });

    $(document).on('click', '#add_cookie', function(e) {
        e.preventDefault();
        if (cookie_validation(api_cookie_row_count)) {
            api_cookie_row_count++;

            $("#api_cookie_row_" + api_cookie_row_count).removeClass('d-none');
            if(api_cookie_row_count == cookie_level) $("#add_cookie").addClass("d-none");
            else $("#add_cookie").removeClass("d-none");

            if(api_cookie_row_count == api_cookie_row_count_rquired) $("#remove_cookie").addClass('d-none');
            else $("#remove_cookie").removeClass('d-none');
        }
    });

    $(document).on('click', '#add_map', function(e) {
        e.preventDefault();
        if (map_validation(api_map_row_count)) {
            api_map_row_count++;

            $("#api_map_row_" + api_map_row_count).removeClass('d-none');
            if(api_map_row_count == map_level) $("#add_map").addClass("d-none");
            else $("#add_map").removeClass("d-none");

            if(api_map_row_count == api_map_row_count_rquired) $("#remove_map").addClass('d-none');
            else $("#remove_map").removeClass('d-none');

            ajax_generate_dynamic_value_dropdown(true,true);

            var selectedElemForamtter = "#api_map_row_formatter_container_"+api_map_row_count+" .api_map_row_formatter_value";
            var selectedValueForamtter = $(selectedElemForamtter).val()
            var selectedNameForamtter = $(selectedElemForamtter).attr('name');
            var selectedIdForamtter = $(selectedElemForamtter).attr('id');
            $(selectedElemForamtter).empty().append($(dataFormatterFieldDropdownStorage).children());
            $(selectedElemForamtter).attr('name',selectedNameForamtter);
            $(selectedElemForamtter).attr('id',selectedIdForamtter);
            setTimeout(function() {
                $("#"+selectedIdForamtter).select2({width: "100%",placeholder: lang_label_select_formatter_data});
            }, 100);
            if(selectedValueForamtter && selectedValueForamtter.length>0){
                for( const selectedValueForamtterSingle of selectedValueForamtter){
                    if(selectedValueForamtterSingle!='') {
                        $("#"+selectedIdForamtter+' option[value='+selectedValueForamtterSingle+']').prop("selected",true);
                    }
                }
            }

            var selectedElemValue = "#api_map_row_value_container_"+api_map_row_count+" .api_map_row_value";
            var selectedValueValue = $(selectedElemValue).val()
            var selectedNameValue = $(selectedElemValue).attr('name');
            var selectedIdValue = $(selectedElemValue).attr('id');
            $(selectedElemValue).empty().append($(dataResponseFieldDropdownStorage).children());
            $(selectedElemValue).attr('name',selectedNameValue);
            $(selectedElemValue).attr('id',selectedIdValue);
            setTimeout(function() {
                $("#"+selectedIdValue).select2();
            }, 100);
            if($(this).find("#"+selectedIdValue+' option[value="' + selectedValueValue + '"]').length === 0) selectedValueValue = "";
        }
    });

    $(document).on('click', '#remove_header', function(e) {
        e.preventDefault();

        $("#api_header_row_" + api_header_row_count + " input").val('');
        $("#api_header_row_" + api_header_row_count + " select option:first").prop('selected', true).trigger('change');
        $("#api_header_row_" + api_header_row_count).addClass('d-none');

        api_header_row_count--;

        if(api_header_row_count == header_level) $("#add_header").addClass("d-none");
        else $("#add_header").removeClass("d-none");

        if(api_header_row_count == api_header_row_count_rquired) $("#remove_header").addClass('d-none');
        else $("#remove_header").removeClass('d-none');
    });

    $(document).on('click', '#remove_body', function(e) {
        e.preventDefault();

        $("#api_body_row_" + api_body_row_count + " input").val('');
        $("#api_body_row_" + api_body_row_count + " select option:first").prop('selected', true).trigger('change');
        $("#api_body_row_" + api_body_row_count).addClass('d-none');

        api_body_row_count--;

        if(api_body_row_count == body_level) $("#add_body").addClass("d-none");
        else $("#add_body").removeClass("d-none");

        if(api_body_row_count == api_body_row_count_rquired) $("#remove_body").addClass('d-none');
        else $("#remove_body").removeClass('d-none');
    });

    $(document).on('click', '#remove_option', function(e) {
        e.preventDefault();

        $("#api_option_row_" + api_option_row_count + " input").val('');
        $("#api_option_row_" + api_option_row_count + " select option:first").prop('selected', true).trigger('change');
        $("#api_option_row_" + api_option_row_count).addClass('d-none');

        api_option_row_count--;

        if(api_option_row_count == option_level) $("#add_option").addClass("d-none");
        else $("#add_option").removeClass("d-none");

        if(api_option_row_count == api_option_row_count_rquired) $("#remove_option").addClass('d-none');
        else $("#remove_option").removeClass('d-none');
    });

    $(document).on('click', '#remove_cookie', function(e) {
        e.preventDefault();

        $("#api_cookie_row_" + api_cookie_row_count + " input").val('');
        $("#api_cookie_row_" + api_cookie_row_count + " select option:first").prop('selected', true).trigger('change');
        $("#api_cookie_row_" + api_cookie_row_count).addClass('d-none');

        api_cookie_row_count--;

        if(api_cookie_row_count == cookie_level) $("#add_cookie").addClass("d-none");
        else $("#add_cookie").removeClass("d-none");

        if(api_cookie_row_count == api_cookie_row_count_rquired) $("#remove_cookie").addClass('d-none');
        else $("#remove_cookie").removeClass('d-none');
    });

    $(document).on('click', '#remove_map', function(e) {
        e.preventDefault();

        $("#api_map_row_dynamic_value_" + api_map_row_count).val('');
        $("#api_map_row_value_" + api_map_row_count).val('').trigger('change');
        $("#api_map_row_formatter_value_" + api_map_row_count).val(null).trigger('change');
        $("#api_map_row_" + api_map_row_count).addClass('d-none');

        api_map_row_count--;

        if(api_map_row_count == map_level) $("#add_map").addClass("d-none");
        else $("#add_map").removeClass("d-none");

        if(api_map_row_count == api_map_row_count_rquired) $("#remove_map").addClass('d-none');
        else $("#remove_map").removeClass('d-none');
    });

    $(document).on('blur', '#id', function(e) {
        e.preventDefault();
        $("#http_api_data_container,#api_data_display_container").removeClass("d-none");
        if(table1==''){
            table1 = load_formatter_datatable();
        }
        else table1.draw(false);
    });

    $(document).on('click','#create-formatter',function(e){
        e.preventDefault();
        $("#formatter-modal").modal('show');
    });

    $('#formatter-modal').on('show.bs.modal', function (event) {
        $("#formatter_name").val('');
        $("#formatter_type").val('');
        $("#formatter_params_container").html('');
        $("#formatter_example").html('').addClass('d-none');
        $("#formatter_type").select2({width: "100%"});
    });

    $('#formatter_type').on('change', function (event) {
        if($("#formatter_type").val()!=''){
            var count = $("#formatter_type option:selected").attr('data-param-count');
            var params = JSON.parse($("#formatter_type option:selected").attr('data-params'));
            var html = generate_formatter_params(count,params);
            var example = '<div class="alert alert-warning alert-dismissible fade show p-4 border-warning border-dashed"  role="alert">'+
            '<h5 class="alert-heading text-warning">'+
            '<i class="fas fa-info-circle fs-1 float-start mt-1 me-3"></i>'+
            lang_formatter_example+
            '</h5>'+
            '<p class="">'+
            $("#formatter_type option:selected").attr('data-example')+
            '</p>'+
            '</div>';
            $("#formatter_params_container").html(html);
            $("#formatter_example").html(example).removeClass('d-none');
        }
        else {
           $("#formatter_params_container").html('');
           $("#formatter_example").html('').addClass('d-none');
        }

        setTimeout(function(){
            $(".formatter_params[param-categoty='dynamic']").autocomplete({source: dataSingleValueStorage});
        }, 1000);

    });

    $('#save_formatter').on('click', function (e) {
        e.preventDefault();
        var formatter_name = $("#formatter_name").val();
        var formatter_type = $("#formatter_type").val();
        var formatter_params = $("input[name='formatter_params[]']").map(function(){return $(this).val();}).get();
        var settings_http_api_id = $("#id").val();

        var is_empty = $('.formatter_params').filter(function() {
            var attr = $(this).attr('required');
            return typeof attr !== typeof undefined && this.value != ''
        });

        if(formatter_name=='' || formatter_type=='' || is_empty.length == 0){
            swal({title: global_lang_warning,text:lang_save_missing_validation,icon: 'warning'});
            return false;
        }

        $.ajax({
            url: http_api_formatter_save_url,
            type: 'POST',
            dataType: 'JSON',
            data:{formatter_name,formatter_type,formatter_params,settings_http_api_id,csrf_token},
            success:function(response) {
                if(response.error === false){
                    $("#formatter-modal").modal('hide');
                    iziToast.success({title: global_lang_success,message: response.message,position: 'bottomRight'});
                    table1.draw(false);

                    var display=''
                    if(response.data.formatter_type!='static-value') display = response.data.formatter_name+' ('+response.data.formatter_type+')';
                    else {
                        var params = JSON.parse(response.data.params);
                        var staticValue = '';
                        if(typeof(params[1])!=='undefined') staticValue = params[1];
                        display = response.data.formatter_name+' : '+staticValue;
                    }
                    var newOption = '<option data-formatter-type="'+response.data.formatter_type+'" value="'+response.data.id+'">'+display+'</option>';
                    $('.api_map_row_formatter_value').append(newOption).trigger('change');

                }
                else swal({ title: global_lang_error, text: response.message, icon: 'error' });
                
            }
        });
    });

    $(document).on('click','.delete-formatter',function(e){
        e.preventDefault();
        var link = $(this).attr("href");
        var id = $(this).attr('data-id');

        swal({
          title: global_lang_confirm,
          text: global_lang_delete_confirmation,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => 
        {
          if (willDelete) 
          {
            $.ajax({
                    context:this,
                    method: 'post',
                    dataType: 'JSON',
                    data: {id,csrf_token},
                    url: link,
                    success: function (response) {
                        if (false === response.error) {
                            iziToast.success({title: global_lang_success,message: response.message,position: 'bottomRight'});
                            table1.draw('page');

                            var getId;
                            $('.api_map_row_formatter_value').each(function(index,item){
                                getId = $(this).attr("id");
                                $("#"+getId+" option[value='"+id+"']").remove().trigger('change');
                            });
                        }
                        if (true === response.error) iziToast.error({title: global_lang_error,message: response.message,position: 'bottomRight'});
                        return false;
                    },
                    error: function (xhr, statusText) {
                        const msg = handleAjaxError(xhr, statusText);
                        swal({title: global_lang_error,text: msg,icon: 'error'});
                        return false;
                    },
                });
          }
        });

    });

    table = $("#mytable").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 9, "desc" ]],
        pageLength: 5,
        lengthMenu: [
            [5,10, 25, 50, 100],
            [5,10, 25, 50, 100],
        ],
        ajax:
            {
                "url": http_api_list_url_data,
                "type": 'POST',
                data: function ( d )
                {
                    d.search_value = $('#search_value').val();
                    d.search_status = $('#search_status').val();
                    d.search_is_mapped = $('#search_is_mapped').val();
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
                targets: [2,4,5,6,7,8,9],
                className: 'text-center'
            },
            {
                targets: '',
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(url_edit_id!='') {

                setTimeout(function(){
                    $('.verify-api[data-id="'+url_edit_id+'"]').trigger('click');
                }, 1000);
            }
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

    $(document).on('change', '#search_status,#search_is_mapped', function(e) {
        table.draw(false);
    });

    $(document).on('keyup', '#search_value', function(e) {
        if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table.draw(false);
    });

    $(document).on('keyup', '#search_value_formatter', function(e) {
        if(e.which == 13 || $(this).val().length>2 || $(this).val().length==0) table1.draw(false);
    });

    $(document).on('change','.api_map_row_value',function(e){
        const i = $(this).parent().data('i');
        const data_type = $("#api_map_row_value_"+i+" option:selected").attr('data-type');
        const formatter_id = "#api_map_row_formatter_value_"+i;

        if(data_type=='single'){
            $(formatter_id+" option").removeAttr('disabled');
            $(formatter_id+" option[data-formatter-type='concat-list-items']").attr('disabled','disabled');
        }
        else if(data_type=='list'){
            $(formatter_id+" option").attr('disabled','disabled');
            $(formatter_id+" option[data-formatter-type='concat-list-items']").removeAttr('disabled');
        }
        else{
            $(formatter_id+" option").removeAttr('disabled');
        }
    });

    $(document).on('select2:selecting','.api_map_row_formatter_value', function (e) {
      const i = $(this).parent().data('i');
      const formatterType = $("#api_map_row_formatter_value_"+i+" option:selected").map(function() {
        return $(this).data("formatter-type");
      }).get();

      if(formatterType.length>0 && jQuery.inArray('concat-list-items', formatterType) !== -1) {
        e.preventDefault();
      }
      if(formatterType.length>0 && jQuery.inArray('static-value', formatterType) !== -1) {
        e.preventDefault();
      }
    });

    $(document).on('select2:select','.api_map_row_formatter_value', function (e) {
        const i = $(this).parent().data('i');
        const formatterType = $("#api_map_row_formatter_value_"+i+" option:selected").map(function() {
        return $(this).data("formatter-type");
        }).get();
        if(formatterType.length>0 && jQuery.inArray('static-value', formatterType) !== -1) {
            $("#api_map_row_value_"+i).val('[space]').trigger('change');
        }
    });

    $(document).on('select2:unselect','.api_map_row_formatter_value', function (e) {
      const i = $(this).parent().data('i');
      const formatterType = $("#api_map_row_formatter_value_"+i+" option:selected").map(function() {
        return $(this).data("formatter-type");
      }).get();
      if(jQuery.inArray('static-value', formatterType) === -1) { //not in array
         $("#api_map_row_value_"+i).val('').trigger('change');
      } else  $("#api_map_row_value_"+i).val('[space]').trigger('change');
    });
});


function load_default_api_rows(){
    if(api_header_row_count_default>0){
        header_validation_enabled = false;
        for(var i=1;i<=api_header_row_count_default;i++){
            $("#add_header").trigger("click");
        }
        header_validation_enabled = true;
    }

    if(api_body_row_count_default>0){
        body_validation_enabled = false;
        for(var i=1;i<=api_body_row_count_default;i++){
            $("#add_body").trigger("click");
        }
        body_validation_enabled = true;
    }

    if(api_option_row_count_default>0){
        option_validation_enabled = false;
        for(var i=1;i<=api_option_row_count_default;i++){
            $("#add_option").trigger("click");
        }
        option_validation_enabled = true;
    }

    if(api_cookie_row_count_default>0){
        cookie_validation_enabled = false;
        for(var i=1;i<=api_cookie_row_count_default;i++){
            $("#add_cookie").trigger("click");
        }
        cookie_validation_enabled = true;
    }
}

function load_default_map_rows(){
    if(api_map_row_count_default>0){
        map_validation_enabled = false;
        for(var i=1;i<=api_map_row_count_default;i++){
            $("#add_map").trigger("click");
        }
        map_validation_enabled = true;
    }
}

function reset_form(){
    api_header_row_count_default = api_header_row_count_default_original;
    api_body_row_count_default = api_body_row_count_default_original;
    api_cookie_row_count_default = api_cookie_row_count_default_original;
    api_option_row_count_default = api_option_row_count_default_original;

    $('.put_id_here').val('');

    $("#api_name").val('');
    $("#api_method").val('').trigger('change');
    $("#api_type").val(url_channel).trigger('change');
    $("#api_endpoint_url").val('');
    $("#test_subscriber_unique_id").val('');

    if(api_header_row_count>0){
        while(api_header_row_count>0){
            $("#remove_header").trigger("click");
        }
    }
    if(api_body_row_count>0){
        while(api_body_row_count>0){
            $("#remove_body").trigger("click");
        }
    }
    if(api_option_row_count>0){
        while(api_option_row_count>0){
            $("#remove_option").trigger("click");
        }
    }
    if(api_cookie_row_count>0){
        while(api_cookie_row_count>0){
            $("#remove_cookie").trigger("click");
        }
    }

    reset_form_mapping();

}

function reset_form_mapping(){
    api_map_row_count_default = api_map_row_count_default_original;
    if(api_map_row_count>0){
        while(api_map_row_count>0){
            $("#remove_map").trigger("click");
        }
    }
}

function populate_verify_formdata(response){
    $("#api_name").val(response.api_name);
    $("#api_method").val(response.api_method).trigger('change');
    $("#api_type").val(response.api_type).trigger('change');
    $("#api_endpoint_url").val(response.api_endpoint_url);
    $("#test_subscriber_unique_id").val(response.test_subscriber_unique_id);

    let api_data = JSON.parse(response.api_data);
    let body_data_type = api_data.body_data_type;
    let body_data_json = api_data.body_data_json;
    if(body_data_type) {
        $('.api_body_data_type[value="'+body_data_type+'"]').prop("checked", true).trigger("change");
        if(body_data_type=='json' && body_data_json && isValidJSON(body_data_json)) {
            let body_data_json_formatted = JSON.stringify(JSON.parse(body_data_json), null, '\t');
            $("#api_body_row_json_value").val(body_data_json_formatted);
        }
    }

    let header_data = api_data.header_data;
    let body_data = api_data.body_data;
    let option_data = api_data.option_data;
    let cookie_data = api_data.cookie_data;
    let call_default_row_loader = false;
    let header_data_length = header_data ? Object.keys(header_data).length : 0;
    let body_data_length = body_data ? Object.keys(body_data).length : 0;
    let option_data_length = option_data ? Object.keys(option_data).length : 0;
    let cookie_data_length = cookie_data ? Object.keys(cookie_data).length : 0;

    if(header_data_length > 0 ){
        api_header_row_count_default = header_data_length;
        call_default_row_loader = true;
    }
    if(body_data_length > 0 ){
        api_body_row_count_default =body_data_length;
        call_default_row_loader = true;
    }
    if(option_data_length > 0 ){
        api_option_row_count_default = option_data_length;
        call_default_row_loader = true;
    }
    if(cookie_data_length > 0 ){
        api_cookie_row_count_default = cookie_data_length;
        call_default_row_loader = true;
    }

    if(call_default_row_loader) load_default_api_rows();

    setTimeout(function() {
        if(header_data_length > 0 ){
            for (let i = 1; i <= header_data_length; i++) {
                $("#api_header_row_key_"+i).val(header_data[i].key);
                let api_type = header_data[i].type;
                $("#api_header_row_type_"+i).val(api_type).trigger("change");
                if(api_type=="static") $("#api_header_row_static_value_"+i).val(header_data[i].value);
                else if(api_type=="dynamic") $("#api_header_row_dynamic_value_"+i).val(header_data[i].value).trigger("change");
            }
        }
        if(body_data_length > 0 ){
            for (let i = 1; i <= body_data_length; i++) {
                $("#api_body_row_key_"+i).val(body_data[i].key);
                let api_type = body_data[i].type;
                $("#api_body_row_type_"+i).val(api_type).trigger("change");
                if(api_type=="static") $("#api_body_row_static_value_"+i).val(body_data[i].value);
                else if(api_type=="dynamic") $("#api_body_row_dynamic_value_"+i).val(body_data[i].value).trigger("change");
                else if(api_type=="file") $("#api_body_row_file_value_"+i).val(body_data[i].value);
            }
        }
        if(option_data_length > 0 ){
            for (let i = 1; i <= option_data_length; i++) {
                $("#api_option_row_key_"+i).val(option_data[i].key).trigger("change");
                $("#api_option_row_static_value_"+i).val(option_data[i].value);
            }
        }
        if(cookie_data_length > 0 ){
            for (let i = 1; i <= cookie_data_length; i++) {
                $("#api_cookie_row_key_"+i).val(cookie_data[i].key);
                let api_type = cookie_data[i].type;
                $("#api_cookie_row_type_"+i).val(api_type).trigger("change");
                if(api_type=="static") $("#api_cookie_row_static_value_"+i).val(cookie_data[i].value);
                else if(api_type=="dynamic") $("#api_cookie_row_dynamic_value_"+i).val(cookie_data[i].value).trigger("change");
            }
        }
    }, 1000);
}

function populate_submit_formdata(response,use_new_map_data){
    $('.put_id_here').val(response.id);
    $('#id').blur();

    dataSingleValueStorage = response.single_data_list;
    dataResponseFieldDropdownStorage = response.response_data_dropdown;
    dataFormatterFieldDropdownStorage = response.formatter_dropdown;

    let last_call_data;

    if(use_new_map_data) last_call_data = JSON.parse(response.last_call_data);
    else last_call_data = response.last_call_data_mapped ? JSON.parse(response.last_call_data_mapped) : JSON.parse(response.last_call_data);

    let error_message = last_call_data.data_error;
    if(typeof(error_message)!=='undefined') {
        iziToast.error({title: global_lang_error,message: error_message,position: 'bottomRight'});
        $("#data_error").html(error_message);
        $("#data_error_container").removeClass("d-none");
    }
    else{
        $("#data_error").html("");
        $("#data_error_container").addClass("d-none");
    }

    let last_call_data_formatted = JSON.stringify(last_call_data, null, '\t');
    $("#api_data_display").text(last_call_data_formatted);

    reset_form_mapping();

    let mapping_data = response.mapping_data;
    let mapping_data_length = 0;
    if(mapping_data){
        mapping_data = JSON.parse(mapping_data);
        mapping_data_length = Object.keys(mapping_data).length;

        if(mapping_data_length > api_map_row_count_default){
            api_map_row_count_default = mapping_data_length;
        }
    }

    load_default_map_rows();

    setTimeout(function() {
        if(mapping_data_length > 0 ){
            for (let i = 1; i <= mapping_data_length; i++) {

                let formtter_values = mapping_data[i].formatter;

                if(formtter_values && formtter_values.length>0){
                    for( const item of formtter_values){
                        if(item!='') {
                            $("#api_map_row_formatter_value_"+i+' option[value='+item+']').prop("selected",true);
                        }
                    }
                }
                $("#api_map_row_formatter_value_"+i).trigger('change');
                $("#api_map_row_dynamic_value_"+i).val(mapping_data[i].field).trigger('change');
                $("#api_map_row_value_"+i).val(mapping_data[i].variable).trigger('change');
            }
        }
    }, 1000);
}

function header_validation(i) {
    if(!header_validation_enabled) return true;
    var error_found = false;
    $('#api_header_row_key_container_'+i).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_header_fields);
        return false;
    }

    var data_container_id = "";
    if($("#api_header_row_type_"+i).val() == 'static') data_container_id = '#api_header_row_static_value_container_'+i;
    else data_container_id = '#api_header_row_dynamic_value_container_'+i;

    $(data_container_id).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_header_fields);
        return false;
    }
    return true;

}

function body_validation(i) {
    if(!body_validation_enabled) return true;
    var error_found = false;
    $('#api_body_row_key_container_'+i).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_body_fields);
        return false;
    }

    var data_container_id = "";
    if($("#api_body_row_type_"+i).val() == 'static') data_container_id = '#api_body_row_static_value_container_'+i;
    else if($("#api_body_row_type_"+i).val() == 'dynamic') data_container_id = '#api_body_row_dynamic_value_container_'+i;
    else data_container_id = '#api_body_row_file_value_container_'+i;

    $(data_container_id).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_body_fields);
        return false;
    }
    return true;

}

function option_validation(i) {
    if(!option_validation_enabled) return true;
    var error_found = false;
    var error_found_data = false;
    $('#api_option_row_key_container_'+i).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_option_fields);
        return false;
    }
    var data_container_id = '#api_option_row_static_value_container_'+i;

    $(data_container_id).find('input, select').each(function() {
        const value = $(this).val();
        if(value=='') {
            error_found = true;
            return false;
        }
        else{
            const data_type = $(this).data('type');
            if(data_type=='boolean' && value!='0' && value!='1'){
                error_found_data= true;
                return false;
            }
            if(data_type=='integer' && !isNumeric(value)){
                error_found_data= true;
                return false;
            }
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_option_fields);
        return false;
    }
    if(error_found_data){
        show_erorr(lang_fill_data_error_option_fields);
        return false;
    }
    return true;

}

function cookie_validation(i) {
    if(!cookie_validation_enabled) return true;
    var error_found = false;
    $('#api_cookie_row_key_container_'+i).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_cookie_fields);
        return false;
    }

    var data_container_id = "";
    if($("#api_cookie_row_type_"+i).val() == 'static') data_container_id = '#api_cookie_row_static_value_container_'+i;
    else data_container_id = '#api_cookie_row_dynamic_value_container_'+i;

    $(data_container_id).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_cookie_fields);
        return false;
    }
    return true;

}

function map_validation(i) {
    if(!map_validation_enabled) return true;
    var error_found = false;
    $('#api_map_row_key_container_'+i).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_map_fields);
        return false;
    }

    $('#api_map_row_value_container_'+i).find('input, select').each(function() {
        if($(this).val()=='') {
            error_found = true;
            return false;
        }
    });

    if(error_found){
        show_erorr(lang_fill_required_map_fields);
        return false;
    }
    return true;

}

function ajax_generate_dynamic_value_list(only_modal_list){
    const cached = false;
    const api_type = $("#api_type").val();
    let api_body_data_type = $('input[name="api_body_data_type"]:checked').val();
    if(api_body_data_type!='json' && !only_modal_list) return false;

    if(api_type==""){
        swal({ title: global_lang_warning, text: lang_error_select_channel, icon: 'warning' });
        return false;
    }

    if(!cached){
        $.ajax({
            url: http_api_get_dynamic_value_list_url,
            method: "POST",
            async : false,
            dataType : 'JSON',
            data: {api_type:api_type,"return_type":"list",csrf_token:csrf_token},
            success:function(response)
            {                
                let custom_field_list = '';
                for (const [key, value] of Object.entries(response)) {
                    if(value=='') continue;
                    custom_field_list += `<li class="list-group-item" title="${value}">${key}</li>`;
                }
                if(!only_modal_list) $("#variable-list").html(custom_field_list);
                $("#variable-list2").html(custom_field_list);
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                swal({title: global_lang_error,text: msg,icon: 'error'});
                return false;
            }

        });
    }
}

function ajax_generate_dynamic_value_dropdown(cached,api_type_required){
    const api_type = $("#api_type").val();

    if(api_type=="" && api_type_required){
        swal({ title: global_lang_warning, text: lang_error_select_channel, icon: 'warning' });
        return false;
    }

    if(api_type=="") {
        dataCustomFieldDropdownStorage = "<select class='form-control select select2 api_row_dynamic_value' name='' id='' style='width:100% !important;'><option value=''>"+lang_label_select_dynamic_data+"</option></select>";
    }

    if(api_type!="" && (!dataCustomFieldDropdownStorage || !cached)){
        $.ajax({
            url: http_api_get_dynamic_value_dropdown_url,
            method: "POST",
            async : false,
            data: {api_type:api_type,csrf_token:csrf_token},
            success:function(response)
            {
                dataCustomFieldDropdownStorage = response;
            },
            error: function (xhr, statusText) {
                const msg = handleAjaxError(xhr, statusText);
                swal({title: global_lang_error,text: msg,icon: 'error'});
                return false;
            }

        });
    }

    var selectedValue = "";
    var selectedId = "";
    var selectedName = "";
    $('.api_row_dynamic_value:visible').each(function() {
        selectedValue = $(this).val();
        selectedId = $(this).attr('id');
        selectedName = $(this).attr('name');
        // $(this).html(dataCustomFieldDropdownStorage);
        $(this).empty().append($(dataCustomFieldDropdownStorage).children());
        $(this).attr('name',selectedName);
        $(this).attr('id',selectedId);
        if($(this).parent().parent().hasClass('api_map_row_key_container')){
            $('#'+selectedId+' option[value="client_thread_id"]').prop('disabled', true);
            $('#'+selectedId+' option[value="subscriber_id"]').prop('disabled', true);
        }
        setTimeout(function() {
            $("#"+selectedId).select2();
        }, 100);
        if($(this).find('option[value="' + selectedValue + '"]').length === 0) selectedValue = "";
        $(this).val(selectedValue).trigger('change');
    });
}

function generate_formatter_params(count,params){
    var html = '';
    var exp = [];
    var dataType = '';
    var paramName = '';
    var paramCategoty = '';
    var paramNameDisplay = '';
    var paramRequired = '';
    var starSign = '';
    var i = 0;
    var colClass = 12/(count-1);
    for (const param of params) {
        if( !param.includes(":subject") ){
            exp = param.split(':');
            dataType = exp[0];
            paramName = exp[1];
            paramCategoty = exp[2];
            paramRequired = '';
            starSign = '';
            if(typeof(exp[3])!=='undefined' && exp[3]=='required') {
                paramRequired = 'required';
                starSign = '*';
            }
            paramNameDisplay = paramName.replaceAll('-',' ');
            html += '<div class="col-12 col-md-'+colClass+' mb-3">'+
                    '<label for="" class="pb-1 text-capitalize">'+paramNameDisplay+' '+starSign+'</label>'+
                        '<input type="text" class="form-control formatter_params '+paramName+'" id="" '+paramRequired+' name="formatter_params[]" placeholder="'+dataType+'" param-categoty="'+paramCategoty+'">'+
                    '</div>';
            i++;
        }
    }
    return html;

}


function load_formatter_datatable() {
    return $("#mytable1").DataTable({
        fixedHeader: false,
        colReorder: true,
        serverSide: true,
        processing:true,
        bFilter: false,
        order: [[ 2, "asc" ]],
        pageLength: 10,
        lengthMenu: [
            [5,10, 25, 50, 100],
            [5,10, 25, 50, 100],
        ],
        ajax:
            {
                "url": http_api_formatter_list_data,
                "type": 'POST',
                data: function ( d )
                {
                    d.search_value = $('#search_value_formatter').val();
                    d.settings_http_api_id = $("#id").val();
                }
            },
            language:
            {
               url: '<?= base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>'
            },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        columnDefs: [
            {
                targets: [0,1,2,3],
                visible: false
            },
            {
                targets: [5],
                className: 'text-center'
            },
            {
                targets: [5],
                sortable: false
            }
        ],
        fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
            if(areWeUsingScroll)
            {
                if (perscroll1) perscroll1.destroy();
                perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
            }
        },
        scrollX: 'auto',
        fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
            if(areWeUsingScroll)
            {
                if (perscroll1) perscroll1.destroy();
                perscroll1 = new PerfectScrollbar('#mytable1_wrapper .dataTables_scrollBody');
            }
        }
    });
}

function clear_xss(string) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#x27;',
        "/": '&#x2F;',
    };
    const reg = /[&<>"'/]/ig;
    return string.replace(reg, (match)=>(map[match]));
}

function reinitiate_formdata(scroll) {
    dataResponseFieldDropdownStorage = null;
    dataFormatterFieldDropdownStorage = null;
    dataFormatterMapStorage = null;
    dataSingleValueStorage = [];

    reset_form();

    $("#http_api_create_block,.http_api_create_container").removeClass('d-none');
    $("#http_api_submit").removeClass('disabled');
    $("#http_api_name").focus();
    $("#http_api_url_container,#http_api_data_container,#api_data_display_container").addClass('d-none');
    if(scroll){
        $('html, body').animate({
            scrollTop: $("#http_api_create_block_scroll").offset().top
        }, 100);
    }
}

function show_erorr(content) {
    swal({ title: lang_save_missing_validation, text: content, icon: 'warning' });
    return false;
}

function isNumeric(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
}

function isValidJSON(str) {
    try {
        JSON.parse(str);
        return true;
    } catch (e) {
        return false;
    }
}
</script>