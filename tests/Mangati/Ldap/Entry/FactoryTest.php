<?php

namespace Mangati\Ldap\Entry;

use PHPUnit_Framework_TestCase;
use Mangati\Ldap\Entry\Factory;
use Mangati\Ldap\Schema\GroupOfNames;
use Mangati\Ldap\Schema\OrganizationalUnit;
use Mangati\Ldap\Schema\Person;

/**
 * FactoryTest
 *
 * @author Rogério Lino <rogeriolino@gmail.com>
 */
class FactoryTest extends PHPUnit_Framework_TestCase
{
    
    private $baseDn;
    
    protected function setUp()
    {
        $this->baseDn = 'dc=company,dc=local';
    }

    public function testGroupOfNamesSerialize()
    {
        $entry = (new Factory())
                ->withDn('cn=test,' . $this->baseDn)
                ->withClass(GroupOfNames::class)
                ->set('description', 'testing')
                ->build();
        ;
        
        $this->assertTrue($entry->is('groupOfNames'));
        $this->assertTrue($entry->is('top'));
        $this->assertEquals('cn=test,dc=company,dc=local', $entry->dn());
        $this->assertEquals('testing', $entry->attrs()->get('description')->getValue());
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
        
        $this->assertTrue($entry->is('person'));
        $this->assertTrue($entry->is('top'));
        $this->assertEquals('cn=test,dc=company,dc=local', $entry->dn());
        $this->assertEquals('testing', $entry->attrs()->get('description')->getValue());
        $this->assertEquals('3333-3333', $entry->attrs()->get('telephoneNumber')->getValue());
        $this->assertEquals('jr', $entry->attrs()->get('sn')->getValue());
    }

    public function testOrganizationalUnitSerialize()
    {
        $entry = (new Factory())
                ->withDn('ou=test,' . $this->baseDn)
                ->withClass(OrganizationalUnit::class)
                ->set('description', 'desc1')
                ->set('description', 'desc2')
                ->build();
        
        $descriptions = iterator_to_array($entry->attrs()->getAll('description'));
        
        $this->assertTrue($entry->is('organizationalUnit'));
        $this->assertTrue($entry->is('top'));
        $this->assertEquals('ou=test,dc=company,dc=local', $entry->dn());
        $this->assertEquals('desc1', $descriptions[0]->getValue());
        $this->assertEquals('desc2', $descriptions[1]->getValue());
    }
}
