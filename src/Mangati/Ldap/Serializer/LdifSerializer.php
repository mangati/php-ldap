<?php

namespace Mangati\Ldap\Serializer;

use Exception;
use Mangati\Ldap\Entry\Entry;
use Mangati\Serializer\SerializerInterface;

/**
 * LdifSerializer
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class LdifSerializer implements SerializerInterface
{
    
    public function serialize($data)
    {
        if (!($data instanceof Entry)) {
            throw new Exception('The serialized data must be a ldap entry.');
        }
        
        $attrs = [];
        $entry = $data;
        
        foreach ($entry->attrs() as $attr) {
            if (!empty($attr->getValue())) {
                $attrs[] = $attr;
            }
        }
        
        $lines = [];
            
        foreach ($attrs as $attr) {
            $lines[] = join(': ', [$attr->getName(), $attr->getValue()]);
        }
        
        $lines[] = "\n";
        
        return "dn: {$entry->dn()}\n" .  join("\n", $lines);
    }

    public function unserialize($data)
    {
        throw new Exception('Not implemented');
    }
}