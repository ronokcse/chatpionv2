<?php
/*
Addon Name: HTTP API Integration
Unique Name: http_api
Modules:
{
   "352":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Bot - HTTP API Integration"
   }
}
Project ID: 71
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.1
Description: 
*/

require_once("application/controllers/Home.php"); // loading home controller

class Http_api extends Home
{
  public $addon_data=array();
  public function __construct()
  {
      parent::__construct();
      // getting addon information in array and storing to public variable
      // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
      //------------------------------------------------------------------------------------------
      $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
      $addondata=$this->get_addon_data($addon_path);
      $this->addon_data=$addondata;      
      
      $function_name=$this->uri->segment(2);    
      // all addon must be login protected
      //------------------------------------------------------------------------------------------
      if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');
      $this->member_validity();       
      // if you want the addon to be accessed by admin and member who has permission to this addon
      //-------------------------------------------------------------------------------------------
      if(isset($addondata['module_id']) && is_array($addondata['module_id']) && !empty($addondata['module_id']))
      {
        if($this->session->userdata('user_type') != 'Admin' && count(array_intersect($addondata['module_id'],$this->module_access))==0)
          {
                redirect('home/login_page', 'location');
                exit();
          }
      }
      $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
      $this->load->helper('http_api/http_api');

  }

  private function check_ajax_csrf(){
     if(!$this->input->is_ajax_request()) {
      echo json_encode(array("error" => true, "message" => $this->lang->line("Bad Request.")));
      exit();          
     }
     if(!$this->csrf_token_check(true)) {
      echo json_encode(array("error" => true, "message" => $this->lang->line("CSRF Token Mismatch!")));
      exit();
     }
  }
    
  public function build_api(){
      $data = array('body'=>'http-api/build','page_title'=>$this->lang->line("HTTP API "));
      $data["formatter_dropdown"] = $this->get_formatter_dropdown();
      $api_type_list = $this->get_api_types();
      $api_type_list[""] = $this->lang->line("Select Channel");
      $data["api_type_list"] = $api_type_list;
      $api_method_list = $this->get_api_methods();
      $api_method_list[""] = $this->lang->line("Select Method");
      $data["api_method_list"] = $api_method_list;

      $data["url_edit_id"] = $this->input->get_post('id',true) ?? "";
      $data["url_channel"] = $this->input->get_post('channel',true) ?? "fb";

      if (!empty($data["url_edit_id"])) {
        $this->db->select('api_type');
        $this->db->where(['id' => $data["url_edit_id"], 'user_id' => $this->user_id]);
        $query = $this->db->get('settings_http_apis');
        $xdata = $query->row();    
        if(empty($xdata)) dd('Bad Request');  
        $data["url_channel"] = $xdata->api_type ?? 'fb';
      }
      if(empty($data["url_channel"])) dd('Bad Request');

      $data["header_level"] = 20;
      $data["body_level"] = 50;
      $data["option_level"] = 20;
      $data["cookie_level"] = 10;
      $data["map_level"] = 50;
      $data["option_list"] = get_curl_constants(); // custom function
      return $this->_viewcontroller($data);
  }

