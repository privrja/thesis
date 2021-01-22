<?php

namespace App\Smiles;

class NextNode {

    /** @var int */
    private $nodeIndex;

    /** @var string */
    private $bondType;

    /** @var int */
    private $rank;

    /**
     * NextNode constructor.
     * @param int $nodeIndex
     * @param string $bondType
     * @param int $rank
     */
    public function __construct(int $nodeIndex, string $bondType, int $rank) {
        $this->nodeIndex = $nodeIndex;
        $this->bondType = $bondType;
        $this->rank = $rank;
    }

    /**
     * @return int
     */
    public function getNodeIndex(): int {
        return $this->nodeIndex;
    }

    /**
     * @return string
     */
    public function getBondType(): string {
        return $this->bondType;
    }

    /**
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
    }

}
