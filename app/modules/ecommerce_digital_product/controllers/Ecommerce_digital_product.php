<?php
/*
Addon Name: E-commerce Digital Product
Unique Name: ecommerce_digital_product
Modules:
{
   "316":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"E-commerce Digital Product"
   }
}
Project ID: 60
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: 
*/

namespace App\Modules\Ecommerce_digital_product\Controllers;

use App\Controllers\Home;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Ecommerce_digital_product extends Home
{
	public $addon_data = array();

    /**
     * CI4 fix: use initController instead of __construct
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Must be logged in
        if (session()->get('logged_in') != 1) {
            redirect()->to(base_url('home/login_page'))->send();
            exit();
        }

        // Check module access (module id 316 for this addon)
        if (function_exists('check_module_access')) {
            check_module_access($module_id = 316);
        }

        // getting addon information in array and storing to public variable
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_path = APPPATH . "modules/" . strtolower($controller_name) . "/controllers/" . ucfirst($controller_name) . ".php"; // path of addon controller
        $this->addon_data = $this->get_addon_data($addon_path); 

        $this->member_validity();

        // user_id of logged in user, we may need it
        $this->user_id = session()->get('user_id');
    }


    public function index()
  	{
          $this->activate(); 
  	}


    public function activate()
    {
        
        $this->ajax_check();
   
        // Get controller short name instead of using CI3 router
        $addon_controller_name = (new \ReflectionClass($this))->getShortName(); // e.g. Ecommerce_digital_product
        $purchase_code = $this->request->getPost('purchase_code');
       
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
   
        $addon_controller_name = (new \ReflectionClass($this))->getShortName(); // e.g. Ecommerce_digital_product
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->basic->update_data("ecommerce_store",array("store_type"=>"digital","status"=>'1'),array("status"=>'0'));

        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    {        
        $this->ajax_check();
 
        $addon_controller_name = (new \ReflectionClass($this))->getShortName(); // e.g. Ecommerce_digital_product

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql = array(); 
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}