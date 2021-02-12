<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Enum\ContainerModeEnum;

class CollaboratorStructure extends AbstractStructure {

    public $mode;

    public function checkInput(): Message {
        if (empty($this->mode)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (!ContainerModeEnum::isOneOf($this->mode)) {
            return new Message(ErrorConstants::ERROR_MODE_FORMAT);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new CollaboratorTransformed();
        $trans->setMode($this->mode);
        return $trans;
    }
}
