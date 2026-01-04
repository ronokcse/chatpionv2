<?php
/**
 * CI4 Compatibility: form_error() function for CI3 -> CI4 migration
 * This provides form_error() function that works with CI4 validation
 */

if (!function_exists('form_error')) {
    /**
     * CI3 Compatibility: Get form validation error
     * Returns the error message for a form field
     * 
     * @param string $field Field name
     * @param string $prefix Error prefix (optional)
     * @param string $suffix Error suffix (optional)
     * @return string Error message or empty string
     */
    function form_error($field = '', $prefix = '', $suffix = '')
    {
        // Try to get validation service
        $validation = \Config\Services::validation();
        
        // Check if validation has been run and has errors
        if ($validation->hasError($field)) {
            $error = $validation->getError($field);
            
            // Apply prefix and suffix if provided
            if ($prefix !== '') {
                $error = $prefix . $error;
            }
            if ($suffix !== '') {
                $error = $error . $suffix;
            }
            
            // CI3 wraps errors in <p> tags by default
            return '<p>' . $error . '</p>';
        }
        
        return '';
    }
}

