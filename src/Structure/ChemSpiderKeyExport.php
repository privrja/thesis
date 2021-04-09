<?php

namespace App\Structure;

use JsonSerializable;

class ChemSpiderKeyExport implements JsonSerializable {

    public $apiKey;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['apiKey' => $this->apiKey];
    }

}
