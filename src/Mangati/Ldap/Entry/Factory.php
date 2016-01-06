<?php

namespace Mangati\Ldap\Entry;

use Exception;
use Mangati\Ldap\Entry\Entry;
use Mangati\Ldap\Attribute\TextAttribute;
use Mangati\Ldap\Schema\ObjectClassInterface;

/**
 * Factory
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Factory
{
    
    /**
     * @var Entry
     */
    private $entry;
    
    private $availableAttributes;
    
    public function __construct()
    {
        $this->availableAttributes = [];
    }
    
    /**
     * @return Entry
     */
    public function withDn($dn)
    {
        $this->entry = new Entry($dn);
        return $this;
    }
    
    /**
     * @return Entry
     */
    public function build()
    {
        return $this->entry;
    }
    
    /**
     * @param string $name
     * @param mixed $value (String or array of string)
     * @return Factory
     */
    public function set($name, $value)
    {
        if (!array_key_exists($name, $this->availableAttributes)) {
            throw new Exception(sprintf('Unavailable attribute : %s', $name));
        }
        
        $definition = $this->availableAttributes[$name];
        
        $attr = $this->entry->attrs()->get($name);
        
        if ($definition['single_valued'] && ($attr || is_array($value))) {
            throw new Exception(sprintf('Single valued attribute already defined: %s', $name));
        }
        
        if (is_array($value)) {
            foreach ($value as $v) {
                $attr = new $definition['class']($name, $v);
                $this->entry->attrs()->add($attr);
            }
        } else {
            $attr = new $definition['class']($name, $value);
            $this->entry->attrs()->add($attr);
        }
        
        return $this;
    }
    
    /**
     * 
     * @param string ObjectClassInterface
     * @return Factory
     */
    public function withClass($objectClass)
    {
        if (!$this->entry) {
            throw new Exception('The DN must be defined first.');
        }
        $this->applyClass($objectClass, $this->entry);
        return $this;
    }
    
    /**
     * 
     * @param string ObjectClassInterface
     * @param Entry
     * @return Entry
     */
    private function applyClass($objectClass, Entry $entry)
    {
        $obj = $this->getObjectClassInstance($objectClass);
        
        foreach ($obj->objectClasses() as $className) {
            $classAttr = new TextAttribute('objectClass', $className);
            $entry->attrs()->add($classAttr);
        }
        
        $this->availableAttributes = array_merge($this->availableAttributes, $obj->availableAttributes());
    }
    
    /**
     * 
     * @param string $objectClass
     * @return \Mangati\Ldap\Schema\ObjectClassInterface
     * @throws Exception
     */
    private function getObjectClassInstance($objectClass)
    {
        if (is_string($objectClass) && class_exists($objectClass)) {
            $objectClass = new $objectClass;
        }
        if ($objectClass instanceof ObjectClassInterface) {
            return $objectClass;
        }
        
        throw new Exception(sprintf('Parâmetro inválido. Precisa ser um ObjectClass. Recebido %s', $objectClass));
    }
    
}