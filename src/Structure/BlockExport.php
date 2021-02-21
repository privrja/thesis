<?php

namespace App\Structure;

use App\Constant\EntityColumnsEnum;
use JsonSerializable;

class BlockExport implements JsonSerializable {

    /** @var int */
    public $id;

    /** @var string */
    public $blockName;

    /** @var string */
    public $acronym;

    /** @var string */
    public $formula;

    /** @var float */
    public $mass;

    /** @var string */
    public $losses;

    /** @var int */
    public $source;

    /** @var string */
    public $identifier;

    /** @var string */
    public $smiles;

    /** @var string */
    public $uniqueSmiles;

    /** @var int|null */
    public $sameAs = null;

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [EntityColumnsEnum::ID => $this->id,
            EntityColumnsEnum::BLOCK_NAME => $this->blockName,
            EntityColumnsEnum::ACRONYM => $this->acronym,
            EntityColumnsEnum::FORMULA => $this->formula,
            EntityColumnsEnum::MASS => $this->mass,
            EntityColumnsEnum::LOSSES => $this->losses,
            EntityColumnsEnum::SMILES => $this->smiles,
            EntityColumnsEnum::UNIQUE_SMILES => $this->uniqueSmiles,
            EntityColumnsEnum::SOURCE => $this->source,
            EntityColumnsEnum::IDENTIFIER => $this->identifier,
            'sameAs' => $this->sameAs
        ];
    }
}
