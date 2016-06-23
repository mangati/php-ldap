<?php

namespace Mangati\Ldap\Schema;

/**
 * OrganizationPerson
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class OrganizationalUnit extends Top
{
    
    public function objectClasses()
    {
        $base = parent::objectClasses();
        $base[] = 'organizationalUnit';
        return $base;
    }
    
    public function rdnAttributeName()
    {
        return 'ou';
    }

    public function requiredAttributes()
    {
        $base = parent::requiredAttributes();
        return array_merge($base, [
            'ou',
        ]);
    }

    public function availableAttributes()
    {
        $base = parent::availableAttributes();
        return array_merge($base, [
            'ou' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ],
            'description' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ]
        ]);
    }
}
