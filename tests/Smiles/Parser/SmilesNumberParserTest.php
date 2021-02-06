<?php

namespace App\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\SmilesNumberParser;
use PHPUnit\Framework\TestCase;

final class SmilesNumberParserTest extends TestCase {

    public function testWithNull() {
        $parser = new SmilesNumberParser();
        $result = $parser->parse(null);
        $this->assertEquals(SmilesNumberParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new SmilesNumberParser();
        $result = $parser->parse('');
        $this->assertEquals(SmilesNumberParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new SmilesNumberParser();
        $result = $parser->parse('15');
        $this->assertEquals(new Accept('1', '5'), $result);
    }

    public function testWithRightData2() {
        $parser = new SmilesNumberParser();
        $result = $parser->parse('%34%C');
        $this->assertEquals(new Accept('34', '%C'), $result);
    }

    public function testWithWrongData() {
        $parser = new SmilesNumberParser();
        $result = $parser->parse(0);
        $this->assertEquals(SmilesNumberParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new SmilesNumberParser();
        $result = $parser->parse('%9%');
        $this->assertEquals(SmilesNumberParser::reject(), $result);
    }

    public function testWithWrongData3() {
        $parser = new SmilesNumberParser();
        $result = $parser->parse('%9');
        $this->assertEquals(SmilesNumberParser::reject(), $result);
    }
}
