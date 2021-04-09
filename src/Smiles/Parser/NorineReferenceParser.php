<?php

namespace App\Smiles\Parser;

use App\Enum\ServerEnum;
use App\Structure\Reference;

class NorineReferenceParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws \App\Exception\IllegalStateException
     */
    public function parse($strText) {
        $norineIdParser = new NorineIdParser();
        $norineIdResult = $norineIdParser->parse($strText);
        if (!$norineIdResult->isAccepted()) {
            return self::reject();
        }
        $reference = new Reference();
        $reference->source = ServerEnum::NORINE;
        $reference->identifier = $norineIdResult->getResult();
        return new Accept($reference, $norineIdResult->getRemainder());
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match NORINE id');
    }

}
