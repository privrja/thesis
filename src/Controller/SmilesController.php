<?php

namespace App\Controller;

use App\Smiles\SmilesHelper;
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
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *)
     */
    public function uniqueSmiles(Request $request) {
        $smilesInput = SmilesHelper::checkInputJson($request);
        if ($smilesInput instanceof JsonResponse) {
            return $smilesInput;
        }
        $length = count($smilesInput);
        $nextCheck = SmilesHelper::checkNext($smilesInput, $length);
        if ($nextCheck instanceof JsonResponse) {
            return $nextCheck;
        }
        return new JsonResponse(SmilesHelper::unique($smilesInput, $length));
    }


}
