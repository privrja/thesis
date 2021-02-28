<?php

namespace App\DataFixtures;

use App\Constant\BaseAminoAcids;
use App\Entity\BlockFamily;
use App\Entity\Container;
use App\Entity\Modification;
use App\Entity\SequenceFamily;
use App\Entity\Setup;
use App\Entity\U2c;
use App\Entity\User;
use App\Enum\ContainerModeEnum;
use App\Enum\ContainerVisibilityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager) {
        /* App setup */
        $setup = new Setup();
        $setup->setSimilarity('tanimoto');
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
        $this->saveModifications($container, $manager);

        $family = new BlockFamily();
        $family->setContainer($container);
        $family->setBlockFamilyName('Other');
        $manager->persist($family);

        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('beauverolide');
        $manager->persist($family);

        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('destruxin');
        $manager->persist($family);

        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('cyclosporin');
        $manager->persist($family);

        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('pseudacyclin');
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
        $this->saveModifications($container, $manager);

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

    /**
     * @inheritDoc
     */
    public static function getGroups(): array {
        return ['dev'];
    }

}
