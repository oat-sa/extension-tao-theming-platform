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

use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\oatbox\filesystem\Directory;
use oat\tao\helpers\CssHandler;
use \tao_models_classes_Service;
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

    const FILE_SYSTEM_ID = 'themingAssetsStorage';
    
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
     * @param Directory $directory
     */
    public function setDataDirectory(Directory $directory)
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoThemingPlatform');

        $ext->setConfig(self::CONFIG_KEY_DATA, $this->getFileReferenceSerializer()->serialize($directory));
    }
    
    /**
     * Get a reference on the data storage directory.
     * 
     * @return Directory
     * @throws common_exception If no default data storage directory is configured.
     */
    public function getDataDirectory()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoThemingPlatform');
        $uri = $ext->getConfig(self::CONFIG_KEY_DATA);
        
        if (empty($uri)) {
            throw new common_Exception('No datasource defined for taoThemingPlatform data storage.');
        }

        return $this->getFileReferenceSerializer()->unserializeDirectory($uri);
    }

    /**
     * Get serializer to persist filesystem object
     * @return ResourceFileSerializer
     */
    protected function getFileReferenceSerializer()
    {
        return $this->getServiceLocator()->get(ResourceFileSerializer::SERVICE_ID);
    }
    
    /**
     * Store a file located at $filePath.
     * 
     * The file will be stored in the data directory with a name corresponding
     * to the basename infered from $filePath.
     * 
     * Example:
     * 
     * PlatformThemingService::storeFile('/tmp/myfile.txt') will store
     * a file 'myfile.txt' in the data directory.
     * 
     * @param string $filePath The absolute path to the file to store.
     * @param string $finalName The final name of the file to store if you'd like to change it e.g. 'myfile.png'.
     *
     * @return string $filename
     */
    public function storeFile($filePath, $finalName = '')
    {
        $filesystem = $this->getDataDirectory()
            ->getFileSystem();

        if (empty($finalName)) {
            $finalName = pathinfo($filePath, PATHINFO_BASENAME);
        }

        $stream = fopen($filePath, 'r+');
        $filesystem->writeStream($finalName, $stream);
        fclose($stream);

        return $finalName;
    }

    /**
     * Whether or not a give $fileName exists in the data directory.
     *
     * @param string $fileName
     * @return bool
     */
    public function hasFile($fileName)
    {
        return $this->getDataDirectory()
            ->getFileSystem()
            ->has($fileName);
    }


    /**
     * @param $cssArray array that contains selectors, property and value
     * ['.myselector1' => ['property1'=>'value', 'property2'=>'value2']]
     * @param $filename string the name of the css file
     */
    public function generateCss($cssArray, $filename)
    {
        $tmpDir = \tao_helpers_File::createTempDir();
        $css = "/* === These styles are generated, do not edit! === */ \n";
        $css .= CssHandler::arrayToCss($cssArray, false);
        $css .= "\n/* === Add your own styles below this line === */\n";
        $tmpFile = $tmpDir.'/theme.css';
        file_put_contents($tmpFile, $css);

        $this->storeFile($tmpFile, $filename);

    }
    
    public function getFileUrl($fileName) {
        return _url('getFile', 'Main', 'taoThemingPlatform', array('file' => $fileName));
    }
}
