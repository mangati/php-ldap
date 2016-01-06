<?php

namespace Mangati\Ldap\Schema;

/**
 * GroupOfNames
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class GroupOfNames extends Top
{
    
    public function objectClasses()
    {
        $base = parent::objectClasses();
        $base[] = 'groupOfNames';
        return $base;
    }
    
    public function rdnAttributeName()
    {
        return 'cn';
    }

    public function requiredAttributes()
    {
        $base = parent::requiredAttributes();
        return array_merge($base, [
            'cn',
            'member'
        ]);
    }

    public function availableAttributes()
    {
        $base = parent::availableAttributes();
        return array_merge($base, [
            'cn' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ],
            'member' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ],
            'description' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ]
        ]);
    }
    
}