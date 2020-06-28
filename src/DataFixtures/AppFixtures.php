<?php

namespace App\DataFixtures;

use App\Constant\BaseAminoAcids;
use App\Constant\ContainerModeEnum;
use App\Constant\ContainerVisibilityEnum;
use App\Entity\Container;
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
        $user = new User();
        $user->setNick("privrja");
        $user->setMail("privrja@gmail.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'nic'));
        $manager->persist($user);

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

        /* Add containers for user kokos */
        $container = new Container();
        $container->setName("Palma");
        $container->setVisibility(ContainerVisibilityEnum::PRIVATE);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RW);
        $manager->persist($u2c);

        $container = new Container();
        $container->setName("Palma Free");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($user);
        $u2c->setMode(ContainerModeEnum::RW);
        $manager->persist($u2c);

        /* Main database data for main visible container */
        $container = new Container();
        $container->setName("Public Container");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $acids = new BaseAminoAcids($container);
        $acidList = $acids->getList();
        foreach ($acidList as $block) {
            $manager->persist($block);
        }

        $manager->flush();
    }

}
