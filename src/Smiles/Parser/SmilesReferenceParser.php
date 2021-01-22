<?php

namespace App\Smiles\Parser;

class SmilesReferenceParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        // TODO
//        $parser = new StringParser();
//        $result = $parser->parseTextWithTemplate($strText, 'SMILES: ');
//        if (!$result->isAccepted()) {
//            return self::reject();
//        }
//
//        $reference = new ReferenceTO();
//        $reference->database = "SMILES";
//        if (preg_match('/^\S+/', $result->getRemainder(), $matches)) {
//            $length = strlen($matches[0]);
//            $reference->identifier = $matches[0];
//            return new Accept($reference, substr($result->getRemainder(), $length));
//        }
//        $reference->identifier = "";
//        return new Accept($reference, $result->getRemainder());
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match SMILES: <SMLIES>');
    }

}
