<?php

namespace Mangati\Ldap\Attribute;

/**
 * Attribute
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
abstract class Attribute
{
    
    private $name;
    
    private $value;
    
    private $options = [];
    
    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->setValue($value);
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $this->parseValue($value);
        return $this;
    }
    
    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
        
    abstract protected function parseValue($value);
    
    public function __toString()
    {
        return "{$this->getName()}={$this->getValue()}";
    }

}