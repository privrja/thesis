<?php

namespace App\Structure;

use JsonSerializable;

class ModificationTransformed extends AbstractTransformed implements JsonSerializable {

    /** @var string */
    private $modificationName;

    /** @var string */
    private $formula;

    /** @var float|null */
    private $mass;

    /** @var bool */
    private $nTerminal;

    /** @var bool */
    private $cTerminal;

    /**
     * @return string
     */
    public function getModificationName(): string {
        return $this->modificationName;
    }

    /**
     * @param string $modificationName
     */
    public function setModificationName(string $modificationName): void {
        $this->modificationName = $modificationName;
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
     * @return bool
     */
    public function isNTerminal(): bool {
        return $this->nTerminal;
    }

    /**
     * @param bool $nTerminal
     */
    public function setNTerminal(bool $nTerminal): void {
        $this->nTerminal = $nTerminal;
    }

    /**
     * @return bool
     */
    public function isCTerminal(): bool {
        return $this->cTerminal;
    }

    /**
     * @param bool $cTerminal
     */
    public function setCTerminal(bool $cTerminal): void {
        $this->cTerminal = $cTerminal;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        $res = ['modificationName' => $this->modificationName, 'formula' => $this->formula, 'mass' => $this->mass, 'nTerminal' => $this->nTerminal, 'cTerminal' => $this->cTerminal];
        if (!empty($this->error)) {
            $res['error'] = $this->error;
        }
        return $res;
    }

}
