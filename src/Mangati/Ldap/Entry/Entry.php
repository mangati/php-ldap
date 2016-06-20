<?php

namespace Mangati\Ldap\Entry;

use Mangati\Ldap\Attribute\Attribute;
use Mangati\Ldap\Attribute\AttributeCollection;
use Mangati\Ldap\Attribute\TextAttribute;

/**
 * Entry
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Entry
{
    
    /**
     * @var string
     */
    private $dn;
    
    /**
     * @var string
     */
    private $parent;
    
    /**
     * @var Attribute
     */
    private $rdn;
    
    /**
     * @var AttributeCollection
     */
    private $attributes;
    
    /**
     * Se o $parent for informado, o valor de $dnOrRdn deve ser apenas o nome do atributo
     * 
     * new Entry('cn=teste', 'dc=site')
     * new Entry('cn=teste,dc=site')
     * 
     * @param string $dnOrRdn
     * @param string $parent
     */
    public function __construct($dnOrRdn, $parent = null)
    {
        if ($parent === null) {
            $parent = substr($dnOrRdn, strpos($dnOrRdn, ',') + 1);
            $rdn = explode('=', substr($dnOrRdn, 0, strpos($dnOrRdn, ',')));
            $dn = $dnOrRdn;
        } else {
            $rdn = $dnOrRdn;
            $dn = "{$rdn},$parent";
        }
        
        $this->dn = $dn;
        $this->parent = $parent;
        $this->rdn = new TextAttribute($rdn[0], $rdn[1]);
        $this->attributes = new AttributeCollection();
        $this->attributes->add($this->rdn);
    }
    
    /**
     * 
     * @return string
     */
    public function dn()
    {
        return $this->dn;
    }
    
    /**
     * 
     * @return Attribute
     */
    public function rdn()
    {
        return $this->rdn;
    }
    
    public function parent()
    {
        return $this->parent;
    }
    
    public function is($objectClass)
    {
        foreach ($this->attributes->getAll('objectClass') as $attr) {
            if (strcasecmp($attr->getValue(), $objectClass)) {
                return true;
            }
        }
        
        return false;
    }
        
    /**
     * @return AttributeCollection
     */
    public function attrs() 
    {
        return $this->attributes;
    }
}
