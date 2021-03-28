<?php

namespace App\Structure;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Constant\ErrorConstants;
use App\Enum\ServerEnum;
use App\Exception\IllegalStateException;
use App\Smiles\Enum\LossesEnum;
use App\Smiles\Graph;
use InvalidArgumentException;
use JsonSerializable;

class BlockStructure extends AbstractStructure implements JsonSerializable {

    /** @var string */
    public $blockName;

    /** @var string */
    public $acronym;

    /** @var string|null */
    public $formula;

    /** @var float|null */
    public $mass;

    /** @var string|null */
    public $losses;

    /** @var string|null */
    public $smiles;

    /** @var int|null */
    public $source;

    /** @var string|null */
    public $identifier;

    /** @var array */
    public $family;

    public function checkInput(): Message {
        if (empty($this->blockName) || empty($this->acronym)) {
            return new Message(ErrorConstants::ERROR_EMPTY_PARAMS);
        }
        if (isset($this->source) && !ServerEnum::isOneOf($this->source)) {
            return new Message(ErrorConstants::ERROR_SERVER_IDENTIFIER);
        }
        if (!isset($this->source) && !empty($this->identifier)) {
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
        if (str_contains('(-2H)', $this->blockName)) {
            $trans->isPolyketide = true;
        }
        $trans->setAcronym($this->acronym);
        $trans->setSource($this->source);
        $trans->setIdentifier($this->identifier);
        $trans->setLosses($this->losses);
        $trans->setSmiles($this->smiles);
        if ($this->family === null) {
            $trans->family = [];
        } else {
            $trans->family = $this->family;
        }
        if (!empty($this->smiles)) {
            try {
                $graph = new Graph($this->smiles);
            } catch (InvalidArgumentException $error) {
                if (!empty($this->formula)) {
                    $trans->setFormula($this->formula);
                }
                if (empty($this->mass) && !empty($this->formula)) {
                    try {
                        $trans->setMass(FormulaHelper::computeMass($trans->getFormula()));
                    } catch (IllegalStateException $e) {
                        $trans->mass = 0;
                    } catch (InvalidArgumentException $e) {
                        $trans->mass = 0;
                    }
                } else {
                    $trans->setMass($this->mass);
                }
                $trans->smiles = $this->smiles;
                $trans->uSmiles = $this->smiles;
                return $trans;
            }
            if (empty($this->formula)) {
                $trans->setFormula($graph->getFormula(LossesEnum::H2O));
            } else {
                $trans->setFormula($this->formula);
            }
            if (empty($this->mass)) {
                try {
                    $trans->setMass(FormulaHelper::computeMass($trans->getFormula()));
                } catch (IllegalStateException $e) {
                    $trans->mass = 0;
                } catch (InvalidArgumentException $e) {
                    $trans->mass = 0;
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
                    $trans->mass = 0;
                } catch (InvalidArgumentException $e) {
                    $trans->mass = 0;
                }
            } else {
                $trans->setMass($this->mass);
            }
        }
        return $trans;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        $res = ['blockName' => $this->blockName, 'acronym' => $this->acronym, 'formula' => $this->formula, 'mass' => $this->mass, 'losses' => $this->losses === null ? '' : $this->losses, 'smiles' => $this->smiles, 'source' => $this->source, 'identifier' => $this->identifier];
        if (!empty($this->error)) {
            $res['error'] = $this->error;
        }
        return $res;
    }

}
