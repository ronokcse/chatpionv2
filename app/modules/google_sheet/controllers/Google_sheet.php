<?php
/*
Addon Name: Google Sheet 
Unique Name: google_sheet
Modules:
{
   "351":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Google Sheet - Google Accounts"
   }
}
Project ID: 70
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.1
Description: 
*/


require_once("application/controllers/Home.php"); // loading home controller

class Google_sheet extends Home
{
	public $addon_data=array();
    public function __construct()
    {
        parent::__construct();
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path); 
        $this->member_validity();
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
        if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');
        if ($this->session->userdata('user_type') != 'Admin' && !in_array(351, $this->module_access)) redirect('home/login_page', 'location');
    }


    public function index()
  	{
        $this->db->where('user_id', $this->user_id);
        $account_details = $this->db->get('google_accounts')->result(); // Fetch results as an array of objects
        $count_account = count($account_details); 
        // Prepare data for the view
        $data['count_account'] = $count_account; // Set the count in the data array
        $data['sheet_account_details'] = $account_details; // Set account details in the data array
        $account_list[''] = $this->lang->line("Select Account");
        foreach ($account_details as $account) {
            $account_list[$account->id] = $account->email; // or $account->name for the name
        }

        $data['account_list'] = $account_list;
        $data['body']='google_sheet_account';
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
            1 => "
            CREATE TABLE IF NOT EXISTS `google_accounts` (
                `id` int NOT NULL AUTO_INCREMENT,
                `user_id` int NOT NULL,
                `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `image` text COLLATE utf8mb4_unicode_ci,
                `access_token` text COLLATE utf8mb4_unicode_ci,
                `refresh_token` text COLLATE utf8mb4_unicode_ci,
                `response_source` text COLLATE utf8mb4_unicode_ci,
                `created_at` datetime DEFAULT NULL,
                `update_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `google_accounts_user_id` (`user_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            2 => "
            CREATE TABLE IF NOT EXISTS `google_sheets` (
                `id` int NOT NULL AUTO_INCREMENT,
                `google_account_id` int DEFAULT NULL,
                `user_id` int DEFAULT NULL,
                `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `sheet_id` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `sheet_names` text COLLATE utf8mb4_unicode_ci,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `sheet_id` (`sheet_id`),
                KEY `user_id` (`user_id`),
                KEY `google_sheets_google_account_id` (`google_account_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            3 => "ALTER TABLE `google_accounts`
                    ADD CONSTRAINT `google_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;",
            4 => "ALTER TABLE `google_sheets`
                    ADD CONSTRAINT `google_sheets_google_account_id` FOREIGN KEY (`google_account_id`) REFERENCES `google_accounts` (`id`) ON DELETE CASCADE;",
            5 => "ALTER TABLE user_input_flow_campaign ADD google_sheet_ids MEDIUMTEXT NULL DEFAULT NULL AFTER unique_id;",
            6 => "ALTER TABLE messenger_bot_postback ADD google_sheet_ids MEDIUMTEXT NULL DEFAULT NULL AFTER visual_flow_campaign_id;",
            7 => "ALTER TABLE messenger_bot ADD google_sheet_ids MEDIUMTEXT NULL DEFAULT NULL AFTER visual_flow_campaign_id;",
            8 => "ALTER TABLE `google_app_config` ADD INDEX(`user_id`);"
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
        $sql = array(); 
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }
    public function connect_action(){
        $status = $this->_check_usage($module_id = 351, $request = 1);
        if ($status == "3") {
            $text = $this->lang->line('sorry, your limit is exceeded for this module.');
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
        $this->sheet->google_auth_url_login();
       
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
            $response = $this->sheet->get_access_token_information($auth_code);
            
            if (!empty($response)) {
                $email_exists = $this->db->select('id')
                    ->where(['email' => $response['email'], 'user_id' => $this->user_id])
                    ->get('google_accounts')
                    ->row();
                if (!$email_exists) {
                    // If the email does not exist, insert the new data
                    $response['user_id'] = $this->user_id;
                    $response['created_at'] = date("Y-m-d H:i:s");
                    $this->db->insert('google_accounts', $response);
                    $insert_id = $this->db->insert_id();
                } else {
                    $insert_id = $email_exists->id ?? '';
                    $this->db->where('id', $insert_id)->update('google_accounts', $response);
                }
        
                $requestForSync = ['id_from_connect_account' => $insert_id];
                $error_message = $this->google_sheet_sync($requestForSync);
                if (!empty($error_message)) {
                    return $this->custom_error_page('', '', $error_message);
                }
        
                $this->session->set_flashdata('google_sheet_auth_connect', '1');
                $this->_insert_usage_log($module_id = 351, $request = 1);
                return redirect('google_sheet');
            }
        }
    }


    public function google_sheet_sync($requestForSync = [])
    {
        $id = $requestForSync['id_from_connect_account'] ?? $this->input->post('id');
        $google_sheet_account_data = $this->db->select(['access_token', 'refresh_token', 'email'])
            ->where(['id' => $id, 'user_id' => $this->user_id])
            ->get('google_accounts')
            ->row();
        $access_token = $google_sheet_account_data->access_token ?? '';
        $refresh_token = $google_sheet_account_data->refresh_token ?? '';
        $user_auth_email = $google_sheet_account_data->email ?? '';
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
    
        $google_sheet_list = $this->sheet->get_google_sheet_list($id);
        if (isset($google_sheet_list['error']) && $google_sheet_list['error'] === true) {
            $errorMessage = $google_sheet_list['message'];
    
            if ($this->input->post('id_from_connect_account')) {
                return $errorMessage;
            } else {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['error' => true, 'message' => $errorMessage]));
            }
        } else {
            $sheets = $google_sheet_list['sheets'] ?? [];
    
            if (!empty($sheets)) {
                $insertValues = [];
                foreach ($sheets as $sheet) {
                    $sheet_id = $this->db->escape($sheet['sheet_id']);
                    $name = $this->db->escape($sheet['name']);
                    $google_account_id = $this->db->escape($sheet['google_account_id']);
                    $user_id = $this->db->escape($sheet['user_id']);
                    $sheet_names = $this->db->escape($sheet['sheet_names']);
                    $created_at = $this->db->escape(date("Y-m-d H:i:s"));
                    $updated_at = $this->db->escape(date("Y-m-d H:i:s"));
            
                    $insertValues[] = "($sheet_id, $name, $google_account_id, $user_id, $sheet_names, $created_at, $updated_at)";
                }
            
                // Combine values into a single string
                $valuesString = implode(', ', $insertValues);
            
                // Construct the SQL query for insert or update
                $sql = "INSERT INTO google_sheets (sheet_id, name, google_account_id, user_id, sheet_names, created_at, updated_at) 
                        VALUES $valuesString 
                        ON DUPLICATE KEY UPDATE 
                            name=VALUES(name), 
                            google_account_id=VALUES(google_account_id), 
                            user_id=VALUES(user_id), 
                            sheet_names=VALUES(sheet_names), 
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

    public function unlink_google_sheet_account()
    {
        $id = $this->input->post('id'); // Get the 'id' from POST request

        // Execute the delete query
        $deleted = $this->db->where('user_id', $this->user_id)
                            ->where('id', $id)
                            ->delete('google_accounts');
        // Check if deletion was successful and send response
        if ($deleted) {
            $this->_delete_usage_log($module_id = 351, $request = 1);
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
        $sheet_account_id = $this->input->post('sheet_account_id');
        $display_columns = array("#", 'id', 'name', 'sheet_id', 'created_at', 'updated_at');
        $search_columns = array('name');
        
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
        if ($sheet_account_id != '') {
            $where_custom .= " AND google_account_id = " . $this->db->escape($sheet_account_id);
        }

        $table = "google_sheets";
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

    public function google_sheet_save()
    {
        // if(!has_module_action_access($this->module_id_google_sheet,[1,2],$this->team_access,$this->is_manager)) {
        //     return response()->json(['error' => true,'message' => __('You are not allowed to perform this action.')]);
        // }

        $sheet_account_id = $this->input->post('sheet_account_id') ?? '';
        $sheet_title = $this->input->post('sheet_title') ?? '';
        $headers_name = $this->input->post('headers_name') ?? '';

        // Validate headers
        if (!empty($headers_name)) {
            foreach ($headers_name as $head) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $head)) {
                    return $this->output
                                ->set_content_type('application/json')
                                ->set_output(json_encode([
                                    'error' => true,
                                    'message' => sprintf($this->lang->line("Google Sheet header names only allow alpha-numeric characters and underscores. Invalid header name found (%s)."), $head)
                                ]));
                }
            }
        }

        // Retrieve Google account data
        $google_sheet_account_data = $this->db->select('access_token, refresh_token, email')
            ->where(['id' => $sheet_account_id, 'user_id' => $this->user_id])
            ->get('google_accounts')
            ->row();
        $access_token = $google_sheet_account_data->access_token ?? '';
        $refresh_token = $google_sheet_account_data->refresh_token ?? '';
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
        // Load Google Sheets library
        $this->load->library('sheet');
        $this->sheet->google_client_id = $google_client_id;
        $this->sheet->google_client_secret = $google_client_secret;
        $this->sheet->access_token = $access_token;
        $this->sheet->refresh_token = $refresh_token;

        // Create Google Sheet
        $google_sheet_details = $this->sheet->create_google_sheet($sheet_title, $sheet_account_id, $headers_name);
        $google_sheet_id = $google_sheet_details['spreadsheetId'] ?? '';
        $sheet_tab_name = $google_sheet_details['sheetTabName'] ?? '';

        if ($google_sheet_id && $sheet_tab_name) {
            // Prepare data for insertion
            $insert_data = [
                'google_account_id' => $sheet_account_id,
                'user_id' => $this->user_id,
                'name' => $sheet_title,
                'sheet_id' => $google_sheet_id,
                'sheet_names' => $sheet_tab_name,
                'created_at' => date("Y-m-d H:i:s"),
            ];

            // Insert into database
            $this->db->insert('google_sheets', $insert_data);

            return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'error' => false,
                            'message' => $this->lang->line('Data has been saved successfully.')
                        ]));
        } else {
            return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'error' => true,
                            'message' => $this->lang->line('Something Went Wrong.')
                        ]));
        }
    }
    public function delete_sheet()
    {
         // Assuming Google library is loaded in CodeIgniter
        // Check module action access
        // if (!$this->has_module_action_access($this->module_id_google_sheet, 3, $this->team_access, $this->is_manager)) {
        //     echo json_encode(['error' => true, 'message' => $this->lang->line('You are not allowed to perform this action.')]);
        //     return;
        // }

        // Get request data
        $id = $this->input->post('id');
        $soft_delete = $this->input->post('soft_delete');
        $sheet_account_id = $this->input->post('sheet_account_id');

        // Handle soft delete (delete from both CodeIgniter and Google Sheets)
        if ($soft_delete == 2) {
            // Retrieve Google account data
            $google_sheet_account_data = $this->db->select(['access_token', 'refresh_token', 'email'])
                ->where(['id' => $sheet_account_id, 'user_id' => $this->user_id])
                ->get('google_accounts')
                ->row();

            $sheet_data = $this->db->select('sheet_id')
                ->where(['id' => $id, 'user_id' => $this->user_id])
                ->get('google_sheets')
                ->row();

            // Check if sheet data exists
            if (!$sheet_data || !$google_sheet_account_data) {
                echo json_encode(['error' => true, 'message' => $this->lang->line('Google Sheet data not found')]);
                return;
            }

            $sheet_id = $sheet_data->sheet_id;
            $access_token = $google_sheet_account_data->access_token;
            $refresh_token = $google_sheet_account_data->refresh_token;

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
            $this->sheet->delete_google_sheet($sheet_id, $sheet_account_id);
        }

        // Delete the sheet from local database
        $this->db->where('id', $id)->delete('google_sheets');
        echo json_encode(['success' => true, 'message' => $this->lang->line('Sheet deleted successfully.')]);
    }

    public function existing_google_sheet_save (){
        // if(!has_module_action_access($this->module_id_google_sheet,[1,2],$this->team_access,$this->is_manager)) {
        //     return response()->json(['error' => true,'message' => __('You are not allowed to perform this action.')]);
        // }

        $sheet_account_id = $this->input->post('sheet_account_id') ?? '';
        $sheet_existing_id = $this->input->post('sheet_existing_id') ?? '';

        // check already exist or not

        $google_sheet_exist = $this->db->where([
            'google_account_id' => $sheet_account_id,
            'user_id' => $this->user_id,
            'sheet_id' => $sheet_existing_id,
        ])
        ->get('google_sheets')
        ->row();
        
        
        if(!empty($google_sheet_exist)){
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'error' => true,
                'message' => $this->lang->line('This Email is already been Exists')
            ]));
        }

        // Fetch Google Sheet account data
        $google_sheet_account_data = $this->db->where([
                'google_accounts.id' => $sheet_account_id,
                'google_accounts.user_id' => $this->user_id
            ])
            ->select(['access_token', 'refresh_token', 'google_accounts.email'])
            ->get('google_accounts')
            ->row();

        $access_token = $google_sheet_account_data->access_token ?? '';
        $refresh_token = $google_sheet_account_data->refresh_token ?? '';
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


        // Set Google client ID and secret in your library
        $this->load->library('sheet');
        $this->sheet->google_client_id = $google_client_id;
        $this->sheet->google_client_secret = $google_client_secret;
        $this->sheet->access_token = $access_token;
        $this->sheet->refresh_token = $refresh_token;

        // Get Google sheet details
        $google_sheet_details = $this->sheet->get_google_sheet_details($sheet_existing_id, $sheet_account_id);
        // Check for errors in Google sheet details
        if (isset($google_sheet_details['error'])) {
            $google_sheet_errors = json_decode($google_sheet_details['error']);
            $error_message = $google_sheet_errors->error->errors[0]->message;
            if(isset($error_message)){
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'error' => true,
                    'message' =>$error_message
                ]));
            }
            else{
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'error' => true,
                    'message' => $this->lang->line('There was an error accessing the Google Sheet')
                ]));
            }
           
        }

        $google_sheet_id = $sheet_existing_id;
        $sheet_title = $google_sheet_details['spreadsheetTitle'] ?? '';
        $sheet_tab_name = $google_sheet_details['sheetNames'] ?? '';
        $sheet_tab_name = implode(',', $sheet_tab_name);

        // Insert data if IDs are valid
        if ($google_sheet_id != '' && $sheet_tab_name != '') {
            $insert_data = [
                'google_account_id' => $sheet_account_id,
                'user_id' => $this->user_id,
                'name' => $sheet_title,
                'sheet_id' => $google_sheet_id,
                'sheet_names' => $sheet_tab_name,
                'created_at' => date("Y-m-d H:i:s"),
            ];
            $this->db->insert('google_sheets', $insert_data);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'error' => false,
                'message' => $this->lang->line('Sheet has been added successfully.')
            ]));
        }

        // Handle errors
        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'error' => true,
            'message' => $this->lang->line('Something Went Wrong.')
        ]));
    }
    

  

}
