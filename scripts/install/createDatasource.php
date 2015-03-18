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

use oat\taoThemingPlatform\model\PlatformThemingService;

/*
 * This post-installation script creates a data storage directory
 * for the taoThemingPlatform extension.
 */
$dataPath = FILES_PATH . 'taoThemingPlatform' . DIRECTORY_SEPARATOR;
if (file_exists($dataPath)) {
    helpers_File::emptyDirectory($dataPath);
}

// Create extension datasource.
$source = tao_models_classes_FileSourceService::singleton()->addLocalSource('Platform Theming datasource', $dataPath);

// Create the assets directory which will contain theming assets.
mkdir($dataPath.'assets');
$directory = new core_kernel_file_File($source->createFile('', 'assets'));

// Assets storage is now set to '/data/taoThemingPlatform/assets'.
PlatformThemingService::singleton()->setDataDirectory($directory);