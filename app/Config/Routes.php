<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default Controller

// 404 Override
$routes->set404Override('App\Controllers\Home::error_404');

// Auto-load module controllers (CI3 style - no explicit routes needed)
// This will auto-detect module controllers from app/modules directory
// Format: /module_name/method_name/param1/param2/...

// Dynamic module routing - auto-detect all modules
$modules_dir = APPPATH . 'modules/';
if (is_dir($modules_dir)) {
    $modules = array_diff(scandir($modules_dir), ['.', '..']);
    
    foreach ($modules as $module) {
        $module_path = $modules_dir . $module;
        if (is_dir($module_path)) {
            $controller_name = ucfirst(strtolower($module));
            $controller_file = $module_path . '/controllers/' . $controller_name . '.php';
            
            if (file_exists($controller_file)) {
                // Check if file has CI4 namespace (not CI3 require_once)
                $file_content = file_get_contents($controller_file);
                
                // Only add routes if file has CI4 namespace (not CI3 style)
                if (strpos($file_content, 'namespace App\\Modules') !== false || 
                    strpos($file_content, 'namespace App\\\\Modules') !== false) {
                    
                    $namespace = 'App\\Modules\\' . $controller_name . '\\Controllers\\' . $controller_name;
                    $module_lower = strtolower($module);
                    
                    // Create catch-all routes for this module
                    // Route order matters - more specific routes first
                    // Use full namespace with backslash prefix
                    $full_namespace = '\\' . $namespace;
                    
                    // Route with 3+ parameters: /module/method/param1/param2/param3/...
                    $routes->add($module_lower . '/(:segment)/(:segment)/(:segment)/(:any)', $full_namespace . '::$1/$2/$3/$4');
                    
                    // Route with 3 parameters: /module/method/param1/param2/param3
                    $routes->add($module_lower . '/(:segment)/(:segment)/(:segment)', $full_namespace . '::$1/$2/$3');
                    
                    // Route with 2 parameters: /module/method/param1/param2
                    $routes->add($module_lower . '/(:segment)/(:segment)/(:any)', $full_namespace . '::$1/$2/$3');
                    
                    // Route with 1 parameter: /module/method/param1 (e.g., edit_team_member/2404)
                    $routes->add($module_lower . '/(:segment)/(:segment)', $full_namespace . '::$1/$2');
                    
                    // Route with method only: /module/method
                    $routes->add($module_lower . '/(:segment)', $full_namespace . '::$1');
                }
            }
        }
    }
}
