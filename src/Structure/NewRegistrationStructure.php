<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class NewRegistrationStructure extends AbstractStructure {

    /** @var string $name */
    public $name;

    /** @var string $password */
    public $password;

    /** @var string $mail */
    private $mail;

    public function checkInput(): Message {
        if (empty($this->name) || empty($this->password)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (strlen($this->password) < 8) {
            return new Message(ErrorConstants::ERROR_CONDITIONS_NOT_MET);
        }
        // TODO if mail is setup then check it
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $ret = new NewRegistrationTransformed();
        $ret->setName($this->name);
        $ret->setPassword($this->password);
        $this->password = '';
        if ($this->mail !== null) {
            $ret->setMail($this->mail);
        }
        return $ret;
    }

}
