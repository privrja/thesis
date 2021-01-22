<?php

namespace App\Smiles\Parser;

class PdbReferenceParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        // TODO
//        $pdbParser = new PdbParser();
//        $pdbResult = $pdbParser->parse($strText);
//        if (!$pdbResult->isAccepted()) {
//            return self::reject();
//        }
//        $pdbIdParser = new PdbIdParser();
//        $pdbIdResult = $pdbIdParser->parse($pdbResult->getRemainder());
//        if (!$pdbIdResult->isAccepted()) {
//            return self::reject();
//        }
//        $reference = new ReferenceTO();
//        $reference->database = $pdbResult->getResult();
//        $reference->identifier = $pdbIdResult->getResult();
//        return new Accept($reference, $pdbIdResult->getRemainder());
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject("Not match PDB: id");
    }

}
