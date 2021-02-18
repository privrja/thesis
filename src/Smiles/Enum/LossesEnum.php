<?php

namespace App\Smiles\Enum;

use App\Enum\PeriodicTableSingleton;

class LossesEnum {

    const NONE = 0;

    const H2O = 1;

    const H2 = 2;

    public static function toLosses(?string $losses) {
        switch ($losses) {
            case 'H20':
                return self::H2O;
            case 'H2':
                return self::H2;
            default:
                return self::NONE;
        }
    }

    public static function subtractLosses(int $losses, array $arMap) {
        switch ($losses) {
            case LossesEnum::NONE:
                return $arMap;
            case LossesEnum::H2:
                return self::remove($arMap, PeriodicTableSingleton::H, 2);
            case LossesEnum::H2O:
            default:
                $arMap = self::remove($arMap, PeriodicTableSingleton::H, 2);
                return self::remove($arMap, PeriodicTableSingleton::O, 1);
        }
    }

    private static function remove(array $arMap, string $atomName, int $count) {
        if (isset($arMap[$atomName])) {
            $arMap[$atomName] -= $count;
            if ($arMap[$atomName] < 1) {
                unset($arMap[$atomName]);
            }
        }
        return $arMap;
    }

}
