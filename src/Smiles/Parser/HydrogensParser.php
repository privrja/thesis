<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;

class HydrogensParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        $hydrogenParser = new HydrogenParser();
        $hydrogenResult = $hydrogenParser->parse($strText);
        if (!$hydrogenResult->isAccepted()) {
            return self::reject();
        }
        $natParser = new NatParser();
        $natResult = $natParser->parse($hydrogenResult->getRemainder());
        if (!$natResult->isAccepted()) {
            return new Accept(1, $hydrogenResult->getRemainder());
        }
        return $natResult;
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match hydrogen and number');
    }

}
