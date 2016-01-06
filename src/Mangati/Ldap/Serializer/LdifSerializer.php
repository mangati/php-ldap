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
    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, array $context = [])
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

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format, array $context = [])
    {
        throw new Exception('Not implemented');
    }
}