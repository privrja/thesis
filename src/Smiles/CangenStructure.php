<?php

namespace App\Smiles;

class CangenStructure {

    private $rank = 0;
    private $lastRank = 0;
    private $productPrime = 0;

    public function isRankSameAsLastRank() {
        return $this->rank === $this->lastRank;
    }

    /**
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
    }

    /**
     * @param int $rank
     */
    public function setRank(int $rank): void {
        $this->rank = $rank;
    }

    /**
     * @return int
     */
    public function getLastRank(): int {
        return $this->lastRank;
    }

    /**
     * @param int $lastRank
     */
    public function setLastRank(int $lastRank): void {
        $this->lastRank = $lastRank;
    }

    /**
     * @return int
     */
    public function getProductPrime(): int {
        return $this->productPrime;
    }

    /**
     * @param int $productPrime
     */
    public function setProductPrime(int $productPrime): void {
        $this->productPrime = $productPrime;
    }

}
