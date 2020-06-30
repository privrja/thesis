<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ContainerVisibilityEnum;

class NewContainerStructure extends AbstractStructure
{

    public $name;
    public $visibility;

    public function checkInput(): Message {
        if ($this->visibility === ContainerVisibilityEnum::TEXT_PUBLIC or $this->visibility === ContainerVisibilityEnum::TEXT_PRIVATE) {
            return new Message(true);
        } else {
            return new Message(false, 'Visibility has not supported format! Supported format is "R"|"RW"');
        }
    }

    public function transform(): AbstractTransformed {
        $trans = new NewContainerTransformed();
        $trans->setName($this->name);
        $trans->setVisibility(ContainerVisibilityEnum::$backValues[$this->visibility]);
        return $trans;
    }

}
