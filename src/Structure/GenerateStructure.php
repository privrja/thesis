<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class GenerateStructure extends AbstractStructure {

    public $token;
    public $mail;

    public function checkInput(): Message {
        if (empty($this->token) || empty($this->mail)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new GenerateTransformed();
        $trans->mail = $this->mail;
        $trans->token = $this->token;
        return $trans;
    }

}