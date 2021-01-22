<?php

namespace App\Smiles;

use App\Enum\PeriodicTableSingleton;
use App\Exception\IllegalStateException;
use App\Smiles\Parser\OrganicSubsetParser;

class BracketElement extends Element {

    /** @var int $hydrogens */
    private $hydrogens;

    private $isOrganicSubset = false;

    /**
     * BracketElement constructor.
     * @param string $name
     * @param int $protons
     * @param int $bindings
     * @param float $mass
     * @param bool $isAromatic
     * @param Charge $charge
     * @param int $hydrogens
     * @throws IllegalStateException
     * @see Element
     */
    public function __construct(string $name, int $protons, int $bindings, float $mass, bool $isAromatic, Charge $charge, int $hydrogens) {
        parent::__construct($name, $protons, $bindings, $mass, $isAromatic);
        assert($hydrogens >= 0);
        $this->charge = $charge;
        $this->hydrogens = $hydrogens;
        $this->setupIsOrganicSubset($name);
    }

    /**
     * @param $name
     * @throws IllegalStateException
     */
    private function setupIsOrganicSubset($name) {
        $organicSubsetParser = new OrganicSubsetParser();
        $organicSubsetResult = $organicSubsetParser->parse($name);
        if ($organicSubsetResult->isAccepted()) {
            $this->isOrganicSubset = true;
        }
    }

    /**
     * @return int
     */
    public function getHydrogens(): int {
        return $this->hydrogens;
    }

    /**
     * @param int $hydrogens
     */
    public function setHydrogens(int $hydrogens): void {
        $this->hydrogens = $hydrogens;
    }

    public function getHydrogensCount($actualBindings) {
        return $this->getHydrogens();
    }

    public function elementSmiles($actualBindings) {
        if ($this->charge->isZero() && $this->hydrogens + $actualBindings === PeriodicTableSingleton::getInstance()->getAtoms()[strtolower($this->name)]->getBindings() + 1 && $this->isOrganicSubset) {
            return parent::elementSmiles($actualBindings);
        }

        $smiles =  '[' . $this->name;
        if ($this->hydrogens > 0) {
            $smiles .= 'H';
            if ($this->hydrogens > 1) {
                $smiles .= $this->hydrogens;
            }
        }
        if (!$this->charge->isZero()) {
            $smiles .= $this->charge->getCharge();
        }
        return $smiles . ']';
    }

}
