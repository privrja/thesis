<?php

namespace App\Structure;

use App\Entity\Block;
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

    /** @var Block[] */
    public $blocks;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        // TODO: Implement jsonSerialize() method.
    }

}
