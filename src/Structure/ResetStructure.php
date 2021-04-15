<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class ResetStructure extends AbstractStructure {

    /** @var string */
    public $mail;

    public function checkInput(): Message {
        if (empty($this->mail)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ResetTransformed();
        $trans->mail = $this->mail;
        return $trans;
    }
}