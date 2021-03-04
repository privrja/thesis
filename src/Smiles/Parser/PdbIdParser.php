<?php

namespace App\Smiles\Parser;

class PdbIdParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        if (preg_match('/^[A-Z0-9]{4}/', $strText)) {
            return new Accept(substr($strText, 0, 4), substr($strText, 4));
        }
        if (preg_match('/^[A-Z0-9]{3}/', $strText)) {
            return new Accept(substr($strText, 0, 3), substr($strText, 3));
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject("Not match PDB idetifier XXX");
    }

}
