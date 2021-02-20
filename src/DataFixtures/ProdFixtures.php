<?php

namespace App\DataFixtures;

use App\Constant\BaseAminoAcids;
use App\Entity\Container;
use App\Entity\Modification;
use App\Entity\U2c;
use App\Entity\User;
use App\Enum\ContainerModeEnum;
use App\Enum\ContainerVisibilityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProdFixtures extends Fixture implements FixtureGroupInterface
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

        /* Main database data for main visible container */
        $container = new Container();
        $container->setContainerName("Public Container");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($userP);
        $u2c->setMode(ContainerModeEnum::RWM);
        $manager->persist($u2c);

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

        $modification = new Modification();
        $modification->setContainer($container);
        $modification->setModificationName('Ethanolamine');
        $modification->setModificationFormula('H5C2N');
        $modification->setModificationMass(43.0421991657);
        $modification->setNTerminal(false);
        $modification->setCTerminal(true);
        $manager->persist($modification);

        $modification = new Modification();
        $modification->setContainer($container);
        $modification->setModificationName('Formyl');
        $modification->setModificationFormula('CO');
        $modification->setModificationMass(27.9949146221);
        $modification->setNTerminal(true);
        $modification->setCTerminal(false);
        $manager->persist($modification);
    }

    /**
     * @inheritDoc
     */
    public static function getGroups(): array {
        return ['prod'];
    }
}
