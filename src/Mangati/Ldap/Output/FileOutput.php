<?php

namespace Mangati\Ldap\Output;

use Mangati\Ldap\Serializer\LdifSerializer;
use Mangati\IO\OutputInterface;

/**
 * FileOutput
 *
 * @author ralfilho
 */
class FileOutput implements OutputInterface
{
    
    /**
     *
     * @var resource
     */
    private $handler;
    
    /**
     * @var string
     */
    private $filename;
    
    /**
     * @var LdifSerializer
     */
    private $serializer;
    
    public function __construct($filename, LdifSerializer $serializer)
    {
        $this->filename = $filename;
        $this->serializer = $serializer;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }
    
    public function open()
    {
        $this->handler = fopen($this->filename, 'w');
    }
    
    public function write($data)
    {
        $serialized = $this->serializer->serialize($data);
        fwrite($this->handler, $serialized);
    }
    
    public function close()
    {
        fclose($this->handler);
    }

}