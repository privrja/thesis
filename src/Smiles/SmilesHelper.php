<?php

namespace App\Smiles;

use App\Base\Message;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;
use App\Structure\UniqueSmilesStructure;
use Exception;
use JsonMapper;
use JsonMapper_Exception;
use Symfony\Component\HttpFoundation\Request;

class SmilesHelper {

    /**
     * @param $smiles
     * @return string
     * @throws IllegalStateException
     */
    static function getUniqueSmiles($smiles) {
        $graph = new Graph($smiles);
        return $graph->getUniqueSmiles();
    }

    static function checkInputJson(Request $request) {
        $mapper = new JsonMapper();
        try {
            $smilesInput = $mapper->mapArray(json_decode($request->getContent()), []);
        } catch (JsonMapper_Exception $e) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_JSON_FORMAT));
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_JSON_FORMAT));
        }
        return $smilesInput;
    }

    static function checkNext(array $smilesInput, int $length) {
        if ($length === 0) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_JSON_FORMAT));
        }
        foreach ($smilesInput as $input) {
            try {
                $input->smiles;
            } catch (Exception $e) {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_JSON_FORMAT));
            }
        }
        return false;
    }

    static function unique(array $smilesInput, int $length): array {
        $smilesFirst = new UniqueSmilesStructure();
        $smilesFirst->id = 0;
        $smilesFirst->smiles = $smilesInput[0]->smiles;
        $smilesFirst->sameAs = null;
        $smilesFirst->smiles = $smilesInput[0]->smiles;
        if (property_exists($smilesInput[0], 'isPolyketide') && isset($smilesInput[0]->isPolyketide)) {
            $smilesFirst->isPolyketide = $smilesInput[0]->isPolyketide;
        }
        try {
            $graph = new Graph(SmilesHelper::canonicalSmiles($smilesFirst->smiles));
            $smilesFirst->unique = $graph->getUniqueSmiles();
        } catch (Exception $e) {
            $smilesFirst->unique = $smilesFirst->smiles;
        }

        $res = [$smilesFirst];
        for ($i = 1; $i < $length; $i++) {
            $smiles = new UniqueSmilesStructure();
            $smiles->id = $i;
            $smiles->smiles = $smilesInput[$i]->smiles;
            if (property_exists($smilesInput[$i], 'isPolyketide') && isset($smilesInput[$i]->isPolyketide)) {
                $smiles->isPolyketide = $smilesInput[$i]->isPolyketide;
            }
            try {
                $graph = new Graph(SmilesHelper::canonicalSmiles($smiles->smiles));
                $smiles->unique = $graph->getUniqueSmiles();
            } catch (Exception $e) {
                $smiles->unique = $smiles->smiles;
            }
            for ($j = 0; $j < $i; $j++) {
                if ($smiles->unique == $res[$j]->unique) {
                    $smiles->sameAs = $j;
                    break;
                } else {
                    $smiles->sameAs = null;
                }
            }
            array_push($res, $smiles);
        }
        return $res;
    }

    static function canonicalSmiles(string $smiles) {
        $stack = [];
        $stackLength = 0;
        $arSmiles = str_split($smiles);
        foreach ($arSmiles as $char) {
            switch ($char) {
                case ']':
                    $stackLength = self::isoText($stack, $stackLength);
                    break;
                case '/':
                case '\\':
                    break;
                case ')':
                    $index = $stackLength - 1;
                    if ($stack[$index] === '(') {
                        array_pop($stack);
                        $stackLength--;
                    } else {
                        array_push($stack, $char);
                        $stackLength++;
                    }
                    break;
                default:
                    array_push($stack, $char);
                    $stackLength++;
                    break;
            }
        }
        return join($stack);
    }

    private static function isoText(&$stack, $stackLength) {
        $text = [];
        $textLength = 0;
        $char = ']';
        $last = '';
        while ($char !== '[') {
            switch ($char) {
                case '@':
                    break;
                case 'H':
                    if ($last !== '@') {
                        array_unshift($text, $char);
                        $textLength++;
                    }
                    break;
                default:
                    array_unshift($text, $char);
                    $textLength++;
                    break;
            }
            $last = $char;
            $char = array_pop($stack);
            $stackLength--;
        }
        array_unshift($text, '[');
        $textLength++;
        if ($textLength === 3 && $text[1] === 'H') {
            $text = [];
            $textLength = 0;
        }
        if ($textLength === 3 || $textLength === 4) {
            $text = [$text[1]];
            $textLength = 1;
        }
        $stack = array_merge($stack, $text);
        return $stackLength + $textLength;
    }

}
