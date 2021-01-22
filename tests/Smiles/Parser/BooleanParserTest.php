<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\BooleanParser;
use PHPUnit\Framework\TestCase;

class BooleanParserTest extends TestCase {

    public function testWithNull() {
        $parser = new BooleanParser();
        $this->assertEquals(BooleanParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new BooleanParser();
        $this->assertEquals(BooleanParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new BooleanParser();
        $this->assertEquals(new Accept(1, '5'), $parser->parse('15'));
    }

    public function testWithRightData2() {
        $parser = new BooleanParser();
        $this->assertEquals(new Accept(0, ' '), $parser->parse('0 '));
    }

    public function testWithWrongData() {
        $parser = new BooleanParser();
        $this->assertEquals(BooleanParser::reject(), $parser->parse('[CH3++]'));
    }

    public function testWithWrongData2() {
        $parser = new BooleanParser();
        $this->assertEquals(BooleanParser::reject(), $parser->parse('false'));
    }

}
