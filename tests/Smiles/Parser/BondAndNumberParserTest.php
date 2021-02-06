<?php

namespace App\Test\Smiles\Parser;

use App\Smiles\Digit;
use App\Smiles\Parser\Accept;
use App\Smiles\Parser\BondAndNumberParser;
use PHPUnit\Framework\TestCase;

final class BondAndNumberParserTest extends TestCase {

    public function testWithNull() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(new Accept(new Digit(5), ''), $parser->parse('5'));
    }

    public function testWithRightData2() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(new Accept(new Digit(52), ''), $parser->parse('%52'));
    }

    public function testWithRightData3() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(new Accept(new Digit(1, false, '='), ''), $parser->parse('=1'));
    }

    public function testWithRightData4() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(new Accept(new Digit(1, false, '='), '5'), $parser->parse('=15'));
    }

    public function testWithWrongData() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse('='));
    }

    public function testWithWrongData2() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse('%%'));
    }

    public function testWithWrongData3() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse('=='));
    }

    public function testWithWrongData4() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse('^5'));
    }

    public function testWithWrongData5() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse('=C'));
    }

    public function testWithWrongData6() {
        $parser = new BondAndNumberParser();
        $this->assertEquals(BondAndNumberParser::reject(), $parser->parse('%5'));
    }

}
