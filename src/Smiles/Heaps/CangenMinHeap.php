<?php

namespace App\Smiles\Heaps;

use App\Smiles\CangenStructure;
use SplMinHeap;

class CangenMinHeap extends SplMinHeap {

    /**
     * @param CangenStructure $value1
     * @param CangenStructure $value2
     * @return int
     */
    protected function compare($value1, $value2) {
        if ($value1->getLastRank() < $value2->getLastRank()) {
            return 1;
        } else if ($value1->getLastRank() > $value2->getLastRank()) {
            return -1;
        }
        if ($value1->getProductPrime() < $value2->getProductPrime()) {
            return 1;
        } else if ($value1->getProductPrime() > $value2->getProductPrime()) {
            return -1;
        }
        return 0;
    }

}
