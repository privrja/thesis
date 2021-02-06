<?php

namespace App\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\AtomParser;
use PHPUnit\Framework\TestCase;

final class AtomParserTest extends TestCase {

    public function testWithNull() {
        $parser = new AtomParser();
        $this->assertEquals(AtomParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new AtomParser();
        $this->assertEquals(AtomParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new AtomParser();
        $this->assertEquals(new Accept('Fe', '++]'), $parser->parse('Fe++]'));
    }

    public function testWithRightData2() {
        $parser = new AtomParser();
        $this->assertEquals(new Accept('C', 'H3'), $parser->parse('CH3'));
    }

    public function testWithRightData3() {
        $parser = new AtomParser();
        $this->assertEquals(new Accept('Fe', '3+'), $parser->parse('Fe3+'));
    }

    public function testWithRightData4() {
        $parser = new AtomParser();
        $this->assertEquals(new Accept('Uus', '+'), $parser->parse('Uus+'));
    }

    public function testWithRightData5() {
        $parser = new AtomParser();
        $this->assertEquals(new Accept('Qq', ''), $parser->parse('Qq'));
    }

    public function testWithWrongData() {
        $parser = new AtomParser();
        $this->assertEquals(AtomParser::reject(), $parser->parse('['));
    }

    public function testWithWrongData2() {
        $parser = new AtomParser();
        $this->assertEquals(AtomParser::reject(), $parser->parse('2'));
    }

    public function testWithWrongData3() {
        $parser = new AtomParser();
        $this->assertEquals(AtomParser::reject(), $parser->parse('+'));
    }

}