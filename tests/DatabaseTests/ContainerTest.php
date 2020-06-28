<?php


namespace App\Tests\DatabaseTests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContainerTest extends KernelTestCase {

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testSearchByName()
    {
//        $containers = $this->entityManager->getRepository(User::class)
//            ->findContainersForLoggedUser();
//        ;
//
//        $this->assertSame(1, $containers);
    }

}