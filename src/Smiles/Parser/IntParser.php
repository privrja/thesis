<?php

namespace App\Smiles\Parser;

class IntParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws \App\Exception\IllegalStateException
     */
    public function parse($strText) {
        $strSign = "";
        $signParser = new SignParser();
        $signResult = $signParser->parse($strText);
        if ($signResult->isAccepted()) {
            if ($signResult->getResult() === "-") {
                $strSign .= '-';
            }
            $strText = $signResult->getRemainder();
        }

        $natParser = new NatParser();
        $natResult = $natParser->parse($strText);
        if (!$natResult->isAccepted()) {
            return self::reject();
        }

        return new Accept($strSign . $natResult->getResult(), $natResult->getRemainder());
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match integer');
    }

}
