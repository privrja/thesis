<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class PassStructure extends AbstractStructure {

    public $password;

    public function checkInput(): Message {
        if (empty($this->password)) {
           return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        } else if (strlen($this->password) < 8) {
            return new Message(ErrorConstants::ERROR_CONDITIONS_NOT_MET);
        }
        return Message::createNoContent();
    }

    public function transform(): AbstractTransformed {
        $trans = new PassTransformed();
        $trans->setPassword($this->password);
        $this->password = '';
        return $trans;
    }
}