<?php

namespace Mangati\Ldap\Attribute;

/**
 * UnicodeAttribute
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class UnicodeAttribute extends Attribute
{
    
    protected function parseValue($value)
    {
        return $this->encodePassword($value);
    }
    
    private function encodePassword($password) 
    {
        $password = "\"".$password."\"";
        $encoded = "";
        for ($i = 0; $i < strlen($password); $i++) { 
            $encoded .= "{$password{$i}}\000"; 
        }
        return $encoded;
    }
    
}