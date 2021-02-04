<?php

namespace App\Base;

use App\Enum\PeriodicTableSingleton;
use App\Exception\IllegalStateException;
use App\Smiles\Enum\LossesEnum;
use App\Smiles\Graph;
use App\Smiles\Parser\AtomParser;
use App\Smiles\Parser\IntParser;
use App\Structure\AtomCount;
use Exception;
use InvalidArgumentException;

class FormulaHelper {

    /**
     * Compute mass
     * @param string $strFormula formula like 'C6H6'
     * @return float mass
     * @throws IllegalStateException
     */
    public static function computeMass($strFormula) {
        if (!isset($strFormula) || empty($strFormula)) {
            throw new InvalidArgumentException();
        }
        $mass = 0;
        while (!empty($strFormula)) {
            $atomCount = self::getAtomCount($strFormula);
            try {
                if (!isset(PeriodicTableSingleton::getInstance()->getAtoms()[$atomCount->getAtom()])) {
                    throw new InvalidArgumentException('Wrong atom name ' . $atomCount->getAtom() . ' in formula!');
                }
                $mass += (PeriodicTableSingleton::getInstance()->getAtoms())[$atomCount->getAtom()]->getMass() * $atomCount->getCount();
            } catch (Exception $exception) {
                throw new InvalidArgumentException();
            }
        }
        return $mass;
    }

    /**
     * Get Formula from SMILES
     * @param string $strSmiles SMILES
     * @param int $losses
     * @see LossesEnum
     * @return string formula
     */
    public static function formulaFromSmiles(string $strSmiles, int $losses = LossesEnum::NONE) {
        $graph = new Graph($strSmiles);
        return $graph->getFormula($losses);
    }

    /**
     * Extract losses from formula
     * @param string $strFormula formula like 'C6H6'
     * @param int $losses
     * @return string formula with extracted losses
     * @throws IllegalStateException
     * @see LossesEnum
     */
    public static function formulaWithLosses(string $strFormula, int $losses = LossesEnum::NONE) {
        if (!isset($strFormula) || empty($strFormula)) {
            throw new InvalidArgumentException();
        }
        $arMap = [];
        while (!empty($strFormula)) {
            $atomCount = self::getAtomCount($strFormula);
            $arMap[$atomCount->getAtom()] = $atomCount->getCount();
        }
        return self::formulaExtractLosses($arMap, $losses);
    }

    /**
     * @param string $strFormula
     * @return AtomCount
     * @throws IllegalStateException
     */
    private static function getAtomCount(string &$strFormula) {
        $atomParser = new AtomParser();
        $result = $atomParser->parse($strFormula);
        if (!$result->isAccepted()) {
            throw new InvalidArgumentException();
        }
        $strFormula = $result->getRemainder();
        $strName = $result->getResult();
        $strCount = 1;
        $numberParser = new IntParser();
        $numberResult = $numberParser->parse($strFormula);
        if ($numberResult->isAccepted()) {
            $strCount = $numberResult->getResult();
            $strFormula = $numberResult->getRemainder();
        }
        return new AtomCount($strName, $strCount);
    }

    /**
     * Extract losses from formula
     * @param $arMap array map of atom => count key:values
     * @param $losses
     * @see LossesEnum
     * @return string reduced formula
     */
    public static function formulaExtractLosses($arMap, $losses) {
        $arMap = LossesEnum::subtractLosses($losses, $arMap);
        ksort($arMap);
        $strFormulaResult = "";
        foreach ($arMap as $key => $value) {
            if ($value === 1) {
                $strFormulaResult .= $key;
            } else {
                $strFormulaResult .= $key . $value;
            }
        }
        return $strFormulaResult;
    }

    /**
     * Translate (Isomeric) SMILES to Generic SMILES
     * @param string $smiles SMILES
     * @return string Generic SMILES
     */
    public static function genericSmiles(string $smiles) {
        $stack = [];
        $smilesNext = str_split($smiles);
        foreach ($smilesNext as $smile) {
            switch ($smile) {
                case ']':
                    $stack = self::isoText($stack);
                    break;
                case '/':
                case '\\':
                    break;
                case ')':
                    $index = sizeof($stack) - 1;
                    if ($stack[$index] === '(') {
                        array_pop($stack);
                    } else {
                        array_push($stack, $smile);
                    }
                    break;
                default:
                    array_push($stack, $smile);
                    break;
            }
        }
        return implode('', $stack);
    }

    /**
     * Work with @@ in SMILES
     * @param $stack
     * @return array
     */
    public static function isoText($stack) {
        $text = [];
        $c = ']';
        $last = '';
        while ($c != '[') {
            switch ($c) {
                case '@':
                    break;
                case 'H':
                    if ($last !== '@') {
                        array_unshift($text, $c);
                    }
                    break;
                default:
                    array_unshift($text, $c);
                    break;
            }
            $last = $c;
            $c = array_pop($stack);
        }
        array_unshift($text, '[');
        if (sizeof($text) === 3 && $text[1] === 'H') {
            $text = [];
        }
        if (sizeof($text) === 3) {
            $text = [$text[1]];
        }
        if (sizeof($text) === 4) {
            $text = [$text[1]];
        }
        return array_merge($stack, $text);
    }

}
