<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\LeftBracketParser;
use PHPUnit\Framework\TestCase;

final class LeftBracketParserTest extends TestCase {

    public function testWithNull() {
        $parser = new LeftBracketParser();
        $result = $parser->parse(null);
        $this->assertEquals(LeftBracketParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new LeftBracketParser();
        $result = $parser->parse('');
        $this->assertEquals(LeftBracketParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new LeftBracketParser();
        $result = $parser->parse('(');
        $this->assertEquals(new Accept('(', ''), $result);
    }

    public function testWithWrongData() {
        $parser = new LeftBracketParser();
        $result = $parser->parse('0');
        $this->assertEquals(LeftBracketParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new LeftBracketParser();
        $result = $parser->parse(')');
        $this->assertEquals(LeftBracketParser::reject(), $result);
    }
}