<?php

namespace App\Structure;

use JsonSerializable;

class UniqueSmilesStructure implements JsonSerializable {

    /** @var int */
    public $id;

    /** @var string */
    public $smiles;

    /** @var string */
    public $acronym;

    /** @var string */
    public $unique;

    /** @var int */
    public $sameAs;

    /** @var BlockSmiles|null */
    public $block;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['id' => $this->id, 'acronym' => $this->acronym,'smiles' => $this->smiles, 'unique' => $this->unique, 'sameAs' => $this->sameAs, 'block' => $this->block];
    }

}
