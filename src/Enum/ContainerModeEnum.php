<?php

namespace App\Enum;

/**
 * Class ModeEnum
 * Modes of user for container R or RW
 * @package App\Constant
 */
abstract class ContainerModeEnum {

    const R = 'R';
    const RW = 'RW';
    const RWM = 'RWM';

    public static function isOneOf(string $value): bool {
        switch ($value) {
            case self::R:
            case self::RW:
            case self::RWM:
                return true;
            default:
                return false;
        }
    }

}
