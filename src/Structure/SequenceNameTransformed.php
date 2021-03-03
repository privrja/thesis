<?php

namespace App\Structure;

class SequenceNameTransformed extends AbstractTransformed implements IValue {

    public $sequenceName;

    function getValue() {
        return $this->sequenceName;
    }

}