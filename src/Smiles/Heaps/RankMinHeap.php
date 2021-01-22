<?php

namespace App\Smiles;

use SplMinHeap;

class RankMinHeap extends SplMinHeap {

    /**
     * @param NextNode $value1
     * @param NextNode $value2
     * @return int
     */
    protected function compare($value1, $value2) {
        if ($value1->getRank() < $value2->getRank()) {
            return 1;
        } else if ($value1->getRank() > $value2->getRank()) {
            return -1;
        }
        return 0;
    }

}
