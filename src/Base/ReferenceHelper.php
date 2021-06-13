<?php

namespace App\Base;

use App\Enum\ServerEnum;

/**
 * Class ReferenceHelper
 * Helper for setup references in right format to exporting files
 */
class ReferenceHelper {

    const SMILES = "SMILES: ";
    const CHEBI = 'CHEBI: ';

    public static function reference($database, $reference, $smiles) {
        if (!isset($reference) || $reference === '') {
            return self::defaultValue($smiles);
        }
        switch ($database) {
            case ServerEnum::PUBCHEM:
                return ServerEnum::$cycloBranchValues[ServerEnum::PUBCHEM] . $reference;
            case ServerEnum::CHEMSPIDER:
                return ServerEnum::$cycloBranchValues[ServerEnum::CHEMSPIDER] . $reference;
            case ServerEnum::PDB:
                return ServerEnum::$cycloBranchValues[ServerEnum::PDB] . $reference;
            case ServerEnum::NORINE:
            case ServerEnum::COCONUT:
            case ServerEnum::NP_ATLAS:
                return $reference;
            case ServerEnum::CHEBI:
                if (str_contains($reference, 'CHEBI:')) {
                    if (str_contains($reference, self::CHEBI)) {
                        return strtoupper($reference);
                    } else {
                        return self::CHEBI . substr($reference, 6);
                    }
                } else {
                    return self::CHEBI . $reference;
                }
            case ServerEnum::SIDEROPHORE_BASE:
                return ServerEnum::$cycloBranchValues[ServerEnum::SIDEROPHORE_BASE] . $reference;
            case ServerEnum::DOI:
                return ServerEnum::$cycloBranchValues[ServerEnum::DOI] . $reference;
            default:
                return self::defaultValue($smiles);
        }
    }

    private static function defaultValue($smiles) {
        if (empty($smiles)) {
            return '';
        }
        return self::SMILES . $smiles;
    }

}
