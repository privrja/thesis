<?php

namespace App\Smiles\Parser;

use App\Smiles\Enum\BondTypeEnum;

class BondParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        if (!isset($strText) || "" === $strText) {
            return self::reject();
        }
        foreach (BondTypeEnum::$backValues as $bond => $value) {
            if ($bond == '') {
                continue;
            }
            if (preg_match('/^' . $bond . '/', $strText)) {
                return new Accept($bond, substr($strText, 1));
            }
        }
        return new Accept('', $strText);
    }

    /**
     * Get instance of Reject
     */
    public static function reject() {
        return new Reject('Not match bond, end of string or null given');
    }

}
