<?php
/**
 * Pusher for CodeIgniter
 *
 * Simple library that wraps the Pusher PHP library (https://github.com/pusher/pusher-http-php)
 * and give access to the Pusher methods using regular CodeIgniter syntax.
 *
 * This library requires that Pusher PHP Library is installed with composer, and that CodeIgniter
 * config is set to autoload the vendor folder. More information in the CodeIgniter user guide at
 * http://www.codeigniter.com/userguide3/general/autoloader.html?highlight=composer
 *
 * @package     CodeIgniter
 * @category    Libraries
 * @author      Mattias Hedman
 * @license     MIT
 * @link        https://github.com/darkwhispering/pusher-for-codeigniter
 * @version     2.0.0
 */

use Pusher\Pusher;

Class Ci_pusher
{

    public function __construct()
    {
        // CI4: Get config from MyConfig
        $config = config('MyConfig');
        
        // Get config variables
        $app_id     = $config->pusher_app_id ?? '';
        $app_key    = $config->pusher_app_key ?? '';
        $app_secret = $config->pusher_app_secret ?? '';
        $options    = $this->options($config);

        // Create Pusher object only if we don't already have one
        if (!isset($this->pusher))
        {
            // Create new Pusher object
            $this->pusher = new Pusher($app_key, $app_secret, $app_id, $options);
            log_message('debug', 'CI Pusher library loaded');

            // Set logger if debug is set to true
            // Tried making it true in config/pusher.php,does not work (alamin)
            /*if (($config->pusher_debug ?? false) === TRUE )
            {
                $this->pusher->set_logger(new Ci_pusher_logger());
                log_message('debug', 'CI Pusher library debug ON');
            }*/
        }
    }

    // --------------------------------------------------------------------

    /**
     * Get Pusher object
     *
     * @return  Object
     */
    public function get_pusher()
    {
        return $this->pusher;
    }

    // --------------------------------------------------------------------

    /**
     * Build optional options array
     *
     * @param object $config Config object
     * @return  array
     */
    private function options($config)
    {
        $options = [];
        
        // CI4: Get config values from MyConfig
        if (!empty($config->pusher_cluster)) {
            $options['cluster'] = $config->pusher_cluster;
        }
        
        // Optional pusher settings (if needed in future)
        // $options['scheme']    = $config->pusher_scheme ?? null;
        // $options['host']      = $config->pusher_host ?? null;
        // $options['port']      = $config->pusher_port ?? null;
        // $options['timeout']   = $config->pusher_timeout ?? null;
        // $options['encrypted'] = $config->pusher_encrypted ?? null;

        $options = array_filter($options);

        return $options;
    }

    // --------------------------------------------------------------------

    /**
    * Enables the use of CI super-global without having to define an extra variable.
    * I can't remember where I first saw this, so thank you if you are the original author.
    *
    * Copied from the Ion Auth library
    *
    * @access  public
    * @param   $var
    * @return  mixed
    */
    public function __get($var)
    {
        return get_instance()->$var;
    }

}

// --------------------------------------------------------------------

/**
 * Logger class
 *
 * Logs all messages to CodeIgniter log
 */
Class Ci_pusher_logger {

    /**
     * Log Pusher log message to CodeIgniter log
     *
     * @param   string  $msg  The debug message
     */
    public function log($msg)
    {
        log_message('debug', $msg);
    }

    // --------------------------------------------------------------------

    /**
    * Enables the use of CI super-global without having to define an extra variable.
    * I can't remember where I first saw this, so thank you if you are the original author.
    *
    * Copied from the Ion Auth library
    *
    * @access  public
    * @param   $var
    * @return  mixed
    */
    public function __get($var)
    {
        return get_instance()->$var;
    }
}
