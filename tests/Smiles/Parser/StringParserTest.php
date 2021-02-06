<?php

namespace App\Test\Smiles\Parser;

use App\Smiles\Parser\Accept;
use App\Smiles\Parser\Reject;
use App\Smiles\Parser\StringParser;
use PHPUnit\Framework\TestCase;

final class StringParserTest extends TestCase {

    public function testWithNull() {
        $parser = new StringParser();
        $result = $parser->parseTextWithTemplate('Hello', null);
        $this->assertEquals(StringParser::reject(), $result);
    }

    public function testWithEmptyString() {
        $parser = new StringParser();
        $parseResult = $parser->parseTextWithTemplate('', '');
        $this->assertEquals(StringParser::reject(), $parseResult);
    }

    public function testWithEmptyString2() {
        $parser = new StringParser();
        $parseResult = $parser->parseTextWithTemplate('Hello', '');
        $this->assertEquals(new Reject('Not match template'), $parseResult);
    }

    public function testWithRightData() {
        $parser = new StringParser();
        $parseResult = $parser->parseTextWithTemplate('Hello', 'Hell');
        $this->assertEquals(new Accept('Hell', 'o'), $parseResult);
    }

    public function testWithWrongData() {
        $parser = new StringParser();
        $parseResult = $parser->parseTextWithTemplate('Hello', 'ello');
        $this->assertEquals(new Reject('Not match template'), $parseResult);
    }
}