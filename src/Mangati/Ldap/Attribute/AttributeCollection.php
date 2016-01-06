<?php

namespace Mangati\Ldap\Attribute;

use Iterator;

/**
 * AttributeCollection
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class AttributeCollection implements Iterator
{
    
    /**
     *
     * @var Attribute[]
     */
    private $collection = [];
    
    private $position;
    
    
    public function __construct()
    {
        $this->position = 0;
    }
    
    /**
     * @return Attribute
     */
    public function get($name) 
    {
        foreach ($this->getAll($name) as $attr) {
            return $attr;
        }
        
        return null;
    }
    
    /**
     * @return Attribute
     */
    public function getAll($name) 
    {
        foreach ($this->collection as $attr) {
            if ($attr->getName() === $name) {
                yield $attr;
            }
        }
    }
    
    /**
     * @return AttributeCollection
     */
    public function add(Attribute $attr) 
    {
        $this->collection[] = $attr;
        
        return $this;
    }
    
    /**
     * @return AttributeCollection
     */
    public function set($name, $value)
    {
        $attr = $this->get($name);
        if ($attr) {
            $attr->setValue($value);
        }
        
        return $this;
    }
    
    public function current()
    {
        return $this->collection[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->collection[$this->position]);
    }
    
}