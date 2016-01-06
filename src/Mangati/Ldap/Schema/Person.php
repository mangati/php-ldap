<?php

namespace Mangati\Ldap\Schema;

/**
 * Person
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Person extends Top
{
    
    public function objectClasses()
    {
        $base = parent::objectClasses();
        $base[] = 'person';
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
            'sn',
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
            'sn' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ],
            'userPassword' => [
                'class' => \Mangati\Ldap\Attribute\PasswordAttribute::class,
                'single_valued' => true
            ],
            'description' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ],
            'telephoneNumber' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ],
        ]);
    }

}