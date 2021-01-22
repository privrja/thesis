<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\IntParser;
use PHPUnit\Framework\TestCase;

class IntParserTest extends TestCase {

    public function testWithNull() {
        $parser = new IntParser();
        $this->assertEquals(IntParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new IntParser();
        $this->assertEquals(IntParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new IntParser();
        $this->assertEquals(new Accept('-2', ' '), $parser->parse('-2 '));
    }

    public function testWithRightData2() {
        $parser = new IntParser();
        $this->assertEquals(new Accept('1', ''), $parser->parse('1'));
    }

    public function testWithRightData3() {
        $parser = new IntParser();
        $this->assertEquals(new Accept('12', 'H5'), $parser->parse('12H5'));
    }

    public function testWithWrongData() {
        $parser = new IntParser();
        $this->assertEquals(new Accept('2', ''), $parser->parse('+2'));
    }

    public function testWithWrogData2() {
        $parser = new IntParser();
        $this->assertEquals(IntParser::reject(), $parser->parse('C+2'));
    }

}