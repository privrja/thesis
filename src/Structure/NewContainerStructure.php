<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;

class NewContainerStructure extends AbstractStructure {

    public $name;
    public $visibility;

    public function checkInput(): Message {
        if ($this->visibility === ContainerVisibilityEnum::TEXT_PUBLIC or $this->visibility === ContainerVisibilityEnum::TEXT_PRIVATE) {
            return Message::createOkMessage();
        } else {
            return new Message(ErrorConstants::ERROR_VISIBILITY_FORMAT);
        }
    }

    public function transform(): AbstractTransformed {
        $trans = new NewContainerTransformed();
        $trans->setName($this->name);
        $trans->setVisibility(ContainerVisibilityEnum::$backValues[$this->visibility]);
        return $trans;
    }

}
