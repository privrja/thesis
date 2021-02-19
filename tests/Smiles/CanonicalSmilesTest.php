<?php

namespace App\Tests\Smiles;

use App\Smiles\SmilesHelper;
use PHPUnit\Framework\TestCase;

class CanonicalSmilesTest extends TestCase {

//    public function testCanonical() {
//        $computed = SmilesHelper::canonicalSmiles('C/C=C/C[C@H](C)[C@H]([C@H]1C(=O)N[C@H](C(=O)N(CC(=O)N([C@H](C(=O)N[C@H](C(=O)N([C@H](C(=O)N[C@H](C(=O)N[C@@H](C(=O)N([C@H](C(=O)N[C@H](C(=O)N([C@H](C(=O)N1C)C(C)C)C)CC(C)C)CC(C)C)C)C)C)CC(C)C)C)C(C)C)CC(C)C)C)C)C(C)C)O');
//        self::assertSame('CC=CCC(C)C(C1C(=O)NC(C(=O)N(CC(=O)N(C(C(=O)NC(C(=O)N(C(C(=O)NC(C(=O)NC(C(=O)N(C(C(=O)NC(C(=O)N(C(C(=O)N1C)C(C)C)C)CC(C)C)CC(C)C)C)C)C)CC(C)C)C)C(C)C)CC(C)C)C)C)C(C)C)O', $computed);
//    }

    public function testCanonicalSmall() {
        $computed = SmilesHelper::canonicalSmiles('N(CCCC(C(=O)O)N)[O-]');
        self::assertSame('N(CCCC(C(=O)O)N)O', $computed);
    }

}