  public function list_api_data()
  {       
      $this->ajax_check();
      $search_value = $this->input->get_post('search_value',true);
      $search_status = $this->input->get_post('search_status',true);
      $search_is_mapped = $this->input->get_post('search_is_mapped',true);
      $search_api_type = $this->input->get_post('search_api_type',true);
      $display_columns = array("#","CHECKBOX",'api_name','api_endpoint_url','api_type', 'status', 'is_mapped', 'actions','created_at','last_called_at');
      $search_columns = array('api_name');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'group_name';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
      $order_by=$sort." ".$order;

      $table = "settings_http_apis";
      $select = array($table . ".*");
      $this->db->select($select);
      $this->db->from($table);
      $this->db->where($table . '.user_id', $this->user_id);
      if ($search_value != '') {
          $this->db->group_start();
          foreach ($search_columns as $value) {
              $this->db->or_like($value, $search_value);
          }
          $this->db->group_end();
      }
      if ($search_status != '') {
          $this->db->where($table . '.status', $search_status);
      }
      if ($search_is_mapped != '') {
          $this->db->where($table . '.is_mapped', $search_is_mapped);
      }
      $this->db->where($table . '.api_type', $search_api_type);
      $this->db->order_by($order_by);
      $this->db->limit($limit, $start);
      $query = $this->db->get();
      $info_obj = $query->result(); 

      $this->db->select($table . '.id');
      $this->db->from($table);
      $this->db->where($table . '.user_id', $this->user_id);
      if ($search_value != '') {
          $this->db->group_start();
          foreach ($search_columns as $value) {
              $this->db->or_like($value, $search_value);
          }
          $this->db->group_end();
      }
      if ($search_status != '') {
          $this->db->where($table . '.status', $search_status);
      }
      if ($search_is_mapped != '') {
          $this->db->where($table . '.is_mapped', $search_is_mapped);
      }
      $this->db->where($table . '.api_type', $search_api_type);
      $total_result = $this->db->count_all_results();

      $info = [];
      foreach ($info_obj as $key => $value)
      {
          $created_at = $value->created_at;
          if(empty($created_at)) $value->created_at = '';
          else $value->created_at = date("jS M y H:i",strtotime($value->created_at));

          $last_called_at = $value->last_called_at;
          if(empty($last_called_at))  $value->last_called_at = '-';
          else $value->last_called_at = date("jS M y H:i",strtotime($value->last_called_at));

          $delete_url = base_url("http_api/delete_api");
          $str="";
          $attr = " data-is-mapped='".$value->is_mapped."' data-id='".$value->id."'";
          if($value->status=='1') {
              $verify_title = $value->is_mapped=='1' ? $this->lang->line('Edit API') : $this->lang->line('Verify API');
              $verify_icon = $value->is_mapped=='1' ? 'fas fa-edit' : 'fas fa-plug';
              $str=$str."<a class='btn btn-circle btn-outline-warning verify-api' ".$attr." href='#' title='".$verify_title."'>".'<i class="'.$verify_icon.'"></i>'."</a>&nbsp;&nbsp;";
          }

          if(check_module_action_access($module_id=352,$actions=3,$response_type=''))
          $str=$str."<a href='".$delete_url."' data-id='".$value->id."' class='delete-http-api btn btn-circle btn-outline-danger' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";

          $width = 100;
          $value->actions = "<div style='min-width:".$width."px'>".$str."</div>";
          $value->is_mapped = $value->is_mapped=='1' ? '<i class="fas fa-check-circle text-success"></i> '.$this->lang->line('Yes') : '<i class="fas fa-times-circle text-muted"></i> '.$this->lang->line('No');
          $status_checked = ($value->status=='1') ? 'checked' : '';
          $value->status = '<label class="custom-switch update-status-switch">
                            <input type="checkbox" name="update-status" data-url="'.base_url("http_api/update_api_status").'" data-id="'.$value->id.'" class="update-status custom-switch-input" '.$status_checked.' value="'.$value->status.'">
                            <span class="custom-switch-indicator"></span>
                          </label>';
          $value->api_type = channel_shortname_to_longname($value->api_type);

          $info[] = (array) $value;
      }

      $data['draw'] =(int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
      echo json_encode($data);
  }

  public function verify_api(){ 
      $this->check_ajax_csrf();
      check_module_action_access($module_id=352,$actions=2,$response_type='json3');

      if(empty($id)){
          $status=$this->_check_usage(352,1);
          if($status=="3") {
            echo json_encode(array('error'=>true,'message'=>$this->lang->line("HTTP API module limit has been exceeded.")));
            exit();
          }
      }

      $id = $this->input->get_post('id', true) ?? "";
      $api_name = $this->input->get_post('api_name', true) ?? "";
      $api_method = $this->input->get_post('api_method', true) ?? "";
      $api_type = $this->input->get_post('api_type', true) ?? "";
      $test_subscriber_unique_id = $this->input->get_post('test_subscriber_unique_id', true) ?? "";
      $api_endpoint_url = $this->input->get_post('api_endpoint_url', true) ?? "";
      $api_body_data_type = $this->input->get_post('api_body_data_type', true) ?? "";
      $api_body_row_json_value = $this->input->get_post('api_body_row_json_value', true) ?? "";

      $api_header_row_key = $this->input->get_post('api_header_row_key', true) ?? [];
      $api_header_row_type = $this->input->get_post('api_header_row_type', true) ?? [];
      $api_header_row_static_value = $this->input->get_post('api_header_row_static_value', true) ?? [];
      $api_header_row_dynamic_value = $this->input->get_post('api_header_row_dynamic_value', true) ?? [];

      $api_body_row_key = $this->input->get_post('api_body_row_key', true) ?? [];
      $api_body_row_type = $this->input->get_post('api_body_row_type', true) ?? [];
      $api_body_row_static_value = $this->input->get_post('api_body_row_static_value', true) ?? [];
      $api_body_row_dynamic_value = $this->input->get_post('api_body_row_dynamic_value', true) ?? [];
      $api_body_row_file_value = $this->input->get_post('api_body_row_file_value', true) ?? [];

      $api_option_row_key = $this->input->get_post('api_option_row_key', true) ?? [];
      $api_option_row_static_value = $this->input->get_post('api_option_row_static_value', true) ?? [];

      $api_cookie_row_key = $this->input->get_post('api_cookie_row_key', true) ?? [];
      $api_cookie_row_type = $this->input->get_post('api_cookie_row_type', true) ?? [];
      $api_cookie_row_static_value = $this->input->get_post('api_cookie_row_static_value', true) ?? [];
      $api_cookie_row_dynamic_value = $this->input->get_post('api_cookie_row_dynamic_value', true) ?? [];

      $data = [];
      $header_data = $body_data = $option_data = $cookie_data = [];

      foreach($api_header_row_key as $key=>$value){
          if(empty($value)) continue;
          $header_data[$key+1]['key'] = $api_header_row_key[$key] ?? '';
          $header_data[$key+1]['key'] = rtrim($header_data[$key+1]['key'],":");
          $header_data[$key+1]['type'] = $api_header_row_type[$key] ?? '';
          if($header_data[$key+1]['type']=='static'){
              $header_data[$key+1]['value'] =  $api_header_row_static_value[$key] ?? '';
          }
          else{ //dynamic
              $dynamic_value = $api_header_row_dynamic_value[$key] ?? '';
              $header_data[$key+1]['value'] =  $dynamic_value;
          }
      }

      foreach($api_body_row_key as $key=>$value){
          if(empty($value)) continue;
          $body_data[$key+1]['key'] = $api_body_row_key[$key] ?? '';
          $body_data[$key+1]['type'] = $api_body_row_type[$key] ?? '';
          if($body_data[$key+1]['type']=='static'){
              $body_data[$key+1]['value'] =  $api_body_row_static_value[$key] ?? '';
          }
          else if($body_data[$key+1]['type']=='dynamic'){
              $dynamic_value = $api_body_row_dynamic_value[$key] ?? '';
              $body_data[$key+1]['value'] =  $dynamic_value;
          }
          else{ //file
              $body_data[$key+1]['value'] =  $api_body_row_file_value[$key] ?? '';
          }
      }

      foreach($api_option_row_key as $key=>$value){
          if(empty($value)) continue;
          $option_data[$key+1]['key'] = $api_option_row_key[$key] ?? '';
          $option_data[$key+1]['value'] =  $api_option_row_static_value[$key] ?? '';
      }

      foreach($api_cookie_row_key as $key=>$value){
          if(empty($value)) continue;
          $cookie_data[$key+1]['key'] = $api_cookie_row_key[$key] ?? '';
          $cookie_data[$key+1]['type'] = $api_cookie_row_type[$key] ?? '';
          if($cookie_data[$key+1]['type']=='static'){
              $cookie_data[$key+1]['value'] =  $api_cookie_row_static_value[$key] ?? '';
          }
          else{ //dynamic
              $dynamic_value = $api_cookie_row_dynamic_value[$key] ?? '';
              $cookie_data[$key+1]['value'] =  $dynamic_value;
          }
      }


      $curtime = date("Y-m-d H:i:s");
      $api_data = [
          "body_data_type" => $api_body_data_type,
          "header_data" => $header_data,
          "body_data" => $body_data,
          "body_data_json" => $api_body_data_type=='json' ? $api_body_row_json_value : '',
          "option_data" => $option_data,
          "cookie_data" => $cookie_data
      ];
      $api_data = json_encode($api_data);
      $insert_data = [
          "user_id" => $this->user_id,
          "api_endpoint_url" => $api_endpoint_url,
          "api_method" => $api_method,
          "api_name" => $api_name,
          "api_type" => $api_type,
          "created_at" => $curtime,
          "test_subscriber_unique_id" => $test_subscriber_unique_id,
          "api_data" => $api_data
      ];

      $update_data = $insert_data;
      unset($update_data['user_id']);
      unset($update_data['created_at']);

      if(empty($id)){
          $this->db->insert('settings_http_apis', $insert_data);
          $id = $this->db->insert_id();        
          $this->_insert_usage_log(352,1);
      }

      // simulating updated data to get test response before real data update
      $data_before_call = $this->db->where(array('id' => $id, 'user_id' => $this->user_id))->get('settings_http_apis')->row();
      foreach($update_data as $key=>$value){
          $data_before_call->{$key} = $value;
      }
      $last_call_data = $this->http_api_trigger($id,$test_subscriber_unique_id,'subscribe_id',$data_before_call,true);

      $update_data['last_call_data'] = json_encode($last_call_data);
      $update_data['last_called_at'] = $curtime;
      $update_data['last_error_message'] = $last_call_data['error'] ?? '';
      $this->db->where(array('id' => $id, 'user_id' => $this->user_id))->update('settings_http_apis', $update_data);

      // using final updated data
      $data = $this->db->where(array('id' => $id, 'user_id' => $this->user_id))->get('settings_http_apis')->row();
      if(empty($data->mapping_data)) $data->mapping_data = json_encode([]);
      // $last_call_data = $data->is_mapped=='1' ? $data->last_call_data_mapped : $data->last_call_data;
      $last_call_data = $data->last_call_data;
      $generate_response_formatter_dropdown = $this->generate_response_formatter_dropdown($id,json_decode($last_call_data,true));
      $data->response_data_dropdown = $generate_response_formatter_dropdown["data_dropdown"] ?? "";
      $data->formatter_dropdown = $generate_response_formatter_dropdown["formatter_dropdown"] ?? "";
      $data->single_data_list = $generate_response_formatter_dropdown["single_data_list"] ?? [];
      echo json_encode($data);
  }

  public function submit_api()
  {    
      $this->check_ajax_csrf();
      check_module_action_access($module_id=352,$actions=[1,2],$response_type='json3');
      $id = $this->input->get_post('id', true) ?? 0;
      $api_map_row_dynamic_value = $this->input->get_post('api_map_row_dynamic_value', true) ?? [];
      $api_map_row_value = $this->input->get_post('api_map_row_value', true) ?? [];

      $mapping_data = [];
      foreach($api_map_row_dynamic_value as $key=>$value){
          if(empty($value)) continue;
          $key2 = $key+1;
          $mapping_data[$key2]['field'] = $value;
          $mapping_data[$key2]['variable'] = $api_map_row_value[$key] ?? '';

          $formatter_variable_name = "api_map_row_formatter_value_".$key2;
          $mapping_data[$key2]['formatter'] = $this->input->post($formatter_variable_name) ?? [];

      }

      $data = $this->db->where(array('id' => $id, 'user_id' => $this->user_id))->get('settings_http_apis')->row();
      $last_call_data_mapped = $data->last_call_data ?? null;

      $insert_data = [
          "mapping_data" => json_encode($mapping_data),
          "is_mapped"=>"1",
          "last_call_data_mapped"=>$last_call_data_mapped
      ];

      $this->db->where(array('id' => $id, 'user_id' => $this->user_id))->update('settings_http_apis', $insert_data);
      echo json_encode(['error' => false,'message' => $this->lang->line('HTTP API data has been mapped and saved successfully.')]);
  }

  public function edit_api() {     
     $this->check_ajax_csrf();
     $id= $this->input->get_post('id', true);
     $data = $this->db->where(array('id' => $id, 'user_id' => $this->user_id))->get('settings_http_apis')->row();
     if(empty($data)) {
        echo json_encode(["error"=>true,"message"=>$this->lang->line("HTTP API not found.")]);
        exit();
     }

     $last_call_data = !empty($data->last_call_data_mapped) ? $data->last_call_data_mapped : $data->last_call_data;
     $generate_response_formatter_dropdown = $this->generate_response_formatter_dropdown($id,json_decode($last_call_data,true));
     $data->response_data_dropdown = $generate_response_formatter_dropdown["data_dropdown"] ?? "";
     $data->formatter_dropdown = $generate_response_formatter_dropdown["formatter_dropdown"] ?? "";
     $data->single_data_list = $generate_response_formatter_dropdown["single_data_list"] ?? [];
     echo json_encode($data);
  }

  private function generate_response_formatter_dropdown($id=0,$last_call_data=[]){
      $data_list = [];
      $last_call_data = array_flatten($last_call_data,true);
      foreach($last_call_data as $key=>$value){
          if(!is_array($value)) {
              $data_list['xitSingleValuedItems'][$key] = $value;
          }
          else {
              if(array_depth($value)>=2){
                  $any_index_not_array = false;
                  foreach ($value as $tk=>$tv){
                      if(!is_array($tv)) $any_index_not_array = true;
                  }
                  if(!$any_index_not_array)
                  $data_list['xitCollectionValuedItems'][$key] = json_encode($value);
              }
          }
      }

      $data_dropdown = '<select name="" id="" class="form-control api_map_row_value">';
      $data_dropdown .= '<option value="">'.$this->lang->line('Select Map Data').'</option>';
      $data_dropdown .= '<option value="[space]"><< '.$this->lang->line('Static Value').' >></option>';
      $data_single_values = [];

      if(isset($data_list['xitSingleValuedItems'])){
          if(isset($data_list['xitCollectionValuedItems'])) $data_dropdown .= ' <optgroup label="Single Items">';
          foreach ($data_list['xitSingleValuedItems'] as $k=>$v){
              $data_dropdown .= '<option data-type="single" value="'.$k.'">'.$k.' : '.$v.'</option>';
              $data_single_values[] = '['.$k.']';
          }
          if(isset($data_list['xitCollectionValuedItems'])) $data_dropdown .= '</optgroup>';
      }

      if(isset($data_list['xitCollectionValuedItems'])){
          if(isset($data_list['xitSingleValuedItems'])) $data_dropdown .= ' <optgroup label="List Items">';
          foreach ($data_list['xitCollectionValuedItems'] as $k=>$v){
              $data_dropdown .= '<option data-type="list" value="'.$k.'">'.$k.'</option>';
          }
          if(isset($data_list['xitSingleValuedItems'])) $data_dropdown .= '</optgroup>';
      }

      if(!isset($data_list['xitSingleValuedItems']) && !isset($data_list['xitCollectionValuedItems'])){
          foreach ($data_list as $k=>$v){
              $display = is_json($v) ? $k : $k.' : '.$v;
              $type = is_json($v) ? 'list' : 'single';
              $data_dropdown .= '<option data-type="'.$type.'" value="'.$k.'">'.$display.'</option>';
              if($type=='single') {
                  $data_single_values[] = '['.$k.']';
              }
          }
      }
      $data_dropdown .= '</select>';

      $formatter_dropdown = '<select multiple name="" id="" class="form-control api_map_row_formatter_value">';
      $formatter_list = $this->db->where('settings_http_api_id', $id)->get('settings_http_api_formatters')->result();
      foreach ($formatter_list as $k=>$v){
          if($v->formatter_type!='static-value') $display = $v->formatter_name.' ('.$v->formatter_type.')';
          else {
              $params = json_decode($v->params,true);
              $display = $v->formatter_name.' : '.$params[1];
          }
          $formatter_dropdown .= '<option data-formatter-type="'.$v->formatter_type.'" value="'.$v->id.'">'.$display.'</option>';
      }
      $formatter_dropdown .= '</select>';

      return ["single_data_list"=>$data_single_values,"data_dropdown"=>$data_dropdown,"formatter_dropdown"=>$formatter_dropdown];
  }

  public function list_api_report_all(){
      $api_type = $this->input->get_post('channel', true) ?? '';
      $data = array('body'=>'http-api/report','page_title'=>$this->lang->line("HTTP API Report"));
      $api_list = $this->get_api_list($this->user_id,$api_type);
      $api_list[''] = $this->lang->line('All HTTP API');
      $data['api_list'] = $api_list;

      $data["url_edit_id"] = $this->input->get_post('id', true) ?? "";
      $data["url_channel"] = $this->input->get_post('channel', true) ?? "";

      if(!empty($data["url_edit_id"])) {
        $xdata = $this->db->select('api_type')->where(array('id' => $data["url_edit_id"], 'user_id' => $this->user_id))->get('settings_http_apis')->row();
          if(empty($xdata)) dd("Bad Request.");
          $data["url_channel"] = $xdata->api_type ?? '';
      }
      if(empty($data["url_channel"])) dd("Bad Request.");
      return $this->_viewcontroller($data);
  }

  public function list_api_report_all_data(){
      $this->ajax_check();
      $search_value = $this->input->get_post('search_value',true);
      $search_settings_http_api_id = $this->input->get_post('search_settings_http_api_id',true);
      $search_status = $this->input->get_post('search_status',true);
      $search_api_type = $this->input->get_post('search_api_type',true);
      $display_columns = array("#",'id','api_name', 'subscriber_unique_id', 'success','created_at','api_response','api_data');
      $search_columns = array('subscriber_unique_id');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'group_name';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
      $order_by=$sort." ".$order;

      $table = "settings_http_api_calls";
      $table2 = "settings_http_apis";
      $select = array("settings_http_api_calls.*", "api_name", "user_id");
 
      $this->db->select($select)->from($table)->where('user_id', $this->user_id)->join($table2, "$table.settings_http_api_id = $table2.id", 'left');      
      if (!empty($allowed_whatsapp_bot_ids)) {
        $this->db->where_in("$table.whatsapp_bot_id", $allowed_whatsapp_bot_ids);
      }  
      if ($search_value != '') {
        $this->db->group_start();
        foreach ($search_columns as $value) {
          $this->db->or_like($value, $search_value);
        }
        $this->db->group_end();
      }      
      if ($search_status != '') {
        $this->db->where('success', $search_status);
      }      
      if ($search_settings_http_api_id != '') {
        $this->db->where('settings_http_api_id', $search_settings_http_api_id);
      }
      
      $this->db->where("$table2.api_type", $search_api_type);
      $info_obj = $this->db->order_by($order_by)->limit($limit, $start)->get()->result();
      

      $this->db->select("$table.id")->from($table)->where('user_id', $this->user_id);      
      if (!empty($allowed_whatsapp_bot_ids)) {
          $this->db->where_in("$table.whatsapp_bot_id", $allowed_whatsapp_bot_ids);
      }      
      $this->db->join($table2, "$table.settings_http_api_id = $table2.id", 'left');      
      if ($search_value != '') {
          $this->db->group_start();
          foreach ($search_columns as $value) {
            $this->db->or_like($value, $search_value);
          }
          $this->db->group_end();
      }      
      if ($search_status != '') {
          $this->db->where('success', $search_status);
      }      
      if ($search_settings_http_api_id != '') {
          $this->db->where('settings_http_api_id', $search_settings_http_api_id);
      }      
      $this->db->where("$table2.api_type", $search_api_type);
      $total_result = $this->db->count_all_results();
      
      $info = [];
      foreach ($info_obj as $key => $value)
      {
          $success = $value->success=='1' ? '<i class="fas fa-check-circle text-success" title="'.$this->lang->line('Success').'"></i> '.$this->lang->line('Success') :  '<i class="fas fa-times-circle text-danger" title="'.$this->lang->line('Error').'"></i> ';
          if(empty($value->success)) $success = '-';

          $value->created_at = date("jS M y H:i",strtotime($value->created_at));
          $value->success = $success;
          $value->api_data = '<textarea class="form-control py-0" style="white-space: pre;overflow-wrap: normal;overflow-x: scroll;height:40px !important;" rows="1">'.str_replace("\/","/",$value->api_data).'</textarea>';
          $value->api_response = !empty($value->api_response) ? '<textarea class="form-control py-0" style="white-space: pre;overflow-wrap: normal;overflow-x: scroll;height:40px !important;" rows="1">'.str_replace("\/",'/',$value->api_response).'</textarea>' : '-';
          $value->actions = '';
          $info[] = (array) $value;
      }

      $data['draw'] =(int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
      echo json_encode($data);
  }

  public function list_formatter_data()
  {
      $this->ajax_check();
      $search_value = $this->input->get_post('search_value',true);
      $settings_http_api_id = $this->input->get_post('settings_http_api_id',true);
      $display_columns = array("#","CHECKBOX",'formatter_name','formatter_type','params', 'actions');
      $search_columns = array('formatter_name','formatter_type');

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'group_name';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
      $order_by=$sort." ".$order;

      $table = "settings_http_api_formatters";
      $this->db->where('settings_http_api_id', $settings_http_api_id);
      if ($search_value != '') {
            $this->db->group_start();
            foreach ($search_columns as $value) {
                $this->db->or_like($value, $search_value);
            }
            $this->db->group_end();
      }
      $info_obj = $this->db->order_by($order_by)->limit($limit, $start)->get($table)->result(); 

      $this->db->where('settings_http_api_id', $settings_http_api_id);
      if ($search_value != '') {
        $this->db->group_start();
        foreach ($search_columns as $value) {
            $this->db->or_like($value, $search_value);
        }
        $this->db->group_end();
      }

      $total_result = $this->db->count_all_results($table); 

      $formatter_options = $this->get_formatter_dropdown(true);
      $info = [];
      foreach ($info_obj as $key => $value)
      {
          $params_raw = $formatter_options[$value->formatter_type]['params'] ?? null;
          $params_decoded = json_decode($value->params);
          $params_array = [];
          foreach ($params_decoded as $k=>$v){
              $exp = isset($params_raw[$k]) ? explode(':',$params_raw[$k]) : [];
              if(!isset($exp[1])) $exp[1] = '';
              if($v=='subject') $v='variable';
              $params_array[] = '<i class="text-muted">'.$exp[1].'</i>'.' : <b class="text-primary">'.$v.'</b>';
          }
          $params = '<b class="text-success">'.$value->formatter_name.'</b> = <b class="text-warning">'.$value->formatter_type.'</b> ( '.implode(' , ',$params_array).' )';

          $delete_url = base_url("http_api/delete_formatter");
          $str="";
          if(check_module_action_access($module_id=352,$actions=3,$response_type=''))
          $str=$str."<a href='".$delete_url."' data-id='".$value->id."' data-table-name='table1' class='delete-formatter btn btn-circle btn-outline-danger' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";
          $width = 40;
          $value->actions = "<div style='min-width:".$width."px'>".$str."</div>";
          $value->params = $params;
          $info [] = (array) $value;
      }

      $data['draw'] =(int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
      echo json_encode($data);
  }

  public function save_formatter(){
      $this->check_ajax_csrf();
      check_module_action_access($module_id=352,$actions=1,$response_type='json3');
      $settings_http_api_id = $this->input->get_post('settings_http_api_id',true);
      $formatter_name = $this->input->get_post('formatter_name',true);
      $formatter_type = $this->input->get_post('formatter_type',true);
      $formatter_params = $this->input->get_post('formatter_params',true);

      $formatter_options = $this->get_formatter_dropdown(true);
      $params_raw = $formatter_options[$formatter_type]['params'] ?? [];
      if(isset($params_raw[0])) unset($params_raw[0]);
      $params_array = ['subject'];
      $i=0;
      foreach ($params_raw as $k=>$v){
          $params_array[] = $formatter_params[$i];
          $i++;
      }
      $data = [
          'settings_http_api_id' => $settings_http_api_id,
          'formatter_name' => $formatter_name,
          'formatter_type' => $formatter_type,
          'params' => json_encode($params_array),
      ];
      $this->db->insert("settings_http_api_formatters", $data);
      $data['id'] = $this->db->insert_id();
      echo json_encode(['error' => false,'message' => $this->lang->line('Formatter has been saved successfully.'),'data'=>$data]);

  }

  public function update_api_status()
  {
    $this->check_ajax_csrf(); // Custom CSRF check
    check_module_action_access($module_id = 352, $actions = 2, $response_type = 'json3'); // Custom access check

    $id = $this->input->get_post('id', true);
    $status = $this->input->get_post('status', true);

    // Update status in database
    $where = array('id' => $id, 'user_id' => $this->user_id);
    $this->db->where($where);
    $query = $this->db->update('settings_http_apis', array('status' => $status));
    echo json_encode(['error' => false,'message' => $this->lang->line('API status has been updated successfully')]);
  }

  public function delete_api()
  {
    $this->check_ajax_csrf(); // Custom CSRF check
    check_module_action_access($module_id = 352, $actions = 3, $response_type = 'json3'); // Custom access check

    $id = $this->input->get_post('id', true);

    $table = 'settings_http_apis';
    $where = array('user_id' => $this->user_id, 'id' => $id);

    // Check if the record exists
    $this->db->select('id');
    $this->db->where($where);
    $query = $this->db->get($table);
    $exist = $query->row(); // Retrieve the result as an object

    if (!isset($exist->id)) {
        echo json_encode(array('error' => true,'message'=>$this->lang->line("HTTP API not found.")));
        exit();
    }

    // Remove user_id from $where for deletion purposes
    unset($where['user_id']);

    try {
        $this->db->trans_begin(); // Begin transaction

        // Delete the record
        $this->db->where($where);
        $this->db->delete($table);

        // Log the deletion
        $this->_delete_usage_log(352, 1);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('error' => true, 'message' => $this->lang->line("Failed to delete HTTP API.")));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('error' => false, 'message' => $this->lang->line("HTTP API has been deleetd successfully.")));
        }
    } catch (Exception $e) {
        $this->db->trans_rollback(); // Rollback transaction in case of error
        $error = $e->getMessage();
        echo json_encode(array('error' => true, 'message' => $error));
    }
  }

