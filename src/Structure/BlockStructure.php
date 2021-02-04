<?php

namespace App\Structure;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Enum\ServerEnum;
use App\Exception\IllegalStateException;
use App\Smiles\Enum\LossesEnum;
use App\Smiles\Graph;

class BlockStructure extends AbstractStructure {

    public $blockName;
    public $acronym;
    public $formula;
    public $mass;
    public $losses;
    public $smiles;
    public $source;
    public $identifier;

    public function checkInput(): Message {
        if (empty($this->blockName) || empty($this->acronym)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (!ServerEnum::isOneOf($this->source)) {
            return new Message(ErrorConstants::ERROR_SERVER_IDENTIFIER);
        }
        if (!empty($this->source) && empty($this->identifier)) {
            return new Message(ErrorConstants::ERROR_SERVER_IDENTIFIER_PROBLEM);
        }
        if (empty($this->formula) && empty($this->smiles)) {
            return new Message(ErrorConstants::ERROR_FORMULA_OR_SMILES);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new BlockTransformed();
        $trans->setblockName($this->blockName);
        $trans->setAcronym($this->acronym);
        $trans->setSource($this->source);
        $trans->setIdentifier($this->identifier);
        $trans->setLosses($this->losses);
        $trans->setSmiles($this->smiles);
        if (!empty($this->smiles)) {
            $graph = new Graph($this->smiles);
            $eLosses = LossesEnum::toLosses($this->losses);
            if (empty($this->formula)) {
                $trans->setFormula($graph->getFormula($eLosses));
            } else {
                $trans->setFormula($this->formula);
            }
            if (empty($this->mass)) {
                try {
                    $trans->setMass(FormulaHelper::computeMass($trans->getFormula()));
                } catch (IllegalStateException $e) {
                }
            } else {
                $trans->setMass($this->mass);
            }
            try {
                $trans->setUSmiles($graph->getUniqueSmiles());
            } catch (IllegalStateException $e) {
                $trans->setUSmiles($this->smiles);
            }
        } else {
            $trans->setFormula($this->formula);
            if (empty($this->mass)) {
                try {
                    $trans->setMass(FormulaHelper::computeMass($this->formula));
                } catch (IllegalStateException $e) {
                }
            } else {
                $trans->setMass($this->mass);
            }
        }
        return $trans;
    }

}
