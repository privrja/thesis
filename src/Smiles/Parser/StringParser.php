<?php

namespace App\Smiles\Parser;

class StringParser {

    /**
     * Parse string with template
     * @param $strText
     * @param $strTemplate
     * @return Accept|Reject
     */
    public function parseTextWithTemplate($strText, $strTemplate) {
        if (!isset($strTemplate) || !isset($strText) || "" === $strText || "" === $strTemplate) {
            return self::reject();
        }

        if (preg_match('/^' . $strTemplate . '/', $strText)) {
            return new Accept($strTemplate, substr($strText, strlen($strTemplate)));
        }
        return self::reject();
    }

    public static function reject() {
        return new Reject('Not match template');
    }

}
