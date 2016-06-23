<?php

namespace Mangati\Ldap\Serializer;

use PHPUnit_Framework_TestCase;
use Mangati\Ldap\Entry\Factory;
use Mangati\Ldap\Schema\GroupOfNames;
use Mangati\Ldap\Schema\OrganizationalUnit;
use Mangati\Ldap\Schema\Person;

/**
 * LdifSerializerTest
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class LdifSerializerTest extends PHPUnit_Framework_TestCase
{
    
    private $baseDn;
    private $serializer;
    
    protected function setUp()
    {
        $this->baseDn = 'dc=company,dc=local';
        $this->serializer = new LdifSerializer();
    }

    public function testGroupOfNamesSerialize()
    {
        $entry = (new Factory())
                ->withDn('cn=test,' . $this->baseDn)
                ->withClass(GroupOfNames::class)
                ->set('description', 'testing')
                ->set('member', [
                    'cn=member1,dc=company,dc=local',
                    'cn=member2,dc=company,dc=local',
                    'cn=member3,dc=company,dc=local'
                ])
                ->build();
        
        $ldif = $this->serializer->serialize($entry);
        
        $this->assertContains('dn: cn=test,dc=company,dc=local', $ldif);
        $this->assertContains('objectClass: top', $ldif);
        $this->assertContains('objectClass: groupOfNames', $ldif);
        $this->assertContains('cn: test', $ldif);
        $this->assertContains('description: testing', $ldif);
        $this->assertContains('member: cn=member1,dc=company,dc=local', $ldif);
        $this->assertContains('member: cn=member2,dc=company,dc=local', $ldif);
        $this->assertContains('member: cn=member3,dc=company,dc=local', $ldif);
    }

    public function testPersonSerialize()
    {
        $entry = (new Factory())
                ->withDn('cn=test,' . $this->baseDn)
                ->withClass(Person::class)
                ->set('sn', 'jr')
                ->set('description', 'testing')
                ->set('telephoneNumber', '3333-3333')
                ->build();
        
        $ldif = $this->serializer->serialize($entry);
        
        $this->assertContains('dn: cn=test,dc=company,dc=local', $ldif);
        $this->assertContains('objectClass: top', $ldif);
        $this->assertContains('objectClass: person', $ldif);
        $this->assertContains('cn: test', $ldif);
        $this->assertContains('sn: jr', $ldif);
        $this->assertContains('description: testing', $ldif);
        $this->assertContains('telephoneNumber: 3333-3333', $ldif);
    }

    public function testOrganizationalUnitSerialize()
    {
        $entry = (new Factory())
                ->withDn('ou=test,' . $this->baseDn)
                ->withClass(OrganizationalUnit::class)
                ->set('description', 'desc1')
                ->set('description', 'desc2')
                ->build();
        
        $ldif = $this->serializer->serialize($entry);
        
        $this->assertContains('dn: ou=test,dc=company,dc=local', $ldif);
        $this->assertContains('objectClass: top', $ldif);
        $this->assertContains('objectClass: organizationalUnit', $ldif);
        $this->assertContains('ou: test', $ldif);
        $this->assertContains("description: desc1\ndescription: desc2", $ldif);
    }
}
