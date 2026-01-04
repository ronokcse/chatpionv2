<?php
/**
 * CI4 Compatibility Helper
 * Provides get_instance() function for CI3 to CI4 migration
 */

if (!function_exists('get_instance')) {
    /**
     * CI4 Compatibility: Get CodeIgniter instance
     * Returns a compatibility object that provides CI3-style access
     */
    function &get_instance()
    {
        static $instance = null;
        
        if ($instance === null) {
            $instance = new class {
                public $load;
                public $db;
                public $session;
                public $config;
                public $lang;
                public $uri;
                public $input;
                public $basic;
                
                public function __construct()
                {
                    // Load services
                    $this->db = \Config\Database::connect();
                    
                    // Session compatibility wrapper for CI3 -> CI4
                    $sessionService = \Config\Services::session();
                    $this->session = new class($sessionService) {
                        private $session;
                        
                        public function __construct($session) {
                            $this->session = $session;
                        }
                        
                        public function userdata($key = null) {
                            if ($key === null) {
                                return $this->session->get();
                            }
                            return $this->session->get($key);
                        }
                        
                        public function set_userdata($key, $value = null) {
                            if (is_array($key)) {
                                foreach ($key as $k => $v) {
                                    $this->session->set($k, $v);
                                }
                            } else {
                                $this->session->set($key, $value);
                            }
                            return true;
                        }
                        
                        public function unset_userdata($key) {
                            if (is_array($key)) {
                                foreach ($key as $k) {
                                    $this->session->remove($k);
                                }
                            } else {
                                $this->session->remove($key);
                            }
                            return true;
                        }
                        
                        public function all_userdata() {
                            return $this->session->get();
                        }
                        
                        public function sess_destroy() {
                            return $this->session->destroy();
                        }
                        
                        // Pass through other methods
                        public function __call($method, $args) {
                            if (method_exists($this->session, $method)) {
                                return call_user_func_array([$this->session, $method], $args);
                            }
                            return null;
                        }
                    };
                    
                    // Config compatibility wrapper for CI3 -> CI4
                    $myConfig = config('MyConfig');
                    $appConfig = config('App');
                    $this->config = new class($myConfig, $appConfig) {
                        private $myConfig;
                        private $appConfig;
                        
                        public function __construct($myConfig, $appConfig) {
                            $this->myConfig = $myConfig;
                            $this->appConfig = $appConfig;
                        }
                        
                        public function item($key) {
                            // Try MyConfig first
                            if (isset($this->myConfig->$key)) {
                                return $this->myConfig->$key;
                            }
                            // Try App config
                            if (isset($this->appConfig->$key)) {
                                return $this->appConfig->$key;
                            }
                            // Return null if not found (CI3 behavior)
                            return null;
                        }
                        
                        // Allow direct property access
                        public function __get($key) {
                            return $this->item($key);
                        }
                    };
                    
                    // Input compatibility wrapper for CI3 -> CI4
                    $request = \Config\Services::request();
                    $this->input = new class($request) {
                        private $request;
                        
                        public function __construct($request) {
                            $this->request = $request;
                        }
                        
                        public function get($key = null, $filter = null, $flags = null) {
                            if ($key === null) {
                                return $this->request->getGet();
                            }
                            return $this->request->getGet($key, $filter, $flags);
                        }
                        
                        public function post($key = null, $filter = null, $flags = null) {
                            if ($key === null) {
                                return $this->request->getPost();
                            }
                            return $this->request->getPost($key, $filter, $flags);
                        }
                        
                        public function getPost($key = null, $filter = null, $flags = null) {
                            if ($key === null) {
                                return array_merge($this->request->getGet(), $this->request->getPost());
                            }
                            $value = $this->request->getPost($key, $filter, $flags);
                            if ($value === null) {
                                $value = $this->request->getGet($key, $filter, $flags);
                            }
                            return $value;
                        }
                        
                        public function cookie($key = null, $filter = null, $flags = null) {
                            if ($key === null) {
                                return $this->request->getCookie();
                            }
                            return $this->request->getCookie($key, $filter, $flags);
                        }
                        
                        public function server($key = null, $filter = null, $flags = null) {
                            if ($key === null) {
                                return $_SERVER;
                            }
                            return $this->request->getServer($key, $filter, $flags);
                        }
                        
                        public function is_cli_request() {
                            return $this->request->isCLI();
                        }
                        
                        public function is_ajax_request() {
                            return $this->request->isAJAX();
                        }
                    };
                    
                    // URI compatibility wrapper for CI3 -> CI4
                    $uriService = \Config\Services::uri();
                    $request = \Config\Services::request();
                    $this->uri = new class($uriService, $request) {
                        private $uri;
                        private $request;
                        
                        public function __construct($uri, $request) {
                            $this->uri = $uri;
                            $this->request = $request;
                        }
                        
                        public function segment($n, $noResult = null) {
                            try {
                                $segment = $this->uri->getSegment($n);
                                return $segment !== null ? $segment : $noResult;
                            } catch (\CodeIgniter\HTTP\Exceptions\HTTPException $e) {
                                // Segment out of range
                                return $noResult;
                            }
                        }
                        
                        public function uri_string() {
                            return $this->uri->getPath();
                        }
                        
                        public function total_segments() {
                            return $this->uri->getTotalSegments();
                        }
                        
                        public function rsegment($n, $noResult = null) {
                            // Reverse segment - not commonly used, but for compatibility
                            $total = $this->uri->getTotalSegments();
                            $index = $total - $n + 1;
                            return $this->segment($index, $noResult);
                        }
                        
                        // Pass through other methods
                        public function __call($method, $args) {
                            if (method_exists($this->uri, $method)) {
                                return call_user_func_array([$this->uri, $method], $args);
                            }
                            return null;
                        }
                    };
                    
                    // Load Basic model
                    $this->basic = new \App\Models\Basic();
                    
                    // Language service with CI3 compatibility
                    $this->lang = new class {
                        public function line($key) {
                            // Try to get language line
                            $result = lang($key);
                            // If key not found, return the key itself (CI3 behavior)
                            return ($result === $key) ? $key : $result;
                        }
                        
                        public function load($file, $lang = null) {
                            $langService = \Config\Services::language();
                            if ($lang) {
                                $langService->setLocale($lang);
                            }
                            return true;
                        }
                    };
                    
                    // Load helper for compatibility
                    $this->load = new class {
                        public function library($lib) {
                            // Libraries are loaded via Services in CI4
                            // This is for compatibility only
                            return true;
                        }
                        
                        public function model($model) {
                            // Models are autoloaded in CI4
                            $modelClass = "App\\Models\\" . ucfirst($model);
                            if (class_exists($modelClass)) {
                                return new $modelClass();
                            }
                            return true;
                        }
                        
                        public function helper($helper) {
                            helper($helper);
                            return true;
                        }
                        
                        public function view($view, $data = [], $return = false) {
                            return view($view, $data);
                        }
                        
                        public function database() {
                            // Return database connection (already available as $this->db)
                            return \Config\Database::connect();
                        }
                    };
                }
            };
        }
        
        return $instance;
    }
}

