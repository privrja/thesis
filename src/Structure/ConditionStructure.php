<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class ConditionStructure extends AbstractStructure {

    public $text;

    public function checkInput(): Message {
        if (empty($this->text)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ConditionTransformed();
        $trans->text = $this->text;
        return $trans;
    }

}
