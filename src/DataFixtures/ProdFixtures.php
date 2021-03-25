<?php

namespace App\DataFixtures;

use App\Constant\FixturesHelper;
use App\Entity\Container;
use App\Entity\U2c;
use App\Enum\ContainerModeEnum;
use App\Enum\ContainerVisibilityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProdFixtures extends Fixture implements FixtureGroupInterface {
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager) {
        FixturesHelper::saveSetup($manager);
        $user = FixturesHelper::saveAdmin($manager, $this->passwordEncoder);

        /* Main database data for main visible container */
        $container = new Container();
        $container->setContainerName("Nonribosomal Peptides and Siderophores");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RWM);
        $manager->persist($u2c);

        $family = FixturesHelper::saveMainBlockFamily($container, $manager);
        FixturesHelper::saveMainBlocks($container, $family, $manager);
        FixturesHelper::saveModifications($container, $manager);
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public static function getGroups(): array {
        return ['prod'];
    }
}
