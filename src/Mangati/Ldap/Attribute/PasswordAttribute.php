<?php

namespace Mangati\Ldap\Attribute;

/**
 * PasswordAttribute
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class PasswordAttribute extends Attribute
{
    
    const MD5     = 'md5';
    const SHA1    = 'sha';
    const SHA256  = 'sha256';
    
    protected function parseValue($value)
    {
        $algorithm = $this->getAlgorithm();
        $hash = hash($algorithm, $value);
        return sprintf("{%s}%s", $algorithm, base64_encode(pack("H*", $hash)));
    }
    
    private function getAlgorithm()
    {
        $options = $this->getOptions();
        if (isset($options['algorithm'])) {
            return $options['algorithm'];
        }
        return self::SHA256;
    }
}
