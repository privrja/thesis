<?php

namespace App\Enum;

class ServerEnum {

    /** @var int servers */
    const PUBCHEM = 0;
    const CHEMSPIDER = 1;
    const NORINE = 2;
    const PDB = 3;
    const CHEBI = 4;
    const MASS_SPEC_BLOCKS = 5;
    const DOI = 6;
    const SIDEROPHORE_BASE = 7;
    const LIPID_MAPS = 8;
    const COCONUT = 9;

    /** @var array mapping int code to CycloBranch format string */
    public static $cycloBranchValues = [
        self::PUBCHEM => 'CID: ',
        self::CHEMSPIDER => 'CSID: ',
        self::PDB => 'PDB: ',
        self::CHEBI => 'ChEBI: ',
        self::SIDEROPHORE_BASE => 'SB: ',
        self::DOI => 'DOI: '
    ];

    public static function isOneOf(int $source): bool {
        return $source >= self::PUBCHEM && $source <= self::COCONUT;
    }

}
