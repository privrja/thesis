<?php

namespace App\Smiles\Parser;

use App\Enum\PeriodicTableSingleton;
use App\Exception\IllegalStateException;

class OrganicSubsetParser implements IParser {

    const LITERALS = ["Br", "Cl", "B", "C", "N", "O", "P", "S", "F", "I", "b", "c", "n", "o", "p", "s", "f", "i"];

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        $stringParser = new StringParser();
        foreach (self::LITERALS as $LITERAL) {
            $parseResult = $stringParser->parseTextWithTemplate($strText, $LITERAL);
            if ($parseResult->isAccepted()) {
                return new Accept(clone(PeriodicTableSingleton::getInstance()->getAtoms()[$LITERAL]), $parseResult->getRemainder());
            }
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match Organic Subset');
    }

}
