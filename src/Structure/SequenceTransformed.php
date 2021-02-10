<?php

namespace App\Structure;

class SequenceTransformed extends AbstractTransformed {

    /** @var string */
    private $sequenceName;

    /** @var string */
    private $sequence;

    /** @var string */
    private $sequenceType;

    /** @var string */
    private $formula;

    /** @var float|null */
    private $mass;

    /** @var string|null */
    private $smiles;

    /** @var string|null */
    private $usmiles;

    /** @var int|null */
    private $source;

    /** @var string|null */
    private $identifier;

    /** @var string|null*/
    private $decays;

    /** @var array */
    private $modifications;

    /** @var array */
    private $blocks;

    /**
     * @return string
     */
    public function getSequenceName(): string {
        return $this->sequenceName;
    }

    /**
     * @param string $sequenceName
     */
    public function setSequenceName(string $sequenceName): void {
        $this->sequenceName = $sequenceName;
    }

    /**
     * @return string
     */
    public function getFormula(): string {
        return $this->formula;
    }

    /**
     * @param string $formula
     */
    public function setFormula(string $formula): void {
        $this->formula = $formula;
    }

    /**
     * @return float|null
     */
    public function getMass(): ?float {
        return $this->mass;
    }

    /**
     * @param float|null $mass
     */
    public function setMass(?float $mass): void {
        $this->mass = $mass;
    }

    /**
     * @return string|null
     */
    public function getSmiles(): ?string {
        return $this->smiles;
    }

    /**
     * @param string|null $smiles
     */
    public function setSmiles(?string $smiles): void {
        $this->smiles = $smiles;
    }

    /**
     * @return int|null
     */
    public function getSource(): ?int {
        return $this->source;
    }

    /**
     * @param int|null $source
     */
    public function setSource(?int $source): void {
        $this->source = $source;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     */
    public function setIdentifier(?string $identifier): void {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getSequenceType(): string {
        return $this->sequenceType;
    }

    /**
     * @param string $sequenceType
     */
    public function setSequenceType(string $sequenceType): void {
        $this->sequenceType = $sequenceType;
    }

    /**
     * @return array
     */
    public function getModifications(): array {
        return $this->modifications;
    }

    /**
     * @param array $modifications
     */
    public function setModifications(array $modifications): void {
        $this->modifications = $modifications;
    }

    /**
     * @return array
     */
    public function getBlocks(): array {
        return $this->blocks;
    }

    /**
     * @param array $blocks
     */
    public function setBlocks(array $blocks): void {
        $this->blocks = $blocks;
    }

    /**
     * @return string|null
     */
    public function getUsmiles(): ?string {
        return $this->usmiles;
    }

    /**
     * @param string|null $usmiles
     */
    public function setUsmiles(?string $usmiles): void {
        $this->usmiles = $usmiles;
    }

    /**
     * @return string
     */
    public function getSequence(): string {
        return $this->sequence;
    }

    /**
     * @param string $sequence
     */
    public function setSequence(string $sequence): void {
        $this->sequence = $sequence;
    }

    /**
     * @return string
     */
    public function getDecays(): ?string {
        return $this->decays;
    }

    /**
     * @param string|null $decays
     */
    public function setDecays(?string $decays): void {
        $this->decays = $decays;
    }

}
