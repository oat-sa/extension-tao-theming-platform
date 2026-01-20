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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 */

namespace oat\taoThemingPlatform\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoThemingPlatform\model\PlatformThemingService;
use oat\taoThemingPlatform\model\PlatformThemingConfig;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use oat\generis\model\fileReference\ResourceFileSerializer;
use League\Flysystem\Local\LocalFilesystemAdapter;

class PlatformThemingServiceTest extends TaoPhpUnitTestRunner
{
    private $service = null;
    private $tempConfig = null;
    private $serviceManager = null;
    private $tempRoot = null;

    public function tearDown(): void
    {
        parent::tearDown();

        // Restore previous Theming config...
        if ($this->service && $this->tempConfig) {
            $this->service->syncThemingConfig($this->tempConfig);
        }

        // Deal with data storage.
        if ($this->service) {
            $filesystem = $this->service->getDataDirectory()->getFileSystem();
            if ($filesystem->fileExists('data.txt')) {
                $filesystem->delete('data.txt');
            }
        }
        @unlink(rtrim(sys_get_temp_dir(), "\\/") . '/tmp-platformthemingtest.txt');
        @unlink(rtrim(sys_get_temp_dir(), "\\/") . '/tmp-mynewname.txt');

        if ($this->tempRoot && is_dir($this->tempRoot)) {
            $this->removeDirectory($this->tempRoot);
        }

        unset($service);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->serviceManager = new ServiceManager(new TestConfigStorage());
        ServiceManager::setServiceManager($this->serviceManager);

        $this->tempRoot = rtrim(sys_get_temp_dir(), "\\/") . '/tao-theming-test-' . uniqid();
        if (!is_dir($this->tempRoot)) {
            mkdir($this->tempRoot, 0777, true);
        }

        $adapterId = 'local-test-adapter';
        $directoryId = 'theming-test-fs';
        $fileSystemService = new FileSystemService(
            [
                FileSystemService::OPTION_ADAPTERS => [
                    $adapterId => [
                        'class' => LocalFilesystemAdapter::class,
                        'options' => ['root' => $this->tempRoot],
                    ],
                ],
                FileSystemService::OPTION_DIRECTORIES => [
                    $directoryId => $adapterId,
                ],
                FileSystemService::OPTION_FILE_PATH => $this->tempRoot,
            ]
        );
        $fileSystemService->setServiceLocator($this->serviceManager);
        $this->serviceManager->overload(FileSystemService::SERVICE_ID, $fileSystemService);

        $serializer = new TestFileReferenceSerializer();
        $serializer->setServiceLocator($this->serviceManager);
        $this->serviceManager->overload(ResourceFileSerializer::SERVICE_ID, $serializer);

        $extensionManager = new TestExtensionManager();
        $extensionManager->setServiceLocator($this->serviceManager);
        $this->serviceManager->overload(\common_ext_ExtensionsManager::SERVICE_ID, $extensionManager);

        $this->service = PlatformThemingService::singleton();

        $dataDirectory = $fileSystemService->getDirectory($directoryId)->getDirectory('assets');
        $this->service->setDataDirectory($dataDirectory);

        // Save current Theming config...
        $this->tempConfig = $this->service->retrieveThemingConfig();

        // Set up all tests with an empty Theming Configuration.
        $this->service->syncThemingConfig(new PlatformThemingConfig());

        // Deal with data storage.
        $testFile = rtrim(sys_get_temp_dir(), "\\/") . '/tmp-platformthemingtest.txt';
        file_put_contents($testFile, 'data');
    }

    /**
     * Aims at testing that a proper empty theming configuration is set.
     */
    public function testEmptyRetrieveThemingConfig()
    {
        $conf = $this->service->retrieveThemingConfig();
        $this->assertEquals(0, count($conf));
    }

    public function testSyncThemingConfig()
    {
        $conf = $this->service->retrieveThemingConfig();
        $conf['key1'] = 'value1';

        $this->service->syncThemingConfig($conf);
        $conf = $this->service->retrieveThemingConfig();

        $this->assertEquals(1, count($conf));
        $this->assertEquals('value1', $conf['key1']);
    }

    /**
     * Aims at testing that the data directors is correctly configured.
     */
    public function testGetDataDirectory()
    {
        // should be data-source/assets.
        $dataDirectory = $this->service->getDataDirectory();
        $this->assertEquals('assets', $dataDirectory->getPrefix());
    }

    /**
     * @depends testGetDataDirectory
     */
    public function testFileStorage()
    {
        $filePath = rtrim(sys_get_temp_dir(), "\\/") . '/tmp-platformthemingtest.txt';
        $this->service->storeFile($filePath);
        $filesystem = $this->service->getDataDirectory()->getFileSystem();
        $this->assertEquals('data', $filesystem->read('tmp-platformthemingtest.txt'));

        $this->service->storeFile($filePath, 'tmp-mynewname.txt');
        $this->assertEquals('data', $filesystem->read('tmp-mynewname.txt'));
    }

    private function removeDirectory(string $path): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }
        @rmdir($path);
    }
}

class TestConfigStorage
{
    private $data = [];

    public function get($key)
    {
        return $this->data[$key] ?? false;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return true;
    }

    public function del($key)
    {
        unset($this->data[$key]);
        return true;
    }

    public function exists($key)
    {
        return array_key_exists($key, $this->data);
    }
}

class TestExtension
{
    private $config = [];

    public function getConfig($key)
    {
        return $this->config[$key] ?? null;
    }

    public function setConfig($key, $value)
    {
        $this->config[$key] = $value;
        return true;
    }
}

class TestExtensionManager extends ConfigurableService
{
    private $extensions = [];

    public function getExtensionById($id)
    {
        if (!isset($this->extensions[$id])) {
            $this->extensions[$id] = new TestExtension();
        }

        return $this->extensions[$id];
    }
}

class TestFileReferenceSerializer extends ConfigurableService
{
    private $directories = [];

    public function serialize($directory)
    {
        $id = 'dir-' . count($this->directories);
        $this->directories[$id] = $directory;
        return $id;
    }

    public function unserializeDirectory($serial)
    {
        if (!isset($this->directories[$serial])) {
            throw new \common_exception_NotFound('Unknown directory serial.');
        }
        return $this->directories[$serial];
    }
}
