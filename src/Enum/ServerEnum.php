<?php

namespace App\Enum;

class ServerEnum {

    /** @var int servers */
    const PUBCHEM = 0;
    const CHEMSPIDER = 1;
    const NORINE = 2;
    const PDB = 3;
    const CHEBI = 4;

    /** @var array mapping int code to string */
    public static $values = [
        self::PUBCHEM => "PubChem",
        self::CHEMSPIDER => "ChemSpider",
        self::NORINE => "Norine",
        self::PDB => "PDB",
        self::CHEBI => "ChEBI",
    ];

    public static $backValues = [
        'CID: ' => self::PUBCHEM,
        'CSID: ' => self::CHEMSPIDER,
        ':' => self::NORINE,
        'PDB: ' => self::PDB,
    ];

    /** @var array mapping int code to CycloBranch format string */
    public static $cycloBranchValues = [
        self::PUBCHEM => 'CID: ',
        self::CHEMSPIDER => 'CSID: ',
        self::PDB => 'PDB: ',
        self::CHEBI => 'ChEBI: ',
    ];

    public static function isOneOf(int $source): bool {
        return $source >= self::PUBCHEM && $source <= self::CHEBI;
    }

}
