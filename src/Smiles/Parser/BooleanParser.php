<?php

namespace App\Smiles\Parser;

class BooleanParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        return UseRegexParser::parseTextWithRegexType($strText, '/^[01]/', $this);
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match boolean in format 0 or 1');
    }

}
