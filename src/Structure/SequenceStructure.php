<?php

namespace App\Structure;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;
use App\Smiles\Enum\LossesEnum;
use App\Smiles\Graph;

class SequenceStructure extends AbstractStructure {

    public $sequenceName;
    public $formula;
    public $mass;
    public $smiles;
    public $source;
    public $identifier;
    public $sequence;
    public $sequenceType;
    public $decays;

    /** @var array */
    public $modifications;

    /** @var array */
    public $blocks;

    public function checkInput(): Message {
        if (empty($this->sequenceName) || empty($this->sequenceType) || empty($this->sequence)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (!isset(SequenceEnum::$backValues[$this->sequenceType])) {
            return new Message(ErrorConstants::ERROR_SEQUENCE_BAD_TYPE);
        }
        if (empty($this->formula) && empty($this->smiles)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (!isset($this->source) || empty($this->identifier)) {
            return new Message(ErrorConstants::ERROR_SERVER_IDENTIFIER_PROBLEM);
        }
        foreach ($this->modifications as $modification) {
            if (!isset($modification->databaseId) && (empty($modification->modificationName) || empty($modification->formula))) {
                return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
            }
        }
        foreach ($this->blocks as $block) {
            if (!isset($block->databaseId) && !isset($block->sameAs) && (empty($block->blockName) || empty($block->acronym) || (empty($block->formula) && empty($block->smiles)))) {
                return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
            }
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SequenceTransformed();
        $trans->setSequenceName($this->sequenceName);
        $trans->setSequenceType($this->sequenceType);
        $trans->setSequence($this->sequence);
        if (empty($this->smiles)) {
            $graph = new Graph($this->smiles);
            if (empty($this->formula)) {
                $trans->setFormula($graph->getFormula(LossesEnum::NONE));
            } else {
                $trans->setFormula($this->formula);
            }
            if (empty($this->mass)) {
                try {
                    $trans->setMass(FormulaHelper::computeMass($trans->getFormula()));
                } catch (IllegalStateException $e) {
                    /* Empty on purpose - mass can be null */
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
                    /* Empty on purpose - mass can be null */
                }
            } else {
                $trans->setMass($this->mass);
            }
        }
        $trans->setSource($this->source);
        $trans->setIdentifier($this->identifier);
        $trans->setDecays($this->decays);
        $trans->setModifications($this->modifications);
        $trans->setBlocks($this->blocks);
        return $trans;
    }
}
