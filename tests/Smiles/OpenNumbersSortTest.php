<?php

namespace App\Test\Smiles;

use App\Smiles\OpenNumbersSort;
use PHPUnit\Framework\TestCase;

final class OpenNumbersSortTest extends TestCase {

    public function testCounter() {
        $structure = new OpenNumbersSort();
        $expected = [0, 1, 2, 3, 3, 3, 4, 4, 4, 4, 4];
        for ($index = 0; $index < 5; ++$index) {
            $structure->addOpenNode($index);
        }
        $structure->addOpenNode(5);
        $structure->addDigit(2, 5);
        $structure->addOpenNode(6);
        $structure->addOpenNode(7);
        $structure->addDigit(1, 7);
        $structure->addOpenNode(8);
        $structure->addOpenNode(9);
        $structure->addDigit(6, 9);
        $structure->addOpenNode(10);
        $structure->addDigit(3, 10);
        $actual = [];
        foreach ($structure->getNodes() as $node) {
            $actual[] = $node->getCounter();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testPairs() {
        $structure = new OpenNumbersSort();
        for ($index = 0; $index < 5; ++$index) {
            $structure->addOpenNode($index);
        }
        $structure->addOpenNode(5);
        $structure->addDigit(2, 5);
        $structure->addOpenNode(6);
        $structure->addOpenNode(7);
        $structure->addDigit(1, 7);
        $structure->addOpenNode(8);
        $structure->addOpenNode(9);
        $structure->addDigit(6, 9);
        $structure->addOpenNode(10);
        $structure->addDigit(3, 10);
        $this->assertEquals(false, $structure->getNodes()[0]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[1]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[2]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[3]->isInPair());
        $this->assertEquals(false, $structure->getNodes()[4]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[5]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[6]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[7]->isInPair());
        $this->assertEquals(false, $structure->getNodes()[8]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[9]->isInPair());
        $this->assertEquals(true, $structure->getNodes()[10]->isInPair());
    }

    public function testNumbers() {
        $structure = new OpenNumbersSort();
        for ($index = 0; $index < 5; ++$index) {
            $structure->addOpenNode($index);
        }
        $structure->addOpenNode(5);
        $structure->addDigit(2, 5);
        $structure->addOpenNode(6);
        $structure->addOpenNode(7);
        $structure->addDigit(1, 7);
        $structure->addOpenNode(8);
        $structure->addOpenNode(9);
        $structure->addDigit(6, 9);
        $structure->addOpenNode(10);
        $structure->addDigit(3, 10);
        $this->assertEquals(1, $structure->getNodes()[1]->getNexts()[0]->getSmilesNumber());
        $this->assertEquals(2, $structure->getNodes()[2]->getNexts()[0]->getSmilesNumber());
        $this->assertEquals(3, $structure->getNodes()[3]->getNexts()[0]->getSmilesNumber());
        $this->assertEquals(2, $structure->getNodes()[5]->getNexts()[0]->getSmilesNumber());
        $this->assertEquals(4, $structure->getNodes()[6]->getNexts()[0]->getSmilesNumber());
        $this->assertEquals(1, $structure->getNodes()[7]->getNexts()[0]->getSmilesNumber());
        $this->assertEquals(4, $structure->getNodes()[9]->getNexts()[0]->getSmilesNumber());
        $this->assertEquals(3, $structure->getNodes()[10]->getNexts()[0]->getSmilesNumber());
    }
}
