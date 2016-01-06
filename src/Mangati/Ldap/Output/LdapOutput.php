<?php

namespace Mangati\Ldap\Output;

use Exception;
use Mangati\Ldap\Entry\Entry;
use Mangati\Ldap\Entry\Manager;
use Mangati\Ldap\Exception\AlreadyExistsException;
use Mangati\IO\OutputInterface;

/**
 * LdapOutput
 *
 * @author ralfilho
 */
class LdapOutput implements OutputInterface
{
    
    private $manager;
    
    public function __construct($host, $port, $user = null, $pass = null)
    {
        $this->manager = new Manager($host, $port, $user, $pass);
    }
    
    public function open()
    {
        $this->manager->connect();
    }
    
    public function write($data)
    {
        if (!($data instanceof Entry)) {
            throw new Exception('The serialized data must be a ldap entry.');
        }
        
        $entry = $data;
        
        try {
            try {
                $this->manager->add($entry);
            } catch (AlreadyExistsException $ex) {
                $this->manager->modify($entry);
            }
        } catch (Exception $e) {
            echo sprintf("ERROR: %s (%s) on %s \n", $e->getMessage(), $e->getCode(), $entry->dn());
        }
    }
    
    public function close()
    {
        $this->manager->close();
    }

}