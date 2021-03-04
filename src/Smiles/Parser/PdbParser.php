<?php

namespace App\Smiles\Parser;

use App\Enum\ServerEnum;

class PdbParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     * @throws \App\Exception\IllegalStateException
     */
    public function parse($strText) {
        $parser = new StringParser();
        $result = $parser->parseTextWithTemplate($strText, 'PDB: ');
        if ($result->isAccepted()) {
            return new Accept(ServerEnum::PDB, $result->getRemainder());
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject("Not match PDB: ");
    }

}
