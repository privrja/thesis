<?php

namespace App\Tests\ApiTests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainerTest extends WebTestCase {

    public function testGetContainerFree() {
        $client = static::createClient();
        $client->request('GET', '/rest/free/container');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"name":"Palma Free"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"name":"Public Container"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"visibility":"PUBLIC"', $client->getResponse()->getContent());
        $this->assertStringNotContainsString('"visibility":"PRIVATE"', $client->getResponse()->getContent());
    }

    public function testGetContainerNoAuth() {
        $client = static::createClient();
        $client->request('GET', '/rest/container');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testGetContainerAuth() {
        $client = self::loginClient();
        $client->request('GET', '/rest/container');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetSpecificContainer() {
        $client = self::loginClient();
        $client->request('GET', '/rest/container/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"name":"Palma Free"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"visibility":"PUBLIC"', $client->getResponse()->getContent());
    }

    public function testNewContainerSameName() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container', [], [], [], json_encode(['name' => 'Palma', 'visibility' => 'PRIVATE']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testNewContainerBadFormat() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container',[], [], [], json_encode(['name' => 'Palma', 'viibility' => 'PRIVATE']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testNewContainerBadValues() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container', [], [], [], json_encode(['name' => 'Palma', 'viibility' => 0]));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testNewContainerSuccess() {
        // TODO remove container after test -> when run test again test fails
        $client = self::loginClient();
        $client->request('POST', '/rest/container', [], [], [], json_encode(['name' => 'Jedle', 'visibility' => 'PRIVATE']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testUpdateContainerBadWithoutContainerId() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container', [], [], [], json_encode(['name' => 'Smrky', 'visibility' => 'PUBLIC']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    // before test drop database, create new, load fixtures.
    public function testUpdateContainerBadWrongVisibility() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container', [], [], [], json_encode(['containerId' => 2, 'name' => 'Smrky', 'visibility' => 'PUBLC']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testUpdateContainerBadWrongContainerId() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container', [], [], [], json_encode(['containerId' => 2554, 'name' => 'Smrky', 'visibility' => 'PUBLIC']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testUpdateContainerSuccessBothArguments() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container', [], [], [], json_encode(['containerId' => 2, 'name' => 'Pluma Private', 'visibility' => 'PRIVATE']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('GET', '/rest/container/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"name":"Pluma Private"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"visibility":"PRIVATE"', $client->getResponse()->getContent());
    }

    public function testUpdateContainerSuccessVisibility() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container', [], [], [], json_encode(['containerId' => 2, 'visibility' => 'PUBLIC']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('GET', '/rest/container/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"visibility":"PUBLIC"', $client->getResponse()->getContent());
    }

    public function testUpdateContainerSuccessContainerName() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container', [], [], [], json_encode(['containerId' => 2, 'name' => 'Palma Free']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('GET', '/rest/container/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"name":"Palma Free"', $client->getResponse()->getContent());
    }

    private static function loginClient() {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByNick('kokos');
        $client->loginUser($testUser);
        return $client;
    }

}
