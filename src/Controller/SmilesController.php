<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;
use App\Smiles\Graph;
use App\Structure\UniqueSmilesStructure;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * Class SmilesController
 * @package App\Controller
 */
class SmilesController extends AbstractController {

    /**
     * @Route("/rest/smiles/unique", name="smiles_unique", methods={"POST"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"SMILES"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Array of smiles objects: Shouldn't be empty. SMILES is special format of molecular structures like: CCC(C)C1C(=O)NC(C(=O)NCCCC(C(=O)NC(C(=O)N2CCCC2C(=O)N1)CC3=CC=CC=C3)NC(=O)C(C(C)CC)NC(=O)C)C(C)CC",
     *          @SWG\Schema(type="string",
     *              example="[{""smiles"": ""CC(=O)CC""}]"),
     *      ),
     *     @SWG\Response(response="200", description="Return Unique SMILES."),
     *     @SWG\Response(response="401", description="Return when input is wrong."),
     *)
     */
    public function uniqueSmiles(Request $request) {
        $mapper = new JsonMapper();
        try {
            $smilesInput = $mapper->mapArray(json_decode($request->getContent()), []);
        } catch (JsonMapper_Exception $e) {
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_JSON_FORMAT));
        }

        $length = count($smilesInput);
        $smilesFirst = new UniqueSmilesStructure();
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
        return new JsonResponse($res);
    }

}
