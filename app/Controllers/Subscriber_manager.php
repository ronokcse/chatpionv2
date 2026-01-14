<?php

namespace App\Controllers;

use App\Controllers\Home;

class Subscriber_manager extends Home
{
    public $input;
    public $is_webview_exist;
    public $is_ecommerce_exist;
    public $is_drip_campaigner_exist;
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        check_module_access($module_id=66);

        $function_name = $this->uri->getSegment(2);
        if($function_name!="" && $function_name!="index" && $function_name!="sync_subscribers" && $function_name!="bot_subscribers" && $function_name!="bot_subscribers_data" &&  $function_name!="contact_group" &&  $function_name!="contact_group_data")
        {
          if(session()->get("facebook_rx_fb_user_info")==0)
          return redirect()->to(base_url('social_accounts/index'))->send();
          $this->load->library("fb_rx_login");
        }
        $this->important_feature();
        $this->member_validity();        
    }

    
    public function index()
    {
        $data['body'] = 'messenger_tools/subscriber_manager_menu_block';
        $data['page_title'] = lang('Subscriber Manager');
        $this->_viewcontroller($data);
    }

    public function contact_group_data()
    { 
      $this->ajax_check();

      $page_id = $this->input->post('page_id',true);
      $searching = $this->input->post('searching',true);
      $display_columns = array("#","id","group_name");

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
      $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
      $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
      $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'group_name';
      $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
      $order_by=$sort." ".$order;

      $where_simple = array();
      $where_simple['messenger_bot_broadcast_contact_group.deleted'] = '0';
      $where_simple['messenger_bot_broadcast_contact_group.invisible'] = '0';
      $where_simple['messenger_bot_broadcast_contact_group.user_id'] = $this->user_id;
      $where_simple['facebook_rx_fb_page_info.bot_enabled'] = "1";

      $explode_page_id = explode_page_id($page_id);
      $page_id = $explode_page_id['page_id'];
      $social_media = $this->using_media_type;

      $where_simple['messenger_bot_broadcast_contact_group.social_media'] = $social_media;

      if($page_id !='') $where_simple['messenger_bot_broadcast_contact_group.page_id'] = $page_id;

      $where = array("where"=> $where_simple);
      if($searching != '') {
        $where['where'] = " messenger_bot_broadcast_contact_group.group_name LIKE  '%".$this->db->escapeLikeString($searching)."%' ";
      }

      $select = array("messenger_bot_broadcast_contact_group.*","facebook_rx_fb_page_info.page_name","facebook_rx_fb_page_info.insta_username","facebook_rx_fb_page_info.page_id AS pageid");
      $join  = array("facebook_rx_fb_page_info"=>"messenger_bot_broadcast_contact_group.page_id=facebook_rx_fb_page_info.id,left");

      $table="messenger_bot_broadcast_contact_group";
      $info = $this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,'group_name');

      $contact_group_table_ids = [];
      $subscribers_group = [];
      foreach($info as $value)
        array_push($contact_group_table_ids, $value['id']);
      $subscribers_data = [];
      if(!empty($contact_group_table_ids))
        $subscribers_data = $this->basic->get_data('messenger_bot_subscribers_label',['where_in'=>['contact_group_id'=>$contact_group_table_ids]],['contact_group_id','count(id) as total_subscriber'],[],'','','','contact_group_id');
      foreach($subscribers_data as $subscriber_count)
        $subscribers_group[$subscriber_count['contact_group_id']] = $subscriber_count['total_subscriber'];

      $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,'group_name');
      $total_result=$total_rows_array[0]['total_rows'];


      for($i=0;$i<count($info);$i++) 
      {
        if($info[$i]['unsubscribe'] == '1')
          $actions = "<a href='javascript:void(0)' class='text-muted disabled mr-1' title='".lang("Delete Label")."'><i class='fas fa-trash-alt'></i></a>";
        else 
          $actions = "<a href='#' class='text-dark delete_label mr-1' social_media='".$info[$i]['social_media']."'  table_id='".$info[$i]['id']."' title='".lang("Delete Label")."'><i class='fas fa-trash-alt'></i></a>";
        $subscriber_count = $subscribers_group[$info[$i]['id']] ?? 0;
        $subscriber_count = custom_number_format($subscriber_count,3);
        $info[$i]['group_name'] = $actions.' '.$info[$i]['group_name'].' ['.$subscriber_count.']';
      }

      $data['draw'] = (int)$_POST['draw'] + 1;
      $data['recordsTotal'] = $total_result;
      $data['recordsFiltered'] = $total_result;
      $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");

      echo json_encode($data);
    }

    
    public function ajax_label_insert()
    {
      $this->ajax_check();
      check_module_action_access($module_id=66,$actions=1,$response_type='json1');

      $return = array();
      $user_id = $this->user_id;
      $group_name = strip_tags(trim($this->input->post("group_name")));
      $page_id    = trim($this->input->post("selected_page_id"));
      
      $explode_page_id = explode_page_id($page_id);
      $page_id = $explode_page_id['page_id'];
      $social_media = $this->using_media_type;
      
      $response = ['id'=>''];
      if(isset($response['id']))
      { 
        $inserted_data = array(
          'user_id'=> $user_id,
          'group_name'=> $group_name,
          'page_id'=> $page_id,
          'social_media' => $social_media
        ); 

        try {
          $this->basic->insert_data("messenger_bot_broadcast_contact_group",$inserted_data);          
          $return['status'] = "1";
          $return['message'] = lang("Label has been created successfully.");                 
        } 
        catch (\Exception $e) {
            $response['error'] = lang("Label already exists. Try a different name."); 
        }        
        
      }
      if(isset($response['error']))
      {
        $return['status'] = "0";
        $return['message'] = $response['error'];
      }

      echo json_encode($return);
    }
    

    
    public function ajax_delete_label()
    {
      $this->ajax_check();

      $return = array();

      $primary_key = trim($this->input->post("table_id",true));
      $social_media = trim($this->input->post("social_media",true));

      $getdata = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("id"=>$primary_key)));

      $page_id = isset($getdata[0]['page_id']) ? $getdata[0]['page_id']:""; //database id
      $label_id = isset($getdata[0]['label_id']) ? $getdata[0]['label_id']:"";

      // if($social_media=='fb')
      // {
      //   $getdata = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_id)));
      //   $page_access_token = isset($getdata[0]['page_access_token']) ? $getdata[0]['page_access_token']:"";
      // }

      if($this->basic->is_exist("messenger_bot_broadcast_contact_group",array("unsubscribe"=>"1","id"=>$primary_key)))
      {   
        $return['status'] = 'failed';
        $return['message'] = lang('Sorry, Unsubscribe label can not be deleted.');

      } 
      else
      {
        // $response = $social_media=='fb' ? $this->fb_rx_login->delete_label($page_access_token,$label_id) : ['success'=>'1'];
        $response = ['success'=>'1'];

        if(isset($response['success']) && $response['success']=='1')
        {
          $this->basic->delete_data("messenger_bot_broadcast_contact_group",array("id"=>$primary_key));
          $return['status'] = 'successfull';
          $return['message'] = lang('Label has been deleted Successfully.');

        }
        else if(isset($response['error']))
        {

          $return['status'] = 'error';
          $return['error_message'] = $response['error'];

        } else
        {
          $return['status'] = 'wrong';
          $return['message'] = lang("Something Went Wrong, please try once again.");

        }

      }

      echo json_encode($return); 
    }
    

    public function get_page_details()
    {
        $this->ajax_check();
        $page_table_id = $this->input->post('page_table_id',true);
        // $social_media = $this->using_media_type;
        $social_media = $this->input->post('media_type',true);
        $facebook_rx_fb_user_info_id  =  session()->get('facebook_rx_fb_user_info');
        session()->set('selected_global_page_table_id',$page_table_id);

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('facebook_rx_fb_user_info_id' => $facebook_rx_fb_user_info_id,'id'=>$page_table_id);
        $page_info = $this->basic->get_data($table_name,$where,'','','','','page_name asc');

        $last_lead_sync = lang("Never Synced");
        if($page_info[0]['last_lead_sync']!='0000-00-00 00:00:00') $last_lead_sync = date_time_calculator($page_info[0]['last_lead_sync'],true);

        $unsubscribed = 0;
        if($social_media=='ig')  $unsubscribed = custom_number_format($page_info[0]['insta_current_unsubscribed_lead_count']);
        else $unsubscribed = custom_number_format($page_info[0]['current_unsubscribed_lead_count']);

        $bot_subscriber_info = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('user_id'=>$this->user_id,'page_table_id'=>$page_table_id,'is_bot_subscriber'=>'1','social_media'=>$social_media)),array('count(id) as total_subscriber'));
        // $bot_unavailable_info = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('user_id'=>$this->user_id,'page_table_id'=>$page_table_id,'is_bot_subscriber'=>'1','unavailable'=>'1','social_media'=>$social_media)),array('count(id) as unavailable'));
        $bot_subscriber = 0;
        $bot_unavailable = 0;
        if(isset($bot_subscriber_info[0]['total_subscriber'])) $bot_subscriber = custom_number_format($bot_subscriber_info[0]['total_subscriber']);
        // if(isset($bot_unavailable_info[0]['unavailable'])) $bot_unavailable = custom_number_format($bot_unavailable_info[0]['unavailable']);

        $subscriber_24 = 0;
        // $migrated_bot_subscriber = 0;

        date_default_timezone_set('UTC');
        $current_time = date("Y-m-d H:i:s");
        $previous_time = date("Y-m-d H:i:s",strtotime('-24 hour',strtotime($current_time)));
        $this->_time_zone_set();
        $where_simple2 = array();

        $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time <'] = $previous_time;
        $where_simple2['messenger_bot_subscriber.last_subscriber_interaction_time !='] = "0000-00-00 00:00:00";
        $where_simple2['messenger_bot_subscriber.is_24h_1_sent'] = '0';
        $where_simple2['social_media'] = $social_media;
        $where_simple2['user_id'] = $this->user_id;
        $where_simple2['page_table_id'] = $page_table_id;
        $where_simple2['unavailable'] = '0';
        $where_simple2['is_bot_subscriber'] = '1';
        $where = array('where'=>$where_simple2);


        $where = array(
            'where' => array(
                'user_id' => $this->user_id,
                'last_subscriber_interaction_time >=' => $previous_time,
                'last_subscriber_interaction_time !=' => "0000-00-00 00:00:00",
                'page_table_id' => $page_table_id,
                'unavailable' => '0',
                'is_bot_subscriber' => '1',
                'social_media'=>$social_media
            )
        );
        $subscriber_24_info = $this->basic->get_data('messenger_bot_subscriber',$where,array('count(id) as total_subscriber'));
        if(isset($subscriber_24_info[0]['total_subscriber'])) $subscriber_24 = custom_number_format($subscriber_24_info[0]['total_subscriber']);

        // $migrated_bot_subscriberinfo = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('user_id'=>$this->user_id,'page_table_id'=>$page_table_id,'is_imported'=>'1','is_bot_subscriber'=>'1')),array('count(id) as total_subscriber'));
        // if(isset($migrated_bot_subscriberinfo[0]['total_subscriber'])) $migrated_bot_subscriber = custom_number_format($migrated_bot_subscriberinfo[0]['total_subscriber']);

       
        if($social_media=='ig')
        $details = "<a target='_BLANK' href='".base_url('message_manager/instagram_message_dashboard/').$page_info[0]['id']."' class='btn btn-outline-danger'><i class='fas fa-eye'></i> ".lang("Details")."</a>";
        else $details = "<a target='_BLANK' href='".base_url('message_manager/message_dashboard/').$page_info[0]['id']."' class='btn btn-outline-danger'><i class='fas fa-eye'></i> ".lang("Details")."</a>";

        $scan_now = '<a href="#" id ="'.$page_info[0]['id'].'" style="margin-top:-5px" class="float-right btn btn-outline-primary btn-sm import_data"><i class="fas fa-search"></i> '.lang("Scan").'</a>';

        $popover="";
        if($page_info[0]['auto_sync_lead']=="0" || $page_info[0]['auto_sync_lead']=="3")
        {
          $enable_disable = 1;
          $enable_disable_class = "auto_sync_lead_page btn-outline-warning";
          $enable_disable_text = "<i class='fas fa-check-circle'></i> ".lang("Enable Auto Scan");
        }
        if($page_info[0]['auto_sync_lead']=="1")
        {
          $enable_disable = 0;
          $enable_disable_class = "btn-outline-danger disabled";
          $enable_disable_text = "<i class='fas fa-clock-o'></i> ".lang("Auto Scan Queued");
          $popover=' <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.lang("Queued").'" data-content="'.lang("Background scanning will be completed by multiple steps depending on total number of subscribers. Queued means it is waiting for the next step. Background scanning will scan page's inbox in background with multiple step & once all subscribers from inbox is imported, it will turn into default state again with Enable button.This option mostly used for pages that has a big subscribers list & possibly get error during Scan page inbox option").'"><i class="fas fa-info-circle"></i> </a>';
        }
        if($page_info[0]['auto_sync_lead']=="2")
        {
          $enable_disable = 1;
          $enable_disable_class = "btn-outline-warning auto_sync_lead_page";
          $enable_disable_text = "<i class='fas fa-spinner'></i> ".lang("Force Auto Scan");
          $popover=' <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.lang("Processing, Force Restart").'" data-content="'.lang("Background scanning is processing. Due to any unexpected server unavailability this process can be corrupted and can run forever. If you think this is processing forever, then you can force restart it.").'"><i class="fas fa-info-circle"></i> </a>';
        }

        $insta_user = $page_info[0]['has_instagram']=='1' ? '<a href="https://instagram.com/'.$page_info[0]['insta_username'].'" target="_BLANK"><i class="fab fa-instagram"></i> '.$page_info[0]['insta_username'].'</a>' : ''; 

        $title = $social_media=='ig' ? '<i class="fab fa-instagram"></i> <a href="https://instagram.com/'.$page_info[0]['insta_username'].'" target="_BLANK">'.$page_info[0]['insta_username'].'</a> <i class="fas fa-info-circle subscriber_info_modal"></i>' : '<i class="fab fa-facebook"></i> <a href="https://facebook.com/'.$page_info[0]['page_id'].'" target="_BLANK">'.$page_info[0]['page_name'].'</a> <i class="fas fa-info-circle subscriber_info_modal"></i>'; 

        $middle_column_content = '
                  <div class="row px-2">
                    <div class="col-md-6 col-12 px-2">
                      <div class="card card-statistic-1">
                        <div class="card-icon bg-body mr-2">
                          <i class="fas fa-user-astronaut text-primary"></i>
                        </div>
                        <div class="card-wrap">
                          <div class="card-header">
                            <h4>'.lang("Bot Subscriber").'</h4>
                          </div>
                          <div class="card-body">
                            '.$bot_subscriber.'<span class="red" data-toggle="tooltip" data-placement="bottom" title="'.lang('Unsubscribed').'"> ('.$unsubscribed.')</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 col-12 px-2">
                      <div class="card card-statistic-1">
                        <div class="card-icon bg-body mr-2">
                          <i class="fas fa-user-clock text-info"></i>
                        </div>
                        <div class="card-wrap">
                          <div class="card-header">
                            <h4>'. lang("24H Subscriber").'</h4>
                          </div>
                          <div class="card-body">
                            '.$subscriber_24.'
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>';
          $middle_column_content .='
          <script>
          $(\'[data-toggle="tooltip"]\').tooltip(); 
          $(\'[data-toggle="popover"]\').popover(); 
          $(\'[data-toggle="popover"]\').on("click", function(e) {e.preventDefault(); return true;});
          $(document).ready(function() {setTimeout(function(){ $(\'#label_id\').select2(); }, 1000); });
          </script>
          ';        

        $label_id=array(''=>lang("Label"));
        $labelinfo = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array('user_id'=>$this->user_id,"invisible"=>"0","page_id"=>$page_table_id)));
        foreach ($labelinfo as $key => $value) {
            $result = $value['id'];
            $label_id[$result] = $value['group_name'];
        }

        $response['middle_column_content'] = $middle_column_content;
        $response['dropdown']=form_dropdown('label_id',$label_id,'','class="form-control select2" id="label_id" style="width:150px !important;"');  
        $response['title']=$title;  
        echo json_encode($response);
    }

    public function client_subscribe_unsubscribe_status_change()
    {
        $this->ajax_check();
        check_module_action_access($module_id=66,$actions=2,$response_type='json1');

        if(empty($_POST['client_subscribe_unsubscribe_status'])) die();

        $client_subscribe_unsubscribe = array();
        $social_media=$this->input->post('social_media');
        $post_val=$this->input->post('client_subscribe_unsubscribe_status');
        $subscriber_details_page=$this->input->post('subscriber_details_page'); // if 1 means called from subscriber action page
        $client_subscribe_unsubscribe = explode("-",$post_val);
        $id = isset($client_subscribe_unsubscribe[0]) ? $client_subscribe_unsubscribe[0]: 0;
        $current_status =  isset($client_subscribe_unsubscribe[1]) ? $client_subscribe_unsubscribe[1]: 0;
        
        if($current_status=="1") $permission="0";
        else $permission="1";

        $client_thread_info = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('id'=>$id,'user_id'=>$this->user_id)));
        $client_thread_id = $client_thread_info[0]['client_thread_id'];
        $page_id = $client_thread_info[0]['page_id'];
        $page_table_id = $client_thread_info[0]['page_table_id'];
        $subscriber_id = $client_thread_info[0]['subscribe_id'];
        $contact_group_id = $client_thread_info[0]['contact_group_id'];


        $where = array
        (
            'id' => $id,
            'user_id' => $this->user_id
        );
        $login_user_id = $this->user_id;
        $data = array('permission' => $permission);
        if($permission=="0") $data["unsubscribed_at"] = date("Y-m-d H:i:s");
        $response='';

        // messenger bot label data block
        $page_access_token = $label_id = $label_auto_id ='';
        $new_label_id = $contact_group_id;
        $new_label_names = "";
        $label_id_names = array(); // assoc array
        //$page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$page_table_id,"bot_enabled"=>"1","user_id"=>$this->user_id)));
        // if(isset($page_info[0]))
        // {
          //$page_access_token = $page_info[0]["page_access_token"];
          $label_info = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_table_id,"user_id"=>$this->user_id,"social_media"=>$social_media)));
          
          foreach ($label_info as $key => $value) 
          {
            if($value['unsubscribe']=='1')
            {
              $label_id = $value['label_id'];
              $label_auto_id = $value['id'];
            }
            $label_id_names[$value['id']] = $value['group_name'];
          }
        //}

        if($permission==0)
        {
          $explode=explode(',', $contact_group_id);
          array_push($explode, $label_auto_id);
          $new=array_unique($explode);
          $new_label_id=implode(',', $new);
          $new_label_id=trim($new_label_id,',');
        }
        else
        { 
          $explode=explode(',', $contact_group_id);                
          foreach(array_keys($explode, $label_auto_id) as $key) unset($explode[$key]);
          $new=array_unique($explode);
          $new_label_id=implode(',', $new);
          $new_label_id=trim($new_label_id,',');
        }
        $data["contact_group_id"] = $new_label_id;   

        $temp=array();
        $new_label_id_exp = explode(',', $new_label_id);
    
        foreach ($new_label_id_exp as $key => $value) 
        {
          if(isset($label_id_names[$value])) $temp[] = $label_id_names[$value];
        }
        $new_label_names = implode(',', $temp);    
        // messenger bot label data block

        $response =array('button'=>'','label'=>$new_label_names,'status'=>'0','message'=>lang("Something went wrong, please try again."));
        if($this->basic->update_data('messenger_bot_subscriber', $where, $data))
        {    
            if($permission=="0")
            {
                // assign bot label unsubscribe              
                // if($page_access_token!="" && $label_id!="" && $social_media=='fb')
                // {
                //   $this->fb_rx_login->assign_label($page_access_token,$subscriber_id,$label_id);    
                // }

                $response['button'] = "<a href='' id ='".$id."-".$permission."' social_media='".$social_media."' title='".lang("Subscribe")."' class='client_thread_subscribe_unsubscribe btn btn-circle btn-primary'><i class='fas fa-user-check'></i></a>";
                $response['button2'] ='<span class="subsribe_unsubscribe_container"><a class="text-primary">'.lang("Unsubscribed").'</a> <a class="text-muted pointer client_thread_subscribe_unsubscribe" social_media="'.$social_media.'" id="'.$id."-".$permission.'">('.lang("Subscribe").')</a></span>'; // called from subscriber action page
                $response['message'] = lang("Subscriber has been unsubscribed successfully.");
                $response['status'] = "1";
                $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count = current_subscribed_lead_count-1,current_unsubscribed_lead_count = current_unsubscribed_lead_count+1 WHERE user_id = '$login_user_id' AND page_id = '$page_id'");
            }
            else  
            {
                // deassign bot label unsubscribe
                // if($page_access_token!="" && $label_id!=""  && $social_media=='fb')
                // {
                //   $this->fb_rx_login->deassign_label($page_access_token,$subscriber_id,$label_id);
                // }

                $response['button'] = "<a href=''  social_media='".$social_media."' id ='".$id."-".$permission."' title='".lang("Unsubscribe")."' class='client_thread_subscribe_unsubscribe btn btn-circle btn-danger'><i class='fas fa-user-times'></i></a>";
                $response['button2'] ='<span class="subsribe_unsubscribe_container"><a class="text-primary">'.lang("Subscribed").'</a> <a class="text-muted pointer client_thread_subscribe_unsubscribe" social_media="'.$social_media.'" id="'.$id."-".$permission.'">('.lang("Unsubscribe").')</a></span>'; // called from subscriber action page
    
                $response['message'] = lang("Subscriber has been subscribed back successfully.");
                $response['status'] = "1";

                if($social_media=='fb')
                $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count = current_subscribed_lead_count+1,current_unsubscribed_lead_count = current_unsubscribed_lead_count-1 WHERE user_id = '$login_user_id' AND page_id = '$page_id'");
                else $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET insta_current_subscribed_lead_count = insta_current_subscribed_lead_count+1,insta_current_unsubscribed_lead_count = insta_current_unsubscribed_lead_count-1 WHERE user_id = '$login_user_id' AND page_id = '$page_id'");
            }
            echo json_encode($response);
        }
    }
   
    public function bot_subscribers($auto_selected_subscriber=0,$auto_selected_page=0)
    {
      
      if(session()->get('user_type') != 'Admin' && count(array_intersect($this->module_access, array(66))) == 0)
      return redirect()->to(base_url('home/login_page'))->send(); 

      $this->is_webview_exist=$this->webview_exist();
      $this->is_ecommerce_exist=$this->ecommerce_exist();
      $switch_to_instagram = $this->using_media_type=='ig' ? '1' : '0';

      $facebook_rx_fb_user_info_id  =  session()->get('facebook_rx_fb_user_info');

      $table_name = "facebook_rx_fb_page_info";
      $where['where'] = array('facebook_rx_fb_user_info_id' => $facebook_rx_fb_user_info_id,"bot_enabled"=>"1");
      if(!empty($this->team_allowed_pages)){
          $where['where_in'] = array("facebook_rx_fb_page_info.id"=>$this->team_allowed_pages);
      }
      $page_list = $this->basic->get_data($table_name,$where,'','','','','page_name asc');


      $page_info = array();

      if(!empty($page_list))
      {
          $i = 1;
          $selected_page_id = $auto_selected_page;
          if($auto_selected_page==0) $selected_page_id = session()->get('selected_global_page_table_id');
          foreach($page_list as $value)
          {
              if($switch_to_instagram=='1' && $value['has_instagram']=='0') continue;

              if($value['id'] == $selected_page_id) $page_info[0] = $value;
              else $page_info[$i] = $value;
              $i++;
          }
      }
      ksort($page_info);

      $data['page_info'] = $page_info;
      $data['page_title'] = $switch_to_instagram=='1' ? lang('Instagram - Sync Subscribers') :  lang('Facebook - Sync Subscribers');  

      $data['user_input_flow_exist'] = 'no';
      if($this->basic->is_exist("add_ons",array("project_id"=>49)))
      {
        if(session()->get('user_type') == 'Admin' || in_array(292,$this->module_access))
          $data['user_input_flow_exist'] = 'yes';
        else
          $data['user_input_flow_exist'] = 'no';
      }

      //$data['page_dropdown'] = $this->get_facebook_instagram_dropdown(session()->get("facebook_rx_fb_user_info"));      

      $data['body'] = 'messenger_tools/bot_subscribers';
      $data['page_title'] = lang('Subscriber Manager');
      $data['auto_selected_subscriber'] = $auto_selected_subscriber; // used for showing single subscriber data
      $data['auto_selected_page'] = $auto_selected_page; // used for showing single page data
      if($this->is_webview_exist) $data['webview_access'] = 'yes';
      else $data['webview_access'] = 'no';

      $data['ecommerce_exist'] = $this->is_ecommerce_exist ? 'yes' : 'no';
      $data['is_webview_exist'] = $this->is_webview_exist ?? false;
      $data['is_ecommerce_exist'] = $this->is_ecommerce_exist ?? false;

      $data['status_list'] = $this->get_payment_status();

      $this->_viewcontroller($data);
    }

    public function my_orders_data()
    { 
        $this->ajax_check();
        $this->load->helpers(array('ecommerce_helper'));
        $ecommerce_config = $this->get_ecommerce_config();
        $currency_position = isset($ecommerce_config['currency_position']) ? $ecommerce_config['currency_position'] : "left";
        $decimal_point = isset($ecommerce_config['decimal_point']) ? $ecommerce_config['decimal_point'] : 0;
        $thousand_comma = isset($ecommerce_config['thousand_comma']) ? $ecommerce_config['thousand_comma'] : '0';


        $search_value = $this->input->post("search_value");
        $subscriber_id = $this->input->post("search_subscriber_id");  
        $search_status = $this->input->post("search_status");        
        $search_date_range = $this->input->post("search_date_range");

        $display_columns = 
        array(
          "#",
          "CHECKBOX",
          'status',
          'discount',
          'payment_amount',
          'currency',
          'payment_method',
          'transaction_id',
          'invoice',
          'manual_filename',
          'updated_at',
          'paid_at'
        );
        $search_columns = array('coupon_code','transaction_id');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 10;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'updated_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple = array();
        if($search_status!="") $where_simple["ecommerce_cart.status"] = $search_status;
        $where_simple["ecommerce_cart.subscriber_id"] = $subscriber_id;
        
        $where_custom="";
        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%".$this->db->escapeLikeString($search_value)."%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
        if($search_date_range!="")
        {
            $exp = explode('|', $search_date_range);
            $from_date = isset($exp[0])?$exp[0]:"";
            $to_date = isset($exp[1])?$exp[1]:"";
            if($from_date!="Invalid date" && $to_date!="Invalid date")
            $where_custom .= " AND ecommerce_cart.updated_at >= '".$this->db->escape($from_date)."' AND ecommerce_cart.updated_at <='".$this->db->escape($to_date)."'";
        }
        
        $where = array("where" => $where_simple);
        if($where_custom != "") {
          $where['where'] = "ecommerce_cart.subscriber_id = '".$this->db->escape($subscriber_id)."'".$where_custom;
        }
        
        $table="ecommerce_cart";
        $select = "ecommerce_cart.id,action_type,ecommerce_cart.user_id,store_id,subscriber_id,coupon_code,coupon_type,discount,payment_amount,currency,ordered_at,transaction_id,card_ending,payment_method,manual_additional_info,manual_filename,paid_at,ecommerce_cart.status,ecommerce_cart.updated_at,status_changed_note";
        $info=$this->basic->get_data($table,$where,$select,$join='',$limit,$start,$order_by,$group_by='');
        // echo $this->db->lastQuery();
        
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join='',$group_by='');

        $total_result=$total_rows_array[0]['total_rows'];
        

        $payment_status = $this->get_payment_status();
        foreach($info as $key => $value) 
        {
            $config_currency = isset($value['currency']) ? $value['currency'] : "USD";
            // $info[$key]['currency']= isset($this->currency_icon[$config_currency]) ? $this->currency_icon[$config_currency] : "$";

            if($value['coupon_code']!='')
            $info[$key]['discount']= mec_number_format($info[$key]['discount'],$decimal_point,$thousand_comma);
            else $info[$key]['discount'] = "";

            $info[$key]['payment_amount'] = mec_number_format($info[$key]['payment_amount'],$decimal_point,$thousand_comma);

            if($info[$key]['payment_method'] == 'Cash on Delivery') $pay = "Cash";
            else $pay = $info[$key]['payment_method'];
            
            $info[$key]['payment_method'] = $pay." ".$info[$key]['card_ending'];
            if(trim($info[$key]['payment_method'])=="") $info[$key]['payment_method'] = "x";

            $info[$key]['transaction_id'] = ($info[$key]['transaction_id']!="") ? "<b class='text-primary'>".$info[$key]['transaction_id']."</b>" : "x";

            $updated_at = date("M j, y H:i",strtotime($info[$key]['updated_at']));
            $info[$key]['updated_at'] =  "<div style='min-width:110px;'>".$updated_at."</div>";

            if($value["paid_at"]!='0000-00-00 00:00:00')
            {
              $paid_at = date("M j, y H:i",strtotime($info[$key]['paid_at']));
              $info[$key]['paid_at'] =  "<div style='min-width:110px;'>".$paid_at."</div>";
            }
            else $info[$key]['paid_at'] = 'x';

            $st1=$st2="";
            $file = base_url('upload/ecommerce/'.$value['manual_filename']);
            $st1 = ($value['payment_method']=='Manual') ? $this->handle_attachment($value['id'], $file):"";
            
            if($value['payment_method']=='Manual')
            $st2 = ' <a data-id="'.$value['id'].'" href="#"  class="btn btn-outline-primary additional_info" data-toggle="tooltip" title="" data-original-title="'.lang("Additional Info").'"><i class="fas fa-info-circle"></i></a>';

            $info[$key]['manual_filename'] = ($st1=="" && $st2=="") ? "x" : "<div style='width:100px;'>".$st1.$st2."</div>"; 
            
            $info[$key]['invoice'] =  "<a class='btn btn-outline-primary' target='_BLANK' data-toggle='tooltip' title='".lang("Invoice")."' href='".base_url("ecommerce/order/".$value['id'])."'><i class='fas fa-receipt'></i></a>";

            $info[$key]["invoice"] .= '<script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';
            $info[$key]["invoice"] .= '<script>$(\'[data-toggle="popover"]\').popover();</script>';

            $payment_status = $info[$key]['status'];

            if($payment_status=='pending') $payment_status_badge = "<span class='text-danger'><i class='fas fa-spinner'></i> ".lang("Pending")."</span>";
            else if($payment_status=='approved') $payment_status_badge = "<span class='text-primary'><i class='fas fa-thumbs-up'></i> ".lang("Approved")."</span>";
            else if($payment_status=='rejected') $payment_status_badge = "<span class='text-danger'><i class='fas fa-thumbs-down'></i> ".lang("Rejected")."</span>";
            else if($payment_status=='shipped') $payment_status_badge = "<span class='text-info'><i class='fas fa-truck'></i> ".lang("Shipped")."</span>";
            else if($payment_status=='delivered') $payment_status_badge = "<span class='text-info'><i class='fas fa-truck-loading'></i> ".lang("Delivered")."</span>";
            else if($payment_status=='completed') $payment_status_badge = "<span class='text-success'><i class='fas fa-check-circle'></i> ".lang("Completed")."</span>";

            if($info[$key]['status_changed_note']!='')$payment_status_badge.='&nbsp;&nbsp;&nbsp;<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="'.lang("Note").'" data-content="'.htmlspecialchars($info[$key]['status_changed_note']).'"><i class="fas fa-comment text-primary"></i> </a>';
            $info[$key]['status'] = $payment_status_badge;           

        }
        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        echo json_encode($data);
    }

    private function handle_attachment($id, $file) 
    {
        $info = pathinfo($file);
        if (isset($info['extension']) && ! empty($info['extension'])) {
            switch (strtolower($info['extension'])) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return $this->manual_payment_display_attachment($file);
                case 'zip':
                case 'pdf':
                case 'txt':
                    return '<div data-id="' . $id . '" id="mp-download-file" class="btn btn-outline-info" data-toggle="tooltip" title="'.lang("Attachment").'"><i class="fas fa-download"></i></div>';
            }
        }
    }

    private function manual_payment_display_attachment($file) 
    {
        $output = '<div class="mp-display-img d-inline">';
        $output .= '<a class="mp-img-item btn btn-outline-info" data-image="' . $file . '" href="' . $file . '">';
        $output .= '<i class="fa fa-image"></i>';
        $output .= '</a>';
        $output .= '</div>';
        $output .= '<script>$(".mp-display-img").Chocolat({className: "mp-display-img", imageSelector: ".mp-img-item"});</script>';

        return $output;
    }

    private function get_payment_status()
    {
      return array('pending'=>lang('Pending'),'approved'=>lang('Approved'),'rejected'=>lang('Rejected'),'shipped'=>lang('Shipped'),'delivered'=>lang('Delivered'),'completed'=>lang('Completed'));
    }

    private function get_ecommerce_config($user_id='0')
    {
      if($user_id=='0') $user_id = $this->user_id;
      $data = $this->basic->get_data("ecommerce_config",array("where"=>array("user_id"=>$user_id)));
      if(isset($data[0])) return $data[0];
      else return array();
    }


    public function bot_subscribers_data()
    { 
        $this->ajax_check();
        session()->remove("bot_subscribers_sql");

        $search_value = $this->input->post("search_value");
        $page_id = $this->input->post("page_id");
        $label_id = $this->input->post("label_id");
        $email_phone_birth = $this->input->post("email_phone_birth");
        $gender = $this->input->post("gender");

        //$explode_page_id = explode_page_id($page_id);
        //$page_id = $explode_page_id['page_id'];
        //$social_media = $explode_page_id['social_media'];
        $social_media = $this->using_media_type;

        $display_columns = 
        array(
          "#",
          "CHECKBOX",
          'image_path',
          'page_name',
          'subscribe_id',
          'first_name',
          'last_name',
          'full_name',
          'actions',
          'gender',
          'label_names',
          'client_thread_id',
          'subscribed_at',
          'social_media'
        );
        $search_columns = array('first_name','last_name','full_name','subscribe_id','gender');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 11;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'subscribed_at';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_simple = array();
        $where_simple['messenger_bot_subscriber.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_user_info_id'] = session()->get('facebook_rx_fb_user_info');
        
        $where_custom = "";
        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%".$this->db->escapeLikeString($search_value)."%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }

        if(is_array($email_phone_birth))
        {
          foreach ($email_phone_birth as $key => $value) {
            if($value == 'has_phone')
              $where_simple['messenger_bot_subscriber.phone_number !='] = '';
            if($value == 'has_email')
              $where_simple['messenger_bot_subscriber.email !='] = '';
            if($value == 'has_birthdate')
              $where_simple['messenger_bot_subscriber.birthdate !='] = '0000-00-00';
          }
        }

        $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=messenger_bot_subscriber.page_table_id,left");  

        if($social_media=='ig') $where_simple['messenger_bot_subscriber.social_media'] = 'ig';
        else $where_simple['messenger_bot_subscriber.social_media !='] = 'ig';
        if($gender != '') $where_simple['messenger_bot_subscriber.gender'] = $gender;
        if($page_id!="") $where_simple['messenger_bot_subscriber.page_table_id'] = $page_id;
        // if($label_id!="") $where_simple["FIND_IN_SET('$label_id',messenger_bot_subscriber.contact_group_id) !="] = 0;  
        $join['messenger_bot_subscribers_label'] = "messenger_bot_subscriber.id=messenger_bot_subscribers_label.subscriber_table_id,left";
        
        if($label_id != '')
          $where_simple['messenger_bot_subscribers_label.contact_group_id'] = $label_id;

        $where = array("where" => $where_simple);
        if($where_custom != "") {
          $where['where'] = "messenger_bot_subscriber.user_id = ".$this->user_id." AND facebook_rx_fb_user_info_id = ".session()->get('facebook_rx_fb_user_info').$where_custom;
        }

        $table="messenger_bot_subscriber";
        $select = "messenger_bot_subscriber.*,page_name,insta_username,GROUP_CONCAT(messenger_bot_subscribers_label.contact_group_id separator ',') as single_contact_table_id";
        
        $info=$this->basic->get_data($table,$where,$select,$join,$limit,$start,$order_by,$group_by='messenger_bot_subscriber.id');
        
        // Get last query for session storage
        $builder_temp = $this->db->table($table);
        $builder_temp->select($select);
        $this->basic->generate_joining_clause($join, $builder_temp);
        $this->basic->generate_where_clause($where, $builder_temp);
        if($this->db->fieldExists('deleted',$table)) {
          $builder_temp->where($table.".deleted","0");
        }
        $builder_temp->orderBy($order_by);
        $builder_temp->groupBy('messenger_bot_subscriber.id');
        if(is_numeric($start) || is_numeric($limit)) {
          $limit_int = is_numeric($limit) ? (int)$limit : null;
          $start_int = is_numeric($start) ? (int)$start : null;
          if($limit_int !== null) {
            $builder_temp->limit($limit_int, $start_int);
          }
        }
        // Get compiled SQL before executing
        $last_query = $builder_temp->getCompiledSelect(false);
        $builder_temp->get(); // Execute query
        session()->set("bot_subscribers_sql", $last_query);         
       
        $total_rows_array=$this->basic->count_row($table,$where,$count=$table.".id",$join,'messenger_bot_subscriber.id');

        $total_result=count($total_rows_array);

        foreach($info as $key => $value) 
        {
            $info[$key]['label_names']= "";

            $info[$key]['subscribed_at']= date("jS M y H:i",strtotime($info[$key]["subscribed_at"]));            
     
            $info[$key]['actions'] = "<a  data-id='".$info[$key]['id']."' data-subscribe-id='".$info[$key]['subscribe_id']."' data-page-id='".$info[$key]['page_table_id'].'-'.$info[$key]['social_media']."' class='btn btn-outline-primary btn-circle subscriber_actions_modal'  href=''><i class='fas fa-briefcase'></i></a>";

            $info[$key]['page_name'] = $info[$key]['social_media'] == 'fb' ? "<a target='_BLANK' href='https://facebook.com/".$info[$key]['page_id']."'>".$info[$key]['page_name'] ."</a>" : "<a target='_BLANK' href='https://instagram.com/".$info[$key]['insta_username']."'>".$info[$key]['insta_username'] ."</a>";

            $info[$key]['full_name'] = $info[$key]['social_media'] == 'fb' ? $info[$key]['full_name'] : "<a target='_BLANK' href='https://instagram.com/".$info[$key]['full_name']."'>".$info[$key]['full_name'] ."</a>";

            $profile_pic = ($value['profile_pic']!="") ? "<img class='rounded-circle' style='height:40px;width:40px;' src='".$value["profile_pic"]."'>" :  "<img class='rounded-circle' style='height:40px;width:40px;' src='".base_url('assets/img/avatar/avatar-1.png')."'>";
            $info[$key]['image_path']=($value["image_path"]!="") ? "<a  target='_BLANK' href='".base_url($value["image_path"])."'><img class='rounded-circle' style='height:40px;width:40px;' src='".base_url($value["image_path"])."'></a>" : $profile_pic;

            if($info[$key]['gender'] == "male") $info[$key]['gender'] ="<i class='fas fa-male blue' style='font-size:18px;' title='".lang('Male')."' data-toggle='tooltip' data-placement='bottom'></i>";
            else if($info[$key]['gender'] == "female") $info[$key]['gender'] ="<i class='fas fa-female purple' style='font-size:18px;' title='".lang('Female')."' data-toggle='tooltip' data-placement='bottom'></i>";

            if($info[$key]['email'] != '') $info[$key]['gender'] .= "&nbsp;&nbsp;<i class='fas fa-at blue' style='font-size:18px;' title='".lang('Email')."' data-toggle='tooltip' data-placement='bottom'></i>";

            if($info[$key]['phone_number'] != '') $info[$key]['gender'] .= "&nbsp;&nbsp;<i class='fas fa-phone blue' style='font-size:18px;' title='".lang('Phone')."' data-toggle='tooltip' data-placement='bottom'></i>";

            if($info[$key]['birthdate'] != '0000-00-00') $info[$key]['gender'] .= "&nbsp;&nbsp;<i class='fas fa-birthday-cake blue' style='font-size:18px;' title='".lang('Birthday')."' data-toggle='tooltip' data-placement='bottom'></i>";

            $info[$key]['social_media'] = $info[$key]['social_media']=='fb' ? "Facebook" : "Instagram";

        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
        echo json_encode($data);
    }


    public function get_label_dropdown()
    {
      $this->ajax_check();
      $page_id=$this->input->post('page_id');// database id

      $table_type = 'messenger_bot_broadcast_contact_group';
      $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"invisible"=>"0");
      $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name asc');
      $label_info=array(''=>lang("Label"));
      foreach($info_type as $value)
      {
          $label_info[$value['id']] = $value['group_name'];
      }
      echo form_dropdown('label_id',$label_info,'','class="form-control select2" id="label_id"');
      echo "<script>$('#label_id').select2();</script>";
    }


    public function download_result()
    {      

       if(!check_module_action_access($module_id=66,$actions=4,'check')){
          return redirect()->to(base_url('home/access_forbidden'))->send();
          exit();
       }

       if(function_exists('ini_set')){
          ini_set('memory_limit', '-1');
       } 


        if($this->is_demo == '1')
        {
            if(session()->get('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $bot_subscribers_sql = session()->get("bot_subscribers_sql");
        if(empty($bot_subscribers_sql)) exit();

        $xp = explode('LIMIT', $bot_subscribers_sql);
        $sql_without_limit = isset($xp[0]) ? $xp[0] : "";
        if(empty($sql_without_limit)) exit();
        $info = $this->basic->execute_query($sql_without_limit);

        $info_count = count($info);
        for($i=0; $i<$info_count; $i++)
        {
            $value = $info[$i]['single_contact_table_id'];
            $type_id = explode(",",$value);
            $type_ids = [];
            foreach($type_id as $value)
              array_push($type_ids,trim($value));

            $table = 'messenger_bot_broadcast_contact_group';
            $select = array('group_name');

            $where_group['where_in'] = array('id'=>$type_ids);
            $where_group['where'] = array('deleted'=>'0');

            $info1 = $this->basic->get_data($table,$where_group,$select);

            $str = '';
            foreach ($info1 as  $value1)
            {
              $str.= $value1['group_name'].","; 
            }
                
            $str = trim($str, ",");
            $info[$i]['contact_group_name']= $str;
        }

        $filename="exported_subscriber_list_".time()."_".$this->user_id.".csv";
        $f = fopen('php://memory', 'w');
        fputs( $f, "\xEF\xBB\xBF" );
        $head=array("Subscriber ID","Page ID","Page-Account","Label IDs","Labels","First Name","Last Name","Full Name","Gender","Locale","Timezone","Email","Phone","Location","Subscribed at","Status");
        fputcsv($f,$head, ",");

        foreach ($info as  $value) 
        {
            $write_info=array();            
            $write_info[] = $value['subscribe_id'];
            $write_info[] = $value['page_id'];
            $write_info[] = empty($value['insta_username']) ? $value['page_name'] : $value['page_name'].'-'.$value['insta_username'];
            $write_info[] = $value['single_contact_table_id'];
            $write_info[] = $value['contact_group_name'];
            $write_info[] = $value['first_name'];
            $write_info[] = $value['last_name'];
            $write_info[] = $value['full_name'];
            $write_info[] = $value['gender'];
            $write_info[] = $value['locale'];
            $write_info[] = $value['timezone'];
            $write_info[] = $value['email'];
            $write_info[] = $value['phone_number'];
            $write_info[] = $value['user_location'];          
            $write_info[] = $value['status'];          
            fputcsv($f, $write_info,',');  
        }

        fseek($f, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        fpassthru($f);         
    }


    public function get_label_dropdown_multiple()
    {
       $this->ajax_check();

       $page_auto_id=$this->input->post('selected_page'); // database id
       $explode_page_id = explode_page_id($page_auto_id);
       $page_auto_id = $explode_page_id['page_id'];
       $social_media = $explode_page_id['social_media'];

       $where = array();
       $where['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_auto_id,"invisible"=>"0","social_media"=>$social_media);  
       $group_info=$this->basic->get_data('messenger_bot_broadcast_contact_group', $where, $select='', $join='', $limit='', $start='', $order_by='group_name', $group_by='', $num_rows=0); 
             
        echo '<script>$("#label_ids").select2();</script>
        <label>'.lang("Choose Labels").'</label>
        <select name="label_ids" class="form-control" id="label_ids" multiple style="width:100%;">';
            foreach ($group_info as $key => $value) 
            {
               echo '<option value="'. $value['id'].'">'.$value['group_name'].'</option>';
            }
            // echo '<option value="" selected="selected">'.lang('Labels').'</option>';            
        echo '</select>';
       

    }

    public function get_sequence_campaigns()
    {
      $this->ajax_check();
      $page_id = $this->input->post("selected_page");
      $user_id = $this->user_id;

      $explode_page_id = explode_page_id($page_id);
      $page_id = $explode_page_id['page_id'];
      $social_media = $explode_page_id['social_media'];

      $sequence_lists = $this->basic->get_data("messenger_bot_drip_campaign",['where'=>['user_id'=>$user_id,'page_id'=>$page_id,'campaign_type !='=>'messenger','media_type'=>$social_media]]);

      $sequence_lists_html = '
        <label>'.lang("Select Sequence Campaign").'</label>
        <select name="sequence_ids" class="form-control" id="sequence_ids" multiple style="width:100%;">';
      foreach ($sequence_lists as $key => $value) 
      {
         $sequence_lists_html .= '<option value="'. $value['id'].'">'.$value['campaign_name'].' ['.$value['campaign_type'].']</option>';
      }

      $sequence_lists_html .='</select><script>$("#sequence_ids").select2();</script>';

      echo $sequence_lists_html;

      
    } 

    public function bulk_sequence_campaign_assign()
    {
      $this->ajax_check();

      $ids = $this->input->post("ids");
      $page_id = $this->input->post("page_id");
      $sequence_id = $this->input->post("sequence_id");
      $drip_type = "custom";

      $subscriber_data = $this->basic->get_data("messenger_bot_subscriber",array("where_in"=>array("id"=>$ids)));

      foreach ($subscriber_data as $value) 
      {
        $subscribe_id = $value["subscribe_id"];

        foreach ($sequence_id as $value2) {
          $this->assign_drip_messaging_id($drip_type,"0",$page_id,$subscribe_id,$value2);
        }
      }

      echo "1";
    }



  
    public function bulk_group_assign()
    {
        $this->ajax_check();
        if(!check_module_action_access($module_id=66,$actions=2,'check')){
           echo "0";
           exit();
        }

        $ids = $this->input->post("ids");
        $page_id = $this->input->post("page_id");
        $group_id = $this->input->post("group_id");

        $explode_page_id = explode_page_id($page_id);
        $page_id = $explode_page_id['page_id'];
        $social_media = $explode_page_id['social_media'];

       
        $get_groupdata=$this->basic->get_data('messenger_bot_broadcast_contact_group',array('where'=>array('page_id'=>$page_id,'social_media'=>$social_media)));

        $label_id=array();
        $unsubscribe_label = "0";
        foreach ($get_groupdata as $key => $value) 
        {
            $label_id[$value['id']]=$value['label_id'];
            if($value['unsubscribe']=='1') $unsubscribe_label = $value['id'];
        }

        $subscriber_data = $this->basic->get_data("messenger_bot_subscriber",array("where_in"=>array("id"=>$ids)));
        
        foreach ($subscriber_data as $key => $value) 
        {
          $id = $value["id"];
          $subscribe_id = $value["subscribe_id"];

          foreach($group_id as $group_table_id)
            $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscribers_label(contact_group_id,subscriber_table_id) 
              VALUES('$group_table_id','$id');");

          $update_data = array();
          if(in_array($unsubscribe_label, $group_id) && $unsubscribe_label!="0")           
          {
            $update_data['permission']='0';
            $update_data['unsubscribed_at']=date("Y-m-d H:i:s");
          }
          else $update_data['permission']='1';

          $this->basic->update_data("messenger_bot_subscriber",array("id"=>$id,"user_id"=>$this->user_id),$update_data);           

        }

        $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE social_media='$social_media' AND page_table_id='$page_id' AND permission='1' AND subscriber_type!='system' AND user_id=".$this->user_id;
        $count_data = $this->db->query($sql)->getRowArray();

        $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE  social_media='$social_media' AND page_table_id='$page_id' AND permission='0' AND subscriber_type!='system' AND user_id=".$this->user_id;
        $count_data2 = $this->db->query($sql2)->getRowArray();

         // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $update_subscription = $social_media=='fb' ? array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed,"current_lead_count"=>$current_lead_count) : array("insta_current_subscribed_lead_count"=>$subscribed,"insta_current_unsubscribed_lead_count"=>$unsubscribed,"insta_current_lead_count"=>$current_lead_count);

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id),$update_subscription);
        echo "1";
    }
    


    public function delete_bulk_subscriber()
    {
        $this->ajax_check();
        if(!check_module_action_access($module_id=66,$actions=3,$response_type='check')){
          echo "error";
          exit();
        }
        $ids = $this->input->post("ids");   
        $page_id = $this->input->post("page_id");  

        // Ensure $ids is an array for CI4 whereIn()
        if (!is_array($ids)) {
            if (is_string($ids) && !empty($ids)) {
                $ids = explode(',', $ids);
            } else {
                $ids = [];
            }
        }
        // Filter out empty values and ensure all are numeric
        $ids = array_filter(array_map('intval', $ids));
        
        if (empty($ids)) {
            echo 'error';
            exit();
        }

        $explode_page_id = explode_page_id($page_id);
        $page_id = $explode_page_id['page_id'];
        $social_media = $explode_page_id['social_media'];

        $get_data = $this->db->table('messenger_bot_subscriber')
            ->select('subscribe_id')
            ->where('user_id', $this->user_id)
            ->whereIn('id', $ids)
            ->get()
            ->getResultArray();
        if(empty($get_data)) {
          echo 'error';
          exit();
        }

        $subscriber_ids = [];
        foreach($get_data as $key=>$val){
          $subscriber_ids[] = $val['subscribe_id'];
        }

        $this->db->transStart();

        $this->db->table("messenger_bot_subscriber")->where('user_id', $this->user_id)->whereIn('id', $ids)->delete();

        // Only delete if subscriber_ids array is not empty
        if (!empty($subscriber_ids) && is_array($subscriber_ids)) {
            $this->db->table("messenger_bot_subscriber_extra_info")->whereIn('subscriber_id', $subscriber_ids)->delete();
        }

        $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE social_media='".$this->db->escape($social_media)."' AND page_table_id='".$this->db->escape($page_id)."' AND permission='1' AND subscriber_type!='system' AND user_id=".$this->user_id;
        $count_data = $this->db->query($sql)->getRowArray();

        $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE social_media='".$this->db->escape($social_media)."' AND page_table_id='".$this->db->escape($page_id)."' AND permission='0' AND subscriber_type!='system' AND user_id=".$this->user_id;
        $count_data2 = $this->db->query($sql2)->getRowArray();

        // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $update_subscription = $social_media=='fb' ? array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed,"current_lead_count"=>$current_lead_count) : array("insta_current_subscribed_lead_count"=>$subscribed,"insta_current_unsubscribed_lead_count"=>$unsubscribed,"insta_current_lead_count"=>$current_lead_count);

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id,"user_id"=>$this->user_id),$update_subscription);

        if($this->db->transStatus() === FALSE)
        {
            $this->db->transRollback();
        } else {
            $this->db->transCommit();
        }

        if($this->db->transStatus() === FALSE) 
        {
            echo 'error';
            exit();
        }

        echo "success";
    }

    public function get_subscriber_formdata()
    {
      $this->ajax_check();
      $id = $this->input->post("id",true);
      $page_table_id = $this->input->post("page_id",true);
      $subscribe_id = $this->input->post("subscribe_id",true);

      $table_name = "messenger_bot_user_custom_form_webview_data";
      $where = array(
        "where"=>array(
          "messenger_bot_user_custom_form_webview_data.page_id"=>$page_table_id,
          "messenger_bot_user_custom_form_webview_data.subscriber_id"=>$subscribe_id
        )
      );
      $join = array('webview_builder'=>"messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id=webview_builder.canonical_id,left");
      $select = array("webview_builder.form_name","messenger_bot_user_custom_form_webview_data.data as form_data","messenger_bot_user_custom_form_webview_data.inserted_at","messenger_bot_user_custom_form_webview_data.web_view_form_canonical_id as form_id");
      $data = $this->basic->get_data($table_name,$where,$select,$join);

      $content = '
        <div class="col-12 col-md-4">
          <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">';

      $i=1;
      foreach($data as $value)
      {
        $unique_id = 'formdata_tab_'.$i;
        $unique_id2 = 'formdata_tab_content_'.$i;
        if($i == 1) $active = 'active';
        else $active = '';
        $insert_date = date('jS M Y, H:i', strtotime($value['inserted_at']));
        $content .= '
            <li class="nav-item">
              <a class="no_radius nav-link '.$active.'" id="'.$unique_id.'" data-toggle="tab" href="#'.$unique_id2.'" role="tab" aria-controls="'.$unique_id.'" aria-selected="true">'
              .$value['form_name'].

              '<br/><p class="form_id">'.lang("Form ID").': '.$value['form_id'].'</p>
               <p class="insert_date">'.lang("Submit Date").': '.$insert_date.'</p>
              </a>

            </li>
        ';
        $i++;
      }
            
      $content .='</ul>
        </div>
        <div class="col-12 col-md-8">
          <div class="tab-content no-padding" id="myTab2Content">';

      $i=1;
      foreach($data as $value)
      {
        $unique_id = 'formdata_tab_'.$i;
        $unique_id2 = 'formdata_tab_content_'.$i;
        if($i == 1) $active = 'active show';
        else $active = '';
        $content .= '<div class="tab-pane fade '.$active.'" id="'.$unique_id2.'" role="tabpanel" aria-labelledby="'.$unique_id.'">';
        $content .= '
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-md">
              <thead><tr>
                <th>Field</th>
                <th>Value</th>
              </tr></thead><tbody>
        ';

        $form_data = json_decode($value['form_data'],true);
        foreach($form_data as $key=>$value)
        {
          if(is_array($value))
            $new_value = implode(',', $value);
          else $new_value = $value;
          $content .= '<tr>
                        <td>'.$key.'</td>
                        <td>'.$new_value.'</td>
                      </tr>';
        }

        $content .= '
            </tbody></table>
          </div>
        ';
        $content .= '</div>';
        $i++;
      }

      $content .='</div>
        </div>
      ';

      if(!empty($data))
        echo $content;
      else
        echo '<div class="col-12 card no_shadow" id="nodata">
                          <div class="card-body">
                            <div class="empty-state">
                              <img class="img-fluid" style="height: 200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                              <h2 class="mt-0">'.lang("We could not find any data.").'</h2>
                            </div>
                          </div>
                        </div>';


    }

    public function get_subscriber_inputflow_data()
    {
      $this->ajax_check();
      $id = $this->input->post("id",true);
      $page_table_id = $this->input->post("page_id",true);
      $subscribe_id = $this->input->post("subscribe_id",true);

      $page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['id'=>$page_table_id,'user_id'=>$this->user_id]],['page_id']);
      $page_id = isset($page_info[0]['page_id']) ? $page_info[0]['page_id'] : 0;

      $table_name = "user_input_flow_questions_answer";
      $where = array(
        "where"=>array(
          "user_input_flow_questions_answer.page_id"=>$page_id,
          "user_input_flow_questions_answer.subscriber_id"=>$subscribe_id
        )
      );
      $join = array(
        'user_input_flow_questions'=>"user_input_flow_questions_answer.question_id=user_input_flow_questions.id,left",
        'user_input_flow_campaign'=>"user_input_flow_questions_answer.flow_campaign_id=user_input_flow_campaign.id,left",
      );

      $select = array("user_input_flow_questions_answer.id as table_id","user_input_flow_campaign.flow_name","user_input_flow_questions.question","user_input_flow_questions_answer.user_answer","user_input_flow_campaign.id as flow_id","serial_no");
      $data = $this->basic->get_data($table_name,$where,$select,$join,$limit='',$start=NULL,'serial_no asc');

      $temp_data = [];
      foreach($data as $temp)
      {
        $temp_data[$temp['flow_id']]['flow_name'] = $temp['flow_name']; 
        $temp_data[$temp['flow_id']][$temp['table_id']]['question'] = $temp['question']; 
        $temp_data[$temp['flow_id']][$temp['table_id']]['answer'] = $temp['user_answer']; 
      }


      $content = '
        <div class="col-12 col-md-4">
          <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">';

      $i=1;
      foreach($temp_data as $key=>$value)
      {
        $unique_id = 'formdata_tab_'.$i;
        $unique_id2 = 'formdata_tab_content_'.$i;
        if($i == 1) $active = 'active';
        else $active = '';
        $content .= '
            <li class="nav-item">
              <a class="no_radius nav-link '.$active.'" id="'.$unique_id.'" data-toggle="tab" href="#'.$unique_id2.'" role="tab" aria-controls="'.$unique_id.'" aria-selected="true">'
              .$value["flow_name"].'
              </a>

            </li>
        ';
        $i++;
      }
            
      $content .='</ul>
        </div>
        <div class="col-12 col-md-8">
          <div class="tab-content no-padding" id="myTab2Content">';

      $i=1;
      foreach($temp_data as $main_data)
      {
        $unique_id = 'formdata_tab_'.$i;
        $unique_id2 = 'formdata_tab_content_'.$i;
        if($i == 1) $active = 'active show';
        else $active = '';
        $content .= '<div class="tab-pane fade '.$active.'" id="'.$unique_id2.'" role="tabpanel" aria-labelledby="'.$unique_id.'">';
        $content .= '
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-md">
              <thead><tr>
                <th>Question</th>
                <th>Answer</th>
              </tr></thead><tbody>
        ';
        unset($main_data['flow_name']);
        $form_data = $main_data;
        foreach($form_data as $value)
        {
          $answer = $value["answer"];
          $substr = substr($value['answer'],0,8);
          if($substr == 'https://') 
          {
            $answer = "<a target='_BLANK' href='".$value["answer"]."'>".lang('Visit Link')."</a>";
          }

          $content .= '<tr>
                        <td>'.$value["question"].'</td>
                        <td>'.$answer.'</td>
                      </tr>';
        }

        $content .= '
            </tbody></table>
          </div>
        ';
        $content .= '</div>';
        $i++;
      }

      $content .='</div>
        </div>
      ';

      if(!empty($data))
        echo $content;
      else
        echo '<div class="col-12 card no_shadow" id="nodata">
                          <div class="card-body">
                            <div class="empty-state">
                              <img class="img-fluid" style="height: 200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                              <h2 class="mt-0">'.lang("We could not find any data.").'</h2>
                            </div>
                          </div>
                        </div>';


    }

    public function get_subscriber_customfields_data()
    {
      $this->ajax_check();
      $id = $this->input->post("id",true);
      $page_table_id = $this->input->post("page_id",true);
      $subscribe_id = $this->input->post("subscribe_id",true);

      $page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where'=>['id'=>$page_table_id,'user_id'=>$this->user_id]],['page_id']);
      $page_id = isset($page_info[0]['page_id']) ? $page_info[0]['page_id'] : 0;

      $table_name = "user_input_custom_fields_assaign";
      $where = array(
        "where"=>array(
          "user_input_custom_fields_assaign.page_id"=>$page_id,
          "user_input_custom_fields_assaign.subscriber_id"=>$subscribe_id
        )
      );
      $join = array(
        'user_input_custom_fields'=>"user_input_custom_fields_assaign.custom_field_id=user_input_custom_fields.id,left"
      );

      $select = array("user_input_custom_fields.name","user_input_custom_fields.reply_type","custom_field_value");
      
      $info = $this->basic->get_data($table_name,$where,$select,$join);

      $content = '<div class="card w-100 no_shadow">
                    <div class="card-body">
                      <div class="section"><div class="section-title">'.lang("Given custom field's value from subscriber").'</div></div>
                      
                      <div class="table-responsive">
                        <table class="table table-sm">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">'.lang("Custom Field").'</th>
                              <th scope="col">'.lang("Reply Type").'</th>
                              <th scope="col">'.lang("Value").'</th>
                            </tr>
                          </thead>
                          <tbody>';
        $i = 1;
        foreach($info as $value)
        {
          $answer = $value["custom_field_value"];
          $substr = substr($value['custom_field_value'],0,8);
          if($substr == 'https://') 
          {
            $answer = "<a target='_BLANK' href='".$value["custom_field_value"]."'>".lang('Visit Link')."</a>";
          }

          $content .= '<tr>
                        <th scope="row">'.$i.'</th>
                        <td>'.$value["name"].'</td>
                        <td>'.$value["reply_type"].'</td>
                        <td>'.$answer.'</td>
                      </tr>';
          $i++;
        }
                            
        $content .=      '</tbody>
                        </table>
                      </div>
                    </div>
                  </div>';

      if(!empty($info))
        echo $content;
      else
        echo '<div class="col-12 card" id="nodata">
                          <div class="card-body">
                            <div class="empty-state">
                              <img class="img-fluid" style="height: 200px" src="'.base_url('assets/img/drawkit/drawkit-nature-man-colour.svg').'" alt="image">
                              <h2 class="mt-0">'.lang("We could not find any data.").'</h2>
                            </div>
                          </div>
                        </div>';


    }

    public function subscriber_actions_modal()
    {
      
      $this->ajax_check();
      $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
      $id = $this->input->post("id",true);
      $page_table_id = $this->input->post("page_id",true);
      $media_type = $this->input->post("media_type",true) ?? 'fb';
      $subscribe_id = $this->input->post("subscribe_id",true);
      $call_from_conversation = $this->input->post("call_from_conversation",true);
      if(empty($call_from_conversation)) $call_from_conversation = '0';

      $explode_page_id = explode_page_id($page_table_id);
      $page_table_id = $explode_page_id['page_id'];
      // $social_media = $explode_page_id['social_media'];
      $social_media = $media_type ?? 'fb';

      $subscriber_info_where = !empty($id) && $id>0 ? array("where"=>array("id"=>$id,"user_id"=>$this->user_id,"social_media"=>$social_media)) : array("where"=>array("subscribe_id"=>$subscribe_id,"user_id"=>$this->user_id,"social_media"=>$social_media));

      $subscriber_info = $this->basic->get_data("messenger_bot_subscriber",$subscriber_info_where);
      if(!isset($subscriber_info[0])) exit();
      $subscriber_data = $subscriber_info[0];

      $default = base_url('assets/images/avatar/avatar-1.png');
      $profile_pic = ($subscriber_data['profile_pic']!="") ? $subscriber_data["profile_pic"] :  $default;
      $subscriber_image =($subscriber_data["image_path"]!="") ? base_url($subscriber_data["image_path"]) : $profile_pic;
      $sdk_locale = $this->sdk_locale();
      $locale = (isset($sdk_locale[$subscriber_data['locale']])) ? $sdk_locale[$subscriber_data['locale']]: $subscriber_data['locale'];
      $timezone="";
      if($subscriber_data["timezone"]!="")
      {
        if($subscriber_data["timezone"]=='0') $timezone="GMT";
        else $timezone="GMT +".$subscriber_data["timezone"];
      }

      //  label assign block
      $table_type = 'messenger_bot_broadcast_contact_group';
      $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_table_id,"invisible"=>"0","social_media"=>$social_media);
      $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name asc');
      $label_info=array();
      foreach($info_type as $value)
      {
          $label_info[$value['id']] = $value['group_name'];
      }
      // $selected_labels = explode(',', $subscriber_data["contact_group_id"]);
      $selected_labels = [];
      $selected_labels_info = $this->basic->get_data('messenger_bot_subscribers_label',['where'=>['subscriber_table_id'=>$subscriber_data['id']]],['contact_group_id']);
      foreach($selected_labels_info as $selected_label_ids)
        array_push($selected_labels, $selected_label_ids['contact_group_id']);
      $label_dropdown = form_dropdown('subscriber_labels',$label_info,$selected_labels,'class="form-control select2" id="subscriber_labels" multiple style="width:100% !important;"');

      
      // subscribe unsubscribe blobk
      if($subscriber_data['permission'] == '1')
      $status ='<span class="subsribe_unsubscribe_container"><a class="text-primary">'.lang("Subscribed").'</a> <a class="text-muted pointer client_thread_subscribe_unsubscribe" social_media="'.$subscriber_data['social_media'].'" id="'.$subscriber_data['id']."-".$subscriber_data['permission'].'">('.lang("Unsubscribe").')</a></span>';
      else $status ='<span class="subsribe_unsubscribe_container"><a class="text-primary">'.lang("Unsubscribed").'</a> <a class="text-muted pointer client_thread_subscribe_unsubscribe" social_media="'.$subscriber_data['social_media'].'" id="'.$subscriber_data['id']."-".$subscriber_data['permission'].'">('.lang("Subscribe").')</a></span>';
      
      // bot strat stop blbok
      $start_stop = '';
      if($subscriber_data['status'] == '1')
      $start_stop = '<span class="client_thread_start_stop_container"><a href="" class="dropdown-item has-icon client_thread_start_stop" social_media="'.$subscriber_data['social_media'].'" button_id="'.$subscriber_data['id']."-".$subscriber_data['status'].'"><i class="far fa-pause-circle"></i> '.lang("Pause Bot Reply").'</a></span>';
      else $start_stop = '<span class="client_thread_start_stop_container"><a href="" class="dropdown-item has-icon client_thread_start_stop" button_id="'.$subscriber_data['id']."-".$subscriber_data['status'].'"><i class="far fa-play-circle"></i> '.lang("Resume Bot Reply").'</a></span>';

      $start_stop2 = '';
      if($subscriber_data['status'] == '1')
      $start_stop2 = '<span class="client_thread_start_stop_container"><a class="pointer text-primary client_thread_start_stop" call-from-conversation="1" social_media="'.$subscriber_data['social_media'].'" button_id="'.$subscriber_data['id']."-".$subscriber_data['status'].'">'.lang("Pause Bot Reply").'</a></span>';
      else $start_stop2 = '<span class="client_thread_start_stop_container"><a class="pointer text-primary client_thread_start_stop" call-from-conversation="1" button_id="'.$subscriber_data['id']."-".$subscriber_data['status'].'">'.lang("Resume Bot Reply").'</a></span>';
      

      $user_input_start_stop = '';
      if($this->addon_exist("custom_field_manager"))
      {
        if(session()->get('user_type') == 'Admin'|| in_array(292,$this->module_access))
        {
          $user_input_start_stop = '<a href="" class="dropdown-item has-icon reset_user_input_flow" social_media="'.$subscriber_data['social_media'].'"  button_id ="'.$subscriber_data['id']."-".$subscriber_data['subscribe_id']."-".$subscriber_data['page_table_id'].'"><i class="fas fa-retweet"></i> '.lang("Reset User Input Flow").'</a>';
        }
        else if(session()->get('user_type') == 'Admin') 
        {
          $user_input_start_stop = '<a href="" class="dropdown-item has-icon reset_user_input_flow" social_media="'.$subscriber_data['social_media'].'"  button_id ="'.$subscriber_data['id']."-".$subscriber_data['subscribe_id']."-".$subscriber_data['page_table_id'].'"><i class="fas fa-retweet"></i> '.lang("Reset User Input Flow").'</a>';
        }
      }
     
      // sequence message block
      $sequence_block='';      
      if($this->is_drip_campaigner_exist)
      {
        $campaign_data=$this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("page_id"=>$page_table_id,"media_type"=>$social_media)),$select='',$join='',$limit='',$start=NULL,$order_by='created_at DESC');
        $drip_types=$this->get_drip_type();
        $option=array('0'=>lang('Choose Message Sequence'));
        foreach ($campaign_data as $key => $value) 
        {
          $option[$value['id']]="";
          if($value['campaign_name']!="") $option[$value['id']].=$value['campaign_name']." : ";
          $option[$value['id']].=$drip_types[$value['drip_type']]." - ".$value['campaign_type']." [".date("jS M, y H:i:s",strtotime($value['created_at']))."]";
        }

        $current_sequence_array=array();
        $user_sequence = $this->basic->get_data("messenger_bot_drip_campaign_assign",array("where"=>array("subscribe_id"=>$subscribe_id,"user_id"=>$this->user_id)));
        foreach ($user_sequence as $key => $value) 
        {
          $current_sequence_array[] = $value['messenger_bot_drip_campaign_id'];
        }

        $sequence_dropdwon = form_dropdown('assign_campaign_id', $option, $current_sequence_array,'style="width:100%" class="form-control inline" id="assign_campaign_id" multiple');
        $last_sent_info='';
        // if($subscriber_data['messenger_bot_drip_campaign_id']!=0)
        // {
        //   $last_sent_info = '<small class="last_sent_info float-right" data-toggle="tooltip" title="'.lang("Last Sent").'"><i class="fas fa-clock"></i> '.date("jS M Y H:i").' ('.lang("Day").'-'.$subscriber_data['messenger_bot_drip_last_completed_day'].')</small>';
        // }
        $sequence_block='
        <div class="section mt-3">
          <div class="section-title mt-0 mb-2 full_width">
            '.lang("Message Sequence").'
            '.$last_sent_info.'                         
          </div>          
          '.$sequence_dropdwon.'
        </div>';
      }

      // optin block
      $show_only_fb = $social_media=='fb' ? 'd-block' : 'd-none';
      $optin_ref = '';
      $optin="DIRECT";
      $refferer_id = $subscriber_data['refferer_id'];
      if($subscriber_data['refferer_uri']!='') $refferer_id='<a href="'.$subscriber_data['refferer_uri'].'" target="_BLANK">'.$refferer_id.'</a>';
      if($subscriber_data['refferer_source']!='') $optin = str_replace('_', ' ', $subscriber_data['refferer_source']);
      if($subscriber_data['refferer_id']!='') $optin_ref='<span style="padding-left:45px;"><b>Refference : </b>'.$refferer_id.'</span>';
      $optinpop="";
      if($optin=='FB PAGE') $optin="DIRECT";
      if($optin=='DIRECT')
      $optinpop='<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="" data-content="'.lang("Direct OPT-IN means the subscriber either came from your Facebook page directly or the source is unknown.").'" data-original-title="'.lang("OPT-IN").'"><i class="fa fa-info-circle"></i> </a>';

      $print_name = ($subscriber_data['full_name']!="")?$subscriber_data['full_name']:$subscriber_data['first_name']." ".$subscriber_data['last_name'];
      if($subscriber_data['link']!="") $print_name_link = '<h4><a href="https://facebook.com/'.$subscriber_data['link'].'" target="_BLANK">'.$print_name.'</a></h4>';
      else $print_name_link = '<h4>'.$print_name.'</h4>';

      $collef_class = $call_from_conversation=='1' ? 'col-12 order-last' : 'col-12 col-md-5 col-lg-4';      
      $colmid_class = $call_from_conversation=='1' ? 'col-12 order-first' : 'col-12 col-md-7 col-lg-8';
      $start_row = $call_from_conversation=='1' ? '<div class="row">' : ''; 
      $end_row = $call_from_conversation=='1' ? '</div>' : '';
      $subscriber_image_html = '<div class="pt-4"></div>';
      if($call_from_conversation=='0')
      {
        $subscriber_image_html = '
         <div class="padding-20">
            <span class="bgimage" style="background-image: url('.$subscriber_image.');"></span>
         </div>';
      }

      $team_info = $this->get_team_member_list();
      $team_info[''] = lang('Select Agent');
      $team_dropdown =  form_dropdown('subscriber_teams', $team_info, $subscriber_data['assigned_used_id'], "class='form-control select2' id='subscriber_teams' style='width:100% !important;'");


      $mid_col_body = '
        <div class="section">
          <div class="section-title margin-0 pb-1">
            '.lang("Assigned Agent").'
          </div>
          <div id="subscriber_teams_container">'.$team_dropdown.'</div>
        </div>';

      $mid_col_body .= '
      <div class="section mt-3">
        <div class="section-title mt-0 mb-2 full_width">
          '.lang("Labels").'
          <a class="blue float-right pointer" data-id="'.$subscriber_data['id'].'" data-social-media="'.$subscriber_data['social_media'].'"  data-page-id="'.$subscriber_data['page_table_id'].'" id="create_label"><small><i class="fas fa-plus-circle"></i> '.lang("Create Label").'</small></a>                              
        </div>                            
        <div id="subscriber_labels_container">'.$label_dropdown.'</div>
      </div>

      '.$sequence_block.'

      <br>
      <div class="section '.$show_only_fb.'">
        <div class="section-title mt-0 mb-2 full_width">
          '.lang("OPT-IN Through").'    
          <span class="float-right text blue">'.$optin.' '.$optinpop.'</span>
        </div>
        '.$optin_ref.'  
      </div>';

      $close_button_class = ($call_from_conversation=='1') ? 'd-none' : '';
      $save_button_class = ($call_from_conversation=='1') ? 'float-right' : '';

      // if($call_from_conversation=='1')
      // {
      //    $middle_column_content =  
      //    '<div class="'.$colmid_class.' colmid" id="middle_column" style="padding:1rem 2rem 0 2rem;">'.
      //     $mid_col_body.'<a class="btn btn-primary float-left mt-4" href="" data-social-media="'.$subscriber_data['social_media'].'" data-subscribe-id="'.$subscriber_data['subscribe_id'].'" data-id="'.$subscriber_data['id'].'" data-page-id="'.$subscriber_data['page_table_id'].'" id="save_changes"><i class="fas fa-save"></i> '.lang("Save Changes").'</a>
      //    </div>';
      // }
      // else
      // {
        $middle_column_content = '
        <div class="'.$colmid_class.' colmid" id="middle_column">
            <div class="card main_card">
              <div class="card-header full_width"  style="display: block;padding-top:25px;">                            
                  <div class="dropleft float-right">
                    <a href="#" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v" style="font-size:25px"></i></a>
                    <div class="dropdown-menu">
                      <div class="dropdown-title">'.lang("Options").'</div>                        
                      <!--'.$start_stop.'-->
                      '.$user_input_start_stop.'
                      <a href="" class="dropdown-item has-icon update_user_details"  social_media="'.$subscriber_data['social_media'].'"  button_id ="'.$subscriber_data['id']."-".$subscriber_data['subscribe_id']."-".$subscriber_data['page_table_id'].'"><i class="fas fa-sync-alt"></i> '.lang("Sync Subscriber Data").'</a>
                      <div class="dropdown-divider"></div>
                      <a href="" class="dropdown-item has-icon red delete_user_details" social_media="'.$subscriber_data['social_media'].'" button_id ="'.$subscriber_data['id']."-".$subscriber_data['page_table_id'].'"><i class="fas fa-trash"></i> '.lang("Delete Subscriber Data").'</a>
                    </div>
                  </div>
                 '.$print_name_link.'
              </div>
              <div class="card-body">
                '.$mid_col_body.'
              </div>

              <div class="card-footer">
                <a class="btn btn-primary float-left '.$save_button_class.'" href="" data-social-media="'.$subscriber_data['social_media'].'" data-subscribe-id="'.$subscriber_data['subscribe_id'].'" data-id="'.$subscriber_data['id'].'" data-page-id="'.$subscriber_data['page_table_id'].'" id="save_changes"><i class="fas fa-save"></i> '.lang("Save Changes").'</a>
                <a class="btn btn-outline-secondary float-right '.$close_button_class.'" data-dismiss="modal"><i class="fas fa-times"></i> '.lang("Close").'</a>
              </div>

            </div>               
        </div>';

      // }
      
      echo $start_row.'
          <div class="'.$collef_class.' collef">
            <div class="card main_card">
              <div class="card-body padding-0">
                '.$subscriber_image_html.' 
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">
                    <i class="fas fa-check-circle subscriber_details blue" data-toggle="tooltip" title="'.lang('Status').'"></i>
                    '.$status.'                    
                  </li> 
                  <li class="list-group-item">
                    <i class="fas fa-robot subscriber_details blue" data-toggle="tooltip" title="'.lang('Bot Status').'"></i>
                    '.$start_stop2.'                    
                  </li>                  
                  <li class="list-group-item"><i class="fas fa-id-card subscriber_details blue" data-toggle="tooltip" title="'.lang('Subscriber ID').'"></i> '.$subscribe_id.'</li>                  
                  ';

                  if(!empty($subscriber_data['gender'])) echo '<li class="list-group-item"><i class="fas fa-mars subscriber_details blue" data-toggle="tooltip" title="'.lang('Gender').'"></i> '.ucfirst($subscriber_data['gender']).'</li>';
                  if(!empty($locale)) echo '<li class="list-group-item"><i class="fas fa-language subscriber_details blue" data-toggle="tooltip" title="'.lang('Language').'"></i> '.$locale.'</li>';
                  if(!empty($timezone)) echo '<li class="list-group-item"><i class="fas fa-globe subscriber_details blue" data-toggle="tooltip" title="'.lang('Timezone').'"></i> '.$timezone.'</li>';

                  $last_update_time = "-";
                  $phone_number_entry_time = "-";
                  $birthdate_entry_time ="-";
                  $last_subscriber_interaction_time = ($subscriber_data['last_subscriber_interaction_time']=='0000-00-00 00:00:00') ? "-" : date('jS M Y g:i a', strtotime($subscriber_data['last_subscriber_interaction_time']));
                
                  if($subscriber_data['email']!='')
                  echo 
                  '<li class="list-group-item"><i class="fas fa-envelope subscriber_details blue" data-toggle="tooltip" title="'.lang('Email').' - '.lang("Last Updated").' : '.$last_update_time.'"></i>'.$subscriber_data['email'].'</li>';

                  if($subscriber_data['phone_number']!='')
                  echo 
                  '<li class="list-group-item"><i class="fas fa-phone subscriber_details blue" data-toggle="tooltip" title="'.lang('Phone').' - '.lang("Last Updated").' : '.$phone_number_entry_time.'"></i>'.$subscriber_data['phone_number'].'</li>';

                  if($subscriber_data['user_location']!='')
                  {
                    $tmp = json_decode($subscriber_data['user_location'],true);
                    if(is_array($tmp)) 
                    {
                      $country_names = $this->get_country_names();
                      $user_country = isset($tmp['country']) ? $tmp['country'] : "";
                      $country_name = isset($country_names[$user_country]) ? ucwords(strtolower($country_names[$user_country])) : $user_country;
                      $tmp["country"] = $country_name;
                      $user_loc = implode(', ', $tmp);
                    }
                    else $user_loc = "";
                    echo 
                    '<li class="list-group-item"><i class="fas fa-map-marker subscriber_details blue" data-toggle="tooltip" title="'.lang('Location').'"></i>'.$user_loc.'</li>';
                  }

                  if($subscriber_data['birthdate']!='0000-00-00')
                  echo 
                  '<li class="list-group-item"><i class="fas fa-birthday-cake subscriber_details blue" data-toggle="tooltip" title="'.lang('Birthday').' - '.lang("Last Updated").' : '.$birthdate_entry_time.'"></i>'.date('jS M Y', strtotime($subscriber_data['birthdate'])).'</li>';

                  if($subscriber_data['last_subscriber_interaction_time']!='0000-00-00 00:00:00')
                  echo 
                  '<li class="list-group-item"><i class="far fa-clock subscriber_details blue" data-toggle="tooltip" title="'.lang("Last Interacted at").'"></i>'.$last_subscriber_interaction_time.'</li>';

              echo    
              '</ul>
                
              </div>
            </div>          
          </div>

          '.$middle_column_content.'
          
          '.$end_row.'

          <script>
          $("#subscriber_teams").select2();

          $("#subscriber_labels").select2({
              placeholder: "'.lang('Choose Label').'",
              allowClear: true
          });

          $("#assign_campaign_id").select2({
               placeholder: "'.lang('Choose Sequence').'",
              allowClear: true
          });
          $(\'[data-toggle="popover"]\').popover(); 
          $(\'[data-toggle="popover"]\').on("click", function(e) {e.preventDefault(); return true;});
          $(\'[data-toggle="tooltip"]\').tooltip({placement: "bottom"});
          </script>';


    }

    public function subscriber_actions_refresh()
    {
      $this->ajax_check();
      $id = $this->input->post("id",true); // subscriber auto id

      $subscriber_info = $this->basic->get_data("messenger_bot_subscriber",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
      if(!isset($subscriber_info[0])) exit();

      $subscriber_data = $subscriber_info[0];
      $page_table_id = $subscriber_data['page_table_id'];
      $contact_group_id = $subscriber_data['contact_group_id'];


      $table_type = 'messenger_bot_broadcast_contact_group';
      $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_table_id,"invisible"=>"0");
      $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name asc');
      $label_info=array();
      foreach($info_type as $value)
      {
          $label_info[$value['id']] = $value['group_name'];
      }
      $selected_labels = explode(',', $contact_group_id);
      $label_dropdown = form_dropdown('subscriber_labels',$label_info,$selected_labels,'class="form-control select2" id="subscriber_labels" multiple style="width:100% !important;"');
      $label_dropdown.='
      <script>
      $("#subscriber_labels").select2({
          placeholder: "'.lang('Choose Label').'",
          allowClear: true
      });
      </script>';

      // broadcast block
      $broadcast_block='';
      if($this->drip_campaigner_exist()) 
      {
        $availablility='<span class="blue">'.lang("Available").'</span>';
        if($subscriber_data['unavailable']=='1' || $subscriber_data['permission']=='0') $availablility='<span class="red">'.lang("Unavailable").'</span>';
        if($subscriber_data['unavailable']=='1') 
        {
          $reason=lang("Error in last send");
          $reason_deatils=$subscriber_data['last_error_message'];
        }
        else if($subscriber_data['permission']=='0')
        {
          $reason =lang("Unsubscribed");
          $reason_deatils = lang("Unsubscribed at")." : ".date("jS M Y H:i:s",strtotime($subscriber_data['unsubscribed_at']));
        }
        $broadcast_block='
          <br>
          <div class="section">
            <div class="section-title mt-0 mb-2 full_width">
              '.lang("Broadcasting Availablity").'  : '.$availablility;

              if($subscriber_data['unavailable']=='1' || $subscriber_data['permission']=='0') {
                $broadcast_block.='
                <div class="alert alert-light alert-has-icon" style="margin-top: 10px;margin-left:45px;">
                  <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                  <div class="alert-body">
                    <div class="alert-title"><small><b>'.lang("Reason")." : </b>".$reason.'</small></div>
                    <small>'.$reason_deatils.'</small>
                  </div>
                </div>';
              }
          $broadcast_block.='
            </div>
          </div>';
      }

      echo json_encode(array("label_dropdown"=>$label_dropdown,"broadcast_block"=>$broadcast_block));

    }



    public function save_subscriber_changes()
    {
      $this->ajax_check();
      if(!check_module_action_access($module_id=66,$actions=2,$response_type='check')){
        echo "0";
        exit();
      }
      $this->is_drip_campaigner_exist=$this->drip_campaigner_exist();
      $id = $this->input->post("id");
      $page_id = $this->input->post("page_id");
      $page_auto_id = trim(str_replace(['-fb','-ig'], '', $page_id));
      $social_media = $this->input->post("media_type");
      $group_id = $this->input->post("group_id");
      $campaign_id = $this->input->post("campaign_id"); // array
      if(!isset($group_id)) $group_id=array();

      $assigned_used_id = $this->input->post("assigned_used_id") ?? null;
      if(!isset($assigned_used_id)) $assigned_used_id = null;

      $agent_name = $this->is_manager==1 ? session()->get("team_username") : session()->get("username");
      $assigned_name = "";
      if(!empty($assigned_used_id)){
        $agent_name_info = $this->addon_exist("team_member") ? $this->basic->get_data("team_members",["where"=>["id"=>$assigned_used_id]],["name"]) : [];
        $assigned_name = $agent_name_info[0]["name"] ?? "Bot";
      }

      $get_groupdata=$this->basic->get_data('messenger_bot_broadcast_contact_group',array('where'=>array('user_id'=>$this->user_id)));

      $label_id=array();
      $unsubscribe_label = "0";
      foreach ($get_groupdata as $key => $value) 
      {
          $label_id[$value['id']]=$value['label_id'];
          if($value['unsubscribe']=='1') $unsubscribe_label = $value['id'];      
      }

      $subscriber_data = $this->basic->get_data("messenger_bot_subscriber",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
      
      foreach ($subscriber_data as $key => $value) // it's a single loop :p
      {

      	 $this->db->transStart();
         $id = $value["id"];
         $subscribe_id = $value["subscribe_id"];

         $this->basic->delete_data("messenger_bot_subscribers_label",array("subscriber_table_id"=>$id));
         foreach($group_id as $group_table_id)
          $this->basic->execute_complex_query("INSERT IGNORE INTO messenger_bot_subscribers_label(contact_group_id,subscriber_table_id) 
              VALUES('$group_table_id','$id');");

         $update_data=array();

         if(in_array($unsubscribe_label, $group_id) && $unsubscribe_label!="0")
         {
            $update_data['permission']='0';
            $update_data['unsubscribed_at']=date("Y-m-d H:i:s");
         }
         else $update_data['permission']='1';

         $this->basic->update_data("messenger_bot_subscriber",array("id"=>$id,"user_id"=>$this->user_id),$update_data);

         // updating team separately as we need check if data updated
         if(!empty($assigned_used_id)){
            $this->basic->update_data("messenger_bot_subscriber",array("id"=>$id,"user_id"=>$this->user_id),["assigned_used_id"=>$assigned_used_id]);
            if($this->db->affectedRows()>=0){
              $message_content="Conversation was assigned to ".$assigned_name;
              $this->system_message_insert_into_conversation($subscribe_id,$page_auto_id,$agent_name,$message_content,$social_media,$this->user_id);
              // Send notification to agent about new conversation assigning.
              $this->agent_assign_notifications($social_media,$assigned_used_id,$subscribe_id);
            }
         }

         $drip_data = array();
         if(!empty($campaign_id) && $this->is_drip_campaigner_exist) {
             // Ensure campaign_id is an array
             if (!is_array($campaign_id)) {
                 if (is_string($campaign_id) && !empty($campaign_id)) {
                     $campaign_id = explode(',', $campaign_id);
                 } elseif (is_numeric($campaign_id)) {
                     $campaign_id = [(int)$campaign_id];
                 } else {
                     $campaign_id = [];
                 }
             }
             // Filter and validate campaign_id array
             $campaign_id = array_values(array_filter(array_map('intval', $campaign_id)));
             
             if (!empty($campaign_id)) {
                 $drip_data = $this->basic->get_data("messenger_bot_drip_campaign",array("where_in"=>array("id"=>$campaign_id),"where"=>array("user_id"=>$this->user_id)));
             }
         }

         $eligible_drip_ids = array();
         foreach ($drip_data as $key => $value2) 
         {
            $eligible_drip_ids[] = $value2['id'];
            $this->assign_drip_messaging_id($value2["drip_type"],"0",$value2['page_id'],$subscribe_id,$value2['id']);// inside home controller
         }

         if($this->is_drip_campaigner_exist )
         {
            $builder = $this->db->table("messenger_bot_drip_campaign_assign");
            $builder->where("subscribe_id",$subscribe_id);
            if(!empty($eligible_drip_ids)) $builder->whereNotIn("messenger_bot_drip_campaign_id",$eligible_drip_ids);
            $builder->delete();
         }

        if($this->db->transStatus() === FALSE)
        {
            $this->db->transRollback();
        } else {
            $this->db->transCommit();
        }
        if ($this->db->transStatus() === FALSE)
        {
            echo "0";
            exit();
        }
      }
      echo "1";

    }

    
    public function create_label_and_assign()
    {
      $this->ajax_check();
      check_module_action_access($module_id=66,$actions=2,$response_type='json1');

      $id = $this->input->post("id",true); // subscriber auto id
      $page_table_id = $this->input->post("page_id",true);
      $social_media = $this->input->post("social_media",true);
      $label_name = $this->input->post("label_name",true);
      $subscriber_info = $this->basic->get_data("messenger_bot_subscriber",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));      

      $subscriber_data = $subscriber_info[0];
      $subscribe_id = $subscriber_data['subscribe_id'];
      
      $is_exists = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$page_table_id,"group_name"=>$label_name,"social_media"=>$social_media)));
      if(isset($is_exists[0]))
      {
           $insert_id = $is_exists[0]['id'];
           $label_id = $is_exists[0]['label_id'];
      }
      else
      {
        $insert_data = array("page_id"=>$page_table_id,"group_name"=>$label_name,"user_id"=>$this->user_id,"social_media"=>$social_media);
        $this->basic->insert_data("messenger_bot_broadcast_contact_group",$insert_data);
        $insert_id = $this->db->insertID();
      }

      echo json_encode(array('status'=>'1','id'=>$insert_id,"text"=>$label_name));
    }
 

    public function start_stop_bot_reply()
    {
        $this->ajax_check();
        check_module_action_access($module_id=66,$actions=2,$response_type='json1');
        $client_subscribe_unsubscribe = array();
        $call_from_conversation = $this->input->post('call_from_conversation');
        if($call_from_conversation=='') $call_from_conversation = '0';
        $post_val=$this->input->post('client_thread_start_stop');
        $client_subscribe_unsubscribe = explode("-",$post_val);
        $id = isset($client_subscribe_unsubscribe[0]) ? $client_subscribe_unsubscribe[0]: 0;
        $current_status =  isset($client_subscribe_unsubscribe[1]) ? $client_subscribe_unsubscribe[1]: 0;
        
        if($current_status=="1") $permission="0";
        else $permission="1";
        
        $where = array
        (
            'id' => $id,
            'user_id' => $this->user_id
        );
        $data = array('status' => $permission);

            
        if($permission=="0")
        {
            $message = lang("Bot reply has been paused successfully.");

            if($call_from_conversation=='0') $response ='<a href="" class="dropdown-item has-icon client_thread_start_stop" button_id="'.$id."-".$permission.'"><i class="far fa-play-circle"></i> '.lang('Resume Bot Reply').'</a>';
            
            else $response = '<span class="client_thread_start_stop_container"><a class="pointer text-primary client_thread_start_stop" call-from-conversation="1"  button_id="'.$id."-".$permission.'">'.lang("Resume Bot Reply").'</a></span>';

            $this->basic->update_data("messenger_bot_subscriber",$where, $data);

        }
        else  
        {  
            $message = lang("Bot reply has been resumed successfully.");

            if($call_from_conversation=='0') $response ='<a href="" class="dropdown-item has-icon client_thread_start_stop" button_id="'.$id."-".$permission.'"><i class="far fa-pause-circle"></i> '.lang('Pause Bot Reply').'</a>';

            else $response = '<span class="client_thread_start_stop_container"><a class="pointer text-primary client_thread_start_stop" call-from-conversation="1"  button_id="'.$id."-".$permission.'">'.lang("Pause Bot Reply").'</a></span>';

            $this->basic->update_data("messenger_bot_subscriber",$where, $data);
        }

        echo json_encode(array("status"=>"1","message"=>$message,"button"=>$response));
    }


    public function reset_user_input_flow()
    {
      $this->ajax_check();
      check_module_action_access($module_id=66,$actions=2,$response_type='json1');
      $value = array();
      $post_val=$this->input->post('post_value');
      $value = explode("-",$post_val);
      $id = isset($value[0]) ? $value[0]: 0; //subscribe auto id
      $client_id = isset($value[1]) ? $value[1]: 0; // subscribe_id
      $page_table_id = isset($value[2]) ? $value[2]: 0; // page auto id

      $facebook_rx_fb_page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $page_table_id, 'user_id' => $this->user_id)),'page_id');
      $fb_page_id = isset($facebook_rx_fb_page_info[0]['page_id']) ? $facebook_rx_fb_page_info[0]['page_id'] : 0;
      $data = ['input_flow_campaign_id'=>0,'last_question_sent_id'=>0];
      $where = ['page_id'=>$fb_page_id,'subscriber_id'=>$client_id];
      $this->basic->update_data('messenger_bot_subscriber_extra_info',$where,$data);

      $response['status'] = '1';
      $response['message'] = lang("User Input Flow for this subscriber has been reset successfully.");
      echo json_encode($response);
    }

    
    //DEPRECATED FUNCTION FOR QUICK BROADCAST//
    public function sync_subscriber_data()
    {
        $this->ajax_check();
        check_module_action_access($module_id=66,$actions=2,$response_type='json1');
        $value = array();
        $post_val=$this->input->post('post_value');
        $social_media=$this->input->post('social_media');
        $value = explode("-",$post_val);
        $id = isset($value[0]) ? $value[0]: 0; //subscribe auto id
        $client_id = isset($value[1]) ? $value[1]: 0; // subscribe_id
        $page_id = isset($value[2]) ? $value[2]: 0; // page auto id

        $response = array();    
        $facebook_rx_fb_page_info = $this->basic->get_data('facebook_rx_fb_page_info', array('where' => array('id' => $page_id, 'user_id' => $this->user_id)));
        $facebook_rx_fb_page_info = $facebook_rx_fb_page_info[0];

        $update_data = $this->subscriber_info($facebook_rx_fb_page_info['page_access_token'],$client_id,$social_media);

        if(!isset($update_data['error'])) 
        {

            $first_name = isset($update_data['first_name']) ? $update_data['first_name'] : "";
            $last_name = isset($update_data['last_name']) ? $update_data['last_name'] : "";
            $profile_pic = isset($update_data['profile_pic']) ? $update_data['profile_pic'] : "";
            $gender = isset($update_data['gender']) ? $update_data['gender'] : "";
            $locale = isset($update_data['locale']) ? $update_data['locale'] : "";
            $timezone = isset($update_data['timezone']) ? $update_data['timezone'] : "";
            $full_name = isset($update_data['name']) ? $update_data['name'] : "";
            
            if ($first_name != "") {

                $data = array
                (
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'profile_pic' => $profile_pic,
                    'is_updated_name' => '1',
                    'is_bot_subscriber' => '1',
                    'is_image_download' => '0',
                    'gender'=>$gender,
                    'locale'=>$locale,
                    'timezone'=>$timezone,
                    'last_name_update_time' => date('Y-m-d H:i:s')
                );
                if($full_name!="") $data["full_name"] = $full_name;
            }
            else  $data = array('is_updated_name' => '1','is_bot_subscriber' => '0','last_name_update_time' => date('Y-m-d H:i:s'));

            // getting previous labels if any
          
            // if($social_media=='fb'){
            //   $xlabels=$this->fb_rx_login->retrieve_level_of_psid($client_id,$facebook_rx_fb_page_info['page_access_token']);
            //   $existing_label_str="";
            //   if(isset($xlabels['data']))
            //   {
            //     $get_groupdata=$this->basic->get_data('messenger_bot_broadcast_contact_group',array('where'=>array('page_id'=>$page_id)));
            //     $label_id=array();
            //     foreach ($get_groupdata as $key => $value) 
            //     {
            //         $label_id[$value['label_id']]=$value['id'];
            //     }
            //     $existing_label_array=array();
            //     foreach ($xlabels['data'] as $key => $value) 
            //     {
            //       if(isset($label_id[$value['id']])) $existing_label_array[]=$label_id[$value['id']];
            //     }
            //     $existing_label_str = implode(',', $existing_label_array);
            //   }
            //   if($existing_label_str!="") $data["contact_group_id"]=$existing_label_str;
            // }

            $this->basic->update_data('messenger_bot_subscriber', array('id' => $id,"user_id"=>$this->user_id), $data);

            $response['status'] = '1';
            $response['message'] = lang("Subscriber data has been synced successfully.");
        }
        else 
        {
            $data = array('last_name_update_time' => date('Y-m-d H:i:s'),'is_updated_name' => '1'); 
            $this->basic->update_data('messenger_bot_subscriber', array('id' => $id), $data);
            $response['status'] = '0';
            $response['message'] = lang($update_data['error']['message']);
        }

        echo json_encode($response);
    }
    

    public function delete_subsriber()
    {
        $this->ajax_check();
        if(!check_module_action_access($module_id=66,$actions=3,$response_type='check')){
          echo "0";
          exit();
        }

        $value = array();
        $post_val=$this->input->post('post_value');
        $social_media=$this->input->post('social_media');
        $value = explode("-",$post_val);
        $id = isset($value[0]) ? $value[0]: 0; //subscribe auto id
        $page_id = isset($value[1]) ? $value[1]: 0; //page auto id

        $this->basic->delete_data('messenger_bot_subscriber',array('id'=>$id,"user_id"=>$this->user_id));

        $sql = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE social_media='$social_media' AND page_table_id='$page_id' AND permission='1' AND subscriber_type!='system' AND user_id=".$this->user_id;
        $count_data = $this->db->query($sql)->getRowArray();

        $sql2 = "SELECT count(id) as permission_count FROM `messenger_bot_subscriber` WHERE social_media='$social_media' AND page_table_id='$page_id' AND permission='0' AND subscriber_type!='system' AND user_id=".$this->user_id;
        $count_data2 = $this->db->query($sql2)->getRowArray();

        // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $update_subscription = $social_media=='fb' ? array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed,"current_lead_count"=>$current_lead_count) : array("insta_current_subscribed_lead_count"=>$subscribed,"insta_current_unsubscribed_lead_count"=>$unsubscribed,"insta_current_lead_count"=>$current_lead_count);

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$page_id),$update_subscription);

        echo "1";
    }


}