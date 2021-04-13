<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class GenerateStructure extends AbstractStructure {

    public $token;
    public $nick;

    public function checkInput(): Message {
        if (empty($this->token) || empty($this->nick)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new GenerateTransformed();
        $trans->nick = $this->nick;
        $trans->token = $this->token;
        return $trans;
    }

}