  public function delete_formatter(){
      $this->check_ajax_csrf();
      check_module_action_access($module_id=352,$actions=3,$response_type='json3');
      $id = $this->input->get_post('id',true);
      $table = 'settings_http_api_formatters';
      $where = ['id'=>$id];
      if($this->db->where($where)->delete($table)) {
          echo json_encode(['error'=>false,'message'=>$this->lang->line("Formatter has been deleted successfully.")]);
      }
      else echo json_encode(['error'=>true,'message'=>$this->lang->line("Something went wrong.")]);
  }

  protected function get_formatter_dropdown($return_options=false){
      $formatter_options = [
          'concat-list-items' => [
              'paramCount'=>3,
              'params'=>['string:subject:dynamic','string:glue:static','string:position:static:required'],
              'example'=>htmlspecialchars($this->lang->line('Using a separator, combine particular index values of a list item to produce a single value.')),
          ],
          'concat-left' => [
              'paramCount'=>3,
              'params'=>['string:subject:dynamic','string:glue:static','string:concat:dynamic:required'],
              'example'=>htmlentities($this->lang->line('Add text to the left separated by a glue.').' <br><i>( phoneNumber => +phoneNumber )</i>')
          ],
          'concat-right' => [
              'paramCount'=>3,
              'params'=>['string:subject:dynamic','string:glue:static','string:concat:dynamic:required'],
              'example'=>htmlentities($this->lang->line('Add text to the right separated by a glue.').' <br><i>( firstName => firstName lastName )</i>')
          ],
          'trim-left' => [
              'paramCount'=>2,
              'params'=>['string:subject:dynamic','string:trim:static:required'],
              'example'=>htmlentities($this->lang->line('Remove a specific text from the left.').' <br><i>( +phoneNumber => phoneNumber )</i>')
          ],
          'trim-right' => [
              'paramCount'=>2,
              'params'=>['string:subject:dynamic','string:trim:static:required'],
              'example'=>htmlentities($this->lang->line('Remove a specific text from the right.').' <br><i>( example@example.com => example )</i>')
          ],
          'split' => [
              'paramCount'=>3,
              'params'=>['string:subject:dynamic','string:separator:static:required','integer:position:static:required'],
              'example'=>htmlentities($this->lang->line('Split a value using a separator to convert it to list and then take a specific index value.').' <br><i>( firstName-middleName-lastName => middleName )</i>')
          ],
          'replace' => [
              'paramCount'=>3,
              'params'=>['string:subject:dynamic','string:search:static:required','string:replace:static'],
              'example'=>htmlentities($this->lang->line('Search for a given text in value, then replace every instance with a different value.').' <br><i>( search@search.com =>  replace@replace.com)</i>')
          ],
          'shorten' => [
              'paramCount'=>2,
              'params'=>['string:subject:dynamic','integer:limit:static:required'],
              'example'=>htmlentities($this->lang->line('To make a value shorter, keep the first few characters and eliminate the rest.').' <br><i>( this is how it works => this is how it... )</i>')
          ],
          'format-number' => [
              'paramCount'=>4,
              'params'=>['float:subject:dynamic','integer:decimal:static:required','string:decimal-separator:static','string:thousand-separator:static'],
              'example'=>htmlentities($this->lang->line('Use decimal places and separators to format a numerical value.').' <br><i>( 12345.6589 => 12,345.66 )</i>')
          ],
          'default-value' => [
              'paramCount'=>2,
              'params'=>['string:subject:dynamic','string:default:static:required'],
              'example'=>htmlentities($this->lang->line('If the value is blank or not found, use a pre-defined value.'))
          ],
          'static-value' => [
              'paramCount'=>2,
              'params'=>['string:subject:dynamic','string:value:static:required'],
              'example'=>htmlentities($this->lang->line('Replace the original dynamic value with a pre-defined static value.'))
          ],
          'day-add' => [
              'paramCount'=>2,
              'params'=>['date:subject:dynamic','integer:day:static:required'],
              'example'=>htmlentities($this->lang->line('Add one or more day with a date value.').'<br> <i>( 2000-01-01 00:00:00 + 30 = 2000-01-31 00:00:00 )</i>')
          ],
          'day-subtract' => [
              'paramCount'=>2,
              'params'=>['date:subject:dynamic','integer:day:static:required'],
              'example'=>htmlentities($this->lang->line('Subtract one or more day from a date value.').' <br><i>( 2000-01-31 00:00:00 -30 = 2000-01-01 00:00:00 )</i>')
          ],
          'number-add' => [
              'paramCount'=>2,
              'params'=>['float:subject:dynamic','integer:number:static:required'],
              'example'=>htmlentities($this->lang->line('Add a number to a numeric value.'). ' <br><i>( 999+1000 = 1999 )</i>')
          ],
          'number-subtract' => [
              'paramCount'=>2,
              'params'=>['float:subject:dynamic','integer:number:static:required'],
              'example'=>htmlentities($this->lang->line('Subtract a number from a numeric value.'). ' <br><i>( 1999-1000 = 999 )</i>')
          ],
          'number-multiply' => [
              'paramCount'=>2,
              'params'=>['float:subject:dynamic','integer:number:static:required'],
              'example'=>htmlentities($this->lang->line('Multiply a number with a numeric value.'). ' <br><i>( 9*10 = 90 )</i>')
          ],
      ];

      ksort($formatter_options);
      if($return_options) return $formatter_options;

      $formatter_dropdown = "<select name='formatter_type' id='formatter_type' class='form-control'>";
      $formatter_dropdown .= "<option value=''>".$this->lang->line("Select Action")."</option>";
      foreach ($formatter_options as $key=>$value) {
          $formatter_dropdown .= "<option data-param-count='".$value['paramCount']."' data-params='".json_encode($value['params'])."' data-example='".$value['example']."' value='".$key."'>".ucwords(str_replace('-',' ',$key))."</option>";
      }
      $formatter_dropdown .= "</select>";
      return $formatter_dropdown;
  }

