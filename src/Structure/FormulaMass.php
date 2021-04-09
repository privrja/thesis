<?php

namespace App\Structure;

use JsonSerializable;

class FormulaMass implements JsonSerializable {

    public $smiles;
    public $formula;
    public $mass;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['smiles' => $this->smiles, 'formula' => $this->formula, 'mass' => $this->mass];
    }

}