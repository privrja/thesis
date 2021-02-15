<?php

namespace App\Tests\Sequence;

use App\Base\SequenceHelper;
use App\Entity\Block;
use App\Enum\SequenceEnum;
use PHPUnit\Framework\TestCase;

final class SequenceTest extends TestCase {

    public function testSequencePseudacyclinOne() {
        $phe = new Block();
        $phe->setAcronym('Phe');
        $pro = new Block();
        $pro->setAcronym('Pro');
        $ile = new Block();
        $ile->setAcronym('Ile');
        $orn = new Block();
        $orn->setAcronym('Orn');
        $ace = new Block();
        $ace->setAcronym('Ace');
        $helper = new SequenceHelper('[Phe]-[Pro]-[Ile]-[Ile]\([Orn]-[Ace]\)', SequenceEnum::BRANCH_CYCLIC, [
            0 => $pro,
            1 => $phe,
            2 => $ace,
            3 => $ile,
            4 => $orn,
        ]);
        $res = $helper->sequenceBlocksStructure();
        $this->assertEquals('Phe', $res[0]->getBlock()->getAcronym());
        $this->assertEquals('Pro', $res[1]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[2]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[3]->getBlock()->getAcronym());
        $this->assertEquals('Orn', $res[4]->getBlock()->getAcronym());
        $this->assertEquals('Ace', $res[5]->getBlock()->getAcronym());

        $this->assertEquals(false, $res[0]->getIsBranch());
        $this->assertEquals(false, $res[1]->getIsBranch());
        $this->assertEquals(false, $res[2]->getIsBranch());
        $this->assertEquals(false, $res[3]->getIsBranch());
        $this->assertEquals(false, $res[4]->getIsBranch());
        $this->assertEquals(true, $res[5]->getIsBranch());

        $this->assertEquals('Pro', $res[0]->getNextBlock()->getAcronym());
        $this->assertEquals('Ile', $res[1]->getNextBlock()->getAcronym());
        $this->assertEquals('Ile', $res[2]->getNextBlock()->getAcronym());
        $this->assertEquals('Orn', $res[3]->getNextBlock()->getAcronym());
        $this->assertEquals('Phe', $res[4]->getNextBlock()->getAcronym());
        $this->assertEquals(null, $res[5]->getNextBlock());

        $this->assertEquals(null, $res[0]->getBranchReference());
        $this->assertEquals(null, $res[1]->getBranchReference());
        $this->assertEquals(null, $res[2]->getBranchReference());
        $this->assertEquals(null, $res[3]->getBranchReference());
        $this->assertEquals('Ace', $res[4]->getBranchReference()->getAcronym());
        $this->assertEquals(null, $res[5]->getBranchReference());
    }

    public function testSequencePseudacyclinTwo() {
        $phe = new Block();
        $phe->setAcronym('Phe');
        $pro = new Block();
        $pro->setAcronym('Pro');
        $ile = new Block();
        $ile->setAcronym('Ile');
        $orn = new Block();
        $orn->setAcronym('Orn');
        $ace = new Block();
        $ace->setAcronym('Ace');
        $helper = new SequenceHelper('[Pro]-[Ile]-[Ile]\([Orn]-[Ace]\)[Phe]', SequenceEnum::BRANCH_CYCLIC, [
            0 => $ile,
            1 => $ace,
            2 => $pro,
            3 => $orn,
            4 => $phe,
        ]);
        $res = $helper->sequenceBlocksStructure();
        $this->assertEquals('Pro', $res[0]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[1]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[2]->getBlock()->getAcronym());
        $this->assertEquals('Orn', $res[3]->getBlock()->getAcronym());
        $this->assertEquals('Ace', $res[4]->getBlock()->getAcronym());
        $this->assertEquals('Phe', $res[5]->getBlock()->getAcronym());

        $this->assertEquals(false, $res[0]->getIsBranch());
        $this->assertEquals(false, $res[1]->getIsBranch());
        $this->assertEquals(false, $res[2]->getIsBranch());
        $this->assertEquals(false, $res[3]->getIsBranch());
        $this->assertEquals(true, $res[4]->getIsBranch());
        $this->assertEquals(false, $res[5]->getIsBranch());

        $this->assertEquals('Ile', $res[0]->getNextBlock()->getAcronym());
        $this->assertEquals('Ile', $res[1]->getNextBlock()->getAcronym());
        $this->assertEquals('Orn', $res[2]->getNextBlock()->getAcronym());
        $this->assertEquals('Phe', $res[3]->getNextBlock()->getAcronym());
        $this->assertEquals(null, $res[4]->getNextBlock());
        $this->assertEquals('Pro', $res[5]->getNextBlock()->getAcronym());

        $this->assertEquals('Ace', $res[3]->getBranchReference()->getAcronym());
    }

