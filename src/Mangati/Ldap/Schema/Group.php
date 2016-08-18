<?php

namespace Mangati\Ldap\Schema;

/**
 * Group
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Group extends Top
{
    
    public function objectClasses()
    {
        $base = parent::objectClasses();
        $base[] = 'group';
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
            'name' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ],
            'groupType' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ],
            'member' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ]
        ]);
    }
}
