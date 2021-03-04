<?php

namespace App\Smiles\Parser;

class NorineIdParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        if (preg_match('/^NOR[0-9]{5}/', $strText)) {
            $identifier = substr($strText, 0, 8);
            if ($identifier === 'NOR00000') {
                return self::reject();
            }
            return new Accept($identifier, substr($strText, 8));
        }
        return self::reject();
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match Norine identifier NORddddd');
    }

}
