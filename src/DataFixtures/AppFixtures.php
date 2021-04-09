<?php

namespace App\DataFixtures;

use App\Constant\FixturesHelper;
use App\Entity\BlockFamily;
use App\Entity\Container;
use App\Entity\U2c;
use App\Entity\User;
use App\Enum\ContainerModeEnum;
use App\Enum\ContainerVisibilityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface {
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager) {
        FixturesHelper::saveSetup($manager);
        $userP = FixturesHelper::saveMainUser($manager, $this->passwordEncoder);
        FixturesHelper::saveAdmin($manager, $this->passwordEncoder);

        $user = new User();
        $user->setNick("kokos");
        $user->setMail("kokos@palma.cz");
        $user->setRoles(["ROLE_USER"]);
        $user->setConditions(true);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'the_new_password'));
        $user->setApiToken("12345");
        $manager->persist($user);

        /* Main database data for main visible container */
        $container = new Container();
        $container->setContainerName("Public Container");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RWM);
        $manager->persist($u2c);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($userP);
        $u2c->setMode(ContainerModeEnum::RW);
        $manager->persist($u2c);

        $family = FixturesHelper::saveMainBlockFamily($container, $manager);
        FixturesHelper::saveMainBlocks($container, $family, $manager);
        FixturesHelper::saveModifications($container, $manager);
        FixturesHelper::saveSequenceFamily($container, $manager);

        $family = new BlockFamily();
        $family->setContainer($container);
        $family->setBlockFamilyName('Other');
        $manager->persist($family);

        /* Add containers for user kokos and privrja */
        $container = new Container();
        $container->setContainerName("Palma");
        $container->setVisibility(ContainerVisibilityEnum::PRIVATE);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RWM);
        $manager->persist($u2c);

        $container = new Container();
        $container->setContainerName("Palma Free");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RWM);
        $manager->persist($u2c);

        $container = new Container();
        $container->setContainerName("Testing database");
        $container->setVisibility(ContainerVisibilityEnum::PRIVATE);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($userP);
        $u2c->setMode(ContainerModeEnum::RWM);
        $manager->persist($u2c);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RW);
        $manager->persist($u2c);
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public static function getGroups(): array {
        return ['dev'];
    }

}
