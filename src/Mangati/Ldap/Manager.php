<?php

namespace Mangati\Ldap;

use Mangati\Ldap\Entry\Entry;
use Mangati\Ldap\Exception\ErrorHandler;
use Mangati\Ldap\Attribute\TextAttribute;
use Mangati\Ldap\Exception\LdapException;

/**
 * Manager
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Manager
{
    /**
     * @var Connection
     */
    private $conn;
    
    /**
     *
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }
    
    private function checkConnection()
    {
        if (!$this->conn->isConnected()) {
            throw new LdapException('Not connected to server.');
        }
    }

    /**
     * Get the LDAP entry by DN
     * @param  string $dn
     * @param  array  $attributes
     * @return Entry
     */
    public function get($dn, array $attributes = [])
    {
        $this->checkConnection();
        $resource = $this->conn->getResource();

        $filter="(objectclass=*)";
        $search = @ldap_read($resource, $dn, $filter, $attributes);
        if ($search === false) {
            ErrorHandler::throwException($this->conn);
        }
        $result = @ldap_get_entries($resource, $search);
        if ($result === false) {
            ErrorHandler::throwException($this->conn);
        }

        $entry = null;

        if ($result['count'] > 0) {
            $row = $result[0];
            $entry = $this->parseArray($row);
        }

        return $entry;
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
        $resource = $this->conn->getResource();
        
        $search = @ldap_search($resource, $baseDn, $filter, $attributes);
        if ($search === false) {
            ErrorHandler::throwException($this->conn);
        }
        $result = @ldap_get_entries($resource, $search);
        if ($result === false) {
            ErrorHandler::throwException($this->conn);
        }
        for ($i = 0; $i < $result['count']; $i++) {
            $row = $result[$i];
            $entry = $this->parseArray($row);
            yield $entry;
        }
    }
    
    /**
     *
     * @param type $baseDn
     * @param type $filter
     * @param array $attributes
     * @param int $pageSize
     * @return \Generator
     * @throws LdapException
     */
    public function pagedSearch($baseDn, $filter, array $attributes = [], $pageSize = 100)
    {
        $this->checkConnection();
        $resource = $this->conn->getResource();
        
        $cookie = '';
        do {
            ldap_control_paged_result($resource, $pageSize, true, $cookie);

            $search = @ldap_search($resource, $baseDn, $filter, $attributes);
            if ($search === false) {
                ErrorHandler::throwException($this->conn);
            }
            
            $result = @ldap_get_entries($resource, $search);
            if ($result === false) {
                ErrorHandler::throwException($this->conn);
            }

            for ($i = 0; $i < $result['count']; $i++) {
                $row = $result[$i];
                $entry = $this->parseArray($row);
                yield $entry;
            }

            ldap_control_paged_result_response($resource, $search, $cookie);

        } while ($cookie !== null && $cookie != '');
    }
    
    /**
     *
     * @param \Mangati\Ldap\Entry\Entry $entry
     * @throws LdapException
     */
    public function add(Entry $entry)
    {
        $this->checkConnection();
        $resource = $this->conn->getResource();
        
        $attrs = $this->convertToArray($entry);
        $rs = @ldap_add($resource, $entry->dn(), $attrs);
        if ($rs === false) {
            ErrorHandler::throwException($this->conn);
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
        $resource = $this->conn->getResource();
        
        // ignore RDN and DN: Cannot modify, need do rename
        $attrs = $this->convertToArray($entry, [$entry->rdn()->getName(), 'DN', 'dn', 'distinguishedName']);
        
        $rs = @ldap_modify($resource, $entry->dn(), $attrs);
        if ($rs === false) {
            ErrorHandler::throwException($this->conn);
        }
    }
    
    /**
     *
     * @param string $dn
     * @param string $newDn
     * @throws LdapException
     */
    public function rename($dn, $newDn, $deleteOldRdn = false)
    {
        $this->checkConnection();
        $resource = $this->conn->getResource();
        
        $newrdn = substr($newDn, 0, strpos($newDn, ','));
        $newparent = substr($newDn, strpos($newDn, ',') + 1);
        
        $rs = @ldap_rename($resource, $dn, $newrdn, $newparent, $deleteOldRdn);
        if ($rs === false) {
            ErrorHandler::throwException($this->conn);
        }
    }
    
    /**
     *
     * @param string $dn
     * @throws LdapException
     */
    public function delete($dn)
    {
        $this->checkConnection();
        $resource = $this->conn->getResource();
        
        $rs = @ldap_delete($resource, $dn);
        if ($rs === false) {
            ErrorHandler::throwException($this->conn);
        }
    }
    
    /**
     * @return bool
     */
    public function isConnected()
    {
        $this->conn->isConnected();
    }
    
    /**
     *
     */
    public function connect()
    {
        $this->conn->connect();
    }
    
    /**
     *
     */
    public function close()
    {
        $this->checkConnection();
        
        $this->conn->close();
    }
    
    /**
     *
     * @param \Mangati\Ldap\Entry\Entry $entry
     * @return array
     */
    private function convertToArray(Entry $entry, $ignore = [])
    {
        $attrs = [];
        
        foreach ($entry->attrs() as $attr) {
            if (empty($attr->getValue()) || in_array($attr->getName(), $ignore)) {
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
            if (strcasecmp($attrName, $entry->rdn()->getName()) === 0) {
                continue;
            }
            $values = $row[$attrName];
            for ($k = 0; $k < $values['count']; $k++) {
                $value = $values[$k];
                $attr = new TextAttribute($attrName, $value);
                $entry->attrs()->add($attr);
            }
        }
        
        return $entry;
    }
}
