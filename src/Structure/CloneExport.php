<?php

namespace App\Structure;

use JsonSerializable;

class CloneExport implements JsonSerializable {

    public $id;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['id' => $this->id];
    }

}
