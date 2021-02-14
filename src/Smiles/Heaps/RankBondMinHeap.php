<?php

namespace App\Smiles\Heaps;

use App\Smiles\Enum\BondTypeEnum;
use App\Smiles\NextNode;
use SplMinHeap;

class RankBondMinHeap extends SplMinHeap {
    /**
     * @param NextNode $value1
     * @param NextNode $value2
     * @return int
     */
    protected function compare($value1, $value2) {
        if (BondTypeEnum::$backValues[$value1->getBondType()] > BondTypeEnum::$backValues[$value2->getBondType()]) {
            return 1;
        } else if (BondTypeEnum::$backValues[$value1->getBondType()] < BondTypeEnum::$backValues[$value2->getBondType()]) {
            return -1;
        }
        if ($value1->getRank() < $value2->getRank()) {
            return 1;
        } else if ($value1->getRank() > $value2->getRank()) {
            return -1;
        }
        return 0;
    }

}
