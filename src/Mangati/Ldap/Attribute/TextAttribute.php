<?php

namespace Mangati\Ldap\Attribute;

/**
 * TextAttribute
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class TextAttribute extends Attribute
{
    protected function parseValue($value) 
    {
        return $value;
    }
}