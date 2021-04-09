<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;
use App\Structure\Reference;

class ServerNumReferenceParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        $serverNumParser = new ServerNumParser();
        $serverResult = $serverNumParser->parse($strText);
        if (!$serverResult->isAccepted()) {
            return self::reject();
        }
        $numberParser = new NatParser();
        $numberResult = $numberParser->parse($serverResult->getRemainder());
        if (!$numberResult->isAccepted()) {
            $zeroParser = new ZeroParser();
            $zeroResult = $zeroParser->parse($serverResult->getRemainder());
            if ($zeroResult->isAccepted()) {
                $reference = new Reference();
                $reference->source = null;
                $reference->identifier = null;
                return new Accept($reference, $zeroResult->getRemainder());
            }
            return self::reject();
        }
        $reference = new Reference();
        $reference->source = $serverResult->getResult();
        $reference->identifier = $numberResult->getResult();
        return new Accept($reference, $numberResult->getRemainder());
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match CSID: number | CID: number');
    }

}
