<?php

namespace Mangati\Ldap\Schema;

/**
 * User
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class User extends OrganizationalPerson
{
    
    public function objectClasses()
    {
        $base = parent::objectClasses();
        $base[] = 'user';
        return $base;
    }

    public function availableAttributes()
    {
        $base = parent::availableAttributes();
        return array_merge($base, [
            'displayName' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ],
            'givenName' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => true
            ],
            'sAMAccountName' => [
                'class' => \Mangati\Ldap\Attribute\PasswordAttribute::class,
                'single_valued' => true
            ],
            'userPrincipalName' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ],
            'userAccountControl' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ],
            'pwdLastSet' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ],
            'unicodePwd' => [
                'class' => \Mangati\Ldap\Attribute\TextAttribute::class,
                'single_valued' => false
            ],
        ]);
    }

}