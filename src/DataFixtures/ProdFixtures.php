<?php

namespace App\DataFixtures;

use App\Constant\BaseAminoAcids;
use App\Constant\FixturesHelper;
use App\Entity\BlockFamily;
use App\Entity\Container;
use App\Entity\Setup;
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
        /* App setup */
        $setup = new Setup();
        $setup->setSimilarity('name');
        $manager->persist($setup);

        /* Testing users */
        $userP = new User();
        $userP->setNick("privrja");
        $userP->setMail("privrja@gmail.com");
        $userP->setRoles(["ROLE_USER"]);
        $userP->setConditions(true);
        $userP->setPassword($this->passwordEncoder->encodePassword($userP, 'nic'));
        $manager->persist($userP);

        $user = new User();
        $user->setNick("admin");
        $user->setMail("admin");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setConditions(true);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'kokos'));
        $manager->persist($user);

        /* Main database data for main visible container */
        $container = new Container();
        $container->setContainerName("Nonribosomal Peptides and Siderophores");
        $container->setVisibility(ContainerVisibilityEnum::PUBLIC);
        $manager->persist($container);

        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($userP);
        $u2c->setMode(ContainerModeEnum::RWM);
        $manager->persist($u2c);

        $family = new BlockFamily();
        $family->setContainer($container);
        $family->setBlockFamilyName('Proteinogenic Amino Acids');
        $manager->persist($family);

        $acids = new BaseAminoAcids($container, $family);
        $acidList = $acids->getList();
        foreach ($acidList as $block) {
            $manager->persist($block);
        }
        $acidFamily = $acids->getFamilyList();
        foreach ($acidFamily as $b2f) {
            $manager->persist($b2f);
        }

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
