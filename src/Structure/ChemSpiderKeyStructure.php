<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class ChemSpiderKeyStructure extends AbstractStructure {

    public $apiKey;

    public function checkInput(): Message {
        if (empty($this->apiKey)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ChemSpiderKeyTransformed();
        $trans->apiKey = $this->apiKey;
        return $trans;
    }
}
