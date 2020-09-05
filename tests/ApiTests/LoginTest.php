<?php

namespace App\Tests\ApiTests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class LoginTest extends WebTestCase {

    protected static function loginClient() {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByNick('kokos');
        $client->loginUser($testUser);
        return $client;
    }

}
