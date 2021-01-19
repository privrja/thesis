<?php

namespace App\Controller;

use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Structure\AbstractStructure;
use App\Structure\AbstractTransformed;
use App\Structure\NewRegistrationTransformed;

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
