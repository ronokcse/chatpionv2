<?php
/*
Addon Name: AI Integration
Unique Name: ai_reply
Modules:
{
   "340":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"Bot - AI Reply"
   }
}
Project ID: 67
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.1
Description: 
*/

namespace App\Modules\Ai_reply\Controllers;

use App\Controllers\Home;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Ai_reply extends Home
{
	public $addon_data=array();
    /**
     * CI4 fix: Use initController instead of __construct and CI4-style helpers
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_path = APPPATH . "modules/" . strtolower($controller_name) . "/controllers/" . ucfirst($controller_name) . ".php"; // path of addon controller
        $this->addon_data = $this->get_addon_data($addon_path);

        $this->member_validity();

        // user_id of logged in user, we may need it
        $this->user_id = $this->session->userdata('user_id');
    }


    public function index()
  	{
          $this->activate(); 
  	}


    public function activate()
    {
        $this->ajax_check();
   
        // CI4 fix: derive controller name via reflection instead of CI3 router
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_controller_name = ucfirst($controller_name); // e.g., Ai_reply
        $purchase_code = $this->input->post('purchase_code', true);
       
        $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
                
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array();  

        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array(); 

        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code); 
    }


    public function deactivate()
    {        
        $this->ajax_check();
   
        // CI4 fix: derive controller name via reflection instead of CI3 router
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_controller_name = ucfirst($controller_name);
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    {        
        $this->ajax_check();
 
        // CI4 fix: derive controller name via reflection instead of CI3 router
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_controller_name = ucfirst($controller_name);

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql = array(); 
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}