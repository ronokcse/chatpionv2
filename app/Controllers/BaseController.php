<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;
    
    /**
     * URI compatibility object for CI3 -> CI4 migration
     * Provides $this->uri->segment() method
     */
    protected $uri;
    
    /**
     * Load compatibility object for CI3 -> CI4 migration
     * Provides $this->load->library() method
     */
    protected $load;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
        
        // Load compatibility object for CI3 -> CI4 migration
        $controller = $this;
        $this->load = new class($controller) {
            private $controller;
            
            public function __construct($controller) {
                $this->controller = $controller;
            }
            
            public function library($libraryName) {
                // Convert library name to property name (e.g., "Fb_rx_login" -> "fb_rx_login")
                $propertyName = strtolower($libraryName);
                
                // Check if already loaded
                if (isset($this->controller->$propertyName)) {
                    return $this->controller->$propertyName;
                }
                
                // Try loading file directly first (for CI3 libraries without namespace)
                $libraryFile = APPPATH . 'Libraries/' . $libraryName . '.php';
                if (file_exists($libraryFile)) {
                    require_once $libraryFile;
                    if (class_exists($libraryName)) {
                        $instance = new $libraryName();
                        $this->controller->$propertyName = $instance;
                        return $instance;
                    }
                }
                
                // Try different class name formats with namespace
                $classNames = [
                    "App\\Libraries\\" . $libraryName,  // Exact match (e.g., Sheet, Fb_rx_login)
                    "App\\Libraries\\" . ucfirst($libraryName),  // First letter uppercase
                    "App\\Libraries\\" . str_replace('_', '', ucwords($libraryName, '_')),  // CamelCase
                ];
                
                foreach ($classNames as $libraryClass) {
                    if (class_exists($libraryClass)) {
                        $instance = new $libraryClass();
                        // Store as property on controller (CI3 style: $this->sheet, $this->fb_rx_login)
                        $this->controller->$propertyName = $instance;
                        return $instance;
                    }
                }
                
                // Return null if not found
                return null;
            }
            
            public function model($modelName) {
                $modelClass = "App\\Models\\" . ucfirst($modelName);
                if (class_exists($modelClass)) {
                    $instance = new $modelClass();
                    $propertyName = strtolower($modelName);
                    $this->controller->$propertyName = $instance;
                    return $instance;
                }
                return null;
            }
            
            public function helper($helperName) {
                helper($helperName);
                return true;
            }
            
            public function view($view, $data = [], $return = false) {
                return view($view, $data);
            }
            
            public function database() {
                return \Config\Database::connect();
            }
        };
        
        // URI compatibility for CI3 -> CI4 migration
        $this->uri = new class($request) {
            private $request;
            private $segments;
            
            public function __construct($request) {
                $this->request = $request;
                // Get URI segments safely
                try {
                    $uri = $request->getUri();
                    $this->segments = $uri->getSegments();
                } catch (\Exception $e) {
                    $this->segments = [];
                }
            }
            
            /**
             * CI3 compatibility: Get URI segment
             * Returns null if segment doesn't exist (like CI3)
             * @param int $n Segment number (1-based)
             * @return string|null
             */
            public function segment($n) {
                // CI3 uses 1-based indexing, CI4 uses 0-based
                $index = $n - 1;
                if ($index < 0 || !isset($this->segments[$index])) {
                    return null;
                }
                return $this->segments[$index];
            }
            
            /**
             * CI4 method: Get segment (0-based)
             * Returns null if segment doesn't exist (safe version)
             * @param int $n Segment number (0-based)
             * @return string|null
             */
            public function getSegment($n) {
                if ($n < 0 || !isset($this->segments[$n])) {
                    return null;
                }
                return $this->segments[$n];
            }
        };
        
        // Form validation compatibility for CI3 -> CI4 migration
        $this->form_validation = new class() {
            private $validation;
            private $rules = [];
            
            public function __construct() {
                $this->validation = \Config\Services::validation();
            }
            
            public function set_rules($field, $label = '', $rules = '') {
                // Store rules for later use
                $this->rules[$field] = [
                    'label' => $label,
                    'rules' => $rules
                ];
                return $this;
            }
            
            public function run($group = '') {
                // Get request data
                $request = \Config\Services::request();
                $data = $request->getPost();
                
                // Ensure all fields in rules have a value (even if empty string) to prevent null trim errors
                foreach ($this->rules as $field => $rule) {
                    if (!isset($data[$field])) {
                        $data[$field] = '';
                    }
                }
                
                // Set rules
                $this->validation->setRules($this->rules);
                
                // Run validation
                return $this->validation->run($data, $group);
            }
            
            // Pass through other methods to validation service
            public function __call($method, $args) {
                if (method_exists($this->validation, $method)) {
                    return call_user_func_array([$this->validation, $method], $args);
                }
                return null;
            }
        };
        
        // Input compatibility for CI3 -> CI4 migration
        $this->input = new class($request) {
            private $request;
            
            public function __construct($request) {
                $this->request = $request;
            }
            
            public function post($key = null, $xss_clean = false) {
                $value = $this->request->getPost($key);
                if ($xss_clean && $value !== null) {
                    $value = esc($value);
                }
                return $value;
            }
            
            public function get($key = null, $xss_clean = false) {
                $value = $this->request->getGet($key);
                if ($xss_clean && $value !== null) {
                    $value = esc($value);
                }
                return $value;
            }
            
            public function cookie($key = null) {
                return $this->request->getCookie($key);
            }
            
            public function is_ajax_request() {
                return $this->request->isAJAX();
            }
        };
        
        // Database compatibility for CI3 -> CI4 migration
        $this->db = new class() {
            private $db;
            private $builder;
            
            public function __construct() {
                $this->db = \Config\Database::connect();
            }
            
            public function insertID() {
                return $this->db->insertID();
            }
            
            public function table($table) {
                $this->builder = $this->db->table($table);
                return $this;
            }
            
            public function select($select = '*', $escape = null) {
                if ($this->builder) {
                    $this->builder->select($select, $escape);
                }
                return $this;
            }
            
            public function distinct($val = true) {
                if ($this->builder) {
                    $this->builder->distinct($val);
                }
                return $this;
            }
            
            public function where($key, $value = null, $escape = null) {
                if ($this->builder) {
                    $this->builder->where($key, $value, $escape);
                }
                return $this;
            }
            
            public function orderBy($orderBy, $direction = '', $escape = null) {
                if ($this->builder) {
                    $this->builder->orderBy($orderBy, $direction, $escape);
                }
                return $this;
            }
            
            public function get($limit = null, $offset = 0) {
                if ($this->builder) {
                    $result = $this->builder->get($limit, $offset);
                    $this->builder = null; // Reset builder
                    return $result;
                }
                return null;
            }
            
            // Pass through other methods to database connection or builder
            public function __call($method, $args) {
                if ($this->builder && method_exists($this->builder, $method)) {
                    $result = call_user_func_array([$this->builder, $method], $args);
                    // If method returns builder, return $this for chaining
                    if ($result === $this->builder) {
                        return $this;
                    }
                    return $result;
                }
                if (method_exists($this->db, $method)) {
                    return call_user_func_array([$this->db, $method], $args);
                }
                return null;
            }
            
            // Allow property access
            public function __get($property) {
                if ($this->builder && property_exists($this->builder, $property)) {
                    return $this->builder->$property ?? null;
                }
                return $this->db->$property ?? null;
            }
        };
    }
}
