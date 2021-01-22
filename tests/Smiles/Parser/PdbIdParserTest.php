<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\PdbIdParser;
use PHPUnit\Framework\TestCase;

final class PdbIdParserTest extends TestCase {

    public function testWithNull() {
        $parser = new PdbIdParser();
        $this->assertEquals(PdbIdParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new PdbIdParser();
        $this->assertEquals(PdbIdParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new PdbIdParser();
        $this->assertEquals(new Accept('FOR', ''), $parser->parse('FOR'));
    }

    public function testWithRightData2() {
        $parser = new PdbIdParser();
        $this->assertEquals(new Accept('MYR', ' 5'), $parser->parse('MYR 5'));
    }

    public function testWithWrongData() {
        $parser = new PdbIdParser();
        $this->assertEquals(PdbIdParser::reject(), $parser->parse('CiD: '));
    }

    public function testWithWrongData2() {
        $parser = new PdbIdParser();
        $this->assertEquals(PdbIdParser::reject(), $parser->parse(' CID'));
    }

    public function testWithWrongData3() {
        $parser = new PdbIdParser();
        $this->assertEquals(PdbIdParser::reject(), $parser->parse('5'));
    }

    public function testWithWrongData4() {
        $parser = new PdbIdParser();
        $this->assertEquals(PdbIdParser::reject(), $parser->parse('CS'));
    }

}
