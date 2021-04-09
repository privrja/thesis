<?php

namespace App\Smiles\Parser;

use App\Exception\IllegalStateException;

class NatParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws IllegalStateException
     */
    public function parse($strText) {
        $firstDigitParser = new FirstDigitParser();
        $firstResult = $firstDigitParser->parse($strText);
        if (!$firstResult->isAccepted()) {
            return self::reject();
        }
        return $this->parseNextDigits($firstResult->getResult(), $firstResult->getRemainder());
    }

    /**
     * @param string $strNumber
     * @param string $strRemainder
     * @return Accept
     * @throws IllegalStateException
     */
    private function parseNextDigits($strNumber, $strRemainder) {
        $nextDigitParser = new NextDigitParser();
        $nextDigitResult = $nextDigitParser->parse($strRemainder);
        while ($nextDigitResult->isAccepted()) {
            $strNumber .= $nextDigitResult->getResult();
            $strRemainder = $nextDigitResult->getRemainder();
            $nextDigitResult = $nextDigitParser->parse($nextDigitResult->getRemainder());
        }
        return new Accept($strNumber, $strRemainder);
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match positive number');
    }

}
