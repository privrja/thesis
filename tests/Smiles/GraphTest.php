<?php

namespace Bbdgnc\Test\Smiles;

use App\Enum\PeriodicTableSingleton;
use App\Smiles\Bond;
use App\Smiles\Charge;
use App\Smiles\Graph;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

final class GraphTest extends TestCase {

    public function testWithNull() {
        $this->expectException(TypeError::class);
        new Graph(null);
    }

    public function testGraph() {
        $graph = new Graph("CCC");
        $expectedGraph = new Graph('');
        for ($i = 0; $i < 3; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->smiles = "CCC";
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph2() {
        $graph = new Graph('C=C');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C=C";
        for ($i = 0; $i < 2; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addBond(0, new Bond(1, '='));
        $expectedGraph->addBond(1, new Bond(0, '='));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph3() {
        $graph = new Graph('C=C(C)C');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C=C(C)C";
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addBond(0, new Bond(1, '='));
        $expectedGraph->addBond(1, new Bond(0, '='));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(3, new Bond(1, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraphWrong() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C=C(C');
    }

    public function testGraphWrong2() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C=C=(C');
    }

    public function testGraphWrong3() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C=C((C)CC)C');
    }

    public function testGraphWrong4() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C=C(');
    }

    public function testGraphWrong5() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C=CC(CC(CC)');
    }

    public function testGraphWrong6() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('CC(Fe)C');
    }

    public function testGraphWrong7() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('CC(C))');
    }

    public function testGraphWrong8() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C%1%CC(=O)CC%1%');
    }

    public function testGraphWrong9() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C1CCC2CC1');
    }

    public function testGraph4() {
        $graph = new Graph('C=C(C)C(#O)C');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C=C(C)C(#O)C";
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addBond(0, new Bond(1, '='));
        $expectedGraph->addBond(1, new Bond(0, '='));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(3, new Bond(1, ''));
        $expectedGraph->addBond(3, new Bond(4, '#'));
        $expectedGraph->addBond(3, new Bond(5, ''));
        $expectedGraph->addBond(4, new Bond(3, '#'));
        $expectedGraph->addBond(5, new Bond(3, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph5() {
        $graph = new Graph('CC(CC(=O)C(C(Br)CC)C)C');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "CC(CC(=O)C(C(Br)CC)C)C";
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['Br']);
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(11, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, '='));
        $expectedGraph->addBond(3, new Bond(5, ''));
        $expectedGraph->addBond(4, new Bond(3, '='));
        $expectedGraph->addBond(5, new Bond(3, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(5, new Bond(10, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(6, new Bond(8, ''));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(8, new Bond(6, ''));
        $expectedGraph->addBond(8, new Bond(9, ''));
        $expectedGraph->addBond(9, new Bond(8, ''));
        $expectedGraph->addBond(10, new Bond(5, ''));
        $expectedGraph->addBond(11, new Bond(1, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph6() {
        $graph = new Graph('C1C(C)C1');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C1C(C)C1";
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(3, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(3, new Bond(1, ''));
        $expectedGraph->addBond(3, new Bond(0, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph7() {
        $graph = new Graph('C%18CC(=O)CC%18');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C%18CC(=O)CC%18";
        for ($i = 0; $i < 3; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(5, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, '='));
        $expectedGraph->addBond(2, new Bond(4, ''));
        $expectedGraph->addBond(3, new Bond(2, '='));
        $expectedGraph->addBond(4, new Bond(2, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(0, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph8() {
        $graph = new Graph('c1ccccc1');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "c1ccccc1";
        for ($i = 0; $i < 6; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['c']);
        }
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(5, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(0, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph9() {
        $graph = new Graph('CCC(C)C(NC(C)=O)C(=O)');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "CCC(C)C(NC(C)=O)C(=O)";
        for ($i = 0; $i < 5; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(4, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(4, new Bond(2, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(4, new Bond(9, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(6, new Bond(8, '='));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(8, new Bond(6, '='));
        $expectedGraph->addBond(9, new Bond(4, ''));
        $expectedGraph->addBond(9, new Bond(10, '='));
        $expectedGraph->addBond(10, new Bond(9, '='));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph10() {
        $graph = new Graph('C1C(C)(C)C1');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C1C(C)(C)C1";
        for ($i = 0; $i < 5; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(4, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(3, ''));
        $expectedGraph->addBond(1, new Bond(4, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(3, new Bond(1, ''));
        $expectedGraph->addBond(4, new Bond(1, ''));
        $expectedGraph->addBond(4, new Bond(0, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph11() {
        $graph = new Graph('C(C(CC)N)(=O)O');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C(C(CC)N)(=O)O";
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(5, '='));
        $expectedGraph->addBond(0, new Bond(6, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(4, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(4, new Bond(1, ''));
        $expectedGraph->addBond(5, new Bond(0, '='));
        $expectedGraph->addBond(6, new Bond(0, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph12() {
        $graph = new Graph('CC[NH4]C');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "CC[NH4]C";
        for ($i = 0; $i < 2; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['N']->asBracketElement();
        $atom->setHydrogens(4);
        $expectedGraph->addNode($atom);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testGraph13() {
        $graph = new Graph('CC#[NH4]C');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "CC#[NH4]C";
        for ($i = 0; $i < 2; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['N']->asBracketElement();
        $atom->setHydrogens(4);
        $expectedGraph->addNode($atom);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, '#'));
        $expectedGraph->addBond(2, new Bond(1, '#'));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testGraph14() {
        $graph = new Graph('C1CCC[NH]1');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C1CCC[NH]1";
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['N']->asBracketElement();
        $atom->setHydrogens(1);
        $expectedGraph->addNode($atom);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(4, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(0, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph15() {
        $graph = new Graph('C1CCC[N]1');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C1CCC[N]1";
        for ($i = 0; $i < 4; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['N']->asBracketElement();
        $expectedGraph->addNode($atom);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(4, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(0, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph16() {
        $graph = new Graph('CCC[O+]');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "CCC[O+]";
        for ($i = 0; $i < 3; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['O']->asBracketElement();
        $atom->setCharge(new Charge('+', 1));
        $expectedGraph->addNode($atom);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraph17() {
        $graph = new Graph('[CH4]CC=[nH-3]');
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['C']->asBracketElement();
        $atom->setHydrogens(4);
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "[CH4]CC=[nH-3]";
        $expectedGraph->addNode($atom);
        for ($i = 0; $i < 2; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['n']->asBracketElement();
        $atom->setCharge(new Charge('-', 3));
        $atom->setHydrogens(1);
        $expectedGraph->addNode($atom);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, '='));
        $expectedGraph->addBond(3, new Bond(2, '='));
        $this->assertEquals($graph, $expectedGraph);
    }

    public function testGraphWrong10() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('[CH4]CC=');
    }

    public function testGraphWrong11() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('[CH4CC');
    }

    public function testGraphWrong12() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C1CC[C1]');
    }

    public function testGraphWrong13() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('C1CC[CH0]1');
    }

    public function testGraphWrong14() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('CCC[C---]');
    }

//    public function testGraphWrong15() {
//        $this->expectException(InvalidArgumentException::class);
//        new Graph('CCC[H]');
//    }

    public function testGraphWrong16() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('CCC[]');
    }

    public function testGraphWrong17() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('CCC=1CC');
    }

    public function testGraphWrong18() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('CCC=1CC=2');
    }

    public function testgraphwrong19() {
        $this->expectException(InvalidArgumentException::class);
        new graph('ccc(=1)cc1');
    }

    public function testGraphWrong20() {
        $this->expectException(InvalidArgumentException::class);
        new Graph('CCC=1CC=CC1');
    }

    public function testGraphRight18() {
        $graph = new Graph('OC(=O)C(Cc1ccccc1)N');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "OC(=O)C(Cc1ccccc1)N";
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        for ($i = 0; $i < 2; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        for ($i = 0; $i < 6; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['c']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, '='));
        $expectedGraph->addBond(1, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(1, '='));
        $expectedGraph->addBond(3, new Bond(1, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(3, new Bond(11, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(5, new Bond(10, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(7, new Bond(8, ''));
        $expectedGraph->addBond(8, new Bond(7, ''));
        $expectedGraph->addBond(8, new Bond(9, ''));
        $expectedGraph->addBond(9, new Bond(8, ''));
        $expectedGraph->addBond(9, new Bond(10, ''));
        $expectedGraph->addBond(10, new Bond(9, ''));
        $expectedGraph->addBond(10, new Bond(5, ''));
        $expectedGraph->addBond(11, new Bond(3, ''));
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testGraphRight19() {
        $graph = new Graph('Cc1ccccc1');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "Cc1ccccc1";
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        for ($i = 0; $i < 6; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['c']);
        }
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(6, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(1, ''));
        $this->assertEquals($expectedGraph, $graph);
        $this->assertEquals(true, $graph->isAromaticRing([1, 2, 3, 4, 5, 6]));
    }

    public function testGraphRight20() {
        $graph = new Graph('Cc1c[nH]cn1');
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "Cc1c[nH]cn1";
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        for ($i = 0; $i < 2; $i++) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['c']);
        }
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['n']->asBracketElement();
        $atom->setHydrogens(1);
        $expectedGraph->addNode($atom);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['c']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['n']);

        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(5, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(1, ''));
        $this->assertEquals($expectedGraph, $graph);
        $this->assertEquals(true, $graph->isAromaticRing([1, 2, 3, 4, 5]));
    }

}
