<?php

namespace App\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\SeparatorParser;
use PHPUnit\Framework\TestCase;

class SeparatorParserTest extends TestCase {

    public function testWithNull() {
        $parser = new SeparatorParser();
        $result = $parser->parse(null);
        $this->assertEquals(SeparatorParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new SeparatorParser();
        $result = $parser->parse('');
        $this->assertEquals(SeparatorParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new SeparatorParser();
        $result = $parser->parse('. ');
        $this->assertEquals(new Accept('.', ' '), $result);
    }

    public function testWithWrongData() {
        $parser = new SeparatorParser();
        $result = $parser->parse('-1');
        $this->assertEquals(SeparatorParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new SeparatorParser();
        $result = $parser->parse('+');
        $this->assertEquals(SeparatorParser::reject(), $result);
    }
}
