<?php


namespace App\Tests\DatabaseTests;

use App\Constant\EntityColumnsEnum;
use App\Entity\Container;
use App\Entity\User;
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

    /**
     * This test if there are only 2 rows in container table with visibility setup to 1 (PUBLIC)
     */
    public function testFindVisibleContainers() {
        $containers = $this->entityManager->getRepository(Container::class)
            ->findBy([EntityColumnsEnum::CONTAINER_VISIBILITY => 'PUBLIC']);
        $cnt = 0;
        foreach ($containers as $container) {
            if ($container->getContainerName() !== 'Palma Free' && $container->getContainerName() !== 'Public Container') {
                self::assertFalse(true);
            }
            ++$cnt;
        }
        self::assertSame(1, $cnt);
    }

    /**
     * This test if there are only one db for user privrja. The result is not Container
     */
    public function testFindContainers() {
        $userRepository = $this->entityManager->getRepository(User::class);
        $usr = $userRepository->findOneBy([EntityColumnsEnum::USER_NICK => 'privrja']);
        $containers = $userRepository->findContainersForLoggedUser($usr->getId());
        /** @var Container $container */
        $counter = 0;
        foreach ($containers as $container) {
            if ($container[EntityColumnsEnum::CONTAINER_NAME] !== 'Testing database' && $container[EntityColumnsEnum::CONTAINER_NAME] !== 'Public Container') {
                self::assertFalse(true);
            }
            $counter++;
        }
        self::assertSame(2, $counter);
    }

}
