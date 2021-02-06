<?php

namespace App\Test\Smiles\Parser;

use App\Enum\PeriodicTableSingleton;
use App\Smiles\Charge;
use App\Smiles\Parser\Accept;
use App\Smiles\Parser\ElementParser;
use PHPUnit\Framework\TestCase;

final class ElementParserTest extends TestCase {

    public function testWithNull() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['C']->asBracketElement();
        $atom->setHydrogens(3);
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[CH3]'));
    }

    public function testWithRightData2() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['C']->asBracketElement();
        $atom->setHydrogens(3);
        $atom->setCharge(new Charge('+', 2));
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[CH3++]'));
    }

    public function testWithRightData3() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['n']->asBracketElement();
        $atom->setCharge(new Charge('-', 1));
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[n-]'));
    }

    public function testWithRightData4() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['n']->asBracketElement();
        $atom->setCharge(new Charge('-', 5));
        $atom->setHydrogens(1);
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[nH-5]'));
    }

    public function testWithRightData5() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['O']->asBracketElement();
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[O]'));
    }

    public function testWithRightData6() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['O'];
        $this->assertEquals(new Accept($atom, ''), $parser->parse('O'));
    }

    public function testWithRightData7() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['Cl'];
        $this->assertEquals(new Accept($atom, ''), $parser->parse('Cl'));
    }

    public function testWithRightData8() {
        $parser = new ElementParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['c'];
        $this->assertEquals(new Accept($atom, ''), $parser->parse('c'));
    }

    public function testWithRightData9() {
        $parser = new ElementParser();
        $this->assertEquals(new Accept(PeriodicTableSingleton::getInstance()->getAtoms()['n'], 'H-5]'), $parser->parse('nH-5]'));
    }

    public function testWithRightData10() {
        $parser = new ElementParser();
        $this->assertEquals(new Accept(PeriodicTableSingleton::getInstance()->getAtoms()['b'], 'r'), $parser->parse('br'));
    }

    public function testWithRightData11() {
        $parser = new ElementParser();
        $this->assertEquals(new Accept(PeriodicTableSingleton::getInstance()->getAtoms()['Si']->asBracketElement(), ''), $parser->parse('[Si]'));
    }

    public function testWithWrongData() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse('H-5]'));
    }

    public function testWithWrongData2() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse('[nH-'));
    }

    public function testWithWrongData3() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse('[nH--5'));
    }

    public function testWithWrongData4() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse('[HN]'));
    }

    public function testWithWrongData5() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse('K'));
    }

    public function testWithWrongData6() {
        $parser = new ElementParser();
        $this->assertEquals(ElementParser::reject(), $parser->parse('Mn'));
    }

}
