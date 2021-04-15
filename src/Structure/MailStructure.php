<?php

namespace App\Structure;

use App\Base\Message;

class MailStructure extends AbstractStructure {

    public $mail;

    public function checkInput(): Message {
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
