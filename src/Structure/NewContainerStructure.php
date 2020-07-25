<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;

/**
 * Class NewContainerStructure
 * @package App\Structure
 */
class NewContainerStructure extends AbstractStructure {

    /**
     * @var string
     */
    public $name;

    /**
     * @var string $visibility values: PUBLIC, PRIVATE
     */
    public $visibility;

    public function checkInput(): Message {
        if ($this->visibility === ContainerVisibilityEnum::TEXT_PUBLIC || $this->visibility === ContainerVisibilityEnum::TEXT_PRIVATE) {
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
