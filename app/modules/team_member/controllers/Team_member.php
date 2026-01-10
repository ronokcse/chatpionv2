<?php
/*
Addon Name: Team Member Manager
Unique Name: team_member
Modules:
{
   "350":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Team Member Manager"
   }
}
Project ID: 65
Addon URI: https://chatpion.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: Manage team member who can perform task on-behalf of you
*/

require_once("application/controllers/Home.php"); // loading home controller

class Team_member extends Home
{
    public $addon_data=array();
    
    public function __construct()
    {
        parent::__construct();

        $function_name=$this->uri->segment(2);
      
        if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');

        // team user can not access
        if($this->is_manager==1)
        redirect('home/login_page', 'location');

        $this->member_validity();        

        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $addondata=$this->get_addon_data($addon_path); 
        $this->addon_data=$addondata; 

        if(isset($addondata['module_id']) && is_numeric($addondata['module_id']) && $addondata['module_id']>0)
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array($addondata['module_id'],$this->module_access))
            {
                  redirect('home/login_page', 'location');
                  exit();
            }
        }  
    }

    public function role_list(){        
        $data['body']='role_list';
        $data['page_title']=$this->lang->line("Team Roles");
        $this->_viewcontroller($data);  
    }

    public function role_list_data()
    {
        $search_value = $_POST['search']['value'];
        $display_columns = array("#",'id','role_name','page_ids');
        $search_columns = array( 'role_name');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'desc';
        $order_by=$sort." ".$order;

        $where_custom="user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
            
        $table="team_roles";        
        $this->db->where($where_custom);    
        $info=$this->basic->get_data($table,$where="",$select='',$join='',$limit,$start,$order_by,$group_by='');
        $this->db->where($where_custom);    
        $total_rows_array=$this->basic->count_row($table,$where="",$count=$table.".id",$join='',$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start);

        echo json_encode($data);
    }

    public function add_role(){        
      $data['body']='add_team_role';
      $table ='modules';
      $modules=$this->basic->get_data('modules',$where='',$select='',$join='',$limit='',$start='',$order_by='module_name asc',$group_by='',$num_rows=0);
      $data['modules'] = (object)$modules;
      $data['page_title']=$this->lang->line("Add Team Role");

      $page_data = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('user_id'=>$this->user_id,'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=> $this->session->userdata('facebook_rx_fb_user_info'))),array("id","page_name"));
      $page_list = [];
      foreach($page_data as $page){
        $page_list[$page['id']] = $page['page_name'];
      }
      $data['page_list'] = $page_list;

      $this->_viewcontroller($data);  
    }

    public function add_role_action(){
      if($this->is_demo == '1')
      {
          echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
          exit();
      }

      if($_SERVER['REQUEST_METHOD'] === 'GET') 
      redirect('home/access_forbidden','location');

      if($_POST)
      {     
          $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Team Role Name").'</b>', 'trim|required');  
          if(empty($_POST['modules'])){

            $this->form_validation->set_rules('modules', '<b>'.$this->lang->line("Modules").'</b>', 'trim|required');      
          }    
              
          if ($this->form_validation->run() == FALSE)
          {
              $this->add_role(); 
          }
          else
          {               
              $this->csrf_token_check();

              $name=strip_tags($this->input->post('name',true));
              $modules=$_POST['modules'];
              $team_access=$_POST['team_access'];              
              $page_ids=$_POST['page_ids'] ?? [];

              $output = [];

              foreach ($modules as $module) {
                  if (isset($team_access[$module])) {
                      $output[$module] = $team_access[$module];
                  }
                  else{
                    $output[$module] = ["1","2","3","4"];
                  }
              }
              $module_access =  json_encode($output);
              $data=array
              (

                  'user_id'=>$this->user_id,
                  'role_name'=>$name,
                  'page_ids'=>implode(',',$page_ids),
                  'module_access'=>$module_access,
              );
              
              if($this->basic->insert_data('team_roles',$data)) $this->session->set_flashdata('success_message',1);   
              else $this->session->set_flashdata('error_message',1);     
              
              redirect('team_member/role_list','location');                 
              
          }
      }   
    }


    public function edit_role($id=0){
      if($id==0) exit();
      $team_roles_data = $this->basic->get_data('team_roles',array('where'=>array('user_id'=>$this->user_id,'id'=>$id)),array("role_name",'module_access','page_ids'));
      $name = $team_roles_data[0]['role_name'] ?? '';
      $module_access = $team_roles_data[0]['module_access'] ?? '';
      $table ='modules';
      $modules=$this->basic->get_data('modules',$where='',$select='',$join='',$limit='',$start='',$order_by='module_name asc',$group_by='',$num_rows=0);
      $data['modules'] = (object)$modules;
      $data['body']='edit_team_role';
      $data['page_title']=$this->lang->line("Update Team Role");
      $data['name']= $name;
      $data['module_access']= $module_access;
      $data['role_id']= $id;
      $data['page_ids']= explode(',', $team_roles_data[0]['page_ids'] ?? '');
      $page_data = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('user_id'=>$this->user_id,'facebook_rx_fb_page_info.facebook_rx_fb_user_info_id'=> $this->session->userdata('facebook_rx_fb_user_info'))),array("id","page_name"));
      $page_list = [];
      foreach($page_data as $page){
        $page_list[$page['id']] = $page['page_name'];
      }
      $data['page_list'] = $page_list;
      $this->_viewcontroller($data);  
    }

    public function update_role_action(){
      if($this->is_demo == '1')
      {
          echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
          exit();
      }

      if($_SERVER['REQUEST_METHOD'] === 'GET') 
      redirect('home/access_forbidden','location');

      if($_POST)
      {     
          $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Team Role Name").'</b>', 'trim|required');  
          if(empty($_POST['modules'])){

            $this->form_validation->set_rules('modules', '<b>'.$this->lang->line("Modules").'</b>', 'trim|required'); 
          }

          $role_id=$_POST['role_id'];    
              
          if ($this->form_validation->run() == FALSE)
          {
              $this->edit_role($role_id); 
          }
          else
          {               
              $this->csrf_token_check();

              $name=strip_tags($this->input->post('name',true));
              $modules=$_POST['modules'];
              $team_access=$_POST['team_access'];
              $role_id=$_POST['role_id'];
              $page_ids=$_POST['page_ids'] ?? [];
              if(empty($page_ids)) $page_ids = [];

              $output = [];

              foreach ($modules as $module) {
                  if (isset($team_access[$module])) {
                      $output[$module] = $team_access[$module];
                  }
                  else{
                    $output[$module] = ["1","2","3","4"];
                  }
              }
              $module_access =  json_encode($output);
              $update_data=array
              (

                  'user_id'=>$this->user_id,
                  'role_name'=>$name,
                  'page_ids'=>implode(',',$page_ids),
                  'module_access'=>$module_access,
              );
              if($this->basic->update_data('team_roles',array("id"=>$role_id),$update_data)) $this->session->set_flashdata('success_message',1);   
              else $this->session->set_flashdata('error_message',1);     
              
              redirect('team_member/role_list','location');                 
              
          }
      }   
    }

    public function delete_role($id)
    {
        $this->ajax_check();
        if($this->is_demo == '1')
        {
            echo json_encode(array("status"=>"0","message"=>$this->lang->line("This feature is disabled in this demo."))); 
            exit();
        }        
        if($id==0) exit();
        if($this->basic->delete_data("team_roles",array("id"=>$id,'user_id'=>$this->user_id)))
        echo json_encode(array("status"=>"1","message"=>$this->lang->line("Team role  has been deleted successfully"))); 
        else echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));        
    }



    public function member_list()
    {
        $data['body']='team_member_list';
        $data['page_title']=$this->lang->line("Team Members");
        $this->_viewcontroller($data);  
    }

    public function member_list_data()
    {           
        $this->ajax_check();
        $search_value = $_POST['search']['value'];
        $display_columns = array("#","CHECKBOX",'user_id','avatar','name', 'email','role_name', 'status', 'actions', 'add_date','last_login_at',);
        $search_columns = array('name', 'email');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 4;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'name';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
        $order_by=$sort." ".$order;

        $table="team_members";
        $where_custom=$table.".user_id = ".$this->user_id;

        if ($search_value != '') 
        {
            foreach ($search_columns as $key => $value) 
            $temp[] = $value." LIKE "."'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .=" AND (".$imp.") ";
        }
            
        $join = array('team_roles'=>"team_roles.id=team_members.team_role_id,left");
        $select= array("team_members.*","team_roles.role_name");
        $this->db->where($where_custom);  
        $info=$this->basic->get_data($table,$where="",$select,$join,$limit,$start,$order_by,$group_by='');
        $this->db->where($where_custom);  
        $total_rows_array=$this->basic->count_row($table,$where="",$count=$table.".id",$join,$group_by='');
        $total_result=$total_rows_array[0]['total_rows'];
        $i=0;
        $base_url=base_url();
        foreach ($info as $key => $value) 
        {
            $status = $info[$i]["status"];
            if($status=='1') $info[$i]["status"] = "<i title ='".$this->lang->line('Active')."'class='status-icon fas fa-toggle-on text-primary'></i>";
            else $info[$i]["status"] = "<i title ='".$this->lang->line('Inactive')."'class='status-icon fas fa-toggle-off gray'></i>";

            $last_login_at = $info[$i]["last_login_at"];
            if($last_login_at=='0000-00-00 00:00:00' || empty($last_login_at)) $info[$i]["last_login_at"] = $this->lang->line("Never");
            else $info[$i]["last_login_at"] = date("jS M y H:i",strtotime($info[$i]["last_login_at"]));

            $info[$i]["add_date"] = date("jS M y",strtotime($info[$i]["add_date"]));
  
            $user_name = $info[$i]["name"];
            $user_id = $info[$i]["id"];
            $str="";   
            
            $str=$str."<a class='btn btn-circle btn-outline-warning' data-toggle='tooltip' title='".$this->lang->line('Edit')."' href='".$base_url.'team_member/edit_team_member/'.$info[$i]["id"]."'>".'<i class="fas fa-edit"></i>'."</a>";
            $str=$str."&nbsp;<a class='btn btn-circle btn-outline-dark change_password' href='' data-toggle='tooltip' title='".$this->lang->line('Change Password')."' data-id='".$user_id."' data-user='".htmlspecialchars($user_name)."'>".'<i class="fas fa-key"></i>'."</a>";
            $str=$str."&nbsp;<a href='".$base_url.'team_member/member_delete_action/'.$info[$i]["id"]."' class='are_you_sure_datatable btn btn-circle btn-outline-danger' csrf_token='".$this->session->userdata('csrf_token_session')."' data-toggle='tooltip' title='".$this->lang->line('Delete')."'>".'<i class="fa fa-trash"></i>'."</a>";

            if($this->session->userdata('license_type') == 'double')
            $info[$i]["actions"] = "<div style='min-width:208px'>".$str."</div>";
            else $info[$i]["actions"] = "<div style='min-width:161px'>".$str."</div>";
            $info[$i]["actions"] .= "<script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
            $logo=base_url("assets/img/avatar/avatar-1.png");
           
            $info[$i]["avatar"] = "<img src='".$logo."' width='40px' height='40px' class='rounded-circle'>";

            $tie="-noicon blue";

            $info[$i]['name'] = "<span data-toggle='tooltip' title=''><i class='fas fa-user".$tie." text-warning'></i> ".$info[$i]['name']." </span><script> $('[data-toggle=\"tooltip\"]').tooltip();</script>";
                
            if($this->is_demo=='1')  $info[$i]["email"] ="******@*****.***";
            $i++;
        }

        $data['draw'] = (int)$_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns ,$start,$primary_key="user_id");

        echo json_encode($data);
    }

    public function add_team_member()
    {       
        $data['body']='add_team_member';     
        $data['page_title']=$this->lang->line('Add Team Member');     
        $team_roles=$this->basic->get_data('team_roles',$where=['where'=>['user_id'=>$this->user_id]],$select='',$join='',$limit='',$start='',$order_by='role_name asc');
        $data['team_roles'] = format_data_dropdown($team_roles,"id","role_name",false);
        $this->_viewcontroller($data);
    }


    public function add_team_member_action() 
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        $status=$this->_check_usage($module_id=350,$request=1);
        if($status!="1")
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>".$this->lang->line("You cannot create more team members.")."</h2>"; 
            exit();
        }


        if($_SERVER['REQUEST_METHOD'] === 'GET') 
        redirect('home/access_forbidden','location');

        if($_POST)
        {
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Full Name").'</b>', 'trim');      
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("Email").'</b>', 'trim|required|valid_email|is_unique[team_members.email]');      
            $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("Mobile").'</b>', 'trim');      
            $this->form_validation->set_rules('password', '<b>'.$this->lang->line("Password").'</b>', 'trim|required');      
            $this->form_validation->set_rules('confirm_password', '<b>'.$this->lang->line("Confirm Password").'</b>', 'trim|required|matches[password]');             
            $this->form_validation->set_rules('status', '<b>'.$this->lang->line("Status").'</b>', 'trim');
            $this->form_validation->set_rules('role_id', '<b>'.$this->lang->line("Package").'</b>', 'trim|required');
                
            if ($this->form_validation->run() == FALSE)
            {
                $this->add_team_member(); 
            }
            else
            {               
                $this->csrf_token_check();
                $name=strip_tags($this->input->post('name',true));
                $email=strip_tags($this->input->post('email',true));
                $mobile=strip_tags($this->input->post('mobile',true));
                $password=md5($this->input->post('password',true));
                $status=$this->input->post('status',true);
                $role_id=$this->input->post('role_id',true);
                if($status=='') $status='0';
                                                       
                $data=array
                (
                    'user_id'=>$this->user_id,
                    'name'=>$name,
                    'email'=>$email,
                    'mobile'=>$mobile,
                    'password'=>$password,
                    'team_role_id'=>$role_id,
                    'status'=>$status,
                    'add_date' => date("Y-m-d H:i:s")
                );
                if($this->basic->insert_data('team_members',$data)) {
                  $this->session->set_flashdata('success_message',1);
                  $this->_insert_usage_log($module_id=350,$request=1);
                }
                else $this->session->set_flashdata('error_message',1);     
                
                redirect('team_member/member_list','location');                 
                
            }
        }   
    }


    public function edit_team_member($id=0)
    {      
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        $data['body']='edit_team_member';     
        $data['page_title']=$this->lang->line('Edit Member');     
        $team_roles=$this->basic->get_data('team_roles',$where=['where'=>['user_id'=>$this->user_id]],$select='',$join='',$limit='',$start='',$order_by='role_name asc');
        $xdata=$this->basic->get_data('team_members',array("where"=>array("id"=>$id)));
        if(!isset($xdata[0])) exit();
        $data['team_roles'] = format_data_dropdown($team_roles,"id","role_name",false);
        $data['xdata'] = $xdata[0];
        $this->_viewcontroller($data);
    }


    public function edit_team_member_action() 
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] === 'GET') 
        redirect('home/access_forbidden','location');

        if($_POST)
        {
            $id = $this->input->post('id');
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("Full Name").'</b>', 'trim');
            $unique_email = "team_members.email.".$id; 
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("Email").'</b>', "trim|required|valid_email|is_unique[$unique_email]");      
            $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("Mobile").'</b>', 'trim');              
            $this->form_validation->set_rules('status', '<b>'.$this->lang->line("Status").'</b>', 'trim');

      
            $this->form_validation->set_rules('role_id', '<b>'.$this->lang->line("Role Name").'</b>', 'trim|required');
                
            if ($this->form_validation->run() == FALSE)
            {
                $this->edit_team_member($id); 
            }
            
            else
            {      
            
                $this->csrf_token_check();

                $name=strip_tags($this->input->post('name',true));
                $email=strip_tags($this->input->post('email',true));
                $mobile=strip_tags($this->input->post('mobile',true));   
                $status=$this->input->post('status',true);
                $role_id=$this->input->post('role_id',true);
                if($status=='') $status='0';
                                                       
                $data=array
                (
                    'user_id'=>$this->user_id,
                    'name'=>$name,
                    'email'=>$email,
                    'mobile'=>$mobile,
                    'team_role_id'=>$role_id,
                    'status'=>$status
                );
                
                if($this->basic->update_data('team_members',array("id"=>$id),$data)) $this->session->set_flashdata('success_message',1);   
                else $this->session->set_flashdata('error_message',1);     
                
                redirect('team_member/member_list','location');             
                
            }
        }   
    }


    public function change_team_member_password_action()
    {
        if($this->is_demo == '1')
        {
            
                $response['status'] = 0;
                $response['message'] = "This feature is disabled in this demo.";
                echo json_encode($response);
                exit();
            
        }

        $this->ajax_check();

        $id = $this->input->post('id');
        if ($_POST) 
        {
            $this->form_validation->set_rules('password', '<b>'. $this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('confirm_password', '<b>'. $this->lang->line("confirm password").'</b>', 'trim|required|matches[password]');
        }
        if ($this->form_validation->run() == false) 
        {
           echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));
           exit();
        } 
        else 
        {
            $this->csrf_token_check();

            $new_password = $this->input->post('password',true);
            $new_confirm_password = $this->input->post('confirm_password',true);

            $table_change_password = 'team_members';
            $where_change_passwor = array('id' => $id);
            $data = array('password' => md5($new_password));
            $this->basic->update_data($table_change_password, $where_change_passwor, $data);

            
            $where['where'] = array('id' => $id);
            $mail_info = $this->basic->get_data('team_members', $where);
            
            $name = $mail_info[0]['name'];
            $to = $mail_info[0]['email'];
            $password = $new_password;

            $mask = $this->config->item('product_name');
            $from = $this->config->item('institute_email');
            $url = site_url();


            $email_template_info = $this->basic->get_data('email_template_management',array('where'=>array('template_type'=>'change_password')),array('subject','message'));
            if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') 
            {
                $subject = $email_template_info[0]['subject'];
                $message = str_replace(array("#USERNAME#","#APP_URL#","#APP_NAME#","#NEW_PASSWORD#"),array($name,$url,$mask,$password),$email_template_info[0]['message']);
            } 
            else 
            {
                $subject = 'Change Password Notification';
                $message = "Dear {$name},<br/> Your <a href='".$url."'>{$mask}</a> password has been changed. Your new password is: {$password}.<br/><br/> Thank you.";
            }
           
            @$this->_mail_sender($from, $to, $subject, $message, $mask);

            echo json_encode(array("status"=>"1","message"=>$this->lang->line("Password has been changed successfully")));
        }
    }

    public function member_delete_action($id)
    {
        $this->ajax_check();
        if($this->is_demo == '1')
        {
            echo json_encode(array("status"=>"0","message"=>$this->lang->line("This feature is disabled in this demo."))); 
            exit();
        }
        
        if($id==0) exit();
     
        if($this->basic->delete_data("team_members",array("id"=>$id,'user_id'=>$this->user_id))){
          $this->_delete_usage_log($module_id=350,$request=1); 
          echo json_encode(array("status"=>"1","message"=>$this->lang->line("Team role  has been deleted successfully"))); 
        }
        else echo json_encode(array("status"=>"0","message"=>$this->lang->line("Something went wrong, please try again")));
        
    }



    public function activate()
    {
        $this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
                  
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array(); 
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array
        (
            1=>"
            CREATE TABLE IF NOT EXISTS `team_members` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `name` varchar(100) NOT NULL,
              `email` varchar(99) NOT NULL,
              `mobile` varchar(100) NOT NULL,
              `password` varchar(99) NOT NULL,
              `status` enum('1','0') NOT NULL,
              `add_date` datetime DEFAULT NULL,
              `last_login_at` datetime DEFAULT NULL,
              `team_role_id` int(11) NOT NULL,
              `deleted` enum('0','1') NOT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              KEY `team_role_id` (`team_role_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            2=>"
            CREATE TABLE IF NOT EXISTS `team_roles` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `role_name` varchar(100) NOT NULL,
              `page_ids` text NOT NULL,
              `module_access` text,
              PRIMARY KEY (`id`),
              KEY `user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            3=>"
            ALTER TABLE `team_members`
              ADD CONSTRAINT `deleteMemberOnDeleteRole` FOREIGN KEY (`team_role_id`) REFERENCES `team_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `deleteMemberOnDeleteUser` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;",

            4=>"
            ALTER TABLE `team_roles`
              ADD CONSTRAINT `deleteRolesOnDeleteUser` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;",

            5=>"INSERT INTO `menu` (`id`, `name`, `icon`, `color`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Team Manager', 'fas fa-user-friends', '#FFC0CB','', 99, '325', '1', '0', '0', (SELECT id FROM add_ons WHERE project_id='65'), '0', 'Team', '0', '0');",

            6=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Team Roles', 'team_member/role_list', '1', 'fas fa-user-lock', '', (SELECT id FROM menu WHERE module_access='325'), '0', '0', '0', '0', '0', 0);",

            7=>"INSERT INTO `menu_child_1` (`id`, `name`, `url`, `serial`, `icon`, `module_access`, `parent_id`, `have_child`, `only_admin`, `only_member`, `is_external`, `is_menu_manager`, `custom_page_id`) VALUES (NULL, 'Team Members', 'team_member/member_list', '1', 'fas fa-user-ninja', '', (SELECT id FROM menu WHERE module_access='325'), '0', '0', '0', '0', '0', 0);",
            
            8=>"ALTER TABLE `announcement` ADD CONSTRAINT `delete_team` FOREIGN KEY (`team_member_id`) REFERENCES `team_members`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION;"
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
          0=> "ALTER TABLE announcement DROP FOREIGN KEY `delete_team`;",
          1=> "DROP TABLE IF EXISTS `team_members`;",
          2=> "DROP TABLE IF EXISTS `team_roles`;"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}