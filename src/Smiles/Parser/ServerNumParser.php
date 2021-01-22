<?php

namespace App\Smiles\Parser;

class ServerNumParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        // TODO
//        if (preg_match('/^CSID: /', $strText)) {
//            return new Accept(ServerEnum::CHEMSPIDER, substr($strText, 6));
//        } else if (preg_match('/^CID: /', $strText)) {
//            return new Accept(ServerEnum::PUBCHEM, substr($strText, 5));
//        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match CSID: | CID: ');
    }

}
