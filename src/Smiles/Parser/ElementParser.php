<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;

class ElementParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        $orgParser = new OrganicSubsetParser();
        $orgResult = $orgParser->parse($strText);
        if ($orgResult->isAccepted()) {
            return $orgResult;
        }

        $bracketAtomParser = new BracketAtomParser();
        $atomResult = $bracketAtomParser->parse($strText);
        if ($atomResult->isAccepted()) {
            return $atomResult;
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match Element');
    }

}
