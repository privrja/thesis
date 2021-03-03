<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class SequenceIdStructure extends AbstractStructure {

    public $id;

    public function checkInput(): Message {
        if (empty($this->id)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ParamTransformed();
        $trans->param = $this->id;
        return $trans;
    }

}
