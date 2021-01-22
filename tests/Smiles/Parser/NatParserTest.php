<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\NatParser;
use PHPUnit\Framework\TestCase;

final class NatParserTest extends TestCase {

    public function testWithNull() {
        $parser = new NatParser();
        $result = $parser->parse(null);
        $this->assertEquals(NatParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new NatParser();
        $result = $parser->parse('');
        $this->assertEquals(NatParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new NatParser();
        $result = $parser->parse('12');
        $this->assertEquals(new Accept(12, ''), $result);
    }

    public function testWithWrongData() {
        $parser = new NatParser();
        $result = $parser->parse('012');
        $this->assertEquals(NatParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new NatParser();
        $result = $parser->parse('a');
        $this->assertEquals(NatParser::reject(), $result);
    }

    public function testWithWrongData3() {
        $parser = new NatParser();
        $result = $parser->parse('-12');
        $this->assertEquals(NatParser::reject(), $result);
    }
}