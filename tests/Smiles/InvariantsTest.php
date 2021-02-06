<?php

namespace App\Test\Smiles;

use App\Enum\PeriodicTableSingleton;
use App\Smiles\Bond;
use App\Smiles\Graph;
use PHPUnit\Framework\TestCase;

final class InvariantsTest extends TestCase {

    public function testWithRightData() {
        $graph = new Graph('C12C3C4C1C5C4C3C25');
        $graph->computeInvariants();
        $graph->rankInvariants();
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C12C3C4C1C5C4C3C25";
        for ($i = 0; $i < 8; ++$i) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(3, ''));
        $expectedGraph->addBond(0, new Bond(7, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(6, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(5, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(0, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(4, new Bond(7, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(2, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(1, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(7, new Bond(0, ''));
        $expectedGraph->addBond(7, new Bond(4, ''));
        foreach ($expectedGraph->getNodes() as $node) {
            $node->setInvariant(30306001);
            $node->getCangenStructure()->setLastRank(1);
            $node->getCangenStructure()->setRank(1);
        }
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testWithRightData2() {
        $graph = new Graph('OCC(CC)CCC(CN)CN');
        $graph->computeInvariants();
        $graph->rankInvariants();
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "OCC(CC)CCC(CN)CN";
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        for ($i = 0; $i < 8; ++$i) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(5, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(5, new Bond(2, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(7, new Bond(8, ''));
        $expectedGraph->addBond(7, new Bond(10, ''));
        $expectedGraph->addBond(8, new Bond(7, ''));
        $expectedGraph->addBond(8, new Bond(9, ''));
        $expectedGraph->addBond(9, new Bond(8, ''));
        $expectedGraph->addBond(10, new Bond(7, ''));
        $expectedGraph->addBond(10, new Bond(11, ''));
        $expectedGraph->addBond(11, new Bond(10, ''));
        $this->setNodeInvariant($expectedGraph, [0], 10108001, 3);
        $this->setNodeInvariant($expectedGraph, [1, 3, 5, 6, 8, 10], 20206002, 4);
        $this->setNodeInvariant($expectedGraph, [2, 7], 30306001, 5);
        $this->setNodeInvariant($expectedGraph, [4], 10106003, 1);
        $this->setNodeInvariant($expectedGraph, [9, 11], 10107002, 2);
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testCangen() {
        $graph = new Graph('C12C3C4C1C5C4C3C25');
        $graph->cangen();
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "C12C3C4C1C5C4C3C25";
        for ($i = 0; $i < 8; ++$i) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(0, new Bond(3, ''));
        $expectedGraph->addBond(0, new Bond(7, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(1, new Bond(6, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(5, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(0, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(4, new Bond(5, ''));
        $expectedGraph->addBond(4, new Bond(7, ''));
        $expectedGraph->addBond(5, new Bond(4, ''));
        $expectedGraph->addBond(5, new Bond(2, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(1, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(7, new Bond(0, ''));
        $expectedGraph->addBond(7, new Bond(4, ''));
        $this->setNodeAll($expectedGraph, [0], 2, 1, 1, 105);
        $this->setNodeAll($expectedGraph, [1], 4, 2, 2, 286);
        $this->setNodeAll($expectedGraph, [2], 8, 5, 5, 285);
        $this->setNodeAll($expectedGraph, [3], 5, 3, 3, 374);
        $this->setNodeAll($expectedGraph, [4], 10, 7, 7, 665);
        $this->setNodeAll($expectedGraph, [5], 12, 8, 8, 2431);
        $this->setNodeAll($expectedGraph, [6], 8, 6, 6, 399);
        $this->setNodeAll($expectedGraph, [7], 6, 4, 4, 442);
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testCangen2() {
        $graph = new Graph('OCC(CC)CCC(CN)CN');
        $graph->computeInvariants();
        $graph->rankInvariants();
        while (true) {
            $graph->rankToPrimes();
            $graph->productPrimes();
            $graph->rankByPrimes();
            if ($graph->ranksEquals()) {
                break;
            }
        }
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "OCC(CC)CCC(CN)CN";
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        for ($i = 0; $i < 8; ++$i) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(5, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(5, new Bond(2, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(7, new Bond(8, ''));
        $expectedGraph->addBond(7, new Bond(10, ''));
        $expectedGraph->addBond(8, new Bond(7, ''));
        $expectedGraph->addBond(8, new Bond(9, ''));
        $expectedGraph->addBond(9, new Bond(8, ''));
        $expectedGraph->addBond(10, new Bond(7, ''));
        $expectedGraph->addBond(10, new Bond(11, ''));
        $expectedGraph->addBond(11, new Bond(10, ''));
        $this->setNodeAll($expectedGraph, [0], 10108001, 3, 3, 13);
        $this->setNodeAll($expectedGraph, [1], 20206002, 6, 6, 115);
        $this->setNodeAll($expectedGraph, [2], 30306001, 9, 9, 1547);
        $this->setNodeAll($expectedGraph, [3], 20206002, 4, 4, 46);
        $this->setNodeAll($expectedGraph, [4], 10106003, 1, 1, 7);
        $this->setNodeAll($expectedGraph, [5], 20206002, 7, 7, 437);
        $this->setNodeAll($expectedGraph, [6], 20206002, 8, 8, 493);
        $this->setNodeAll($expectedGraph, [7], 30306001, 10, 10, 2299);
        $this->setNodeAll($expectedGraph, [8, 10], 20206002, 5, 5, 87);
        $this->setNodeAll($expectedGraph, [9, 11], 10107002, 2, 2, 11);
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testCangen3() {
        $graph = new Graph('OCC(CC)CCC(CN)CN');
        $graph->cangen();
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "OCC(CC)CCC(CN)CN";
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        for ($i = 0; $i < 8; ++$i) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['N']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, ''));
        $expectedGraph->addBond(2, new Bond(1, ''));
        $expectedGraph->addBond(2, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(5, ''));
        $expectedGraph->addBond(3, new Bond(2, ''));
        $expectedGraph->addBond(3, new Bond(4, ''));
        $expectedGraph->addBond(4, new Bond(3, ''));
        $expectedGraph->addBond(5, new Bond(2, ''));
        $expectedGraph->addBond(5, new Bond(6, ''));
        $expectedGraph->addBond(6, new Bond(5, ''));
        $expectedGraph->addBond(6, new Bond(7, ''));
        $expectedGraph->addBond(7, new Bond(6, ''));
        $expectedGraph->addBond(7, new Bond(8, ''));
        $expectedGraph->addBond(7, new Bond(10, ''));
        $expectedGraph->addBond(8, new Bond(7, ''));
        $expectedGraph->addBond(8, new Bond(9, ''));
        $expectedGraph->addBond(9, new Bond(8, ''));
        $expectedGraph->addBond(10, new Bond(7, ''));
        $expectedGraph->addBond(10, new Bond(11, ''));
        $expectedGraph->addBond(11, new Bond(10, ''));
        $this->setNodeAll($expectedGraph, [0], 6, 4, 4, 19);
        $this->setNodeAll($expectedGraph, [1], 12, 8, 8, 217);
        $this->setNodeAll($expectedGraph, [2], 18, 11, 11, 4807);
        $this->setNodeAll($expectedGraph, [3], 8, 5, 5, 62);
        $this->setNodeAll($expectedGraph, [4], 2, 1, 1, 11);
        $this->setNodeAll($expectedGraph, [5], 14, 9, 9, 899);
        $this->setNodeAll($expectedGraph, [6], 16, 10, 10, 851);
        $this->setNodeAll($expectedGraph, [7], 20, 12, 12, 6409);
        $this->setNodeAll($expectedGraph, [8], 10, 6, 6, 111);
        $this->setNodeAll($expectedGraph, [9], 3, 2, 2, 13);
        $this->setNodeAll($expectedGraph, [10], 10, 7, 7, 185);
        $this->setNodeAll($expectedGraph, [11], 4, 3, 3, 17);
        $this->assertEquals($expectedGraph, $graph);
    }

    public function testCangen4() {
        $graph = new Graph('CC(=O)C');
        $graph->cangen();
        $expectedGraph = new Graph('');
        $expectedGraph->smiles = "CC(=O)C";
        for ($i = 0; $i < 2; ++$i) {
            $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        }
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['O']);
        $expectedGraph->addNode(PeriodicTableSingleton::getInstance()->getAtoms()['C']);
        $expectedGraph->addBond(0, new Bond(1, ''));
        $expectedGraph->addBond(1, new Bond(0, ''));
        $expectedGraph->addBond(1, new Bond(2, '='));
        $expectedGraph->addBond(1, new Bond(3, ''));
        $expectedGraph->addBond(2, new Bond(1, '='));
        $expectedGraph->addBond(3, new Bond(1, ''));
        $this->setNodeAll($expectedGraph, [0], 1, 1, 1, 7);
        $this->setNodeAll($expectedGraph, [1], 6, 4, 4,30);
        $this->setNodeAll($expectedGraph, [2], 4, 3, 3, 7);
        $this->setNodeAll($expectedGraph, [3], 2, 2, 2, 7);
        $this->assertEquals($expectedGraph, $graph);
    }

    private function setNodeInvariant(Graph $graph, array $indexes, int $invariant, int $lastRank) {
        foreach ($indexes as $index) {
            $graph->getNodes()[$index]->setInvariant($invariant);
            $graph->getNodes()[$index]->getCangenStructure()->setLastRank($lastRank);
            $graph->getNodes()[$index]->getCangenStructure()->setRank($lastRank);
        }
    }

    private function setNodeAll(Graph $graph, array $indexes, int $invariant, int $rank, int $lastRank, int $productPrime) {
        foreach ($indexes as $index) {
            $graph->getNodes()[$index]->setInvariant($invariant);
            $graph->getNodes()[$index]->getCangenStructure()->setRank($rank);
            $graph->getNodes()[$index]->getCangenStructure()->setLastRank($lastRank);
            $graph->getNodes()[$index]->getCangenStructure()->setProductPrime($productPrime);
        }
    }
}