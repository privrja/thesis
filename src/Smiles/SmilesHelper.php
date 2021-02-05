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
        $graph = new Graph($smilesFirst->smiles);
        try {
            $smilesFirst->unique = $graph->getUniqueSmiles();
        } catch (IllegalStateException $e) {
            $smilesFirst->unique = null;
        }

        $res = [$smilesFirst];
        for ($i = 1; $i < $length; $i++) {
            $smiles = new UniqueSmilesStructure();
            $smiles->id = $i;
            $smiles->smiles = $smilesInput[$i]->smiles;
            $graph = new Graph($smiles->smiles);
            try {
                $smiles->unique = $graph->getUniqueSmiles();
            } catch (IllegalStateException $e) {
                $smiles->unique = null;
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

}
