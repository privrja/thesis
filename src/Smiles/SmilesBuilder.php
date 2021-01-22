<?php

namespace App\Smiles;

use InvalidArgumentException;

class SmilesBuilder {

    private $arSmiles = [];

    /** @var string[] $arBadInput */
    private $arBadInput = [];

    /**
     * SmilesBuilder constructor.
     * @param string $strText
     */
    public function __construct($strText) {
        $arTexts = $this->prepareTexts($strText);
        foreach ($arTexts as $strSmiles) {
            try {
                $this->arSmiles[] = new Smiles($strSmiles);
            } catch (InvalidArgumentException $exception) {
                $this->arBadInput[] = $strSmiles;
            }
        }
    }

    public function getSmiles() {
        return $this->arSmiles;
    }

    /**
     * Split string to array by dot separator
     * @param string $strText
     * @return array
     */
    private function prepareTexts($strText) {
        return explode('.', $this->removeWhiteSpace($strText));
    }

    /**
     * Remove all whitespace from string
     * @param string $strText
     * @return null|string|string[]
     */
    private function removeWhiteSpace($strText) {
        $strTrimText = preg_replace('/\s+/', '', $strText);
        return !isset($strTrimText) ? $strText : $strTrimText;
    }

    /**
     * Remove unnecessary parentheses from SMILES
     * example CCC(CC)(C) -> CCC(CC)C
     * example C(=O)C(C(C)) -> C(=O)CCC
     * @param {Array} stackRight
     * @return {Array}
     */
    public static function removeUnnecessaryParentheses($strSmiles) {
        $stackRight = str_split($strSmiles, 1);
        $stackRightLength = sizeof($stackRight);
        if ($stackRightLength === 0) {
            return [];
        }
        $stackLeft = [];
        $stackLeftLength = 0;
        $lastLiteral = $literal = "";
        while ($stackRightLength > 0) {
            $literal = array_shift($stackRight);
            $stackRightLength--;
            if (")" === $literal && ")" === $lastLiteral) {
                SmilesBuilder::removeParentheses($stackLeft, $stackLeftLength, false, $literal);
            } else {
                $stackLeft[] = $literal;
                $stackLeftLength++;
            }
            $lastLiteral = $literal;
        }

        $literal = array_pop($stackLeft);
        $stackLeftLength--;
        if (")" === $literal && $stackRightLength === 0) {
            SmilesBuilder::removeParentheses($stackLeft, $stackLeftLength);
        } else {
            $stackLeft[] = $literal;
        }
        return implode('', $stackLeft);
    }

    /**
     * Remove unnecessary parentheses from stack
     * go through stack and when find proper closing bracket,
     * then remove it and push back removed data when searching in stack
     * @param $stack
     * @param $stackLength
     * @param bool $end
     * @param string $literal
     */
    public static function removeParentheses(&$stack, &$stackLength, $end = true, $literal = "") {
        $stackTmp = [];
        $leftBraces = 0;
        $rightBraces = 1;
        if (!$end) {
            array_pop($stack);
            $stackLength--;
        }
        while (true) {
            $lit = array_pop($stack);
            $stackLength--;
            if ("(" === $lit) {
                $leftBraces++;
            } else if (")" === $lit) {
                $rightBraces++;
            }
            if ($leftBraces === $rightBraces) {
                SmilesBuilder::moveAllValuesInStackToAnotherStack($stackTmp, $stack, $stackLength);
                if (!$end) {
                    $stack[] = $literal;
                }
                break;
            }
            if ($stackLength < 0) {
                throw new InvalidArgumentException();
            }
            $stackTmp[] = $lit;
        }
    }

    /**
     * Remove all values from stackSource and push it to stackDestination
     * @param {Array} stackSource stack to remove values
     * @param {Array} stackDestination stack to add values from stackSource
     */
    public static function moveAllValuesInStackToAnotherStack($stackSource, &$stackDestination, &$stackDestinationLength) {
        while (!empty($stackSource)) {
            $stackDestination[] = array_pop($stackSource);
            $stackDestinationLength++;
        }
    }

}
