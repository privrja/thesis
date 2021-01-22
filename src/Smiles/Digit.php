<?php

namespace App\Smiles;

class Digit {

    /** @var int $digit */
    private $digit;

    /** @var bool $accepted */
    private $accepted;

    /** @var string $bondType */
    private $bondType;

    /**
     * Digit constructor.
     * @param int $digit
     * @param bool $accepted
     * @param string $bondType
     */
    public function __construct(int $digit, bool $accepted = false, $bondType = '') {
        $this->digit = $digit;
        $this->accepted = $accepted;
        $this->bondType = $bondType;
    }

    /**
     * @return int
     */
    public function getDigit(): int {
        return $this->digit;
    }

    /**
     * @return bool
     */
    public function isAccepted(): bool {
        return $this->accepted;
    }

    /**
     * @return string
     */
    public function getBondType(): string {
        return $this->bondType;
    }

    private function printOnlyDigit(): string {
        return $this->digit > 9 ? '%' . $this->digit : $this->digit;
    }

    public function printDigit(): string {
        return $this->bondType . $this->printOnlyDigit();
    }

}
