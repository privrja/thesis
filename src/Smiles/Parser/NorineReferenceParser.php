<?php

namespace App\Smiles\Parser;

class NorineReferenceParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        // TODO
//        $norineIdParser = new NorineIdParser();
//        $norineIdResult = $norineIdParser->parse($strText);
//        if (!$norineIdResult->isAccepted()) {
//            return self::reject();
//        }
//        $reference = new ReferenceTO();
//        $reference->database = ServerEnum::NORINE;
//        $reference->identifier = $norineIdResult->getResult();
//        return new Accept($reference, $norineIdResult->getRemainder());
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match NORINE id');
    }

}
