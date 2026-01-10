<?php
/*
Addon Name: Google Contacts Integration
Unique Name: google_contacts
Modules:
{
   "353":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Google Contacts - Google Accounts"
   }
}
Project ID: 72
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: 
*/


require_once("application/controllers/Home.php"); // loading home controller

class Google_contacts extends Home
{
    public $addon_data=array();
    public function __construct()
    {
        // if($this->basic->is_exist("add_ons",array("project_id"=>72)))
        parent::__construct();
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path); 
        $this->member_validity();
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
        if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');
        if ($this->session->userdata('user_type') != 'Admin' && !in_array(353, $this->module_access)) redirect('home/login_page', 'location');
    }


    public function index()
    {
        $this->db->where('user_id', $this->user_id);
        $account_details = $this->db->get('google_contacts_account')->result(); // Fetch results as an array of objects
        $count_account = count($account_details); 
        // Prepare data for the view
        $data['count_account'] = $count_account; // Set the count in the data array
        $data['sheet_account_details'] = $account_details; // Set account details in the data array
        $account_list[''] = $this->lang->line("Select Account");
        foreach ($account_details as $account) {
            $account_list[$account->id] = $account->email; // or $account->name for the name
        }

        $data['account_list'] = $account_list;
        $data['body']='google_contacts_account';
        $data['page_title']=$this->lang->line("Google Accounts ");
        $this->_viewcontroller($data);  
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
        $sql = array(
            0 => "
             CREATE TABLE IF NOT EXISTS `google_contacts` (
            `id` int NOT NULL AUTO_INCREMENT,
            `google_contacts_account_id` int NOT NULL,
            `user_id` int NOT NULL,
            `resource_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `photo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `phone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` datetime DEFAULT NULL,
            `updated_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `resource_name` (`resource_name`),
            KEY `user_id` (`user_id`),
            KEY `google_contacts_account_id` (`google_contacts_account_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            1 => "
             CREATE TABLE IF NOT EXISTS `google_contacts_account` (
            `id` int NOT NULL AUTO_INCREMENT,
            `user_id` int NOT NULL,
            `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `image` text COLLATE utf8mb4_unicode_ci,
            `access_token` text COLLATE utf8mb4_unicode_ci,
            `refresh_token` text COLLATE utf8mb4_unicode_ci,
            `response_source` text COLLATE utf8mb4_unicode_ci,
            `created_at` datetime DEFAULT NULL,
            `update_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `google_contacts_account_user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ",
            2 => "ALTER TABLE `google_contacts`
                    ADD CONSTRAINT `google_contacts_account` FOREIGN KEY (`google_contacts_account_id`) REFERENCES `google_contacts_account` (`id`) ON DELETE CASCADE;",
            3 => "ALTER TABLE `google_contacts_account`
                    ADD CONSTRAINT `google_contacts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;",
        
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
              0=> "DROP TABLE IF EXISTS `google_contacts`;",
              1=> "DROP TABLE IF EXISTS `google_contacts_account`;"
          );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

    public function connect_action(){
        check_module_action_access($module_id=353,$actions=[1,2],$response_type='403');

        $status = $this->_check_usage($module_id = 353, $request = 1);
        if ($status == "3") {
            $text = $this->lang->line('Sorry, your limit is exceeded for this module.');
            exit();
            $this->session->set_userdata('limit_cross', $text);
            redirect('google_sheet/index', 'location');
            exit();
        }

        $login_config=$this->basic->get_data("login_config",array("where"=>array("status"=>"1")));
        if(isset($login_config[0]))
        {           
            $google_client_id=$login_config[0]["google_client_id"];
            $google_client_secret=$login_config[0]["google_client_secret"];
        }
        else{
            show_error('Google client Id And Client Secret is requeired');
        }
        $this->load->library('sheet');
        $this->sheet->google_client_id = $google_client_id;
        $this->sheet->google_client_secret = $google_client_secret;
        $this->sheet->google_auth_url_login_for_contacts();
       
    }
    public function google_get_access_token()
    {
        if ($this->input->get('code')) {
            $auth_code = $this->input->get('code');
            $login_config=$this->basic->get_data("login_config",array("where"=>array("status"=>"1")));
            if(isset($login_config[0]))
            {           
                $google_client_id=$login_config[0]["google_client_id"];
                $google_client_secret=$login_config[0]["google_client_secret"];
            }
            else{
                show_error('Google client Id And Client Secret is requeired');
            }
            $this->load->library('sheet');
            $this->sheet->google_client_id = $google_client_id;
            $this->sheet->google_client_secret = $google_client_secret;
            $response = $this->sheet->get_access_token_information_for_contacts($auth_code);
            if (!empty($response)) {
                $email_exists = $this->db->select('id')
                    ->where(['email' => $response['email'], 'user_id' => $this->user_id])
                    ->get('google_contacts_account')
                    ->row();
                if (!$email_exists) {
                    // If the email does not exist, insert the new data
                    $response['user_id'] = $this->user_id;
                    $response['created_at'] = date("Y-m-d H:i:s");
                    $this->db->insert('google_contacts_account', $response);
                    $insert_id = $this->db->insert_id();
                } else {
                    $insert_id = $email_exists->id ?? '';
                    $this->db->where('id', $insert_id)->update('google_contacts_account', $response);
                }
        
                $requestForSync = ['id_from_connect_account' => $insert_id];
                $error_message = $this->google_contact_sync($requestForSync);
                if (!empty($error_message)) {
                    return $this->custom_error_page('', '', $error_message);
                }
        
                $this->session->set_flashdata('google_contact_auth_connect', '1');
                $this->_insert_usage_log($module_id = 353, $request = 1);
                return redirect('google_contacts');
            }
        }
    }

    public function google_contact_sync($requestForSync = [])
    {
        check_module_action_access($module_id=353,$actions=2,$response_type='json3');

        $id = $requestForSync['id_from_connect_account'] ?? $this->input->post('id');
        $google_contacts_account_data = $this->db->select(['access_token', 'refresh_token', 'email'])
            ->where(['id' => $id, 'user_id' => $this->user_id])
            ->get('google_contacts_account')
            ->row();
        $access_token = $google_contacts_account_data->access_token ?? '';
        $refresh_token = $google_contacts_account_data->refresh_token ?? '';
        $user_auth_email = $google_contacts_account_data->email ?? '';
        $login_config=$this->basic->get_data("login_config",array("where"=>array("status"=>"1")));
        
        if(isset($login_config[0]))
        {           
            $google_client_id=$login_config[0]["google_client_id"];
            $google_client_secret=$login_config[0]["google_client_secret"];
        }
    
        if (empty($google_client_id) || empty($google_client_secret)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => true, 'message' => 'Google client ID and client secret are required.']));
        }
    
        // Set Google client details
        $this->load->library('sheet');
        $this->sheet->google_client_id = $google_client_id;
        $this->sheet->google_client_secret = $google_client_secret;
        $this->sheet->access_token = $access_token;
        $this->sheet->refresh_token = $refresh_token;
        $this->sheet->user_auth_email = $user_auth_email;
        $this->sheet->user_id = $this->user_id;
    
        $google_contact_list = $this->sheet->get_google_contact_list($id);
        if (isset($google_contact_list['error']) && $google_contact_list['error'] === true) {
            $errorMessage = $google_contact_list['message'];
    
            if ($this->input->post('id_from_connect_account')) {
                return $errorMessage;
            } else {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['error' => true, 'message' => $errorMessage]));
            }
        } else {
            $contacts = $google_contact_list['contacts'] ?? [];
    
            if (!empty($contacts)) {
                $insertValues = [];
                foreach ($contacts as $contact) {
                    $google_contacts_account_id = $this->db->escape($contact['google_contacts_account_id']);
                    $user_id = $this->db->escape($contact['user_id']);
                    $resource_name = $this->db->escape($contact['resourceName']);
                    $name = $this->db->escape($contact['name']);
                    $photo = $this->db->escape($contact['photo']);
                    $email = $this->db->escape($contact['email']);
                    $phone = $this->db->escape($contact['phone']);
                    $created_at = $this->db->escape(date("Y-m-d H:i:s"));
                    $updated_at = $this->db->escape(date("Y-m-d H:i:s"));
            
                    $insertValues[] = "($google_contacts_account_id, $user_id,$resource_name,$name,$photo,$email,$phone, $created_at, $updated_at)";
                }
            
                // Combine values into a single string
                $valuesString = implode(', ', $insertValues);
            
                // Construct the SQL query for insert or update
                $sql = "INSERT INTO google_contacts (google_contacts_account_id,user_id,resource_name,name, photo, email,phone, created_at, updated_at) 
                        VALUES $valuesString 
                        ON DUPLICATE KEY UPDATE 
                            google_contacts_account_id=VALUES(google_contacts_account_id), 
                            user_id=VALUES(user_id), 
                            resource_name=VALUES(resource_name), 
                            name=VALUES(name), 
                            photo=VALUES(photo),
                            email=VALUES(email), 
                            phone=VALUES(phone), 
                            updated_at=VALUES(updated_at)";
                $this->basic->execute_complex_query($sql);
                
            }

    
            if (isset($requestForSync['id_from_connect_account'])) {
                return "";
            } else {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['error' => false, 'message' => 'Data has been synced successfully.']));
            }
        }
    }

    public function unlink_google_contacts_account()
    {
        check_module_action_access($module_id=353,$actions=3,$response_type='json3');

        $id = $this->input->post('id'); // Get the 'id' from POST request

        // Execute the delete query
        $deleted = $this->db->where('user_id', $this->user_id)
                            ->where('id', $id)
                            ->delete('google_contacts_account');
        // Check if deletion was successful and send response
        if ($deleted) {
            $this->_delete_usage_log($module_id = 353, $request = 1);
            return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['error' => false, 'message' => 'Data has been deleted successfully.']));
        } else {
            return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['error' => true, 'message' => 'No data found or deletion failed.']));
        }
    }

    public function account_list_data()
    {
        $search_value = $this->input->post('search_value');
        $contacts_account_id = $this->input->post('contacts_account_id');
        $display_columns = array("#", 'id', 'name', 'photo','phone','email', 'created_at', 'updated_at');
        $search_columns = array('name','phone');
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'id';
        $order = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
        $order_by = $sort . " " . $order;
    
        $where_custom = "user_id = " . $this->user_id;
        
        if ($search_value != '') {
            $temp = array();
            foreach ($search_columns as $column) {
                $temp[] = $column . " LIKE '%" . $this->db->escape_like_str($search_value) . "%'";
            }
            $where_custom .= " AND (" . implode(" OR ", $temp) . ")";
        }
        
        // Add the sheet_account_id condition if it is provided
        if ($contacts_account_id != '') {
            $where_custom .= " AND google_contacts_account_id = " . $this->db->escape($contacts_account_id);
        }

        $table = "google_contacts";
        $this->db->where($where_custom);
        $info = $this->basic->get_data($table, $where = "", $select = "", $join = "", $limit, $start, $order_by);
        
        $this->db->where($where_custom);
        $total_rows_array = $this->basic->count_row($table, $where = "", $count = $table . ".id", $join = "");
        $total_result = $total_rows_array[0]['total_rows'];
    
        $data = array(
            "draw" => intval($this->input->post('draw')) + 1,
            "recordsTotal" => $total_result,
            "recordsFiltered" => $total_result,
            "data" => $info,
        );
        echo json_encode($data);
    }

    public function google_contact_save()
    {
        check_module_action_access($module_id=353,$actions=[1,2],$response_type='json3');

        $sheet_account_id = $this->input->post('sheet_account_id') ?? '';
        $contact_name = $this->input->post('contact_name') ?? '';
        $contact_phone_number = $this->input->post('contact_phone_number') ?? '';
        $contact_email = $this->input->post('contact_email') ?? '';

        $google_contacts_account_data = $this->db->select(['access_token', 'refresh_token', 'email'])
            ->where(['id' => $sheet_account_id, 'user_id' => $this->user_id])
            ->get('google_contacts_account')
            ->row();
        $access_token = $google_contacts_account_data->access_token ?? '';
        $refresh_token = $google_contacts_account_data->refresh_token ?? '';
        $user_auth_email = $google_contacts_account_data->email ?? '';
        $login_config=$this->basic->get_data("login_config",array("where"=>array("status"=>"1")));
        
        if(isset($login_config[0]))
        {           
            $google_client_id=$login_config[0]["google_client_id"];
            $google_client_secret=$login_config[0]["google_client_secret"];
        }
    
        if (empty($google_client_id) || empty($google_client_secret)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => true, 'message' => 'Google client ID and client secret are required.']));
        }
        // Load Google Sheets library
        $this->load->library('sheet');
        $this->sheet->google_client_id = $google_client_id;
        $this->sheet->google_client_secret = $google_client_secret;
        $this->sheet->access_token = $access_token;
        $this->sheet->refresh_token = $refresh_token;
        $contact_details = $this->sheet->create_google_contact( $sheet_account_id,$contact_name,$contact_phone_number,$contact_email);
        if(isset($contact_details['success']) && $contact_details['success'] == true && !empty($contact_details['resourceName'])){
            $insert_data = [
                'google_contacts_account_id' => $sheet_account_id,
                'user_id' => $this->user_id,
                'resource_name' => str_replace('people/', '', $contact_details['resourceName']),
                'name' => $contact_name,
                'phone' => $contact_phone_number,
                'email' => $contact_email,
                'created_at' => date("Y-m-d H:i:s"),
            ];

            // Insert into database
            $this->db->insert('google_contacts', $insert_data);

            return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'error' => false,
                            'message' => $this->lang->line('Data has been saved successfully.')
                        ]));
        } 
        else{
            return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'error' => true,
                        'message' => $contact_details['message']
                    ]));
        }

       
    }
    public function delete_contact()
    {
        check_module_action_access($module_id=353,$actions=3,$response_type='json3');
        // Get request data
        $id = $this->input->post('id');
        $soft_delete = $this->input->post('soft_delete');
        $contact_account_id = $this->input->post('contact_account_id');

        // Handle soft delete (delete from both CodeIgniter and Google Sheets)
        if ($soft_delete == 2) {
            // Retrieve Google account data
            $google_contacts_account_data = $this->db->select(['access_token', 'refresh_token', 'email'])
                ->where(['id' => $contact_account_id, 'user_id' => $this->user_id])
                ->get('google_contacts_account')
                ->row();
            $contact_data = $this->db->select('resource_name')
                ->where(['id' => $id, 'user_id' => $this->user_id])
                ->get('google_contacts')
                ->row();

            // Check if sheet data exists
            if (empty($contact_data) || !$google_contacts_account_data) {
                echo json_encode(['error' => true, 'message' => $this->lang->line('Google Contact not found')]);
                return;
            }
            $resource_name = $contact_data->resource_name ?? '';
            $access_token = $google_contacts_account_data->access_token;
            $refresh_token = $google_contacts_account_data->refresh_token;

            // Retrieve Google client ID and secret from config
            $login_config=$this->basic->get_data("login_config",array("where"=>array("status"=>"1")));
            if(isset($login_config[0]))
            {           
                $google_client_id=$login_config[0]["google_client_id"];
                $google_client_secret=$login_config[0]["google_client_secret"];
            }
            else{
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'error' => true,
                    'message' => $this->lang->line('Google client Id And Client Secret is required')
                ]));
            }
            

            // Set Google API credentials and delete the sheet
            $this->load->library('sheet');
            $this->sheet->google_client_id = $google_client_id;
            $this->sheet->google_client_secret = $google_client_secret;
            $this->sheet->access_token = $access_token;
            $this->sheet->refresh_token = $refresh_token;
            // Attempt to dthe Google Sheet
            $this->sheet->delete_google_contact($resource_name, $contact_account_id);
        }
        // Delete the sheet from local database
        $this->db->where('id', $id)->delete('google_contacts');
        echo json_encode(['error' => false, 'message' => $this->lang->line('Contact deleted successfully.')]);
    }

}
