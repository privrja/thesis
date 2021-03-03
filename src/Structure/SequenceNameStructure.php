<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class SequenceNameStructure extends AbstractStructure {

    public $sequenceName;

    public function checkInput(): Message {
        if (empty($this->sequenceName)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SequenceNameTransformed();
        $trans->sequenceName = $this->sequenceName;
        return $trans;
    }

}
