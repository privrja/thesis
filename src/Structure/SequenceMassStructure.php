<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class SequenceMassStructure extends AbstractStructure {

    /** @var float */
    public $mass;

    /** @var float */
    public $range;

    public function checkInput(): Message {
        if(!isset($this->mass)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if ($this->mass <= 0) {
            return new Message(ErrorConstants::MASS_POSITIVE);
        }
        if (isset($this->range) && $this->range > 0) {
            return new Message(ErrorConstants::MASS_POSITIVE);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SequenceMassTransformed();
        if (!isset($this->range)) {
            $this->range = 0.25;
        }
        $trans->from = $this->mass - ($this->range);
        $trans->to = $this->mass + ($this->range);
        return $trans;
    }

}
