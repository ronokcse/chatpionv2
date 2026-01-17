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
     * CI3-style language library compatibility
     * Provides $this->lang->line() and $this->lang->load()
     */
    protected $lang;
    
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
     * Pagination compatibility object for CI3 -> CI4 migration
     * Provides $this->pagination->initialize() and $this->pagination->create_links()
     *
     * Note: actual property is declared on `Home` to avoid dynamic properties.
     */

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
        
        // CI3-style language compatibility wrapper
        $this->lang = new class {
            public function line(string $key)
            {
                $result = lang($key);
                return ($result === $key) ? $key : $result;
            }

            public function load(string $file, string $lang = null)
            {
                $langService = \Config\Services::language();
                if ($lang) {
                    $langService->setLocale($lang);
                }
                // CI3 load() usually just prepares language lines;
                // CI4's lang() will read them based on locale.
                return true;
            }
        };

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

                // CI3 -> CI4 built-in library mapping
                // CI3: $this->load->library('email') -> CI4 Email service
                if ($propertyName === 'email') {
                    // CI3 Email library compatibility wrapper over CI4 Email service
                    $emailService = \Config\Services::email();
                    $instance = new class($emailService) {
                        private $email;

                        public function __construct($email)
                        {
                            $this->email = $email;
                        }

                        // CI3: initialize($config)
                        public function initialize(array $config = [])
                        {
                            // CI4 Email supports initialize()
                            if (!empty($config)) {
                                $this->email->initialize($config);
                            }
                            return $this;
                        }

                        // CI3: from($from, $name = '')
                        public function from(string $from, string $name = '')
                        {
                            $this->email->setFrom($from, $name);
                            return $this;
                        }

                        // CI3: to($to)
                        public function to($to)
                        {
                            $this->email->setTo($to);
                            return $this;
                        }

                        // CI3: bcc($bcc)
                        public function bcc($bcc)
                        {
                            $this->email->setBCC($bcc);
                            return $this;
                        }

                        // CI3: subject($subject)
                        public function subject(string $subject)
                        {
                            $this->email->setSubject($subject);
                            return $this;
                        }

                        // CI3: message($message)
                        public function message(string $message)
                        {
                            $this->email->setMessage($message);
                            return $this;
                        }

                        // CI3: attach($file)
                        public function attach(string $file, string $disposition = '', string $newname = null, string $mime = '')
                        {
                            // CI4 Email attach supports (file, disposition, newName, mime)
                            $this->email->attach($file, $disposition, $newname, $mime);
                            return $this;
                        }

                        // CI3: send()
                        public function send(bool $autoClear = true)
                        {
                            return $this->email->send($autoClear);
                        }

                        // CI3: print_debugger()
                        public function print_debugger($include = ['headers', 'subject', 'body'])
                        {
                            return $this->email->printDebugger($include);
                        }

                        // Allow access to underlying service if needed
                        public function __call($name, $arguments)
                        {
                            return $this->email->$name(...$arguments);
                        }
                    };

                    $this->controller->$propertyName = $instance;
                    return $instance;
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
            
            public function helpers($helperNames) {
                // CI3 -> CI4 compatibility: $this->load->helpers(array('helper1', 'helper2'))
                // In CI4, helper() accepts both string and array
                helper($helperNames);
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
        
        // Pagination compatibility for CI3 -> CI4 migration (offset-based, CI3 style)
        if (property_exists($this, 'pagination') && empty($this->pagination)) {
            $req = $request;
            $this->pagination = new class($req) {
                private $request;
                private $config = [];
                
                public function __construct($request) {
                    $this->request = $request;
                }
                
                public function initialize($config = []) {
                    $this->config = is_array($config) ? $config : [];
                    return true;
                }
                
                public function create_links() {
                    $cfg = $this->config;
                    
                    $baseUrl = rtrim((string)($cfg['base_url'] ?? ''), '/');
                    $totalRows = (int)($cfg['total_rows'] ?? 0);
                    $perPage = (int)($cfg['per_page'] ?? 0);
                    $uriSegment = (int)($cfg['uri_segment'] ?? 3); // CI3 default
                    $numLinks = (int)($cfg['num_links'] ?? 2);
                    
                    if ($totalRows <= 0 || $perPage <= 0 || $baseUrl === '') {
                        return '';
                    }
                    
                    $totalPages = (int)ceil($totalRows / $perPage);
                    if ($totalPages <= 1) {
                        return '';
                    }
                    
                    // CI3 pagination is offset-based: segment value = offset
                    $offset = 0;
                    try {
                        $seg = $this->request->getUri()->getSegment($uriSegment);
                        $offset = is_numeric($seg) ? (int)$seg : 0;
                    } catch (\Throwable $e) {
                        $offset = 0;
                    }
                    if ($offset < 0) $offset = 0;
                    
                    $currentPage = (int)floor($offset / $perPage) + 1;
                    if ($currentPage < 1) $currentPage = 1;
                    if ($currentPage > $totalPages) $currentPage = $totalPages;
                    
                    $attr = '';
                    if (!empty($cfg['attributes']) && is_array($cfg['attributes'])) {
                        foreach ($cfg['attributes'] as $k => $v) {
                            $attr .= ' ' . htmlspecialchars((string)$k) . '="' . htmlspecialchars((string)$v) . '"';
                        }
                    }
                    
                    $fullOpen = (string)($cfg['full_tag_open'] ?? '<ul class="pagination">');
                    $fullClose = (string)($cfg['full_tag_close'] ?? '</ul>');
                    
                    $firstLinkText = (string)($cfg['first_link'] ?? 'First');
                    $firstOpen = (string)($cfg['first_tag_open'] ?? '<li>');
                    $firstClose = (string)($cfg['first_tag_close'] ?? '</li>');
                    
                    $lastLinkText = (string)($cfg['last_link'] ?? 'Last');
                    $lastOpen = (string)($cfg['last_tag_open'] ?? '<li>');
                    $lastClose = (string)($cfg['last_tag_close'] ?? '</li>');
                    
                    $nextLinkText = (string)($cfg['next_link'] ?? 'Next');
                    $nextOpen = (string)($cfg['next_tag_open'] ?? '<li>');
                    $nextClose = (string)($cfg['next_tag_close'] ?? '</li>');
                    
                    $prevLinkText = (string)($cfg['prev_link'] ?? 'Previous');
                    $prevOpen = (string)($cfg['prev_tag_open'] ?? '<li>');
                    $prevClose = (string)($cfg['prev_tag_close'] ?? '</li>');
                    
                    $curOpen = (string)($cfg['cur_tag_open'] ?? '<li class="active"><a>');
                    $curClose = (string)($cfg['cur_tag_close'] ?? '</a></li>');
                    
                    $numOpen = (string)($cfg['num_tag_open'] ?? '<li>');
                    $numClose = (string)($cfg['num_tag_close'] ?? '</li>');
                    
                    $html = $fullOpen;
                    
                    // First / Prev
                    if ($currentPage > 1) {
                        $html .= $firstOpen . '<a href="' . htmlspecialchars($baseUrl) . '"'.$attr.'>' . $firstLinkText . '</a>' . $firstClose;
                        $prevOffset = ($currentPage - 2) * $perPage;
                        $prevUrl = $prevOffset > 0 ? ($baseUrl . '/' . $prevOffset) : $baseUrl;
                        $html .= $prevOpen . '<a href="' . htmlspecialchars($prevUrl) . '"'.$attr.'>' . $prevLinkText . '</a>' . $prevClose;
                    }
                    
                    // Number links window
                    $start = max(1, $currentPage - $numLinks);
                    $end = min($totalPages, $currentPage + $numLinks);
                    for ($i = $start; $i <= $end; $i++) {
                        if ($i === $currentPage) {
                            $html .= $curOpen . $i . $curClose;
                            continue;
                        }
                        $pageOffset = ($i - 1) * $perPage;
                        $url = $pageOffset > 0 ? ($baseUrl . '/' . $pageOffset) : $baseUrl;
                        $html .= $numOpen . '<a href="' . htmlspecialchars($url) . '"'.$attr.'>' . $i . '</a>' . $numClose;
                    }
                    
                    // Next / Last
                    if ($currentPage < $totalPages) {
                        $nextOffset = $currentPage * $perPage;
                        $nextUrl = $baseUrl . '/' . $nextOffset;
                        $html .= $nextOpen . '<a href="' . htmlspecialchars($nextUrl) . '"'.$attr.'>' . $nextLinkText . '</a>' . $nextClose;
                        
                        $lastOffset = ($totalPages - 1) * $perPage;
                        $lastUrl = $lastOffset > 0 ? ($baseUrl . '/' . $lastOffset) : $baseUrl;
                        $html .= $lastOpen . '<a href="' . htmlspecialchars($lastUrl) . '"'.$attr.'>' . $lastLinkText . '</a>' . $lastClose;
                    }
                    
                    $html .= $fullClose;
                    return $html;
                }
            };
        }
        
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
