<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\MoreDigitNumberParser;
use PHPUnit\Framework\TestCase;

class MoreDigitNumberParserTest extends TestCase {

    public function testWithNull() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse(null);
        $this->assertEquals(MoreDigitNumberParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('');
        $this->assertEquals(MoreDigitNumberParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('%34%');
        $this->assertEquals(new Accept(34, '%'), $result);
    }

    public function testWithRightData2() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('%10');
        $this->assertEquals(new Accept(10, ''), $result);
    }

    public function testWithWrongData() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('5%');
        $this->assertEquals(MoreDigitNumberParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('55%');
        $this->assertEquals(MoreDigitNumberParser::reject(), $result);
    }

    public function testWithWrongData3() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('%9');
        $this->assertEquals(MoreDigitNumberParser::reject(), $result);
    }

    public function testWithWrongData4() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('%0');
        $this->assertEquals(MoreDigitNumberParser::reject(), $result);
    }

    public function testWithWrongData5() {
        $parser = new MoreDigitNumberParser();
        $result = $parser->parse('0');
        $this->assertEquals(MoreDigitNumberParser::reject(), $result);
    }
}