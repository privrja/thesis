<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;

class UpdateContainerStructure extends AbstractStructure {

    public $containerName;
    public $visibility;

    public function checkInput(): Message {
        if (empty($this->containerName) && empty($this->visibility)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if ($this->visibility === ContainerVisibilityEnum::PUBLIC || $this->visibility === ContainerVisibilityEnum::PRIVATE || $this->visibility === null) {
            return Message::createOkMessage();
        } else {
            return new Message(ErrorConstants::ERROR_VISIBILITY_FORMAT);
        }
    }

    public function transform(): AbstractTransformed {
        $trans = new UpdateContainerTransformed();
        $trans->setContainerName($this->containerName);
        if ($this->visibility !== null) {
            $trans->setVisibility($this->visibility);
        } else {
            $trans->setVisibility(null);
        }
        return $trans;
    }

}
