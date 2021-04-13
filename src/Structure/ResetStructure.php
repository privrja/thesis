<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class ResetStructure extends AbstractStructure {

    /** @var string */
    public $nick;

    public function checkInput(): Message {
        if (empty($this->nick)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ResetTransformed();
        $trans->nick = $this->nick;
        return $trans;
    }
}