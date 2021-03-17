<?php

namespace App\Structure;

use JsonSerializable;

class SequenceTransformed extends AbstractTransformed implements JsonSerializable {

    /** @var string */
    private $sequenceName;

    /** @var string */
    private $sequence;

    /** @var string */
    private $sequenceOriginal;

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

    /** @var mixed|null */
    private $nModification;

    /** @var mixed|null */
    private $cModification;

    /** @var mixed|null */
    private $bModification;

    /** @var array */
    private $family;

    /** @var array */
    public $organism;

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

    /**
     * @return mixed|null
     */
    public function getNModification() {
        return $this->nModification;
    }

    /**
     * @param mixed|null $nModification
     */
    public function setNModification($nModification): void {
        $this->nModification = $nModification;
    }

    /**
     * @return mixed|null
     */
    public function getCModification() {
        return $this->cModification;
    }

    /**
     * @param mixed|null $cModification
     */
    public function setCModification($cModification): void {
        $this->cModification = $cModification;
    }

    /**
     * @return mixed|null
     */
    public function getBModification() {
        return $this->bModification;
    }

    /**
     * @param mixed|null $bModification
     */
    public function setBModification($bModification): void {
        $this->bModification = $bModification;
    }

    /**
     * @return array
     */
    public function getFamily(): array {
        return $this->family;
    }

    /**
     * @param array $family
     */
    public function setFamily(array $family): void {
        $this->family = $family;
    }

    /**
     * @return string
     */
    public function getSequenceOriginal(): string {
        return $this->sequenceOriginal;
    }

    /**
     * @param string $sequenceOriginal
     */
    public function setSequenceOriginal(string $sequenceOriginal): void {
        $this->sequenceOriginal = $sequenceOriginal;
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
     * @inheritDoc
     */
    public function jsonSerialize() {
        $res = ['sequenceType' => $this->sequenceType,
            'sequenceName' => $this->sequenceName,
            'formula' => $this->formula,
            'mass' => $this->mass,
            'sequence' => $this->sequence,
            'nModification' => $this->nModification,
            'cModification' => $this->cModification,
            'bModification' => $this->bModification,
            'source' => $this->source,
            'identifier' => $this->identifier];
        if (!empty($this->error)) {
            $res['error'] = $this->error;
        }
        return $res;
    }

}
