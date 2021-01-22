<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;
use App\Smiles\Charge;

class ChargeParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        if (!isset($strText) || "" === $strText) {
            return self::reject();
        }

        $signParser = new SignParser();
        $signResult = $signParser->parse($strText);
        if (!$signResult->isAccepted()) {
            return new Accept(new Charge(), $strText);
        }

        $signNextResult = $signParser->parse($signResult->getRemainder());
        if ($signNextResult->isAccepted() && $signNextResult->getResult() === $signResult->getResult()) {
            return new Accept(new Charge($signResult->getResult(), 2), $signNextResult->getRemainder());
        }

        $natParser = new NatParser();
        $natResult = $natParser->parse($signResult->getRemainder());
        if ($natResult->isAccepted()) {
            return new Accept(new Charge($signResult->getResult(), $natResult->getResult()), $natResult->getRemainder());
        }
        return new Accept(new Charge($signResult->getResult(), 1), $signResult->getRemainder());
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match charge');
    }

}
