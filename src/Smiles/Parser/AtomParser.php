<?php

namespace App\Smiles\Parser;

class AtomParser implements IParser {

    /**
     * Parse text
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        $intIndex = 0;
        if (empty($strText) || "" === $strText || !ctype_alpha($strText[$intIndex])) {
            return self::reject();
        }
        $strName = "";
        $intLength = strlen($strText);
        while (ctype_alpha($strText[$intIndex])) {
            if ($intIndex > 0 && ctype_upper($strText[$intIndex])) {
                break;
            }
            $strName .= $strText[$intIndex];
            $intIndex++;
            if ($intIndex >= $intLength) {
                return new Accept($strName, '');
            }
        }
        return new Accept($strName, substr($strText, $intIndex));
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match Atom in []');
    }

}
