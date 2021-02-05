<?php

namespace App\Structure;

class SequenceEnum {

    const LINEAR = 0;
    const CYCLIC = 1;
    const BRANCHED = 2;
    const BRANCH_CYCLIC = 3;
    const LINEAR_POLYKETIDE = 4;
    const CYCLIC_POLYKETIDE = 5;
    const OTHER = 6;

    /** @var array mapping int code to string */
    public static $values = [
        self::LINEAR => "linear",
        self::CYCLIC => "cyclic",
        self::BRANCHED => "branched",
        self::BRANCH_CYCLIC => "branch-cyclic",
        self::LINEAR_POLYKETIDE => "linear-polyketide",
        self::CYCLIC_POLYKETIDE => "cyclic-polyketide",
        self::OTHER => "other"
    ];

    static function isOneOf(int $value): bool {
        return $value >= self::LINEAR && $value <= self::OTHER;
    }

}
