<?php

namespace App\Smiles\Parser;

class ReferenceParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        $serverNumReferenceParser = new ServerNumReferenceParser();
        $serverNumReferenceResult = $serverNumReferenceParser->parse($strText);
        if ($serverNumReferenceResult->isAccepted()) {
            return $serverNumReferenceResult;
        }

        $pdbReferenceParser = new PdbReferenceParser();
        $pdbReferenceResult = $pdbReferenceParser->parse($strText);
        if ($pdbReferenceResult->isAccepted()) {
            return $pdbReferenceResult;
        }

        $smilesReferenceParser = new SmilesReferenceParser();
        $smilesResult = $smilesReferenceParser->parse($strText);
        if ($smilesResult->isAccepted()) {
            return $smilesResult;
        }

        $norineReferenceParser = new NorineReferenceParser();
        $norineReferenceResult = $norineReferenceParser->parse($strText);
        if ($norineReferenceResult->isAccepted()) {
            return $norineReferenceResult;
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match Reference');
    }

}
