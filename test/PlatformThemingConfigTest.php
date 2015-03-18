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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\taoThemingPlatform\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoThemingPlatform\model\PlatformThemingConfig;

class TestsTestCase extends TaoPhpUnitTestRunner
{
    public function testValidEmptyInstantiation()
    {
        $config = new PlatformThemingConfig();
        $this->assertInstanceOf('oat\\taoThemingPlatform\\model\\PlatformThemingConfig', $config);
        $this->assertEquals(array(), $config->getArrayCopy());
        $this->assertSame($config['unknown'], null);
        $this->assertFalse(isset($config['unknown']));
    }
    
    /**
     * @depends testValidEmptyInstantiation
     */
    public function testValidInstantiation()
    {
        $data = array(
            'value1' => 1,
            'value2' => 'string',
            'value3' => null,
            'value4' => true,
            'value5' => 14.5 
        );
        
        $config = new PlatformThemingConfig($data);
        
        // As assigning a null value is similar to unset...
        unset($data['value3']);
        
        $this->assertEquals($data, $config->getArrayCopy());
        $this->assertEquals($data['value1'], $config['value1']);
        $this->assertEquals($data['value2'], $config['value2']);
        $this->assertEquals($data['value4'], $config['value4']);
        $this->assertTrue(isset($data['value1']));
        $this->assertTrue(isset($data['value2']));
        $this->assertTrue(isset($data['value4']));
    }
    
    /**
     * @depends testValidInstantiation
     */
    public function testOffsetSet()
    {
        $config = new PlatformThemingConfig();
        $config['value1'] = 13.3777;
        $this->assertTrue(isset($config['value1']));
    }
    
    /**
     * @depends testOffsetSet
     */
    public function testOffsetUnset()
    {
        $config = new PlatformThemingConfig();
        $config['value1'] = 1337; 
        $this->assertEquals(1337, $config['value1']);
        
        unset($config['value1']);
        $this->assertFalse(isset($config['value1']));
    }
    
    public function testOutOfRangeSet()
    {
        $this->setExpectedException('\\OutOfRangeException');
        $config = new PlatformThemingConfig();
        $config[0] = 'val1';
    }
    
    public function testOutOfRangeGet()
    {
        $this->setExpectedException('\\OutOfRangeException');
        $config = new PlatformThemingConfig();
        $val = $config[0];
    }
    
    public function testOutOfRangeExists()
    {
        $this->setExpectedException('\\OutOfRangeException');
        $config = new PlatformThemingConfig();
        $isset = isset($config[0]);
    }
    
    public function testOutOfRangeUnset()
    {
        $this->setExpectedException('\\OutOfRangeException');
        $config = new PlatformThemingConfig();
        unset($config[0]);
    }
    
    public function testUnsetWithNothingSet()
    {
        // Should produce nothing...
        $config = new PlatformThemingConfig();
        unset($config['unknown']);
        $this->assertTrue(true);
    }
}