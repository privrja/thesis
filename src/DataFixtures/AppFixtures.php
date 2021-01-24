<?php

namespace App\DataFixtures;

use App\Constant\BaseAminoAcids;
use App\Constant\ContainerModeEnum;
use App\Constant\ContainerVisibilityEnum;
use App\Entity\Container;
use App\Entity\Modification;
use App\Entity\U2c;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager) {

        /* Testing users */
        $userP = new User();
        $userP->setNick("privrja");
        $userP->setMail("privrja@gmail.com");
        $userP->setRoles(["ROLE_USER"]);
        $userP->setPassword($this->passwordEncoder->encodePassword($userP, 'nic'));
        $manager->persist($userP);

        $user = new User();
        $user->setNick("admin");
        $user->setMail("admin");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'kokos'));
        $manager->persist($user);

        $user = new User();
        $user->setNick("kokos");
        $user->setMail("kokos@palma.cz");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'the_new_password'));
        $user->setApiToken("12345");
        $manager->persist($user);

        /* Add containers for user kokos and privrja */
        $container = new Container();
        $container->setContainerName("Palma");
        $container->setVisibility(ContainerVisibilityEnum::PRIVATE);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RW);
        $manager->persist($u2c);

        $container = new Container();
        $container->setContainerName("Palma Free");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RW);
        $manager->persist($u2c);

        $container = new Container();
        $container->setContainerName("Testing database");
        $container->setVisibility(ContainerVisibilityEnum::PRIVATE);
        $manager->persist($container);

        $this->saveModifications($container, $manager);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($userP);
        $u2c->setMode(ContainerModeEnum::RW);
        $manager->persist($u2c);

        /* Main database data for main visible container */
        $container = new Container();
        $container->setContainerName("Public Container");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $acids = new BaseAminoAcids($container);
        $acidList = $acids->getList();
        foreach ($acidList as $block) {
            $manager->persist($block);
        }

        $this->saveModifications($container, $manager);

        $manager->flush();
    }

    public function saveModifications(Container $container, ObjectManager $manager) {
        $modification = new Modification();
        $modification->setContainer($container);
        $modification->setModificationName('Acetyl');
        $modification->setModificationFormula('H2C2O');
        $modification->setModificationMass(42.0105650000);
        $modification->setNTerminal(true);
        $modification->setCTerminal(false);
        $manager->persist($modification);

        $modification = new Modification();
        $modification->setContainer($container);
        $modification->setModificationName('Amidated');
        $modification->setModificationFormula('HNO-1');
        $modification->setModificationMass(-0.9840155848);
        $modification->setNTerminal(false);
        $modification->setCTerminal(true);
        $manager->persist($modification);
    }

}
