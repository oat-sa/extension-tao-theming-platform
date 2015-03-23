<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\taoThemingPlatform\controller;

use \tao_actions_CommonModule;
use \tao_helpers_File;
use \oat\taoThemingPlatform\model\PlatformThemingService;

/**
 * Main Controller of the Extension.
 *
 * @author Open Assessment Technologies SA
 * @package taoThemingPlatform
 * @license GPL-2.0
 *
 */
class Main extends tao_actions_CommonModule {

    /**
     * initialize the Controller.
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Main Controller Action.
     */
    public function index() {
        $this->setView('index.tpl');
    }
    
    /**
     * Get a file from the data directory as the HTTP response with the appropriate
     * content-type header set.
     */
    public function getFile() {
        $file = $this->getRequestParameter('file');
        $service = PlatformThemingService::singleton();
        $dataDirectory = $service->getDataDirectory();
        
        $finalPath = rtrim($dataDirectory->getAbsolutePath(), "\\/") . DIRECTORY_SEPARATOR . $file;
        
        if (tao_helpers_File::securityCheck($finalPath, true) === false) {
            die();
        }
        
        $mime = tao_helpers_File::getMimeType($finalPath, true);
        header('Content-Type: ' . $mime);
        echo file_get_contents($finalPath);
    }
}
