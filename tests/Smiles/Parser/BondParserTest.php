<?php

namespace Bbdgnc\Test\Smiles\Parser;


use App\Smiles\Parser\Accept;
use App\Smiles\Parser\BondParser;
use PHPUnit\Framework\TestCase;

final class BondParserTest extends TestCase {

    public function testWithNull() {
        $parser = new BondParser();
        $result = $parser->parse(null);
        $this->assertEquals(BondParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new BondParser();
        $result = $parser->parse('');
        $this->assertEquals(BondParser::reject(), $result);
    }

    public function testWithRightData() {
        $parser = new BondParser();
        $result = $parser->parse('=');
        $this->assertEquals(new Accept('=', ''), $result);
    }

    public function testWithWrongData() {
        $parser = new BondParser();
        $result = $parser->parse('CC');
        $this->assertEquals(new Accept('', 'CC'), $result);
    }
}