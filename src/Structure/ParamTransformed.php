<?php

namespace App\Structure;

class ParamTransformed extends AbstractTransformed implements IValue {

    public $param;

    function getValue() {
        return $this->param;
    }

}
