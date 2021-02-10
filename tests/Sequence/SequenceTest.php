<?php

namespace App\Tests\Sequence;

use App\Base\SequenceHelper;
use App\Entity\Block;
use App\Structure\SequenceEnum;
use PHPUnit\Framework\TestCase;

final class SequenceTest extends TestCase {

    public function testSequence() {
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
    }

}
