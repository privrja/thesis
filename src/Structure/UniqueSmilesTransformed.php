<?php

namespace App\Structure;

class UniqueSmilesTransformed extends AbstractTransformed {

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

}
