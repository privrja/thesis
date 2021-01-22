<?php

namespace App\Structure;

use JsonSerializable;

class UniqueSmilesStructure implements JsonSerializable {

    /** @var string */
    public $smiles;

    /** @var string */
    public $unique;

    /** @var int */
    public $sameAs;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [ 'smiles' => $this->smiles, 'unique' => $this->unique, 'sameAs' => $this->sameAs];
    }

}
