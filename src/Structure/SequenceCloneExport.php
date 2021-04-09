<?php

namespace App\Structure;

use JsonSerializable;

class SequenceCloneExport implements JsonSerializable {

    public $id;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['id' => $this->id];
    }

}
