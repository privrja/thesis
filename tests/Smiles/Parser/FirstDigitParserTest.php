<?php
namespace App\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\FirstDigitParser;
use PHPUnit\Framework\TestCase;

final class FirstDigitParserTest extends TestCase {

    public function testWithNull() {
        $parser = new FirstDigitParser();
        $result = $parser->parse(null);
        $this->assertEquals(FirstDigitParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new FirstDigitParser();
        $result = $parser->parse('');
        $this->assertEquals(FirstDigitParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new FirstDigitParser();
        $result = $parser->parse('5');
        $this->assertEquals(new Accept(5, ''), $result);
    }

    public function testWithWrongData() {
        $parser = new FirstDigitParser();
        $result = $parser->parse('0');
        $this->assertEquals(FirstDigitParser::reject(), $result);
    }

    public function testWithWrongData2() {
        $parser = new FirstDigitParser();
        $result = $parser->parse('a');
        $this->assertEquals(FirstDigitParser::reject(), $result);
    }
}