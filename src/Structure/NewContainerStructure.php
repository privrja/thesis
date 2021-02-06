<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Enum\ContainerVisibilityEnum;

/**
 * Class NewContainerStructure
 * @package App\Structure
 */
class NewContainerStructure extends AbstractStructure {

    /**
     * @var string
     */
    public $containerName;

    /**
     * @var string $visibility values: PUBLIC, PRIVATE
     */
    public $visibility;

    public function checkInput(): Message {
        if (empty($this->containerName)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        } else if ($this->visibility === ContainerVisibilityEnum::PUBLIC || $this->visibility === ContainerVisibilityEnum::PRIVATE) {
            return Message::createOkMessage();
        } else {
            return new Message(ErrorConstants::ERROR_VISIBILITY_FORMAT);
        }
    }

    public function transform(): AbstractTransformed {
        $trans = new NewContainerTransformed();
        $trans->setContainerName($this->containerName);
        $trans->setVisibility($this->visibility);
        return $trans;
    }

}
