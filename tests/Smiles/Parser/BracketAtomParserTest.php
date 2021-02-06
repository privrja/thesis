<?php

namespace App\Test\Smiles\Parser;

use App\Enum\PeriodicTableSingleton;
use App\Smiles\Charge;
use App\Smiles\Parser\Accept;
use App\Smiles\Parser\BracketAtomParser;
use PHPUnit\Framework\TestCase;

final class BracketAtomParserTest extends TestCase {

    public function testWithNull() {
        $parser = new BracketAtomParser();
        $this->assertEquals(BracketAtomParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new BracketAtomParser();
        $this->assertEquals(BracketAtomParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new BracketAtomParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['C']->asBracketElement();
        $atom->setHydrogens(3);
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[CH3]'));
    }

    public function testWithRightData2() {
        $parser = new BracketAtomParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['C']->asBracketElement();
        $atom->setHydrogens(3);
        $atom->setCharge(new Charge('+', 2));
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[CH3++]'));
    }

    public function testWithRightData3() {
        $parser = new BracketAtomParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['n']->asBracketElement();
        $atom->setCharge(new Charge('-', 1));
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[n-]'));
    }

    public function testWithRightData4() {
        $parser = new BracketAtomParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['n']->asBracketElement();
        $atom->setCharge(new Charge('-', 5));
        $atom->setHydrogens(1);
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[nH-5]'));
    }

    public function testWithRightData5() {
        $parser = new BracketAtomParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['O']->asBracketElement();
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[O]'));
    }

    public function testWithRightData6() {
        $parser = new BracketAtomParser();
        $atom = PeriodicTableSingleton::getInstance()->getAtoms()['Fe']->asBracketElement();
        $this->assertEquals(new Accept($atom, ''), $parser->parse('[Fe]'));
    }

    public function testWithWrongData() {
        $parser = new BracketAtomParser();
        $this->assertEquals(BracketAtomParser::reject(), $parser->parse('nH-5]'));
    }

    public function testWithWrongData2() {
        $parser = new BracketAtomParser();
        $this->assertEquals(BracketAtomParser::reject(), $parser->parse('[nH-'));
    }

    public function testWithWrongData3() {
        $parser = new BracketAtomParser();
        $this->assertEquals(BracketAtomParser::reject(), $parser->parse('[nH--5'));
    }

    public function testWithWrongData4() {
        $parser = new BracketAtomParser();
        $this->assertEquals(BracketAtomParser::reject(), $parser->parse('[HN]'));
    }

}
