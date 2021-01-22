<?php

namespace Bbdgnc\Test\Smiles\Parser;

use PHPUnit\Framework\TestCase;

final class ReferenceParserTest extends TestCase {

    // TODO

//    public function testWithNull() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse(null));
//    }
//
//    public function testWithEmptyString() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse(''));
//    }
//
//    public function testWithRightData() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::NORINE;
//        $reference->identifier = 'NOR00863';
//        $this->assertEquals(new Accept($reference, ''), $parser->parse('NOR00863'));
//    }
//
//    public function testWithRightData2() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::NORINE;
//        $reference->identifier = 'NOR00001';
//        $this->assertEquals(new Accept($reference, ' 5'), $parser->parse('NOR00001 5'));
//    }
//
//    public function testWithRightData3() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::PDB;
//        $reference->identifier = 'FOR';
//        $this->assertEquals(new Accept($reference, ''), $parser->parse('PDB: FOR'));
//    }
//
//    public function testWithRightData4() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::PUBCHEM;
//        $reference->identifier = 88;
//        $this->assertEquals(new Accept($reference, ''), $parser->parse('CID: 88'));
//    }
//
//    public function testWithRightData5() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::CHEMSPIDER;
//        $reference->identifier = 454123;
//        $this->assertEquals(new Accept($reference, ''), $parser->parse('CSID: 454123'));
//    }
//
//    public function testWithRightData6() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::PDB;
//        $reference->identifier = 4564;
//        $this->assertEquals(new Accept($reference, ''), $parser->parse('PDB: 4564'));
//    }
//
//    public function testWithRightData7() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::PDB;
//        $reference->identifier = 4564;
//        $this->assertEquals(new Accept($reference, '8'), $parser->parse('PDB: 45648'));
//    }
//
//    public function testWithRightData8() {
//        $parser = new ReferenceParser();
//        $reference = new ReferenceTO();
//        $reference->database = "SMILES";
//        $reference->identifier = "CCC";
//        $this->assertEquals(new Accept($reference, ''), $parser->parse('SMILES: CCC'));
//    }
//
//    public function testWithWrongData() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse('PDB MYR'));
//    }
//
//    public function testWithWrongData2() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse(': NOR00123'));
//    }
//
//    public function testWithWrongData3() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse('5'));
//    }
//
//    public function testWithWrongData4() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse(':NOR88888'));
//    }
//
//    public function testWithWrongData5() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse(': 8888'));
//    }
//
//    public function testWithWrongData6() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse('PDB: 34,'));
//    }
//
//    public function testWithWrongData7() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse('CID: MYR'));
//    }
//
//    public function testWithWrongData8() {
//        $parser = new ReferenceParser();
//        $this->assertEquals(ReferenceParser::reject(), $parser->parse('CSID: NOR00864'));
//    }

}
