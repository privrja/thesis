<?php

namespace App\Smiles;

use App\Exception\IllegalStateException;
use App\Exception\NotFoundException;

class OpenNumbersSort {

    /** @var PairSmilesNumber[] $nodes */
    private $nodes = [];

    /** @var int $length */
    private $length = 0;

    /**
     * @return PairSmilesNumber[]
     */
    public function getNodes(): array {
        return $this->nodes;
    }

    /**
     * Add new open node
     * @param int $nodeNumber
     */
    public function addOpenNode(int $nodeNumber): void {
        $this->nodes[] = new PairSmilesNumber($nodeNumber, $this->getLastCounter($this->length - 1), $this->length, $this);
        $this->length++;
    }

    /**
     * Add new digit to nodes
     * @param int $first node number of first node
     * @param int $second node number of second node
     * @throws IllegalStateException
     */
    public function addDigit(int $first, int $second): void {
        $firstIndex = $this->findFirst($first);
        $secondIndex = $this->findSecond($second);
        $nfsStructure = new NfsStructure($this->nodes[$firstIndex]->getCounter() + 1, $firstIndex, $secondIndex);
        $this->nodes[$firstIndex]->add($nfsStructure);
        $this->nodes[$firstIndex]->increment();
        $increment = $this->nodes[$firstIndex]->getCounter();
        for ($index = $firstIndex + 1 ; $index < $this->length; ++$index) {
            $this->nodes[$index]->incrementAll($increment);
        }
    }

    /**
     * Get last counter in nodes array from specified node
     * @param $secondIndex
     * @return int
     */
    private function getLastCounter($secondIndex): int {
        if ($this->length === 0) {
            return 0;
        }
        return $this->nodes[$secondIndex]->getCounter();
    }

    /**
     * Find node in nodes with node number
     * @param int $nodeNumber
     * @return int
     * @throws NotFoundException
     */
    private function findNode(int $nodeNumber): int {
        for ($index = 0; $index < $this->length; ++$index) {
            if ($this->nodes[$index]->getNodeNumber() === $nodeNumber) {
                return $index;
            }
        }
        throw new NotFoundException();
    }

    /**
     * Find first node ind nodes
     * @param $first
     * @return int
     * @throws IllegalStateException
     */
    private function findFirst($first) {
        try {
            return $this->findNode($first);
        } catch (NotFoundException $exception) {
            throw new IllegalStateException();
        }
    }

    /**
     * Find second node ind nodes
     * @param int $second
     * @return int
     * @throws IllegalStateException
     */
    private function findSecond(int $second) {
        if ($this->nodes[$this->length - 1]->getNodeNumber() === $second) {
            return $this->length - 1;
        }
        try {
            return $this->findNode($second);
        } catch (NotFoundException $exception) {
            throw new IllegalStateException();
        }
    }

}
