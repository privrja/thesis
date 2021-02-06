<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class FamilyStructure extends AbstractStructure {

    public $family;

    public function checkInput(): Message {
        if (empty($this->family)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new FamilyTransformed();
        $trans->setFamily($this->family);
        return $trans;
    }

}
