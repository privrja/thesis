<?php

namespace App\Constant;

use App\Entity\BlockFamily;
use App\Entity\Container;
use App\Entity\Modification;
use App\Entity\Organism;
use App\Entity\SequenceFamily;
use App\Entity\Setup;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FixturesHelper {

    public static function saveModifications(Container $container, ObjectManager $manager) {
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

    public static function saveSequenceFamily(Container $container, ObjectManager $manager) {
        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('beauverolides');
        $manager->persist($family);

        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('destruxins');
        $manager->persist($family);

        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('cyclosporins');
        $manager->persist($family);

        $family = new SequenceFamily();
        $family->setContainer($container);
        $family->setSequenceFamilyName('pseudacyclins');
        $manager->persist($family);
    }

    public static function saveSetup(ObjectManager $manager) {
        $setup = new Setup();
        $setup->setSimilarity('tanimoto');
        $manager->persist($setup);
    }

    public static function saveMainUser(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder) {
        $userP = new User();
        $userP->setNick("user");
        $userP->setRoles(["ROLE_USER"]);
        $userP->setConditions(true);
        $userP->setPassword($passwordEncoder->encodePassword($userP, '12345678'));
        $manager->persist($userP);
        return $userP;
    }

    public static function saveAdmin(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder) {
        $user = new User();
        $user->setNick("admin");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setConditions(true);
        $user->setPassword($passwordEncoder->encodePassword($user, '12345678'));
        $manager->persist($user);
        return $user;
    }

    public static function saveMainBlockFamily(Container $container, ObjectManager $manager) {
        $family = new BlockFamily();
        $family->setContainer($container);
        $family->setBlockFamilyName('Proteinogenic Amino Acids');
        $manager->persist($family);
        return $family;
    }

    public static function saveMainBlocks(Container $container, BlockFamily $family, ObjectManager $manager) {
        $acids = new BaseAminoAcids($container, $family);
        $acidList = $acids->getList();
        foreach ($acidList as $block) {
            $manager->persist($block);
        }
        $acidFamily = $acids->getFamilyList();
        foreach ($acidFamily as $b2f) {
            $manager->persist($b2f);
        }
    }

    public static function saveOrganisms(Container $container, ObjectManager $manager) {
        $organisms = [
            'Actinosynnema mirum',
            'Alteromonas haloplanktis',
            'Apergillus ochraceous',
            'Aspergillus flavus',
            'Aspergillus fumigatus',
            'Aspergillus nidulans',
            'Aspergillus niger',
            'Aspergillus terreus',
            'Aureobasidium pullulans',
            'Cenococcum geophilum',
            'Cunninghamella blakesleeana',
            'Erwinia amylovora',
            'Fusarium cubense',
            'Halomonas meridiana',
            'Magnaporthe grisea',
            'Neurospora crassa',
            'Penicillium chrysogenum',
            'Pseudomonas stutzeri',
            'Rhizobium leguminosarum',
            'Serratia sp. V4',
            'Shewanella putrefaciens',
            'Streptomyces coelicolor',
            'Streptomyces olivaceus',
            'Streptomyces pilosus',
            'Suillus granulatus',
            'Suillus luteus',
            'Trichoderma hamatum',
            'Trichoderma harzianum',
            'Trichoderma koningii',
            'Trichoderma longibrachiatum',
            'Trichoderma pseudokoningii',
            'Trichoderma viride',
            'Ustilago maydis',
            'Ustilago sphaerogena',
            'Vibrio salmonicida',
            'Vibrio sp. DS40M4',
            'Vibrio strain BLI-41',
            'Vibrio vulnificus'];
        foreach($organisms as $organismName) {
            $organism = new Organism();
            $organism->setContainer($container);
            $organism->setOrganism($organismName);
            $manager->persist($organism);
            $manager->flush();
        }
    }

}
