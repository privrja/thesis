<?php

namespace App\Smiles\Parser;


class NorineParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        // TODO
//        $parser = new StringParser();
//        $result = $parser->parseTextWithTemplate($strText, ': ');
//        if ($result->isAccepted()) {
//            return new Accept(ServerEnum::NORINE, $result->getRemainder());
//        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject("Not match : ");
    }

}
