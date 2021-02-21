<?php

namespace App\Structure;

use App\Entity\Modification;
use App\Entity\SequenceFamily;
use JsonSerializable;

class SequenceExport implements JsonSerializable {

    /** @var string */
    public $sequenceName;

    /** @var string */
    public $sequenceType;

    /** @var string */
    public $sequence;

    /** @var string */
    public $smiles;

    /** @var string */
    public $formula;

    /** @var float */
    public $mass;

    /** @var string */
    public $decays;

    /** @var int */
    public $source;

    /** @var string */
    public $identifier;

    /** @var SequenceFamily[] */
    public $family = [];

    /** @var Modification */
    public $nModification;

    /** @var Modification */
    public $cModification;

    /** @var Modification */
    public $bModification;

    /** @var BlockExport[] */
    public $blocks = [];

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [
            'sequenceName' => $this->sequenceName,
            'sequenceType' => $this->sequenceType,
            'sequence' => $this->sequence,
            'smiles' => $this->smiles,
            'formula' => $this->formula,
            'mass' => $this->mass,
            'decays' => $this->decays,
            'source' => $this->source,
            'identifier' => $this->identifier,
            'family' => $this->family,
            'nModification' => $this->nModification,
            'cModification' => $this->cModification,
            'bModification' => $this->bModification,
            'blocks' => $this->blocks
            ];
    }

}