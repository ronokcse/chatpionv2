<?php

namespace App\Libraries\Session;

use CodeIgniter\Session\Handlers\FileHandler as BaseFileHandler;
use CodeIgniter\Session\SessionConfig;

class FileHandler extends BaseFileHandler
{
    public function __construct(SessionConfig $config, string $ipAddress)
    {
        // Call parent BaseHandler constructor (not FileHandler constructor)
        // This avoids the ini_set() call that causes the error when headers are already sent
        \CodeIgniter\Session\Handlers\BaseHandler::__construct($config, $ipAddress);

        // Set savePath manually, always using @ to suppress errors
        // This prevents "Session ini settings cannot be changed after headers have already been sent" error
        // which occurs when max_input_vars warning is output before session initialization
        if (! empty($this->savePath)) {
            $this->savePath = rtrim($this->savePath, '/\\');
            // Always use @ to suppress errors, even if headers are already sent
            @ini_set('session.save_path', $this->savePath);
        } else {
            $sessionPath = rtrim(ini_get('session.save_path'), '/\\');

            if ($sessionPath === '') {
                $sessionPath = WRITEPATH . 'session';
            }

            $this->savePath = $sessionPath;
        }

        // Call configureSessionIDRegex which is needed
        $this->configureSessionIDRegex();
    }
}
