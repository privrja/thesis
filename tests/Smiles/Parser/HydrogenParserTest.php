<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\HydrogenParser;
use PHPUnit\Framework\TestCase;

final class HydrogenParserTest extends TestCase {

    public function testWithNull() {
        $parser = new HydrogenParser();
        $result = $parser->parse(null);
        $this->assertEquals(HydrogenParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new HydrogenParser();
        $result = $parser->parse('');
        $this->assertEquals(HydrogenParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new HydrogenParser();
        $result = $parser->parse('H');
        $this->assertEquals(new Accept('H', ''), $result);
    }

    public function testWithRightData2() {
        $parser = new HydrogenParser();
        $result = $parser->parse('H+]');
        $this->assertEquals(new Accept('H', '+]'), $result);
    }

    public function testWithWrongData() {
        $parser = new HydrogenParser();
        $result = $parser->parse('[');
        $this->assertEquals(HydrogenParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new HydrogenParser();
        $result = $parser->parse('E');
        $this->assertEquals(HydrogenParser::reject(), $result);
    }

    public function testWithWrongData3() {
        $parser = new HydrogenParser();
        $result = $parser->parse('4');
        $this->assertEquals(HydrogenParser::reject(), $result);
    }
}