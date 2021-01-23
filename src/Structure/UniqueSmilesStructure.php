<?php

namespace App\Structure;

use JsonSerializable;

class UniqueSmilesStructure implements JsonSerializable {

    public $id;

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
        return [ 'id' => $this->id, 'smiles' => $this->smiles, 'unique' => $this->unique, 'sameAs' => $this->sameAs];
    }

}
