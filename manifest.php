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
return array(
    'name' => 'taoThemingPlatform',
	'label' => 'Platform Theming',
	'description' => '',
    'license' => 'GPL-2.0',
    'version' => '0.1.3',
	'author' => 'Open Assessment Technologies SA',
	'requires' => array(
        'tao' => '>=2.7'
    ),
	'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#taoThemingPlatformManager',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#taoThemingPlatformManager', array('ext'=>'taoThemingPlatform')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'taoThemingPlatform','mod' => 'Main', 'act' => 'getFile')),
    ),
    'install'	=> array(
        'php'	=> array(
            dirname(__FILE__).'/scripts/install/createDatasource.php',
        )
    ),
    'uninstall' => array(
    ),
    'update' => 'oat\\taoThemingPlatform\\scripts\\update\\Updater',
    'routes' => array(
        '/taoThemingPlatform' => 'oat\\taoThemingPlatform\\controller'
    ),    
	'constants' => array(
	    # views directory
	    "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
	    
		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'taoThemingPlatform/',
	    
	    #BASE WWW required by JS
	    'BASE_WWW' => ROOT_URL.'taoThemingPlatform/views/'
	),
    'extra' => array(
        'structures' => dirname(__FILE__).DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);
