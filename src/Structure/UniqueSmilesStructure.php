<?php

namespace App\Structure;

use App\Base\Message;

class UniqueSmilesStructure extends AbstractStructure {

    /** @var string */
    public $smiles;

    public function checkInput(): Message {
        if (empty($this->smiles)) {
            return new Message("SMILES is empty");
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $sm = new UniqueSmilesTransformed();
        $sm->setSmiles($this->smiles);
        return $sm;
    }

}
