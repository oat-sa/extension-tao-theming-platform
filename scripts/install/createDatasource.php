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

use oat\oatbox\extension\InstallAction;
use oat\oatbox\filesystem\FileSystemService;
use oat\taoThemingPlatform\model\PlatformThemingService;

/**
 * Class createDatasource
 *
 * @author Gyula Szucs, <gyula@taotesting.com>
 */
class createDatasource extends InstallAction
{
    /**
     * @param array $params
     * @return \common_report_Report
     */
    public function __invoke($params)
    {

        /** @var FileSystemService $fsService */
        $fsService = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
        $fs = $fsService->createFileSystem(PlatformThemingService::FILE_SYSTEM_ID, 'taoThemingPlatform');

        $fs->createDir('assets');

        $assetsDir = $fsService->getDirectory(PlatformThemingService::FILE_SYSTEM_ID)->getDirectory('assets');

        $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fsService);

        PlatformThemingService::singleton()->setDataDirectory($assetsDir);

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, 'Assets file storage registered.');
    }
}