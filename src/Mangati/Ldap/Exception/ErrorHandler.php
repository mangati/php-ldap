<?php

namespace Mangati\Ldap\Exception;

use Mangati\Ldap\Connection;

/**
 * Handler
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class ErrorHandler
{
    
    /**
     *
     * @throws BadSearchFilterException
     * @throws NoSuchObjectException
     * @throws InvalidCredentialException
     * @throws AlreadyExistsException
     * @throws LdapException
     */
    public static function throwException(Connection $conn)
    {
        $error = ldap_error($conn->getResource());
        $code = ldap_errno($conn->getResource());
        
        switch ($code) {
            case -7:
                throw new BadSearchFilterException($error, $code);
            case 32:
                throw new NoSuchObjectException($error, $code);
            case 49:
                throw new InvalidCredentialException($error, $code);
            case 68:
                throw new AlreadyExistsException($error, $code);
        }
        throw new LdapException($error, $code);
    }
}
