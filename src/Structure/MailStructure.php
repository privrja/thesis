<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class MailStructure extends AbstractStructure {

    public $mail;

    public function checkInput(): Message {
        if (empty($this->mail)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (!preg_match('/^..*@..*\...*$/', $this->mail)) {
            return new Message(ErrorConstants::ERROR_MAIL_WRONG_INPUT);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new MailTransformed();
        if (!empty($this->mail)) {
            $trans->mail = $this->mail;
        }
        return $trans;
    }

}
