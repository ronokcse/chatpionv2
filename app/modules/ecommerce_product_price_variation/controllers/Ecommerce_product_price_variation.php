<?php
/*
Addon Name: E-commerce Product Price Variation
Unique Name: ecommerce_product_price_variation
Modules:
{
   "281":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"E-commerce Product Price Variation"
   }
}
Project ID: 45
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: https://xeroneit.net
Version: 1.0
Description: 
*/

namespace App\Modules\Ecommerce_product_price_variation\Controllers;

use App\Controllers\Home;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Ecommerce_product_price_variation extends Home
{
	public $addon_data=array();

    /**
     * CI4 fix: Use initController instead of __construct
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
   
        // CI4: derive controller name via reflection instead of router
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_controller_name = ucfirst($controller_name); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code = $this->input->post('purchase_code', true);
       
        $this->addon_credential_check($purchase_code, strtolower($addon_controller_name)); // returns json status,message if error
                
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array();  

        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array(
            1=> "
            CREATE TABLE IF NOT EXISTS `ecommerce_attribute_product_price` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `product_id` int(11) NOT NULL,
              `attribute_id` int(11) NOT NULL,
              `attribute_option_name` varchar(255) NOT NULL,
              `price_indicator` varchar(5) NOT NULL,
              `amount` float NOT NULL,
              `stock` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        ); 

        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code); 
    }


    public function deactivate()
    {        
        $this->ajax_check();
   
        // CI4: derive controller name via reflection instead of router
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_controller_name = ucfirst($controller_name); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    {        
        $this->ajax_check();
 
        // CI4: derive controller name via reflection instead of router
        $controller_name = (new \ReflectionClass($this))->getShortName();
        $addon_controller_name = ucfirst($controller_name); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql = array(1=>"DROP TABLE IF EXISTS `ecommerce_attribute_product_price`;"); 
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }


}