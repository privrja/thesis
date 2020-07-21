<?php


namespace App\Controller;


use App\Base\Message;
use App\Constant\ContainerVisibilityEnum;
use App\Constant\ErrorConstants;
use App\Structure\AbstractStructure;
use App\Structure\AbstractTransformed;

class UpdateContainerStructure extends AbstractStructure
{

    public $containerId;
    public $name;
    public $visibility;

    public function checkInput(): Message
    {
        if ($this->containerId === null or $this->containerId < 0) {
            return new Message(ErrorConstants::ERROR_EMPTY_ID);
        }
        if ($this->name === null && $this->visibility === null) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if ($this->visibility === null or $this->visibility === ContainerVisibilityEnum::TEXT_PUBLIC or $this->visibility === ContainerVisibilityEnum::TEXT_PRIVATE) {
            return Message::createOkMessage();
        } else {
            return new Message(ErrorConstants::ERROR_VISIBILITY_FORMAT);
        }
    }

    public function transform(): AbstractTransformed
    {
        $trans = new UpdateContainerTransformed();
        $trans->setContainerId($this->containerId);
        $trans->setName($this->name);
        if ($this->visibility !== null) {
            $trans->setVisibility(ContainerVisibilityEnum::$backValues[$this->visibility]);
        } else {
            $trans->setVisibility(null);
        }
        return $trans;
    }

}
