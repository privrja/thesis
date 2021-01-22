<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\RightBracketParser;
use PHPUnit\Framework\TestCase;

final class RightBracketParserTest extends TestCase {

    public function testWithNull() {
        $parser = new RightBracketParser();
        $result = $parser->parse(null);
        $this->assertEquals(RightBracketParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new RightBracketParser();
        $result = $parser->parse('');
        $this->assertEquals(RightBracketParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new RightBracketParser();
        $result = $parser->parse(')');
        $this->assertEquals(new Accept(')', ''), $result);
    }

    public function testWithWrongData() {
        $parser = new RightBracketParser();
        $result = $parser->parse('-1');
        $this->assertEquals(RightBracketParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new RightBracketParser();
        $result = $parser->parse('(');
        $this->assertEquals(RightBracketParser::reject(), $result);
    }
}
