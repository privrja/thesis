<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;
use App\Smiles\Graph;

class SequenceSmilesStructure extends AbstractStructure {

    public $smiles;

    public function checkInput(): Message {
        if (empty($this->smiles)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ParamTransformed();
        try {
            $graph = new Graph($this->smiles);
            $trans->param = $graph->getUniqueSmiles();
        } catch (IllegalStateException $e) {
            $trans->param = $this->smiles;
        }
        return $trans;
    }

}
