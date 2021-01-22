<?php

namespace App\Smiles;

use App\Smiles\Enum\VertexStateEnum;

class Node {

    /** @var Element atom */
    private $atom;

    /** @var Digit[] $arDigits */
    private $arDigits = [];

    /** @var int $invariant */
    private $invariant;

    /** @var CangenStructure $cangenStructure */
    private $cangenStructure;

    /** @var bool $inRing */
    private $inRing = false;

    /** @var Bond[] */
    private $arBonds;

    /**
     * @var int $vertexState
     * @see VertexStateEnum
     */
    private $vertexState = VertexStateEnum::NOT_FOUND;

    /**
     * Node constructor.
     * @param Element $atom
     * @param array $arBounds
     */
    public function __construct(Element $atom, array $arBounds = []) {
        $this->atom = $atom;
        $this->arBonds = $arBounds;
        $this->cangenStructure = new CangenStructure();
    }

    public function actualBindings(): int {
        $actualBindings = 0;
        foreach ($this->arBonds as $bond) {
            $actualBindings += $bond->getBondType();
        }
        return $actualBindings;
    }

    public function hydrogensCount(): int {
        return $this->atom->getHydrogensCount($this->actualBindings());
    }

    public function addBond(Bond $bond): void {
        $this->arBonds[] = $bond;
    }

    public function computeInvariants(): void {
        $this->invariant = "";
        $this->invariant .= sizeof($this->arBonds);
        $this->invariant .= $this->actualBindingsWithZero();
        $this->invariant .= $this->protonNumber();
        $this->invariant .= $this->atom->getCharge()->getSignValue();
        $this->invariant .= $this->atom->getCharge()->getChargeSize();
        $this->invariant .= $this->hydrogensCount();
    }

    private function protonNumber() {
        return $this->addZero($this->atom->getProtons());
    }

    private function actualBindingsWithZero() {
        return $this->addZero($this->actualBindings());
    }

    private function addZero($number) {
        return $number < 10 ? '0' . $number : $number;
    }

    /**
     * @return Element
     */
    public function getAtom(): Element {
        return $this->atom;
    }

    public function elementSmiles() {
        return $this->atom->elementSmiles($this->actualBindings());
    }
    /**
     * @return mixed
     */
    public function getInvariant() {
        return $this->invariant;
    }

    /**
     * @return Bond[]
     */
    public function getBonds(): array {
        return $this->arBonds;
    }

    /**
     * @param $invariant
     */
    public function setInvariant($invariant) {
        $this->invariant = $invariant;
    }

    /**
     * @return CangenStructure
     */
    public function getCangenStructure(): CangenStructure {
        return $this->cangenStructure;
    }

    /**
     * @param CangenStructure $cangenStructure
     */
    public function setCangenStructure(CangenStructure $cangenStructure): void {
        $this->cangenStructure = $cangenStructure;
    }

    /**
     * @return int
     */
    public function getVertexState(): int {
        return $this->vertexState;
    }

    /**
     * @param int $vertexState
     */
    public function setVertexState(int $vertexState): void {
        $this->vertexState = $vertexState;
    }

    /**
     * @return Digit[]
     */
    public function getDigits(): array {
        return $this->arDigits;
    }

    /**
     * @param Digit[] $arDigits
     */
    public function setDigits(array $arDigits): void {
        $this->arDigits = $arDigits;
    }

    /**
     * Add digit to arDigits
     * @param Digit $digit
     */
    public function addDigit(Digit $digit): void {
        $this->arDigits[] = $digit;
    }

    public function deleteDigit(int $digit): void {
        $arDigitsLength = sizeof($this->arDigits);
        for ($index = 0; $index < $arDigitsLength; ++$index) {
            if ($digit === $this->arDigits[$index]->getDigit()) {
                array_splice($this->arDigits, $index, 1);
                return;
            }
        }
    }

    /**
     * Check if digits are empty
     * @return bool
     */
    public function isDigitsEmpty(): bool {
        return empty($this->arDigits);
    }

    /**
     * @return bool
     */
    public function isInRing(): bool {
        return $this->inRing;
    }

    /**
     * @param bool $inRing
     */
    public function setInRing(bool $inRing): void {
        $this->inRing = $inRing;
    }

}