  protected function get_api_list($user_id = 0, $api_type = null)
  {
    if ($user_id == 0) {
        $user_id = $this->user_id;
    }  
    $this->db->select(['id', 'api_name'])->from('settings_http_apis')->where(array('user_id' => $user_id, 'is_mapped' => '1'));  
    if (!empty($api_type)) {
        $this->db->where('api_type', $api_type);
    }  
    $this->db->order_by('api_name', 'asc');
    $query = $this->db->get();
    $get = $query->result();  
    $result = [];
    foreach ($get as $val) {
        $result[$val->id] = $val->api_name;
    }  
    return $result;
  }
  

  public function get_dynamic_value_dropdown(){
      $list = $this->get_dynamic_value_list();
      echo form_dropdown('', $list, '', ['class' => 'form-control select select2 api_row_dynamic_value']);
  }

  public function get_dynamic_value_list(){
      $user_id = $this->input->get_post('user_id', true);
      if(empty($user_id)) $user_id = $this->user_id;
      $api_type = $this->input->get_post('api_type', true);
      $return_type = $this->input->get_post('return_type', true);
      if(empty($return_type)) $return_type = "dropdown";

      $result = [];
      if($return_type=='list'){
          $result = [
              "#LEAD_USER_FIRST_NAME#"=> "First Name",
              "#LEAD_USER_LAST_NAME#"=> "Last Name",
              "#LEAD_USER_EMAIL#"=> "Email",
              "#LEAD_USER_AVATAR#"=> "Avatar",
              "#LEAD_USER_PHONE#"=> "Phone Number",
              "#LEAD_USER_CHAT_ID#"=> "Chat ID",
              "#LEAD_USER_SUBSCRIBER_ID#"=> "Subscriber ID"
          ];
      } else{
          $result = [
              "" =>  $this->lang->line("Select Field"),
              "Subscriber Fields" => [
                  "first_name"=> "First Name",
                  "last_name"=> "Last Name",
                  "email"=> "Email",
                  "profile_pic"=> "Avatar",
                  "phone_number"=> "Phone Number",
                  "client_thread_id"=> "Chat ID",
                  "subscribe_id"=> "Subscriber ID"
              ]
          ];
      }      
      
      $table = "user_input_custom_fields";
      if($api_type=='fb') {
        if($return_type=='list') $result["#LEAD_USER_GENDER#"] = "Gender";
        else $result["Subscriber Fields"]["gender"] = "Gender";
      }
      else{
        if($return_type=='list'){
            unset($result["#LEAD_USER_FIRST_NAME#"]);
            unset($result["#LEAD_USER_LAST_NAME#"]);
            $result["#LEAD_USER_FULL_NAME#"] = "Full Name";
        } else {
            unset($result["Subscriber Fields"]["first_name"]);
            unset($result["Subscriber Fields"]["last_name"]);
            $result["Subscriber Fields"]["full_name"] = "Full Name";
        }
      }
      $media_type = $api_type;      

      $this->db->where('user_id', $this->user_id);
      if (isset($media_type)) {
        $this->db->where('media_type', $media_type);
      }
      $get = $this->db->order_by('name', 'asc')->get($table)->result();

      foreach ($get as $key=>$val){
         if($return_type=='list') $result["#".$val->name."#"] = $val->name;
         else $result["Custom Fields"][$val->id] = $val->name;
      }
      if($return_type=='dropdown') return $result;
      else echo json_encode($result);
  }

