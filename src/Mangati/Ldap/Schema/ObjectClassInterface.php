<?php

namespace Mangati\Ldap\Schema;

/**
 * ObjectClass
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
interface ObjectClassInterface
{

    
    /**
     * @return array
     */
    public function objectClasses();
    
    /**
     * @return array
     */
    public function requiredAttributes();
    
    /**
     * @return array
     */
    public function availableAttributes();
    
    /**
     * @return string
     */
    public function rdnAttributeName();
}
