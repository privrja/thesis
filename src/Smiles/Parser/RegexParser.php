<?php

namespace App\Smiles\Parser;

class RegexParser {

    /**
     * Parse text
     * @param string $strText
     * @param string $strRegex
     * @return Accept|Reject
     */
    public function parseTextWithRegexByLengthOne($strText, $strRegex) {
        if (preg_match($strRegex, $strText)) {
            return new Accept($strText[0], substr($strText, 1));
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match regex');
    }

}
