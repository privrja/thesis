<?php

namespace App\Structure;

use JsonSerializable;

class UniqueSmilesTransformed extends AbstractTransformed implements JsonSerializable {

    /** @var string */
    private $smiles;

    /**
     * @return string
     */
    public function getSmiles(): string {
        return $this->smiles;
    }

    /**
     * @param string $smiles
     */
    public function setSmiles(string $smiles): void {
        $this->smiles = $smiles;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['smiles' => $this->smiles];
    }

}
