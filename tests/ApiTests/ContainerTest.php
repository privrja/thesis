<?php

namespace App\Tests\ApiTests;

class ContainerTest extends LoginTest {

    // before test drop database, create new, load fixtures.
    public function testGetContainerFree() {
        $client = static::createClient();
        $client->request('GET', '/rest/free/container');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"containerName":"Palma Free"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"containerName":"Public Container"', $client->getResponse()->getContent());
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
        $this->assertStringContainsString('"containerName":"Palma Free"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"visibility":"PUBLIC"', $client->getResponse()->getContent());
    }

    public function testNewContainerSameName() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container', [], [], [], json_encode(['containerName' => 'Palma', 'visibility' => 'PRIVATE']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testNewContainerBadFormat() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container',[], [], [], json_encode(['containerName' => 'Palma', 'viibility' => 'PRIVATE']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testNewContainerBadValues() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container', [], [], [], json_encode(['containerName' => 'Palma', 'visibility' => 0]));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testNewContainerSuccess() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container', [], [], [], json_encode(['containerName' => 'Jedle', 'visibility' => 'PRIVATE']));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testUpdateContainerBadWithoutContainerId() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container', [], [], [], json_encode(['containerName' => 'Smrky', 'visibility' => 'PUBLIC']));
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    public function testUpdateContainerBadWrongVisibility() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container/2', [], [], [], json_encode(['containerName' => 'Smrky', 'visibility' => 'PUBLC']));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testUpdateContainerBadWrongContainerId() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container/255', [], [], [], json_encode(['containerName' => 'Smrky', 'visibility' => 'PUBLIC']));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testUpdateContainerSuccessBothArguments() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container/2', [], [], [], json_encode(['containerName' => 'Pluma Private', 'visibility' => 'PRIVATE']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('GET', '/rest/container/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"containerName":"Pluma Private"', $client->getResponse()->getContent());
        $this->assertStringContainsString('"visibility":"PRIVATE"', $client->getResponse()->getContent());
    }

    public function testUpdateContainerSuccessVisibility() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container/2', [], [], [], json_encode(['visibility' => 'PUBLIC']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('GET', '/rest/container/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"visibility":"PUBLIC"', $client->getResponse()->getContent());
    }

    public function testUpdateContainerSuccessContainerName() {
        $client = self::loginClient();
        $client->request('PUT', '/rest/container/2', [], [], [], json_encode(['containerName' => 'Palma Free']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('GET', '/rest/container/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('"containerName":"Palma Free"', $client->getResponse()->getContent());
    }

    public function testDeleteContainerBad() {
        $client = self::loginClient();
        $client->request('DELETE', '/rest/container/88');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDeleteContainerSuccess() {
        $client = self::loginClient();
        $client->request('DELETE', '/rest/container/5');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

}
