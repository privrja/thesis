<?php

namespace App\Smiles;

use App\Exception\IllegalStateException;

class Element {

    protected $name = "";
    protected $protons = 0;
    protected $bindings = 0;
    protected $mass = 0;

    /** @var bool $isAromatic */
    protected $isAromatic;

    /** @var Charge $charge */
    protected $charge;

    /**
     * Element constructor.
     * @param string $name shortcut of atom name ex.: H, C, O, N or He
     * @param int $protons number of protons
     * must be non negative number
     * @param int $bindings number of typical bindings ex.: O have 2, C have 4, N have 3, ...
     * must be non negative number
     * @param float $mass
     * must be positive number
     * @param bool $isAromatic
     */
    public function __construct(string $name, int $protons, int $bindings, float $mass, bool $isAromatic = false) {
        assert($protons >= 0);
        assert($bindings >= 0);
        assert($mass > 0);
        $this->name = $name;
        $this->protons = $protons;
        $this->bindings = $bindings;
        $this->mass = $mass;
        $this->isAromatic = $isAromatic;
        $this->charge = new Charge();
    }

    // TODO check unused param
    public function elementSmiles($actualBindings) {
        return $this->name;
    }

    /**
     * @return Charge
     */
    public function getCharge(): Charge {
        return $this->charge;
    }

    /**
     * @param Charge $charge
     */
    public function setCharge(Charge $charge): void {
        $this->charge = $charge;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getProtons() {
        return $this->protons;
    }

    /**
     * @return int
     */
    public function getBindings() {
        return $this->bindings;
    }

    /**
     * @return int
     */
    public function getMass() {
        return $this->mass;
    }

    /**
     * @return bool
     */
    public
    function isAromatic(): bool {
        return $this->isAromatic;
    }

    public function asNonAromatic() {
        if ($this->isAromatic) {
            $this->bindings++;
            $this->isAromatic = false;
        }
    }

    public function getHydrogensCount($actualBindings) {
        $hydrogensCount = $this->bindings - $actualBindings;
        return $hydrogensCount < 0 ? 0 : $hydrogensCount;
    }

    /**
     * @return BracketElement
     * @throws IllegalStateException
     */
    public function asBracketElement() {
        return new BracketElement($this->name, $this->protons, $this->bindings, $this->mass, $this->isAromatic, new Charge(), 0);
    }

}
