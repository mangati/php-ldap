<?php

namespace Mangati\Ldap\Schema;

/**
 * Top
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Top implements ObjectClassInterface
{
    
    public function rdnAttributeName()
    {
        return 'objectClass';
    }
    
    public function objectClasses()
    {
        return ['top'];
    }

    public function requiredAttributes()
    {
        return [];
    }
    
    public function availableAttributes()
    {
        return [];
    }
}
