<?php

namespace App\Smiles\Enum;

class BondTypeEnum {

    /** @var int simple bond - */
    const SIMPLE = 1;

    /** @var int double bond = */
    const DOUBLE = 2;

    /** @var int triple bond # */
    const TRIPLE = 3;

    /** @var array map bond numbers to strings */
    public static $values = array(
        self::SIMPLE => '',
        self::DOUBLE => '=',
        self::TRIPLE => '#',
    );

    /** @var array bond string to bond numbers */
    public static $backValues = array(
        '' => self::SIMPLE,
        '-' => self::SIMPLE,
        '=' => self::DOUBLE,
        '#' => self::TRIPLE,
    );

    /**
     * Return true when bond is simple
     * otherwise false
     * @param string $strBond
     * @return bool
     */
    public static function isSimple(string $strBond) {
        return self::$backValues[$strBond] == 1;
    }

    /**
     * Return true when bond is = or #
     * otherwise false
     * @param string $strBond
     * @return bool
     */
    public static function isMultipleBinding(string $strBond) {
        return self::$backValues[$strBond] > 1;
    }

}
