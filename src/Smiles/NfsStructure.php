<?php

namespace App\Smiles;

class NfsStructure {

    /** @var int $smilesNumber */
    private $smilesNumber;

    /** @var int $firstNumber */
    private $firstNumber;

    /** @var int $secondNumber */
    private $secondNumber;

    /**
     * NfsStructure constructor.
     * @param int $smilesNumber
     * @param int $firstNumber
     * @param int $secondNumber
     */
    public function __construct(int $smilesNumber, int $firstNumber, int $secondNumber) {
        $this->smilesNumber = $smilesNumber;
        $this->firstNumber = $firstNumber;
        $this->secondNumber = $secondNumber;
    }

    /**
     * @return int
     */
    public function getSmilesNumber(): int {
        return $this->smilesNumber;
    }

    /**
     * @return int
     */
    public function getFirstNumber(): int {
        return $this->firstNumber;
    }

    /**
     * @return int
     */
    public function getSecondNumber(): int {
        return $this->secondNumber;
    }

    /**
     * Increment smilesNumber
     */
    public function increment(): void {
        $this->smilesNumber++;
    }

}
