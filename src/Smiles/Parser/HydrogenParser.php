<?php

namespace App\Smiles\Parser;

class HydrogenParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        return UseRegexParser::parseTextWithRegexType($strText, '/^H/', $this);
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match H');
    }

}
