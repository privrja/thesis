<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\HydrogensParser;
use PHPUnit\Framework\TestCase;

final class HydrogensParserTest extends TestCase {

    public function testWithNull() {
        $parser = new HydrogensParser();
        $result = $parser->parse(null);
        $this->assertEquals(HydrogensParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new HydrogensParser();
        $result = $parser->parse('');
        $this->assertEquals(HydrogensParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new HydrogensParser();
        $result = $parser->parse('H2');
        $this->assertEquals(new Accept(2, ''), $result);
    }

    public function testWithRightData2() {
        $parser = new HydrogensParser();
        $result = $parser->parse('H+');
        $this->assertEquals(new Accept(1, '+'), $result);
    }

    public function testWithWrongData() {
        $parser = new HydrogensParser();
        $result = $parser->parse('[');
        $this->assertEquals(HydrogensParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new HydrogensParser();
        $result = $parser->parse('E');
        $this->assertEquals(HydrogensParser::reject(), $result);
    }

    public function testWithWrongData3() {
        $parser = new HydrogensParser();
        $result = $parser->parse('0');
        $this->assertEquals(HydrogensParser::reject(), $result);
    }

}