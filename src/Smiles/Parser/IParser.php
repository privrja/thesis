<?php

namespace App\Smiles\Parser;

interface IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse(string $strText);

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject();
}
