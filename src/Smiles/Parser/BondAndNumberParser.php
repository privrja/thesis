<?php

namespace App\Smiles\Parser;

use App\Smiles\Digit;

class BondAndNumberParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws \App\Exception\IllegalStateException
     */
    public function parse($strText) {
        $bondParser = new BondParser();
        $bondResult = $bondParser->parse($strText);
        if (!$bondResult->isAccepted()) {
            return self::reject();
        }
        $smilesNumberParser = new SmilesNumberParser();
        $numberResult = $smilesNumberParser->parse($bondResult->getRemainder());
        if ($numberResult->isAccepted()) {
            return new Accept(new Digit($numberResult->getResult(), false, $bondResult->getResult()), $numberResult->getRemainder());
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match bond and number');
    }

}
