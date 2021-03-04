<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;
use App\Structure\Reference;

class SmilesReferenceParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        $parser = new StringParser();
        $result = $parser->parseTextWithTemplate($strText, 'SMILES: ');
        if (!$result->isAccepted()) {
            return self::reject();
        }

        $reference = new Reference();
        $reference->source = "SMILES";
        if (preg_match('/^\S+/', $result->getRemainder(), $matches)) {
            $length = strlen($matches[0]);
            $reference->identifier = $matches[0];
            return new Accept($reference, substr($result->getRemainder(), $length));
        }
        $reference->identifier = "";
        return new Accept($reference, $result->getRemainder());
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match SMILES: <SMLIES>');
    }

}
