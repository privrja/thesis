<?php

namespace App\Structure;

use App\Enum\ServerEnum;

class SequenceTransformed extends AbstractTransformed {

    /** @var string */
    private $sequenceName;

    /** @var string */
    private $formula;

    /** @var float|null */
    private $mass;

    /** @var string|null */
    private $smiles;

    /** @var string|null */
    private $usmiles;

    /** @var ServerEnum|null */
    private $source;

    /** @var string|null */
    private $identifier;

    /** @var string */
    private $sequenceType;

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
     * @return ServerEnum|null
     */
    public function getSource(): ?ServerEnum {
        return $this->source;
    }

    /**
     * @param ServerEnum|null $source
     */
    public function setSource(?ServerEnum $source): void {
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

}
