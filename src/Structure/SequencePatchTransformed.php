<?php

namespace App\Structure;

class SequencePatchTransformed extends AbstractTransformed {

    /** @var string | null */
    public $sequenceName;

    /** @var string | null */
    public $formula;

    /** @var float | null */
    public $mass;

    /** @var int | null */
    public $source;

    /** @var string | null */
    public $identifier;

    /** @var string | null */
    public $sequenceType;

    /** @var array */
    public $family;

    /** @var array */
    public $organism;

}
