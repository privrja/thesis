<?php

namespace App\Structure;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;

class ModificationStructure extends AbstractStructure {

    public $modificationName;
    public $formula;

    /** @var float */
    public $mass;
    public $nTerminal = false;
    public $cTerminal = false;


    public function checkInput(): Message {
        if (empty($this->modificationName) || empty($this->formula)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new ModificationTransformed();
        $trans->setModificationName($this->modificationName);
        $trans->setFormula($this->formula);
        if(empty($this->mass)) {
            try {
                $trans->setMass(FormulaHelper::computeMass($this->formula));
            } catch (IllegalStateException $e) {
                /* Empty on purpose - mass can be null */
            }
        } else {
            $trans->setMass($this->mass);
        }
        if (isset($this->nTerminal)) {
            $trans->setNTerminal($this->nTerminal);
        }
        if (isset($this->cTerminal)) {
            $trans->setCTerminal($this->cTerminal);
        }
        return $trans;
    }

}
