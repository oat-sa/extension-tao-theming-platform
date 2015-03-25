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
use Jig\Utils\FsUtils;
use oat\tao\helpers\Template;
use oat\taoThemingPlatform\model\PlatformThemingConfig;

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
     * @return PlatformThemingService
     */
    protected function getPlatformService(){
        return PlatformThemingService::singleton();
    }

    /**
     * Main Controller Action.
     */
    public function index() {


        $themingConfig = $this->getPlatformService()->retrieveThemingConfig();

        $values = array('themingConfig' => $themingConfig);
        $formContainer = new ThemeForm($values);
        $myForm = $formContainer->getForm();

        $this->setData('formTitle', __('Customize Platform'));
        $this->setData('myForm', $myForm->render());
        $this->setData('logo_title', $themingConfig['message']);
        $this->setData('logo', ($themingConfig['logo'] === null) ? Template::img('tao-logo.png', 'tao') : $this->getPlatformService()->getFileUrl($themingConfig['logo']));
        
        $this->setData('header-background', ($themingConfig['header-background'])?:'#fff');
        $this->setData('action-background', ($themingConfig['action-background'])?:'#fff');
        $this->setData('active-background', ($themingConfig['active-background'])?:'#fff');
        $this->setData('inactive-background', ($themingConfig['inactive-background'])?:'#fff');

        $this->setData('header-color', ($themingConfig['header-color'])?:'#fff');
        $this->setData('action-color', ($themingConfig['action-color'])?:'#fff');
        $this->setData('active-color', ($themingConfig['active-color'])?:'#fff');
        $this->setData('inactive-color', ($themingConfig['inactive-color'])?:'#fff');
        (isset($themingConfig['css-file']))?$this->setData('css-file', array('download'=>true,'file'=>$themingConfig['css-file'])):'';
        
        $this->setView('index.tpl');

    }

    public function getBase64(){

        //TODO Resize the image
        if($this->hasRequestParameter('upload')){
            $filename = $this->getRequestParameter('upload');
            $return['base64'] = 'data:' . FsUtils::getMimeType($filename)  . ';base64,'.base64_encode(file_get_contents($filename));
        }
        else{
            $return['base64'] = Template::img('tao-logo.png', 'tao');
        }
        $this->returnJson($return);
    }

    public function upload(){

        if(!$this->hasRequestParameter('content')){
            throw new \common_exception_MissingParameter('content');
        }

        $file = \tao_helpers_Http::getUploadedFile('content');;

        $filename = $this->getPlatformService()->storeFile($file['tmp_name'], 'platformtheme.css');

        $theme = $this->getPlatformService()->retrieveThemingConfig();
        $theme['css-file'] =  $filename;
        $this->getPlatformService()->syncThemingConfig($theme);
        $this->returnJson(array('success' => __('Style modified')));
    }

    public function saveTheme(){

        if (!\tao_helpers_Request::isAjax()) {
            throw new \common_exception_IsAjaxAction(__FUNCTION__);
        }

        $data = array();
        if($this->hasRequestParameter('logo')){
            $data = $this->getRequestParameter('logo');
            if(isset($data['logo'])){
                $logoFile = $this->getPlatformService()->storeFile($data['logo']['uploaded_file'], $data['logo']['name']);
                $data['logo'] = $logoFile;

            }
        }


        $dataArray = array();
        if($this->hasRequestParameter('css')){
            $css = $this->getRequestParameter('css');
            $formatedArray = array();
            foreach($css as $key => $array){

                $properties = explode('-',$key, 2);
                if(isset($properties[1])){
                    $value = $array['value'];
                    $pos = strpos($value, ' !important');
                    if($pos !== false){
                        $value = substr($value, 0, $pos);
                    }

                    $dataArray[$key] = $value;
                    $property = $properties[1];
                    $formatedArray[$array['selector']][$property] = $array['value'];
                }
            }

            $this->getPlatformService()->generateCss($formatedArray, 'platformtheme.css');
            $data['css-file'] = 'platformtheme.css';
        }

        $data = array_merge($data, $dataArray);
        $previousConf = $this->getPlatformService()->retrieveThemingConfig();
        $previousArray = $previousConf->getArrayCopy();
        $missingConf = array_diff_key($previousArray, $data);
        $data = array_merge($data, $missingConf);

        $this->getPlatformService()->syncThemingConfig(new PlatformThemingConfig($data));

        $return['success'] = true;
        $return['message'] = __('Your theme has been saved');
        $this->returnJson($return);

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
        if($this->hasRequestParameter('download')){
            header('Content-disposition: attachment; filename=theme.css');
        }
        header('Content-Type: ' . $mime);
        echo file_get_contents($finalPath);
    }
}
