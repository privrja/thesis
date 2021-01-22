<?php

namespace App\Smiles;

class PairSmilesNumber {

    /** @var NfsStructure[] $nexts */
    private $nexts = [];

    /** @var int $length */
    private $length = 0;

    /** @var int $position */
    private $position = 0;

    /** @var OpenNumbersSort $openNumbersSort */
    private $openNumbersSort;

    /** @var int $nodeNumber */
    private $nodeNumber = 0;

    /** @var int $counter */
    private $counter = 0;

    /**
     * PairSmilesNumber constructor.
     * @param int $nodeNumber
     * @param int $counter
     * @param int $position
     * @param OpenNumbersSort $openNumbersSort
     */
    public function __construct(int $nodeNumber, int $counter, int $position, OpenNumbersSort $openNumbersSort) {
        $this->nodeNumber = $nodeNumber;
        $this->counter = $counter;
        $this->openNumbersSort = $openNumbersSort;
        $this->position = $position;
    }

    /**
     * Retun Node number
     * @return int
     */
    public function getNodeNumber() {
        return $this->nodeNumber;
    }

    /**
     * Return counter
     * @return int
     */
    public function getCounter(): int {
        return $this->counter;
    }

    /**
     * Increment counter
     */
    public function increment(): void {
        $this->counter++;
    }

    /**
     * if array is epmty return false
     * otherwise true
     * @return bool
     */
    public function isInPair(): bool {
        return $this->length !== 0;
    }

    /**
     * add nfsStructure to this node and ending node
     * @param NfsStructure $nfsStructure
     */
    public function add(NfsStructure $nfsStructure): void {
        $this->nexts[] = $nfsStructure;
        $this->length++;
        $this->openNumbersSort->getNodes()[$nfsStructure->getSecondNumber()]->addSecond($nfsStructure);
    }

    /**
     * add nfsStructure only to this node
     * @param NfsStructure $nfsStructure
     */
    public function addSecond(NfsStructure $nfsStructure): void {
        $this->nexts[] = $nfsStructure;
        $this->length++;
    }

    /**
     * Increment counter and increment all nfsStructures in node which is need to increment
     * @param int $inc output param, increment number
     */
    public function incrementAll(&$inc): void {
        self::increment();
            for ($index = 0; $index < $this->getLength(); $index++) {
            if ($inc === $this->nexts[$index]->getSmilesNumber() && $this->nexts[$index]->getFirstNumber() === $this->position) {
                $this->nexts[$index]->increment();
                $inc++;
            }
        }
    }

    /**
     * Return array with numbers and their nodes
     * @return NfsStructure[]
     */
    public function getNexts(): array {
        return $this->nexts;
    }

    /**
     * Return length of nexts array
     * @return int
     */
    public function getLength(): int {
        return $this->length;
    }

}
