<?php

namespace App\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\ZeroParser;
use PHPUnit\Framework\TestCase;

class ZeroParserTest extends TestCase {

    public function testWithNull() {
        $parser = new ZeroParser();
        $this->assertEquals(ZeroParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new ZeroParser();
        $this->assertEquals(ZeroParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new ZeroParser();
        $this->assertEquals(new Accept('0', ''), $parser->parse('0'));
    }

    public function testWithWrongData() {
        $parser = new ZeroParser();
        $this->assertEquals(ZeroParser::reject(), $parser->parse('1'));
    }

    public function testWithWrongData2() {
        $parser = new ZeroParser();
        $this->assertEquals(ZeroParser::reject(), $parser->parse('-1'));
    }

    public function testWithWrongData3() {
        $parser = new ZeroParser();
        $this->assertEquals(ZeroParser::reject(), $parser->parse('a'));
    }

    public function testWithWrongData4() {
        $parser = new ZeroParser();
        $this->assertEquals(ZeroParser::reject(), $parser->parse('NOR00864'));
    }

}
