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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 */

namespace oat\taoThemingPlatform\model;

use \tao_models_classes_Service;
use \core_kernel_file_File;
use \common_ext_ExtensionsManager;
use \common_Exception;

/**
 * The main Service of the Extension.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @author Dieter Raber <dieter@taotesting.com>
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 */
class PlatformThemingService extends tao_models_classes_Service
{
    /**
     * Configuration key for data e.g. images, other assets, ... storage.
     * 
     * @var string
     */
    const CONFIG_KEY_DATA = 'themingPlatformData';
    
    /**
     * Configuration key for theming configuration.
     * 
     * @var string
     */
    const CONFIG_KEY_CONF = 'themingPlatformConf';
    
    /**
     * A PlatformThemingConfig cache property.
     * 
     * If the Theming Configuration is retrieved multiple time,
     * the value held in this property will be returned.
     * 
     * If the Theming Configuration is synchronized, the value
     * of this property is updated with the newly synchronized
     * ThemingConfiguration object.
     * 
     * @var \oat\taoThemingPlatform\model\PlatformThemingConfig
     */
    private $themingConfigMemCache = null;
    
    /**
     * Retrieve the Theming Configuration.
     * 
     * The return PlatformThemingConfig object will be set up
     * with the configuration data from /data/taoThemingPlatform/assets.
     * 
     * @return \oat\taoThemingPlatform\model\PlatformThemingConfig
     */
    public function retrieveThemingConfig()
    {
        if (is_null($this->themingConfigMemCache) === false) {
            
            return $this->themingConfigMemCache;
        } else {
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoThemingPlatform');
            $jsonConfig = $ext->getConfig(self::CONFIG_KEY_CONF);
            
            $arrayConfig = array();
            if (empty($jsonConfig) === false) {
                $arrayConfig = json_decode($jsonConfig, true);
            }
            
            $themingConfig = new PlatformThemingConfig($arrayConfig);
            $this->themingConfigMemCache = $themingConfig;
            
            return clone $themingConfig;
        }
    }
    
    /**
     * Synchronize the Theming Configuration.
     * 
     * The configuration represented by the given PlatformThemingConfig
     * object will be serialized and stored in /data/taoThemingPlatform/assets.
     * 
     * @param \oat\taoThemingPlatform\model\PlatformThemingConfig $config
     */
    public function syncThemingConfig(PlatformThemingConfig $config)
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoThemingPlatform');
        $ext->setConfig(self::CONFIG_KEY_CONF, json_encode($config->getArrayCopy()));
        $this->themingConfigMemCache = $config;
    }
    
    /**
     * Set the data storage directory.
     * 
     * @param core_kernel_file_File $directory
     */
    public function setDataDirectory(core_kernel_file_File $directory)
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoThemingPlatform');
        $ext->setConfig(self::CONFIG_KEY_DATA, $directory->getUri());
    }
    
    /**
     * Get a reference on the data storage directory.
     * 
     * You can call core_kernel_file_File::getAbsolutePath() and/or core_kernel_file_File::getRelativePath()
     * on the return object to know where to store data assets.
     * 
     * @return core_kernel_file_File
     * @throws common_exception If no default data storage directory is configured.
     */
    public function getDataDirectory()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoThemingPlatform');
        $uri = $ext->getConfig(self::CONFIG_KEY_DATA);
        
        if (empty($uri)) {
            throw new common_Exception('No datasource defined for taoThemingPlatform data storage.');
        }
        
        return new core_kernel_file_File($uri);
    }
}
