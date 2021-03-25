<?php


namespace App\Structure;

class SequenceMassTransformed extends AbstractTransformed implements IValue {

    /** @var float */
    public $from;

    /** @var float */
    public $to;

    public function getValue() {
        return $this;
    }

}
