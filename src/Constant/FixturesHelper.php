<?php

namespace App\Constant;

use App\Entity\Container;
use App\Entity\Modification;
use App\Entity\SequenceFamily;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

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

    public static function saveSequenceFamily(Container $container, EntityManagerInterface $manager) {
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
    }

}
