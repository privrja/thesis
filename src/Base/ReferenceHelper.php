<?php

namespace App\Base;

use App\Enum\ServerEnum;

/**
 * Class ReferenceHelper
 * Helper for setup references in right format to exporting files
 */
class ReferenceHelper {

    const SMILES = "SMILES: ";

    public static function reference($database, $reference, $smiles) {
        if ($reference == 0) {
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
                return $reference;
            default:
                return self::defaultValue($smiles);
        }
    }

    private static function defaultValue($smiles) {
        return self::SMILES . $smiles;
    }

}
