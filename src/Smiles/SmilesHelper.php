<?php

namespace App\Smiles;

use App\Exception\IllegalStateException;

class SmilesHelper {

    /**
     * @param $smiles
     * @return string
     * @throws IllegalStateException
     */
    static function getUniqueSmiles($smiles) {
        $graph = new Graph($smiles);
        return $graph->getUniqueSmiles();
    }

}
