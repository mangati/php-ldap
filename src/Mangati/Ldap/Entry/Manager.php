<?php

namespace Mangati\Ldap\Entry;

use Mangati\Ldap\Attribute\TextAttribute;
use Mangati\Ldap\Exception\AlreadyExistsException;
use Mangati\Ldap\Exception\BadSearchFilterException;
use Mangati\Ldap\Exception\InvalidCredentialException;
use Mangati\Ldap\Exception\LdapException;
use Mangati\Ldap\Exception\NoSuchObjectException;

/**
 * Manager
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Manager
{
    
    /**
     * @var string
     */
    private $host;
    
    /**
     * @var int
     */
    private $port;
    
    /**
     * @var string
     */
    private $user;
    
    /**
     * @var string
     */
    private $pass;
    
    /**
     * @var resource
     */
    private $conn;
    
    /**
     * @var resource
     */
    private $bind;
    
    /**
     * @var bool
     */
    private $connected;
    
    /**
     * 
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $pass
     */
    public function __construct($host, $port, $user = null, $pass = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->connected = false;
    }
    
    /**
     * 
     * @throws LdapException
     */
    public function connect()
    {
        if (!extension_loaded('ldap')) {
            throw new LdapException('The ldap module is needed.');
        }
        
        $this->conn = @ldap_connect($this->host, $this->port);
        if ($this->conn === false) {
            $this->throwException();
        }
        
        ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        
        $this->bind = @ldap_bind($this->conn, $this->user, $this->pass);
        if ($this->bind === false) {
            $this->throwException();
        }
        
        $this->connected = true;
    }
    
    private function checkConnection()
    {
        if (!$this->connected) {
            throw new LdapException('Not connected to server.');
        }
    }
    
    /**
     * 
     * @param type $baseDn
     * @param type $filter
     * @param array $attributes
     * @return \Generator
     * @throws LdapException
     */
    public function search($baseDn, $filter, array $attributes = [])
    {
        $this->checkConnection();
        
        $search = @ldap_search($this->conn, $baseDn, $filter, $attributes);
        if ($search === false) {
            $this->throwException();
        }
        $result = @ldap_get_entries($this->conn, $search);
        if ($result === false) {
            $this->throwException();
        }
        for ($i = 0; $i < $result['count']; $i++) {
            $row = $result[$i];
            $entry = $this->parseArray($row);
            yield $entry;
        }
    }
    
    /**
     * 
     * @param \Mangati\Ldap\Entry\Entry $entry
     * @throws LdapException
     */
    public function add(Entry $entry)
    {
        $this->checkConnection();
        
        $attrs = $this->convertToArray($entry);
        $rs = @ldap_add($this->conn, $entry->dn(), $attrs);
        if ($rs === false) {
            $this->throwException();
        }
    }
    
    /**
     * 
     * @param \Mangati\Ldap\Entry\Entry $entry
     * @throws LdapException
     */
    public function modify(Entry $entry)
    {
        $this->checkConnection();
        
        $attrs = $this->convertToArray($entry);
        $rs = @ldap_modify($this->conn, $entry->dn(), $attrs);
        if ($rs === false) {
            $this->throwException();
        }
    }
    
    /**
     * 
     * @param type $dn
     * @param type $newDn
     * @throws LdapException
     */
    public function rename($dn, $newDn)
    {
        $this->checkConnection();
        
        $newrdn = substr($newDn, 0, strpos($newDn, ','));
        $newparent = substr($newDn, strpos($newDn, ',') + 1);
        
        $rs = @ldap_rename($this->conn, $dn, $newrdn, $newparent, false);
        if ($rs === false) {
            $this->throwException();
        }
    }
    
    /**
     * 
     * @param type $dn
     * @throws LdapException
     */
    public function delete($dn)
    {
        $this->checkConnection();
        
        $rs = @ldap_delete($this->conn, $dn);
        if ($rs === false) {
            $this->throwException();
        }
    }
    
    public function close()
    {
        $this->checkConnection();
        
        @ldap_close($this->conn);
    }
    
    /**
     * 
     * @param \Mangati\Ldap\Entry\Entry $entry
     * @return array
     */
    private function convertToArray(Entry $entry)
    {
        $attrs = [];
        
        foreach ($entry->attrs() as $attr) {
            if (empty($attr->getValue())) {
                continue;
            }
            
            if (array_key_exists($attr->getName(), $attrs)) {
                $currentValue = $attrs[$attr->getName()];
                if (!is_array($currentValue)) {
                    $attrs[$attr->getName()] = [$currentValue];
                }
                $attrs[$attr->getName()][] = $attr->getValue();
                
            } else {
                $attrs[$attr->getName()] = $attr->getValue();
            }
        }
        
        return $attrs;
    }
    
    /**
     * @param array $row
     * @return \Mangati\Ldap\Entry\Entry
     */
    private function parseArray(array $row)
    {
        $dn = $row['dn'];
        $entry = new Entry($dn);
        for ($j = 0; $j < $row['count']; $j++) {
            $attrName = $row[$j];
            $values = $row[$attrName];
            for ($k = 0; $k < $values['count']; $k++) {
                $value = $values[$k];
                $attr = new TextAttribute($attrName, $value);
                $entry->attrs()->add($attr);
            }
        }
        return $entry;
    }
    
    private function throwException() 
    {
        $error = ldap_error($this->conn);
        $code = ldap_errno($this->conn);
        
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
