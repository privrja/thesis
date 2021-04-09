<?php

namespace App\Structure;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Enum\SequenceEnum;
use App\Enum\ServerEnum;
use App\Exception\IllegalStateException;

class SequencePatchStructure extends AbstractStructure {

    /** @var string|null */
    public $sequenceName;

    /** @var string|null */
    public $formula;

    /** @var float|null */
    public $mass;

    /** @var int|null */
    public $source;

    /** @var string|null */
    public $identifier;

    /** @var string|null */
    public $sequenceType;

    /** @var array */
    public $family;

    /** @var array */
    public $organism;

    public function checkInput(): Message {
        if (empty($this->sequenceName) && empty($this->formula) && !isset($this->mass)
            && !isset($this->source) && empty($this->identifier) && empty($this->sequenceType)
            && !isset($this->family) && !isset($this->organism)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (isset($this->sequenceType) && !isset(SequenceEnum::$backValues[$this->sequenceType])) {
            return new Message(ErrorConstants::ERROR_SEQUENCE_BAD_TYPE);
        }
        if (isset($this->source) && !ServerEnum::isOneOf($this->source)) {
            return new Message(ErrorConstants::ERROR_SERVER_IDENTIFIER_PROBLEM);
        }
        return Message::createOkMessage();
    }

    public function transform(): AbstractTransformed {
        $trans = new SequencePatchTransformed();
        $trans->sequenceName = $this->sequenceName;
        $trans->sequenceType = $this->sequenceType;
        $trans->formula = $this->formula;
        if (!empty($this->mass)) {
            $trans->mass = $this->mass;
        } else {
            if (!empty($this->formula)) {
                try {
                    $trans->mass = FormulaHelper::computeMass($trans->formula);
                } catch (IllegalStateException $e) {
                    /* Empty on purpose */
                }
            }
        }
        $trans->source = $this->source;
        $trans->identifier = $this->identifier;
        $trans->family = $this->family;
        $trans->organism = $this->organism;
        return $trans;
    }

}