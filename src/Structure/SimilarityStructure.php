<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;

class SimilarityStructure extends AbstractStructure {

    /** @var int[] */
    public $blocks;

    /** @var string */
    public $sequenceName;

    /** @var int|null */
    public $blockLength;

    public function checkInput(): Message {
        if (!empty($this->sequenceName) && (!isset($this->blocks) || empty($this->blocks))) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SimilarityTransformed();
        $trans->sequenceName = $this->sequenceName;
        if (!isset($this->blockLength)) {
            $trans->blockLength = sizeof($this->blocks);
        } else {
            $trans->blockLength = $this->blockLength;
        }
        $trans->blocks = '(';
        for ($i = 0; $i < $trans->blockLength; $i++) {
            if ($i === 0) {
                $trans->blocks .= $this->blocks[$i];
            } else {
                $trans->blocks .= ', ' . $this->blocks[$i];
            }
        }
        $trans->blocks .= ')';
        return $trans;
    }

}
