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
    public $blockLengthUnique;

    /** @var int|null */
    public $blockLength;

    public function checkInput(): Message {
        if (empty($this->sequenceName) && empty($this->blocks)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SimilarityTransformed();
        $trans->sequenceName = $this->sequenceName;
        $trans->blockLengthUnique = $this->setBlockLength($this->blockLengthUnique);
        $trans->blockLength = $this->setBlockLength($this->blockLength);
        $trans->blocks = '(';
        for ($i = 0; $i < $trans->blockLengthUnique; $i++) {
            if ($i === 0) {
                $trans->blocks .= $this->blocks[$i];
            } else {
                $trans->blocks .= ', ' . $this->blocks[$i];
            }
        }
        $trans->blocks .= ')';
        return $trans;
    }

    private function setBlockLength($length) {
        if (!isset($length)) {
            return sizeof($this->blocks);
        } else {
            return $length;
        }
    }

}
