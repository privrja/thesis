<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class SequenceFormulaStructure extends AbstractStructure {

    public $sequenceFormula;

    public function checkInput(): Message {
        if (empty($this->sequenceFormula)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ParamTransformed();
        $trans->param = $this->sequenceFormula;
        return $trans;
    }
}
