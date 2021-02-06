<?php

namespace App\Test\Smiles;

use App\Smiles\SmilesBuilder;
use PHPUnit\Framework\TestCase;

final class RemoveUnnecessaryParenthesesTest extends TestCase {

    public function testWithRightData() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CCC(C)C1NC(=O)C2C(C)CCN2(C(=O)C(CC(C)C)OC(=O)CCNC(=O)C(C)N(C)C(=O)C(C(C)C)N(C)C1(=O))");
        $this->assertEquals("CCC(C)C1NC(=O)C2C(C)CCN2C(=O)C(CC(C)C)OC(=O)CCNC(=O)C(C)N(C)C(=O)C(C(C)C)N(C)C1=O", $result);
    }


    public function testWithRightData2() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CCC(C)C(NC(C)=O)C(=O)NC2CCCNC(=O)C(NC(=O)C(NC(=O)C3CCCN3(C(=O)C(Cc1ccccc1)NC2(=O)))C(C)CC)C(C)CC");
        $this->assertEquals("CCC(C)C(NC(C)=O)C(=O)NC2CCCNC(=O)C(NC(=O)C(NC(=O)C3CCCN3C(=O)C(Cc1ccccc1)NC2=O)C(C)CC)C(C)CC", $result);
    }

    public function testWithRightData3() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("N(C(C(=O))C)");
        $this->assertEquals("NC(C=O)C", $result);
    }

    public function testWithRightData4() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("N(C(C(=O))(C(C(C)(C))))(C)");
        $this->assertEquals("N(C(C=O)CC(C)C)C", $result);
    }

    public function testWithRightData5() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("C(C(C(C)C))C");
        $this->assertEquals("C(CC(C)C)C", $result);
    }

    public function testWithRightData6() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CCC(C)(C)C=O(C=O)C(=O)CC");
        $this->assertEquals("CCC(C)(C)C=O(C=O)C(=O)CC", $result);
    }

    public function testWithRightData7() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CCC(C)(C)C=O(C=O)[Fe]C(=O)CC");
        $this->assertEquals("CCC(C)(C)C=O(C=O)[Fe]C(=O)CC", $result);
    }

    public function testWithRightData8() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CC(C(=O))([Fe])");
        $this->assertEquals("CC(C=O)[Fe]", $result);
    }

    public function testWithRightData9() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CC(C(=O))([Fe+]CC(=O)C(CC)(#C))");
        $this->assertEquals("CC(C=O)[Fe+]CC(=O)C(CC)#C", $result);
    }

    public function testWithRightData10() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CC(C(=O))([Fe+]CC(=O)C(CC)(#C(C)))");
        $this->assertEquals("CC(C=O)[Fe+]CC(=O)C(CC)#CC", $result);
    }

    public function testWithRightData11() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CC(C(=O))([Fe+]CC(=O)C(CC)(#C(CC(C))))");
        $this->assertEquals(
            "CC(C=O)[Fe+]CC(=O)C(CC)#CCCC", $result);
    }

    public function testWithRightData12() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("CCC(CC)(C)");
        $this->assertEquals("CCC(CC)C", $result);
    }

    public function testWithRightData13() {
        $result = SmilesBuilder::removeUnnecessaryParentheses("C(=O)C(C(C))");
        $this->assertEquals("C(=O)CCC", $result);
    }
}