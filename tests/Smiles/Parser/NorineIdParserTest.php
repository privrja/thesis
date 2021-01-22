<?php

namespace Bbdgnc\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\NorineIdParser;
use PHPUnit\Framework\TestCase;

final class NorineIdParserTest extends TestCase {

    public function testWithNull() {
        $parser = new NorineIdParser();
        $this->assertEquals(NorineIdParser::reject(), $parser->parse(null));
    }

    public function testWithEmptyString() {
        $parser = new NorineIdParser();
        $this->assertEquals(NorineIdParser::reject(), $parser->parse(''));
    }

    public function testWithRightData() {
        $parser = new NorineIdParser();
        $this->assertEquals(new Accept('NOR00683', ''), $parser->parse('NOR00683'));
    }

    public function testWithRightData2() {
        $parser = new NorineIdParser();
        $this->assertEquals(new Accept('NOR00001', ''), $parser->parse('NOR00001'));
    }

    public function testWithWrongData() {
        $parser = new NorineIdParser();
        $this->assertEquals(NorineIdParser::reject(), $parser->parse('00683'));
    }

    public function testWithWrongData2() {
        $parser = new NorineIdParser();
        $this->assertEquals(NorineIdParser::reject(), $parser->parse('683'));
    }

    public function testWithWrongData3() {
        $parser = new NorineIdParser();
        $this->assertEquals(NorineIdParser::reject(), $parser->parse('NOR00000'));
    }

    public function testWithWrongData4() {
        $parser = new NorineIdParser();
        $this->assertEquals(NorineIdParser::reject(), $parser->parse('NOR'));
    }

    public function testWithWrongData5() {
        $parser = new NorineIdParser();
        $this->assertEquals(NorineIdParser::reject(), $parser->parse('NORINE00568'));
    }

}

