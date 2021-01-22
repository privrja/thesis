<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\LeftSquareBracketParser;
use PHPUnit\Framework\TestCase;

final class LeftSquareBracketParserTest extends TestCase {

    public function testWithNull() {
        $parser = new LeftSquareBracketParser();
        $result = $parser->parse(null);
        $this->assertEquals(LeftSquareBracketParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new LeftSquareBracketParser();
        $result = $parser->parse('');
        $this->assertEquals(LeftSquareBracketParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new LeftSquareBracketParser();
        $result = $parser->parse('[');
        $this->assertEquals(new Accept('[', ''), $result);
    }

    public function testWithWrongData() {
        $parser = new LeftSquareBracketParser();
        $result = $parser->parse('1');
        $this->assertEquals(LeftSquareBracketParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new LeftSquareBracketParser();
        $result = $parser->parse(']');
        $this->assertEquals(LeftSquareBracketParser::reject(), $result);
    }

}