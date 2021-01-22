<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\SignParser;
use PHPUnit\Framework\TestCase;

final class SignParserTest extends TestCase {

    public function testWithNull() {
        $parser = new SignParser();
        $this->assertEquals(SignParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new SignParser();
        $this->assertEquals(SignParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new SignParser();
        $this->assertEquals(new Accept('+', ''), $parser->parse('+'));
    }

    public function testWithRightData2() {
        $parser = new SignParser();
        $this->assertEquals(new Accept('-', '8'), $parser->parse('-8'));
    }

    public function testWithWrongData() {
        $parser = new SignParser();
        $this->assertEquals(SignParser::reject(), $parser->parse('0'));
    }

    public function testWithWrongData2() {
        $parser = new SignParser();
        $this->assertEquals(SignParser::reject(), $parser->parse('K'));
    }

}
