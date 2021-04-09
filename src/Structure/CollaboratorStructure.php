<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Enum\ContainerModeEnum;

class CollaboratorStructure extends AbstractStructure {

    /** @var string */
    public $user;

    /** @var string */
    public $mode;

    public function checkInput(): Message {
        if (empty($this->mode) || empty($this->user)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (!ContainerModeEnum::isOneOf($this->mode)) {
            return new Message(ErrorConstants::ERROR_MODE_FORMAT);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new CollaboratorTransformed();
        $trans->user = $this->user;
        $trans->mode = $this->mode;
        return $trans;
    }
}
