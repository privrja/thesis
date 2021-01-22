<?php

namespace App\Smiles\Parser;

class UseRegexParser {

    /**
     * @param string $strText text to parse
     * @param string $strRegex regex
     * @param IParser $classReference reference to parser for reject message
     * @return Accept|Reject
     */
    public static function parseTextWithRegexType($strText, $strRegex, IParser $classReference) {
        $regexParser = new RegexParser();
        $result = $regexParser->parseTextWithRegexByLengthOne($strText, $strRegex);
        if ($result->isAccepted()) {
            return $result;
        }
        return $classReference::reject();
    }

}
