<?php

namespace Mangati\Ldap\Schema;

/**
 * OrganizationPerson
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class InetOrgPerson extends OrganizationalPerson
{
    public function objectClasses()
    {
        $base = parent::objectClasses();
        $base[] = 'inetOrgPerson';
        return $base;
    }

    public function availableAttributes()
    {
        $base = parent::availableAttributes();
        return array_merge($base, [
            'userid' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ]
        ]);
    }
}