  protected function get_api_types(){
      $list = ["fb"=>"Facebook","ig"=>"Instagram"];
      return $list;
  }

  protected function get_api_methods(){
      $enum = $this->basic->get_enum_values('settings_http_apis','api_method');
      $result = [];
      foreach($enum as $val){
          $result[$val] = $val;
      }
      return $result;
  }

  public function index()
  {
    redirect(base_url('http_api/build_api?channel=fb'));
  }

  public function activate()
  {
      $this->ajax_check();

      if(!method_exists($this, 'http_api_trigger')) {
          echo json_encode(array('status' => '0', 'message' => "This addon requires minimum ".$this->config->item('product_name')." version v9.3.4"));
          exit();
      }

      $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
      $purchase_code=$this->input->get_post('purchase_code',true);
      $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
      
      //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
      $sidebar=array(); 
      // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
      $sql=
      array
      (
          0 => "CREATE TABLE IF NOT EXISTS `settings_http_apis` (
            `id` int NOT NULL AUTO_INCREMENT,
            `user_id` int NOT NULL,
            `api_endpoint_url` text COLLATE utf8mb4_unicode_ci,
            `api_method` enum('GET','POST','PUT','PATCH','DELETE') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `api_name` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `api_type` enum('fb','ig') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
            `is_mapped` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
            `created_at` datetime DEFAULT NULL,
            `test_subscriber_unique_id` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `last_call_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `last_call_data_mapped` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'last_call_data copied after mapping done',
            `api_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `mapping_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `total_call` int NOT NULL,
            `total_success` int NOT NULL,
            `total_error` int NOT NULL,
            `last_called_at` datetime DEFAULT NULL,
            `last_error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`,`api_type`),
            KEY `last_called_at` (`last_called_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

          1 => "CREATE TABLE IF NOT EXISTS `settings_http_api_calls` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `subscriber_unique_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `settings_http_api_id` int NOT NULL,
            `api_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `api_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `success` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `subscriber_unique_id` (`subscriber_unique_id`),
            KEY `settings_http_api1` (`settings_http_api_id`),
            KEY `created_at` (`created_at`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

          2 => "CREATE TABLE IF NOT EXISTS `settings_http_api_formatters` (
            `id` int NOT NULL AUTO_INCREMENT,
            `settings_http_api_id` int NOT NULL,
            `formatter_name` varchar(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `formatter_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            PRIMARY KEY (`id`),
            KEY `settings_http_api_formatters1` (`settings_http_api_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

          3 => "ALTER TABLE `settings_http_apis` ADD CONSTRAINT `settings_http_apis1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;",

          4 => "ALTER TABLE `settings_http_api_calls` ADD CONSTRAINT `settings_http_api_calls1` FOREIGN KEY (`settings_http_api_id`) REFERENCES `settings_http_apis` (`id`) ON DELETE CASCADE;",

          5 => "ALTER TABLE `settings_http_api_formatters` ADD CONSTRAINT `settings_http_api_formatters1` FOREIGN KEY (`settings_http_api_id`) REFERENCES `settings_http_apis` (`id`) ON DELETE CASCADE;",
      );
      //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
      $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code);
  }


  public function deactivate()
  {        
      $this->ajax_check();

      $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
      // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
      $this->unregister_addon($addon_controller_name);      
  }

  public function delete()
  {        
      $this->ajax_check();

      $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

      // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
      $sql=array
      (       
          0=> "DROP TABLE IF EXISTS `settings_http_api_calls`;",
          1=> "DROP TABLE IF EXISTS `settings_http_api_formatters`;",
          2=> "DROP TABLE IF EXISTS `settings_http_apis`;"
      );  
      
      // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
      $this->delete_addon($addon_controller_name,$sql);         
  }


}