<?php

namespace Bbdgnc\Test\Smiles;

use App\Smiles\Graph;
use PHPUnit\Framework\TestCase;

final class UniqueSmilesTest extends TestCase {

    public function testAceton() {
        $graph = new Graph('CC(=O)C');
        $this->assertEquals('CC(C)=O', $graph->getUniqueSmiles());
    }

    public function testRightData() {
        $graph = new Graph('OCC(CC)CCC(CN)CN');
        $this->assertEquals('CCC(CO)CCC(CN)CN', $graph->getUniqueSmiles());
    }

    public function testAminobuturicAcid() {
        $graph = new Graph('OC(C(CC)N)=O');
        $this->assertEquals('CCC(N)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testSarcosine() {
        $graph = new Graph('N(CC(=O)O)C');
        $this->assertEquals('CNCC(O)=O', $graph->getUniqueSmiles());
    }

    public function testMethylLeucine() {
        $graph = new Graph('N(C(C(=O)O)CC(C)C)C');
        $this->assertEquals('CNC(CC(C)C)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testValine() {
        $graph = new Graph('NC(C(=O)O)C(C)C');
        $this->assertEquals('CC(C)C(N)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testAlanine() {
        $graph = new Graph('NC(C(=O)O)C');
        $this->assertEquals('CC(N)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testMethylValine() {
        $graph = new Graph('N(C(C(=O)O)C(C)C)C');
        $this->assertEquals('CNC(C(C)C)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testMethylValine2() {
        $graph = new Graph('OC(=O)C(C(C)C)NC');
        $this->assertEquals('CNC(C(C)C)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testRightData2() {
        $graph = new Graph('N(C(C(=O)O)C(C(C)CC=CC)O)C');
        $this->assertEquals('CNC(C(O)C(C)CC=CC)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testIsoleucine() {
        $graph = new Graph('NC(C(CC)C)C(O)=O');
        $this->assertEquals('CCC(C)C(N)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testIsoleucine2() {
        $graph = new Graph('CCC(C)C(N)C(O)=O');
        $this->assertEquals('CCC(C)C(N)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testIsoleucine3() {
        $graph = new Graph('NC(C(CC)C)C(=O)O');
        $this->assertEquals('CCC(C)C(N)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testCyclic() {
        $graph = new Graph('OC(=O)C1C(C)CCN1');
        $this->assertEquals('CC1CCNC1C(O)=O', $graph->getUniqueSmiles());
    }

    public function testLinear() {
        $graph = new Graph('OC(=O)C(CC(C)C)O');
        $this->assertEquals('CC(C)CC(O)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testBetaAlanine() {
        $graph = new Graph('OC(=O)CCN');
        $this->assertEquals('NCCC(O)=O', $graph->getUniqueSmiles());
    }

    public function testMethylAlanine() {
        $graph = new Graph('OC(=O)C(C)NC');
        $this->assertEquals('CNC(C)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testAceticAcid() {
        $graph = new Graph('OC(C)=O');
        $this->assertEquals('CC(O)=O', $graph->getUniqueSmiles());
    }

    public function testDiaminopentanoicAcid() {
        $graph = new Graph('NC(CCCN)C(O)=O');
        $this->assertEquals('NCCCC(N)C(O)=O', $graph->getUniqueSmiles());
    }

    public function testProline() {
        $graph = new Graph('OC(=O)C1CCCN1');
        $this->assertEquals('OC(=O)C1CCCN1', $graph->getUniqueSmiles());
    }

    public function testDeferoxamine() {
        $graph = new Graph('CC(=O)N(CCCCCNC(=O)CCC(=O)N(CCCCCNC(=O)CCC(=O)N(CCCCCN)O)O)O');
        $this->assertEquals('CC(=O)N(O)CCCCCNC(=O)CCC(=O)N(O)CCCCCNC(=O)CCC(=O)N(O)CCCCCN', $graph->getUniqueSmiles());
    }

    public function testAcetamide() {
        $graph = new Graph('NCCCCCN(C(C)=O)O');
        $this->assertEquals('CC(=O)N(O)CCCCCN', $graph->getUniqueSmiles());
    }

    public function testSuccinicAcid() {
        $graph = new Graph('OC(=O)CCC(=O)O');
        $this->assertEquals('OC(=O)CCC(O)=O', $graph->getUniqueSmiles());
    }

    public function testHydroxycadaverine() {
        $graph = new Graph('N(O)CCCCCN');
        $this->assertEquals('NCCCCCNO', $graph->getUniqueSmiles());
    }

    public function testRightData3() {
        $graph = new Graph('OC(C=C(C)CCO)=O');
        $this->assertEquals('CC(CCO)=CC(O)=O', $graph->getUniqueSmiles());
    }

    public function testRightData4() {
        $graph = new Graph('CC(=CC(=O)O)CCO');
        $this->assertEquals('CC(CCO)=CC(O)=O', $graph->getUniqueSmiles());
    }

    public function testRightData5() {
        $graph = new Graph('N(CCCC(C(=O)O)N)[O-1]');
        $this->assertEquals('NC(CCCN[O-1])C(O)=O', $graph->getUniqueSmiles());
    }

    public function testRightData6() {
        $graph = new Graph('N(CCCC(C(=O)O)N)[O-]');
        $this->assertEquals('NC(CCCN[O-1])C(O)=O', $graph->getUniqueSmiles());
    }

    public function testCyclic2() {
        $graph = new Graph('CC(=CC(=O)N(CCCC1C(=O)NC(C(=O)N1)CCCN(C(=O)C=C(C)CCOC(=O)C(CCCN(C(=O)C=C(C)CCO)[O-])NC(=O)C)[O-])[O-])CCO');
        $this->assertEquals('CC(=O)NC(CCCN([O-1])C(=O)C=C(C)CCO)C(=O)OCCC(C)=CC(=O)N([O-1])CCCC1NC(=O)C(CCCN([O-1])C(=O)C=C(C)CCO)NC1=O', $graph->getUniqueSmiles());
    }

    public function testCyclic3() {
        $graph = new Graph('C(C(C1C(=C(C(=O)O1)O)O)O)O');
        $this->assertEquals('OCC(O)C1OC(=O)C(=C1O)O', $graph->getUniqueSmiles());
    }

    public function testCyclic4() {
        $graph = new Graph('C1=CC=CC=C1C');
        $this->assertEquals('CC1=CC=CC=C1', $graph->getUniqueSmiles());
    }

    public function testCubane() {
        $graph = new Graph('C12C3C4C1C5C4C3C25');
        $this->assertEquals('C12C3C4C1C5C2C3C45', $graph->getUniqueSmiles());
    }

    /** in original with brackets: C[C]=1=CCCC=1 */
    public function testCyclic5() {
        $graph = new Graph('CC(=CCC1)=C1');
        $this->assertEquals('CC=1=CCCC=1', $graph->getUniqueSmiles());
    }

    /** in original: C[C]=1=CCC=C=1 */
    public function testCyclic6() {
        $graph = new Graph('CC(=C=CC1)=C1');
        $this->assertEquals('CC=1=CCC=C=1', $graph->getUniqueSmiles());
    }

    /** in original: C[C]=1=C=CC=C=1 */
    public function testCyclic8() {
        $graph = new Graph('CC(=C=C1)=C=C1');
        $this->assertEquals('CC=1=C=CC=C=1', $graph->getUniqueSmiles());
    }

    public function testCyclic9() {
        $graph = new Graph('CC=1C=CC=C=1');
        $this->assertEquals('CC1=C=CC=C1', $graph->getUniqueSmiles());
    }

    public function testCyclic10() {
        $graph = new Graph('O1CCCCC1N1CCCCC1');
        $this->assertEquals('C1CCN(CC1)C2CCCCO2', $graph->getUniqueSmiles());
    }

    /** i think its unnecessary */
    public function testCyclic11() {
        $graph = new Graph('CCC(C)C1C(=O)N(C(C(=O)N(C(C(=O)NCCC(=O)OC(C(=O)N2CCC(C2C(=O)N1)C)CC=C)C)C)C(C)C)C');
        $this->assertEquals('CCC(C)C1NC(=O)C2C(C)CCN2C(=O)C(CC=C)OC(=O)CCNC(=O)C(C)N(C)C(=O)C(C(C)C)N(C)C1=O', $graph->getUniqueSmiles());
    }

    /** i think its unnecesarry */
    public function testCyclic12() {
        $graph = new Graph('CCC(C)C2C(=O)N(C(C(=O)N(C(C(=O)NCCC(=O)OC(C(=O)N1CCC(C1C(=O)N2)C)CC=C)C)C)C(C)C)C');
        $this->assertEquals('CCC(C)C1NC(=O)C2C(C)CCN2C(=O)C(CC=C)OC(=O)CCNC(=O)C(C)N(C)C(=O)C(C(C)C)N(C)C1=O', $graph->getUniqueSmiles());
    }

    public function testCyclic13() {
        $graph = new Graph('CCC1C(=O)N(CC(=O)N(C(C(=O)NC(C(=O)N(C(C(=O)NC(C(=O)NC(C(=O)N(C(C(=O)N(C(C(=O)N(C(C(=O)N(C(C(=O)N1)C(C(C)CC=CC)O)C)C(C)C)C)CC(C)C)C)CC(C)C)C)C)C)CC(C)C)C)C(C)C)CC(C)C)C)C');
        $this->assertEquals('CCC1NC(=O)C(C(O)C(C)CC=CC)N(C)C(=O)C(C(C)C)N(C)C(=O)C(CC(C)C)N(C)C(=O)C(CC(C)C)N(C)C(=O)C(C)NC(=O)C(C)NC(=O)C(CC(C)C)N(C)C(=O)C(NC(=O)C(CC(C)C)N(C)C(=O)CN(C)C1=O)C(C)C', $graph->getUniqueSmiles());
    }

    public function testPhenylAlanine() {
        $graph = new Graph('OC(=O)C(Cc1ccccc1)N');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('NC(CC1=CC=CC=C1)C(O)=O', $smiles);
    }

    public function testAromatic() {
        $graph = new Graph('Cc1ccccc1');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('CC1=CC=CC=C1', $smiles);
    }

    public function testCyclic14() {
        $graph = new Graph('CC1C2(C54CC2)C3C1CC3C4C5');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('CC1C2CC3C4CC45CCC15C23', $smiles);
    }

    public function testCyclic15() {
        $graph = new Graph('C1CC1C(C2CC2)C3CC3');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('C1CC1C(C2CC2)C3CC3', $smiles);
    }

    public function testCyclic16() {
        $graph = new Graph('C1CC1C(C2CC24)C3CC3CCC4');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('C1CC2CC2C(C3CC3)C4CC4C1', $smiles);
    }

    public function testCyclic17() {
        $graph = new Graph('C15CC1C(C2C(CCC5)C24)C3CC3CCC4');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('C1CC2CC2C3C4CC4CCCC5C(C1)C35', $smiles);
    }

    public function testCyclic18() {
        $graph = new Graph('C15CC1C(C2C(C5)C24)C3CC3C4');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('C1C2CC3C4CC5CC5C(C12)C34', $smiles);
    }

    public function testCyclic19() {
        $graph = new Graph('C15CC1C(C2C(C5)C624)C3CC3C4CC6');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('C1CC23C1C4CC4C5C6CC6CC3C25', $smiles);
    }

    public function testCyclic20() {
        $graph = new Graph('C15CC1C(C2C(C5)C6724)C3CC3C4CC6CC7');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('C1CC234C1CC4C5CC5C6C7CC7CC3C26', $smiles);
    }

    public function testCyclicAromatic() {
        $graph = new Graph('Cc1c[nH]cn1');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('CC1=CNC=N1', $smiles);
    }

    public function testCyclicAromatic2() {
        $graph = new Graph('NC(C(O)=O)Cc1nc[nH]c1');
        $smiles = $graph->getUniqueSmiles();
        $this->assertEquals('NC(CC1=CNC=N1)C(O)=O', $smiles);
    }

}
