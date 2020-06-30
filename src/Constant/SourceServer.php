<?php

namespace App\Constant;

abstract class SourceServer {

    /** @var int servers */
    const PUBCHEM = 0;
    const CHEMSPIDER = 1;
    const NORINE = 2;
    const PDB = 3;
    const CHEBI = 4;

    /** @var array mapping int code to string */
    public static $values = [
        self::PUBCHEM => "PubChem",
        self::CHEBI => "ChEBI",
        self::NORINE => "Norine",
//        self::PDB => "PDB"
    ];

    /** @var array mapping int code to string */
    public static $allValues = [
        self::PUBCHEM => "PubChem",
        self::CHEMSPIDER => "ChemSpider",
        self::NORINE => "Norine",
        self::PDB => "PDB",
        self::CHEBI => "ChEBI"
    ];

    public static $backValues = [
        'CID: ' => self::PUBCHEM,
        'CSID: ' => self::CHEMSPIDER,
        ':' => self::NORINE,
        'PDB: ' => self::PDB,
    ];

    public static $cycloBranchValues = [
        self::PUBCHEM => 'CID: ',
        self::CHEMSPIDER => 'CSID: ',
        self::PDB => 'PDB: ',
        self::CHEBI => 'ChEBI: ',
    ];

    /**
     * Create link to web page to the molecule
     * @param int $intServerEnum enum code for server
     * @param string $strIdentifier molecule identifier
     * @return string link to molecule on web
     */
    public static function getLink($intServerEnum, $strIdentifier) {
        switch ($intServerEnum) {
            default:
            case self::PUBCHEM:
                return "https://pubchem.ncbi.nlm.nih.gov/compound/" . $strIdentifier;
            case self::CHEMSPIDER:
                return "http://www.chemspider.com/Chemical-Structure." . $strIdentifier . ".html";
            case self::CHEBI:
                return "https://www.ebi.ac.uk/chebi/searchId.do?chebiId=" . $strIdentifier;
            case self::PDB:
                return "http://www.rcsb.org/ligand/" . $strIdentifier;
            case self::NORINE:
                return "https://bioinfo.lifl.fr/norine/result.jsp?ID=" . $strIdentifier;
        }
    }

}