    public function testSequencePseudacyclinThree() {
        $phe = new Block();
        $phe->setAcronym('Phe');
        $pro = new Block();
        $pro->setAcronym('Pro');
        $ile = new Block();
        $ile->setAcronym('Ile');
        $orn = new Block();
        $orn->setAcronym('Orn');
        $ace = new Block();
        $ace->setAcronym('Ace');
        $helper = new SequenceHelper('[Pro]-[Ile]-[Ile]\([Orn]-[Ace]-[Ile]\)[Phe]', SequenceEnum::BRANCH_CYCLIC, [
            0 => $ile,
            1 => $ace,
            2 => $pro,
            3 => $orn,
            4 => $phe,
        ]);
        $res = $helper->sequenceBlocksStructure();
        $this->assertEquals('Pro', $res[0]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[1]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[2]->getBlock()->getAcronym());
        $this->assertEquals('Orn', $res[3]->getBlock()->getAcronym());
        $this->assertEquals('Ace', $res[4]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[5]->getBlock()->getAcronym());
        $this->assertEquals('Phe', $res[6]->getBlock()->getAcronym());

        $this->assertEquals(false, $res[0]->getIsBranch());
        $this->assertEquals(false, $res[1]->getIsBranch());
        $this->assertEquals(false, $res[2]->getIsBranch());
        $this->assertEquals(false, $res[3]->getIsBranch());
        $this->assertEquals(true, $res[4]->getIsBranch());
        $this->assertEquals(true, $res[5]->getIsBranch());
        $this->assertEquals(false, $res[6]->getIsBranch());

        $this->assertEquals('Ile', $res[0]->getNextBlock()->getAcronym());
        $this->assertEquals('Ile', $res[1]->getNextBlock()->getAcronym());
        $this->assertEquals('Orn', $res[2]->getNextBlock()->getAcronym());
        $this->assertEquals('Phe', $res[3]->getNextBlock()->getAcronym());
        $this->assertEquals(null, $res[4]->getNextBlock());
        $this->assertEquals(null, $res[5]->getNextBlock());
        $this->assertEquals('Pro', $res[6]->getNextBlock()->getAcronym());

        $this->assertEquals('Ace', $res[3]->getBranchReference()->getAcronym());
        $this->assertEquals('Ile', $res[4]->getBranchReference()->getAcronym());
    }

    public function testSequenceLinear() {
        $phe = new Block();
        $phe->setAcronym('Phe');
        $pro = new Block();
        $pro->setAcronym('Pro');
        $ile = new Block();
        $ile->setAcronym('Ile');
        $helper = new SequenceHelper('[Phe]-[Pro]-[Ile]', SequenceEnum::LINEAR, [
            0 => $ile,
            1 => $pro,
            2 => $phe,
        ]);
        $res = $helper->sequenceBlocksStructure();
        $this->assertEquals('Phe', $res[0]->getBlock()->getAcronym());
        $this->assertEquals('Pro', $res[1]->getBlock()->getAcronym());
        $this->assertEquals('Ile', $res[2]->getBlock()->getAcronym());

        $this->assertEquals(false, $res[0]->getIsBranch());
        $this->assertEquals(false, $res[1]->getIsBranch());
        $this->assertEquals(false, $res[2]->getIsBranch());

        $this->assertEquals('Pro', $res[0]->getNextBlock()->getAcronym());
        $this->assertEquals('Ile', $res[1]->getNextBlock()->getAcronym());
        $this->assertEquals(null, $res[2]->getNextBlock());
    }

    public function testSequenceBranchFirst() {
        $thr = new Block();
        $thr->setAcronym('Thr');
        $pro = new Block();
        $pro->setAcronym('Pro');
        $his = new Block();
        $his->setAcronym('His');
        $gly = new Block();
        $gly->setAcronym('Gly');
        $gln = new Block();
        $gln->setAcronym('Gln');
        $tyr = new Block();
        $tyr->setAcronym('Tyr');
        $helper = new SequenceHelper('\([Thr]-[Pro]\)[His]-[Gly]-[Gln]-[Tyr]-[Thr]', SequenceEnum::BRANCH_CYCLIC, [
            0 => $thr,
            1 => $pro,
            2 => $his,
            3 => $gly,
            4 => $gln,
            5 => $tyr,
            6 => $thr,
        ]);
        $res = $helper->sequenceBlocksStructure();
        $this->assertEquals('Thr', $res[0]->getBlock()->getAcronym());
        $this->assertEquals('Pro', $res[1]->getBlock()->getAcronym());
        $this->assertEquals('His', $res[2]->getBlock()->getAcronym());
        $this->assertEquals('Gly', $res[3]->getBlock()->getAcronym());
        $this->assertEquals('Gln', $res[4]->getBlock()->getAcronym());
        $this->assertEquals('Tyr', $res[5]->getBlock()->getAcronym());
        $this->assertEquals('Thr', $res[6]->getBlock()->getAcronym());

        $this->assertEquals(false, $res[0]->getIsBranch());
        $this->assertEquals(true, $res[1]->getIsBranch());
        $this->assertEquals(false, $res[2]->getIsBranch());
        $this->assertEquals(false, $res[3]->getIsBranch());
        $this->assertEquals(false, $res[4]->getIsBranch());
        $this->assertEquals(false, $res[5]->getIsBranch());
        $this->assertEquals(false, $res[6]->getIsBranch());

        $this->assertEquals('His', $res[0]->getNextBlock()->getAcronym());
        $this->assertEquals(null, $res[1]->getNextBlock());
        $this->assertEquals('Gly', $res[2]->getNextBlock()->getAcronym());
        $this->assertEquals('Gln', $res[3]->getNextBlock()->getAcronym());
        $this->assertEquals('Tyr', $res[4]->getNextBlock()->getAcronym());
        $this->assertEquals('Thr', $res[5]->getNextBlock()->getAcronym());
        $this->assertEquals('Thr', $res[6]->getNextBlock()->getAcronym());
    }

}
