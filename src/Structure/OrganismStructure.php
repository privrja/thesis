<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class OrganismStructure extends AbstractStructure {

    /** @var string */
    public $organism;

    public function checkInput(): Message {
        if(empty($this->organism)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new OrganismTransformed();
        $trans->organism = $this->organism;
        return $trans;
    }

}
