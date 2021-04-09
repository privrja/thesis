<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class SetupSimilarityStructure extends AbstractStructure {

    public $similarity;

    public function checkInput(): Message {
        if (empty($this->similarity)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if ($this->similarity !== 'name' && $this->similarity !== 'tanimoto') {
            return new Message(ErrorConstants::ERROR_SIMILARITY_FORMAT);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SetupSimilarityTransformed();
        $trans->similarity = $this->similarity;
        return  $trans;
    }
}
