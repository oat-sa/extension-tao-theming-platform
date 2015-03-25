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

use \ArrayAccess;
use \Countable;
use \RangeException;
use \OutOfRangeException;

/**
 * A Theming Configuration Value data object.
 * 
 * PlatformThemingConfig objects aims at storing Theming Configuration Values. All the values
 * it stores are associated with a given key, and must be scalar values.
 * 
 * PlatformThemingConfig implements the ArrayAccess interface. PlatformThemingConfig objects
 * must ben then addressed as arrays using the [] notation.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @author Dieter Raber <dieter@taotesting.com>
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 *
 */
class PlatformThemingConfig implements ArrayAccess, Countable
{
    /**
     * Data place holder for value storage.
     * It is an associative array.
     * 
     * @var array
     */
    private $dataPlaceholder = array();
    
    /**
     * Create a new PlatformThemingConfig object.
     * 
     * The $data array will be used to create the key/value pairs
     * composing the PlatformThemingConfig object. The $data array
     * must be an associative array where keys are strings, and values
     * are scalar ones.
     * 
     * @param array $data An optional array of data.
     * @throws RangeException If a value of the $data array is not scalar.
     * @throws OutOfRangeException If a key of the $data array is not a string.
     */
    public function __construct(array $data = array())
    {
        $dataPlaceholder = array();
        
        foreach (array_keys($data) as $key) {
            $this->offsetSet($key, $data[$key]);
        }
    }
    
    /**
     * Get the data placeholder.
     * 
     * @return array
     */
    protected function getDataPlaceholder()
    {
        return $this->dataPlaceholder;
    }
    
    /**
     * Set the data placeholder.
     * 
     * @param array $dataPlaceholder
     */
    protected function setDataPlaceholder(array $dataPlaceholder)
    {
        $this->dataPlaceholder = $dataPlaceholder;
    }
    
    /**
     * Get the PlatformThemingConfig object as an associative array
     * where keys are offsets and values are Theming Configuration Values.
     * 
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->dataPlaceholder;
    }
    
    /**
     * Whether or not a Theming Configuration Value exists.
     * 
     * Call this method if you want to know whether or not a Theming Configuration Value
     * exists at a given $offset.
     * 
     * @param string $offset
     * @throws OutOfRangeException If $offset is not a string.
     */
    public function offsetExists($offset) {
        
        if (is_string($offset) === false) {
            $type = gettype($offset);
            throw new OutOfRangeException("PlatformThemingConfig objects can only be addressed with strings, '${type}' given.");
        }
        
        $dataPlaceholder = $this->getDataPlaceholder();
        return isset($dataPlaceholder[$offset]);
    }
    
    /**
     * Get a Theming Configuration Value.
     * 
     * Get a Theming Configuration Value associated with a particular $offset.
     * 
     * @param string $offset
     * @throws OutOfRangeException If $offset is not a string.
     * @return mixed The value at $offset or null if there is nothing at $offset.
     */
    public function offsetGet($offset) {
        
        if (is_string($offset) === false) {
            $type = gettype($offset);
            throw new OutOfRangeException("PlatformThemingConfig objects can only be addressed with strings, '${type}' given.");
        }
        
        if ($this->offsetExists($offset) === true) {
            $dataPlaceholder = $this->getDataPlaceholder();
            return $dataPlaceholder[$offset];
        } else {
            return null;
        }
    }
    
    /**
     * Set a Theming Configuration Value.
     * 
     * Set a Theming Configuration $value at a given $offset. If $value is the null value,
     * then the call to this method will be similar to offsetUnset.
     * 
     * @param string $offset
     * @param mixed $value A scalar value.
     * @throws OutOfRangeException If $offset is not a string.
     * @throws RangeException If $value is not a scalar value.
     */
    public function offsetSet($offset, $value) {
        
        if (is_string($offset) === false) {
            $type = gettype($offset);
            throw new OutOfRangeException("PlatformThemingConfig objects can only be addressed with strings, '${type}' given.");
        }
        
        if (is_null($value)) {
            $this->offsetUnset($offset);
        } elseif (is_scalar($value) === false) {
            $type = gettype($value);
            throw new RangeException("PlatformThemingConfig objects only store scalar values, '${type}' given.");
        } else {
            $dataPlaceholder = $this->getDataPlaceholder();
            $dataPlaceholder[$offset] = $value;
            $this->setDataPlaceholder($dataPlaceholder);
        }
    }
    
    /**
     * Unset the Theming Configuration Value.
     * 
     * Remove a Theming Configuration Value at a given $offset. If no value for
     * such $offset exists, the call to this method will have no effect.
     * 
     * @param string $offset
     * @throws OutOfRangeException If $offset is not a string.
     */
    public function offsetUnset($offset) {
        
        if (is_string($offset) === false) {
            $type = gettype($offset);
            throw new OutOfRangeException("PlatformThemingConfig objects can only be addressed with strings, '${type}' given.");
        }
        
        if ($this->offsetExists($offset) === true) {
            $dataPlaceholder = $this->getDataPlaceholder();
            unset($dataPlaceholder[$offset]);
            $this->setDataPlaceholder($dataPlaceholder);
        }
    }
    
    /**
     * Count the number of key/Theming Configuration Value pairs
     * held by the object.
     * 
     * @return integer
     */
    public function count() {
        $dataPlaceholder = $this->getDataPlaceholder();
        return count($dataPlaceholder);
    }
}