<?php

namespace ZFTest\MvcAuth\Authorization;

use PHPUnit_Framework_TestCase as TestCase;
use ZF\MvcAuth\Authorization\AclAuthorizationFactory;

class AclAuthorizationFactoryTest extends TestCase
{
    public function testFactoryGeneratesAclFromConfiguration()
    {
        $config = array(
            array(
                'resource' => 'ZendCon\V1\Rest\Session\Controller::collection',
                'privileges' => array('POST'),
            ),
            array(
                'resource' => 'ZendCon\V1\Rest\Session\Controller::resource',
                'privileges' => array('PATCH', 'DELETE'),
            ),
            array(
                'resource' => 'ZendCon\V1\Rpc\Message\Controller::message',
                'privileges' => array('POST'),
            ),
        );

        $acl = AclAuthorizationFactory::factory($config);

        $this->assertInstanceOf('ZF\MvcAuth\Authorization\AclAuthorization', $acl);
        $this->assertInstanceOf('Zend\Permissions\Acl\Acl', $acl);
        $this->assertTrue($acl->hasRole('guest'));
        $this->assertFalse($acl->hasRole('authenticated'));

        // Add a non-guest role to the ACL
        $acl->addRole('authenticated');

        // Test access to a collection that has ACLs in place
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::collection', 'POST'));
        $this->assertFalse($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::collection', 'POST'));
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::collection', 'GET'));
        $this->assertTrue($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::collection', 'GET'));

        // Test access to a resource that has ACLs in place
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::resource', 'PATCH'));
        $this->assertFalse($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::resource', 'PATCH'));
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::resource', 'DELETE'));
        $this->assertFalse($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::resource', 'DELETE'));
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::resource', 'GET'));
        $this->assertTrue($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::resource', 'GET'));

        // Test access to an RPC service that has ACLs in place
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rpc\Message\Controller::message', 'POST'));
        $this->assertFalse($acl->isAllowed('guest', 'ZendCon\V1\Rpc\Message\Controller::message', 'POST'));
    }

    public function testFactoryGeneratesBlacklistAclFromConfiguration()
    {
        $config = array(
            'deny_by_default' => true,
            array(
                'resource' => 'ZendCon\V1\Rest\Session\Controller::collection',
                'privileges' => array('GET'),
            ),
            array(
                'resource' => 'ZendCon\V1\Rest\Session\Controller::resource',
                'privileges' => array('GET'),
            ),
            array(
                'resource' => 'ZendCon\V1\Rpc\Message\Controller::message',
                'privileges' => array('GET'),
            ),
        );

        $acl = AclAuthorizationFactory::factory($config);

        $this->assertInstanceOf('ZF\MvcAuth\Authorization\AclAuthorization', $acl);
        $this->assertInstanceOf('Zend\Permissions\Acl\Acl', $acl);
        $this->assertTrue($acl->hasRole('guest'));
        $this->assertFalse($acl->hasRole('authenticated'));

        // Add a non-guest role to the ACL
        $acl->addRole('authenticated');

        // Test access to a collection that has ACLs in place
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::collection', 'POST'));
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::collection', 'GET'));
        $this->assertFalse($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::collection', 'POST'));
        $this->assertTrue($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::collection', 'GET'));

        // Test access to a resource that has ACLs in place
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::resource', 'PATCH'));
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rest\Session\Controller::resource', 'GET'));
        $this->assertFalse($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::resource', 'POST'));
        $this->assertTrue($acl->isAllowed('guest', 'ZendCon\V1\Rest\Session\Controller::resource', 'GET'));

        // Test access to an RPC service that has ACLs in place
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rpc\Message\Controller::message', 'POST'));
        $this->assertTrue($acl->isAllowed('authenticated', 'ZendCon\V1\Rpc\Message\Controller::message', 'GET'));
        $this->assertFalse($acl->isAllowed('guest', 'ZendCon\V1\Rpc\Message\Controller::message', 'POST'));
        $this->assertTrue($acl->isAllowed('guest', 'ZendCon\V1\Rpc\Message\Controller::message', 'GET'));
    }
}
