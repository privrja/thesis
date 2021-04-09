<?php

namespace App\Structure;

use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;
use App\Smiles\Graph;

class SequenceSmilesStructure extends AbstractStructure {

    /** @var string[] */
    public $smiles;

    public function checkInput(): Message {
        if (empty($this->smiles)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ParamTransformed();
        $trans->param = [];
        foreach ($this->smiles as $smile) {
            try {
                $graph = new Graph($smile);
                array_push($trans->param, $graph->getUniqueSmiles());
            } catch (IllegalStateException $e) {
                array_push($trans->param, $smile);
            }
        }
        return $trans;
    }

}
