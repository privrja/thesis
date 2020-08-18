<?php


namespace App\Controller;


use App\Base\Message;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;
use App\Structure\AbstractStructure;
use App\Structure\AbstractTransformed;

class UpdateContainerStructure extends AbstractStructure
{

    public $name;
    public $visibility;

    public function checkInput(): Message
    {
        if ($this->name === null && $this->visibility === null) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if ($this->visibility === null || $this->visibility === ContainerVisibilityEnum::PUBLIC || $this->visibility === ContainerVisibilityEnum::PRIVATE) {
            return Message::createOkMessage();
        } else {
            return new Message(ErrorConstants::ERROR_VISIBILITY_FORMAT);
        }
    }

    public function transform(): AbstractTransformed
    {
        $trans = new UpdateContainerTransformed();
        $trans->setName($this->name);
        if ($this->visibility !== null) {
            $trans->setVisibility($this->visibility);
        } else {
            $trans->setVisibility(null);
        }
        return $trans;
    }

}
