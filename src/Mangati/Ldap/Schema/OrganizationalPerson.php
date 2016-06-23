<?php

namespace Mangati\Ldap\Schema;

/**
 * OrganizationPerson
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class OrganizationalPerson extends Person
{
    public function objectClasses()
    {
        $base = parent::objectClasses();
        $base[] = 'organizationalPerson';
        return $base;
    }
}
