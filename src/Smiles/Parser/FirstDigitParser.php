<?php

namespace App\Smiles\Parser;

class FirstDigitParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        return UseRegexParser::parseTextWithRegexType($strText, '/^[1-9]/', $this);
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match [1-9]');
    }

}
