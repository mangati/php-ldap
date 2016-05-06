<?php

namespace Mangati\Ldap;

use Mangati\Ldap\Exception\ErrorHandler;

/**
 * Connection
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class Connection
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
    private $resource;
    
    /**
     * @var bool
     */
    private $connected;
    
    /**
     * @var bool
     */
    private $useTls;
    
    /**
     *
     * @var int
     */
    private $protocolVersion = 3;
    
    /**
     * 
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $pass
     */
    public function __construct($host, $port, $user = null, $pass = null, $useTls = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->useTls = $useTls;
        $this->resource = null;
        $this->connected = false;
    }
    
    /**
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * 
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * 
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * 
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }
    
    /**
     * 
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }
    
    /**
     * 
     * @return bool
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * 
     * @return bool
     */
    public function isTls()
    {
        return $this->useTls;
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
        
        $this->resource = @ldap_connect($this->host, $this->port);
        if ($this->resource === false) {
            $this->close();
            ErrorHandler::throwException($this);
        }
        
        ldap_set_option($this->resource, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->resource, LDAP_OPT_PROTOCOL_VERSION, $this->protocolVersion);
        
        if ($this->useTls) {
            ldap_start_tls($this->resource);
        }
        
        $bind = @ldap_bind($this->resource, $this->user, $this->pass);
        if ($bind === false) {
            $this->close();
            ErrorHandler::throwException($this);
        }
        
        $this->connected = true;
    }
    
    /**
     * 
     */
    public function close()
    {
        if ($this->resource !== null) {
            @ldap_close($this->resource);
        }
        $this->resource = null;
        $this->connected = false;
    }
}
