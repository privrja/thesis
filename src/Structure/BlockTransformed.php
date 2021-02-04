<?php

namespace App\Structure;

class BlockTransformed extends AbstractTransformed {

    /** @var string */
    private $blockName;

    /** @var string */
    private $acronym;

    /** @var string|null */
    private $losses = null;

    /** @var string */
    private $formula;

    /** @var float|null */
    private $mass = null;

    /** @var string|null */
    private $smiles = null;

    /** @var string|null */
    private $uSmiles = null;

    /** @var int|null */
    private $source = null;

    /** @var string|null */
    private $identifier = null;

    /**
     * @return string
     */
    public function getBlockName(): string {
        return $this->blockName;
    }

    /**
     * @param string $blockName
     */
    public function setBlockName(string $blockName): void {
        $this->blockName = $blockName;
    }

    /**
     * @return string
     */
    public function getAcronym(): string {
        return $this->acronym;
    }

    /**
     * @param string $acronym
     */
    public function setAcronym(string $acronym): void {
        $this->acronym = $acronym;
    }

    /**
     * @return string|null
     */
    public function getLosses(): ?string {
        return $this->losses;
    }

    /**
     * @param string|null $losses
     */
    public function setLosses(?string $losses): void {
        $this->losses = $losses;
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
     * @return string|null
     */
    public function getUSmiles(): ?string {
        return $this->uSmiles;
    }

    /**
     * @param string|null $uSmiles
     */
    public function setUSmiles(?string $uSmiles): void {
        $this->uSmiles = $uSmiles;
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

}
