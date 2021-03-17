<?php

namespace App\Structure;

use App\Entity\Modification;
use App\Entity\Organism;
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
    public $sequenceOriginal;

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

    /** @var Organism[] */
    public $organism = [];

    /** @var Modification */
    public $nModification;

    /** @var Modification */
    public $cModification;

    /** @var Modification */
    public $bModification;

    /** @var BlockExport[] */
    public $blocks = [];

    /** @var string|null */
    public $uniqueSmiles;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [
            'sequenceName' => $this->sequenceName,
            'sequenceType' => $this->sequenceType,
            'sequence' => $this->sequence,
            'sequenceOriginal' => $this->sequenceOriginal,
            'smiles' => $this->smiles,
            'uniqueSmiles' =>$this->uniqueSmiles,
            'formula' => $this->formula,
            'mass' => $this->mass,
            'decays' => $this->decays,
            'source' => $this->source,
            'identifier' => $this->identifier,
            'family' => $this->family,
            'organism' => $this->organism,
            'nModification' => $this->nModification,
            'cModification' => $this->cModification,
            'bModification' => $this->bModification,
            'blocks' => $this->blocks
            ];
    }

}
