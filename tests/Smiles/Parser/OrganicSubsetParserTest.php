<?php

namespace App\Test\Smiles\Parser;

use App\Enum\PeriodicTableSingleton;
use App\Smiles\Parser\Accept;
use App\Smiles\Parser\OrganicSubsetParser;
use PHPUnit\Framework\TestCase;

final class OrganicSubsetParserTest extends TestCase {

    public function testWithNull() {
        $parser = new OrganicSubsetParser();
        $result = $parser->parse(null);
        $this->assertEquals(OrganicSubsetParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new OrganicSubsetParser();
        $result = $parser->parse('');
        $this->assertEquals(OrganicSubsetParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new OrganicSubsetParser();
        $result = $parser->parse('Cl');
        $this->assertEquals(new Accept(PeriodicTableSingleton::getInstance()->getAtoms()['Cl'], ''), $result);
    }

    public function testWithRightData2() {
        $parser = new OrganicSubsetParser();
        $result = $parser->parse('Fe');
        $this->assertEquals(new Accept(PeriodicTableSingleton::getInstance()->getAtoms()['F'], 'e'), $result);
    }

    public function testWithWrongData() {
        $parser = new OrganicSubsetParser();
        $result = $parser->parse('Ge');
        $this->assertEquals(OrganicSubsetParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new OrganicSubsetParser();
        $result = $parser->parse('[Ge]');
        $this->assertEquals(OrganicSubsetParser::reject(), $result);
    }

}
