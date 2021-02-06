<?php

namespace App\Test\Smiles\Parser;

use App\Smiles\Charge;
use App\Smiles\Parser\Accept;
use App\Smiles\Parser\ChargeParser;
use PHPUnit\Framework\TestCase;

final class ChargeParserTest extends TestCase {

    public function testWithNull() {
        $parser = new ChargeParser();
        $this->assertEquals(ChargeParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new ChargeParser();
        $this->assertEquals(ChargeParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new ChargeParser();
        $this->assertEquals(new Accept(new Charge('+', 2), ''), $parser->parse('+2'));
    }

    public function testWithRightData2() {
        $parser = new ChargeParser();
        $this->assertEquals(new Accept(new Charge('-', 8), ''), $parser->parse('-8'));
    }

    public function testWithRightData3() {
        $parser = new ChargeParser();
        $this->assertEquals(new Accept(new Charge('-', 2), ''), $parser->parse('--'));
    }

    public function testWithRightData4() {
        $parser = new ChargeParser();
        $this->assertEquals(new Accept(new Charge('+', 1), ''), $parser->parse('+'));
    }

    public function testWithWrongData() {
        $parser = new ChargeParser();
        $this->assertEquals(new Accept(new Charge(), ']CC'), $parser->parse(']CC'));
    }

    public function testWithWrongData2() {
        $parser = new ChargeParser();
        $this->assertEquals(new Accept(new Charge(), '0'), $parser->parse('0'));
    }

}