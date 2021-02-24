<?php

namespace App\Structure;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Enum\SequenceEnum;
use App\Exception\IllegalStateException;
use App\Smiles\Enum\LossesEnum;
use App\Smiles\Graph;
use InvalidArgumentException;
use JsonSerializable;

class SequenceStructure extends AbstractStructure implements JsonSerializable {

    /** @var string */
    public $sequenceName;

    /** @var string|null */
    public $formula;

    /** @var float|null */
    public $mass;

    /** @var string|null */
    public $smiles;

    /** @var int|null */
    public $source;

    /** @var string|null */
    public $identifier;

    /** @var string */
    public $sequence;

    /** @var string */
    public $sequenceOriginal;

    /** @var string */
    public $sequenceType;

    /** @var string|null */
    public $decays;

    /** @var mixed|null */
    public $nModification;

    /** @var mixed|null */
    public $cModification;

    /** @var mixed|null */
    public $bModification;

    /** @var array */
    public $family;

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
        if (!isset($this->source) && !empty($this->identifier)) {
            return new Message(ErrorConstants::ERROR_SERVER_IDENTIFIER_PROBLEM);
        }
        if (isset($this->blocks)) {
            foreach ($this->blocks as $block) {
                if (!isset($block->databaseId) && !isset($block->sameAs) && (empty($block->blockName) || empty($block->acronym) || (empty($block->formula) && empty($block->smiles)))) {
                    return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
                }
            }
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SequenceTransformed();
        $trans->setSequenceName($this->sequenceName);
        $trans->setSequenceType($this->sequenceType);
        $trans->setSequence($this->sequence);
        $trans->setSequenceOriginal($this->sequenceOriginal);
        if (!empty($this->smiles)) {
            $trans->setSmiles($this->smiles);
            $graph = null;
            try {
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
            } catch (InvalidArgumentException $exception) {
                $trans->setUSmiles($this->smiles);
                $trans->setFormula($this->formula);
                if (empty($this->mass)) {
                    try {
                        $trans->setMass(FormulaHelper::computeMass($this->formula));
                    } catch
                    (IllegalStateException $e) {
                        /* Empty on purpose - mass can be null */
                    }
                } else {
                    $trans->setMass($this->mass);
                }
            }
        } else {
            $trans->setFormula($this->formula);
            if (empty($this->mass)) {
                try {
                    $trans->setMass(FormulaHelper::computeMass($this->formula));
                } catch
                (IllegalStateException $e) {
                    /* Empty on purpose - mass can be null */
                }
            } else {
                $trans->setMass($this->mass);
            }
        }
        $trans->setSource($this->source);
        $trans->setIdentifier($this->identifier);
        $trans->setDecays($this->decays);
        $trans->setNModification($this->nModification);
        $trans->setCModification($this->cModification);
        $trans->setBModification($this->bModification);
        if ($this->family === null) {
            $trans->setFamily([]);
        } else {
            $trans->setFamily($this->family);
        }
        if ($this->blocks === null) {
            $trans->setBlocks([]);
        } else {
            $trans->setBlocks($this->blocks);
        }
        return $trans;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        $res = ['sequenceType' => $this->sequenceType,
            'sequenceName' => $this->sequenceName,
            'formula' => $this->formula,
            'mass' => $this->mass,
            'sequence' => $this->sequence,
            'nModification' => $this->nModification,
            'cModification' => $this->cModification,
            'bModification' => $this->bModification,
            'source' => $this->source,
            'identifier' => $this->identifier];
        if (!empty($this->error)) {
            $res['error'] = $this->error;
        }
        return $res;
    }
}
