<?php

namespace App\Tests\ApiTests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainerTest extends WebTestCase {

    public function testGetContainerFree() {
        $client = static::createClient();
        $client->request('GET', '/rest/container/free');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"name":"Palma Free"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"name":"Public Container"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"visibility":1', $client->getResponse()->getContent());
        $this->assertStringNotContainsString('"visibility":0', $client->getResponse()->getContent());
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

    private static function loginClient() {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByNick('kokos');
        $client->loginUser($testUser);
        return $client;
    }

}
