<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;

class SmilesNumberParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        $firstDigit = new FirstDigitParser();
        $resultDigit = $firstDigit->parse($strText);
        if ($resultDigit->isAccepted()) {
            return $resultDigit;
        }
        $natParser = new MoreDigitNumberParser();
        $resultNat = $natParser->parse($strText);
        if ($resultNat->isAccepted()) {
            return $resultNat;
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match SMILES number, format: %[1-9][0-9]*% + [0-9]');
    }

}
